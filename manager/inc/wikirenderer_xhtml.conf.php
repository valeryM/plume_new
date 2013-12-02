<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/**
 * WikiRenderer Configuration for the xhtml output in Plume CMS.
 *
 * @author Laurent Jouanneau <jouanneau@netcourrier.com>
 * @copyright 2003 Laurent Jouanneau
 * @module Wiki Renderer
 * @version 2.0RC1
 * @since 20/12/2003
 *
 * @author Loic d'Anterroches
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */


class WikiRenderXhtmlConfig 
{
    /**
     * @var array inline tags
     */
    var $inlinetags = array(
                            'strong' => array('__', '__', null, null),
                            'em' => array('\'\'', '\'\'', null, null),
                            'code' => array('@@', '@@', null, null),
                            'q' => array('^^', '^^', array('lang', 'cite'), 
                                         null),
                            'cite' => array('{{', '}}', array('title'), null),
                            'acronym' => array('??', '??', array('title'), 
                                               null),
                            'link' => array('[', ']', 
                                            array('href', 'lang', 'title'),
                                            'xhtml_buildlink'),
                            'image' => array('((', '))', 
                                             array('src', 'alt', 'align',
                                                   'longdesc'),
                                             'xhtml_wikibuildimage'),
                            'anchor' => array('~~', '~~', array('id'), 
                                              'xhtml_wikibuildanchor')
                            );

    /**
     * @var array block tags, the order is important
     */
    var $bloctags = array(
                          'title' => true, 
                          'list' => true, 
                          'pre' => true,
                          'hr' => true, 
                          'blockquote' => true, 
                          'definition' => true, 
                          'tableth' => true, 
                          'gallery' => true, 
                          'p' => true
                          );
    
    /**
     * @var array simple tags, direct replacement
     */
    var $simpletags = array('%%%' => '<br />');

    /**
     * @var integer Minimal level for the titles
     */
    var $minHeaderLevel = 2;
    var $headerOrder = false;
    var $blocAttributeTag = '��';
    var $inlineTagSeparator = '|';
    var $escapeSpecialChars = true;
    var $checkWikiWord = false; //Not used into a wiki
    var $checkWikiWordFunction = null;
}

/** ------------------------------------------------------ *
 *  Needed functions for the generations of the XHTML code *
 *  ------------------------------------------------------ *
 */

/**
 * Generation of the image. It generates also the image in the
 * case of the gallery.
 */
function xhtml_wikibuildimage($contents, $attr)
{
    $baseurl = www::getManagedWebsiteUrl();
    $cnt = count($contents);
    $attribs = array();
    if ($cnt > 4) $cnt=4;
    switch ($cnt){
    case 4:
        $attribs['longdesc'] = $contents[3];
    case 3:
        // multiple arguments can be passed in any order in the
        // the form of "arg1:arg2:arg3" these include alignement,
        // type, size of the thumbnail
        if (!isset($contents[2])) {
            $contents[2] = '';
        }
        $contents[2] = ':'.$contents[2].':';
        // simple alignement put in style.
        if (preg_match('/\:(l|g|r|d|c)\:/i',$contents[2],$match)) {
            $match[1] = strtolower($match[1]);
            if ($match[1]=='l' || $match[1]=='g') {
                $attribs['class'] = 'px-left';
            } elseif ($match[1]=='r' ||  $match[1]=='d') {
                $attribs['class'] = 'px-right';
            } elseif ($match[1]=='c') {
                $attribs['class'] = 'px-center';
            }
        } 
        // is it a gallery? (need the wrapping div)
        if (preg_match('/\:gal/i', $contents[2])) {
            $attribs['gallery'] = true;
            // default max sizes
            $attribs['width'] = 200;
            $attribs['height'] = 200;
        } 
        // is it a link through thumbnail?
        if (preg_match('/\:([0-9]+)x([0-9]+)\:/i', $contents[2], $match)) {
            $attribs['width'] = (int) $match[1];
            $attribs['height'] = (int) $match[2];
        }
    case 2:
        $attribs['alt'] = $contents[1];
    case 1:
    default:
        //if (ereg('^/',$contents[0])) {
        if (preg_match('#^/#',$contents[0])) {
        	
            $attribs['file'] = $contents[0];
            $contents[0] = $baseurl.$contents[0];
        }
        $attribs['src'] = $contents[0];
    }

    if (!isset($attribs['alt'])) $attribs['alt'] = '';
    if (!isset($attribs['style'])) $attribs['style'] = '';

    // Need to generate the thumbnail if needed, this is done
    // only for locale files.
    $ok = false;
    if (!empty($attribs['width']) && !empty($attribs['file'])) {
        include_once dirname(__FILE__).'/class.thumbnail.php';

        $thumbdir = config::f('xmedia_root').'/thumb';
        $thumb = new thumbnail($thumbdir);
        $thumbfile = $thumb->getPath($attribs['file'], 
                                     $attribs['width'], 
                                     $attribs['height']);
        $thumburl = '';
        $thumbsize = array();
        $ok = true;
        if (!file_exists($thumbfile)) {
            // create thumbnail and get url to it
            $doc_root = www::getDocumentRoot();
            $ok = $thumb->create($doc_root.$attribs['file'], 
                                 $thumbfile, $attribs['width'], 
                                 $attribs['height']);
            if ($ok) {
                $thumburl = config::f('rel_url_files').'/thumb/'
                    .$thumb->getName($attribs['file'], 
                                     $attribs['width'], 
                                     $attribs['height']);
                $thumbsize = $thumb->getSize();
            } else {
                return 'Error building thumbnail: '.$attribs['file'].'<br />';
            }
        } else {
            // the thumbnail already exists, get the info from the file
            $thumburl = config::f('rel_url_files').'/thumb/'
                .$thumb->getName($attribs['file'], 
                                 $attribs['width'], 
                                 $attribs['height']);
            $thumbsize = getimagesize($thumbfile);
        }
    }

    // need to generate the HTML depending of the request.

    if (empty($attribs['gallery'])) {
        if (empty($attribs['width'])) {
            // simple image
            $c = ' ';
            foreach ($attribs as $k => $v) {
                if ($k != 'file') $c .= $k.'="'.$v.'" ';
            }
            return '<img'.$c.'/>';
        } else {
            // thumbnail.
            if ($ok) {
                return '<a href="'.$attribs['src'].'" title="'
                    .$attribs['alt'].'"><img src="'.$thumburl.'" alt="" '
                    .$thumbsize[3].' style="'.$attribs['style'].'"/></a>';
            }
        }
    } else {
        // gallery layout
        if ($ok) {
            return '<div class="gallery-img"><a href="'.$attribs['src']
                .'" title="'.$attribs['alt'].'"><img class="gallery-thumb" '
                .'src="'.$thumburl.'" alt="" '.$thumbsize[3]
                .' /></a><p class="gallery-legend">'.$attribs['longdesc']
                .'</p></div>'."\n";
        }
    }
}

function xhtml_wikibuildanchor($contents, $attr){
    return '<a id="'.$contents[0].'"></a>';
}



class xhtml_gallery extends WikiRendererBloc
{
    var $type = 'gallery';
    var $regexp = '/^\$(.*)/';
    var $_openTag = "<div class=\"gallery\">\n<div class=\"gallery-top\">\n&nbsp;\n</div>";
    var $_closeTag = "<div class=\"gallery-bottom\">\n&nbsp;\n</div></div>";

    function getRenderedLine()
    {
        return $this->_renderInlineTag($this->_detectMatch[1]);
    }

}

/**
 * traite les signes de types table
 */
class xhtml_tableth extends WikiRendererBloc 
{
    var $type='table';
    var $regexp="/^\| ?(.*)/";
    var $_openTag='<table class="wiki-table">';
    var $_closeTag='</table>';
    var $_colcount=0;

    function open()
    {
        $this->_colcount=0;
        return $this->_openTag;
    }
    
    function getRenderedLine()
    {
    
        $result=explode(' | ',trim($this->_detectMatch[1]));
        $str='';
        $t='';
    
        if((count($result) != $this->_colcount) && ($this->_colcount!=0))
            $t='</table><table class="wiki-table">';
        $this->_colcount=count($result);
    
        for($i=0; $i < $this->_colcount; $i++){
            if (preg_match('/\s*!(.*)!\s*$/', $result[$i], $match)) {
                $str .= '<th>'. $this->_renderInlineTag($match[1]).'</th>';
            } else {
                $str.='<td>'. $this->_renderInlineTag($result[$i]).'</td>';
            }
        }
        $str=$t.'<tr>'.$str.'</tr>';
    
        return $str;
    }

}


function xhtml_buildlink($contents, $attr)
{
    global $_PX_context;
    $baseurl = '';
    if (!empty($_PX_context['out'])) {
        $baseurl = $_PX_context['out'];
    }
    $cnt=count($contents);
    $attribut='';
    if ($cnt>1) {
        if (preg_match('#^xlink://(.+)#',$contents[1],$preg)) {
            $data = explode('/',$preg[1]);
            if (count($data) == 1) {
                $id   = $data[0];
                $type = '';
            } elseif (count($data) > 1) {
                $id   = $data[0];
                $type = $data[1];
            }
            return text::XlinkCreate($id,$type,$contents[0]);
        } 
        if ($cnt> count($attr))
            $cnt=count($attr)+1;
        if (strpos($contents[1],'javascript:')!==false) // for security reason
            $contents[1]='#';
        //if (ereg('^/',$contents[1]))
        if (preg_match('#^/#',$contents[1]))
            $contents[1] = $baseurl.$contents[1];
        //if (ereg('^mailto:',$contents[1])) {
        if (preg_match('#^mailto:#',$contents[1])) {        	
            $contents[1] = 'mailto:'.text::HexEncode(substr($contents[1],7));
            $contents[0] = text::HexEncode($contents[0], true);
        }
        for ($i=1;$i<$cnt;$i++){
            $attribut.=' '.$attr[$i-1].'="'.$contents[$i].'"';
        }
    } elseif ($cnt == 1) {
        if (preg_match('#^xlink://(.+)#',$contents[0],$preg)) {
            $data = explode('/',$preg[1]);
            if (count($data) == 1) {
                $id   = $data[0];
                $type = '';
            } elseif (count($data) > 1) {
                $id   = $data[0];
                $type = $data[1];
            }
            return text::XlinkCreate($id,$type,'');
        }
        if (strpos($contents[0],'javascript:')!==false) // for security reason
            $contents[0]='#';
        //if (ereg('^/',$contents[0]))
        if (preg_match('#^/#',$contents[0]))
            $contents[0] = $baseurl.$contents[0];
    
        $attribut=' href="'.$contents[0].'"';
        if (strlen($contents[0]) > 40)
            $contents[0]=substr($contents[0],0,40).'(..)';
    }
    if ($cnt >= 1) 
        return '<a'.$attribut.'>'.$contents[0].'</a>';
    return '';
    
}


/**
 * traite les signes de types liste
 */
class xhtml_list extends WikiRendererBloc 
{

    var $_previousTag;
    var $_firstItem;
    var $_firstTagLen;
    var $type='list';
    var $regexp="/^([\*#-]+)(.*)/";

    function open()
    {
        $this->_previousTag = $this->_detectMatch[1];
        $this->_firstTagLen = strlen($this->_previousTag);
        $this->_firstItem=true;

        if (substr($this->_previousTag,-1,1) == '#')
            return "<ol>\n";
        else
            return "<ul>\n";
    }

    function close()
    {
        $t=$this->_previousTag;
        $str='';

        for ($i=strlen($t); $i >= $this->_firstTagLen; $i--){
            $str.=($t{$i-1}== '#'?"</li></ol>\n":"</li></ul>\n");
        }
        return $str;
    }

    function getRenderedLine()
    {
        $t=$this->_previousTag;
        $d=strlen($t) - strlen($this->_detectMatch[1]);
        $str='';

        if ( $d > 0 ){ // on remonte d'un ou plusieurs cran dans la hierarchie...
            $l=strlen($this->_detectMatch[1]);
            for($i=strlen($t); $i>$l; $i--){
                $str.=($t{$i-1}== '#'?"</li></ol>\n":"</li></ul>\n");
            }
            $str.="</li>\n<li>";
            $this->_previousTag=substr($this->_previousTag,0,-$d); // pour �tre sur...

        } elseif ( $d < 0 ){ // un niveau de plus
            $c=substr($this->_detectMatch[1],-1,1);
            $this->_previousTag.=$c;
            $str=($c == '#'?"<ol>\n<li>":"<ul>\n<li>");

        } else {
            $str=($this->_firstItem ? '<li>':'</li><li>');
        }
        $this->_firstItem=false;
        return $str.$this->_renderInlineTag($this->_detectMatch[2]);
    }
}


/**
 * traite les signes de types hr
 */
class xhtml_hr extends WikiRendererBloc 
{

    var $type='hr';
    var $regexp='/^={4,} *$/';
    var $_closeNow=true;

    function getRenderedLine(){
        return '<hr />';
    }

}

/**
 * traite les signes de types titre
 */
class xhtml_title extends WikiRendererBloc 
{
    var $type='title';
    var $regexp="/^(\!{1,3})(.*)/";
    var $_closeNow=true;
    var $_minlevel=3;
    var $_order=false;

    function WRB_title(&$wr)
    {
        $this->_minlevel = $wr->config->minHeaderLevel;
        $this->_order = $wr->config->headerOrder;
        parent::WikiRendererBloc($wr);
    }

    function getRenderedLine()
    {
        if($this->_order)
            $hx= $this->_minlevel + strlen($this->_detectMatch[1])-1;
        else
            $hx= $this->_minlevel + 3-strlen($this->_detectMatch[1]);
        return '<h'.$hx.'>'.$this->_renderInlineTag($this->_detectMatch[2]).'</h'.$hx.'>';
    }
}

/**
 * traite les signes de type paragraphe
 */
class xhtml_p extends WikiRendererBloc 
{
    var $type='p';
    var $regexp="/(.*)/";
    var $_openTag='<p>';
    var $_closeTag='</p>';
}

/**
 * traite les signes de types pre (pour afficher du code..)
 */
class xhtml_pre extends WikiRendererBloc 
{

    var $type='pre';
    var $regexp="/^ (.*)/";
    var $_openTag='<pre>';
    var $_closeTag='</pre>';

    function getRenderedLine()
    {
        return $this->_renderInlineTag($this->_detectMatch[1]);
    }

}


/**
 * traite les signes de type blockquote
 */
class xhtml_blockquote extends WikiRendererBloc 
{
    var $type='bq';
    var $regexp="/^(\>+)(.*)/";

    function open()
    {
        $this->_previousTag = $this->_detectMatch[1];
        $this->_firstTagLen = strlen($this->_previousTag);
        $this->_firstLine = true;
        return str_repeat('<blockquote>',$this->_firstTagLen).'<p>';
    }

    function close()
    {
        return '</p>'.str_repeat('</blockquote>',strlen($this->_previousTag));
    }


    function getRenderedLine()
    {

        $d=strlen($this->_previousTag) - strlen($this->_detectMatch[1]);
        $str='';

        if( $d > 0 ){ // on remonte d'un cran dans la hierarchie...
            $str='</p>'.str_repeat('</blockquote>',$d).'<p>';
            $this->_previousTag=$this->_detectMatch[1];
        }elseif( $d < 0 ){ // un niveau de plus
            $this->_previousTag=$this->_detectMatch[1];
            $str='</p>'.str_repeat('<blockquote>',-$d).'<p>';
        }else{
            if($this->_firstLine)
                $this->_firstLine=false;
            else
                $str='<br />';
        }
        return $str.$this->_renderInlineTag($this->_detectMatch[2]);
    }
}

/**
 * traite les signes de type definition list (dl)
 */
class xhtml_definition extends WikiRendererBloc 
{

    var $type='dfn';
    var $regexp="/^;(.*) : (.*)/i";
    var $_openTag='<dl>';
    var $_closeTag='</dl>';

    function getRenderedLine()
    {
        $dt=$this->_renderInlineTag($this->_detectMatch[1]);
        $dd=$this->_renderInlineTag($this->_detectMatch[2]);
        return "<dt>$dt</dt>\n<dd>$dd</dd>\n";
    }
}
?>
