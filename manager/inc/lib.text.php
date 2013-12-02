<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Plume CMS, a website management application.
# Copyright (C) 2001-2005 Loic d'Anterroches and contributors.
#
# Plume CMS is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# Plume CMS is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

require_once dirname(__FILE__).'/lib.path.php';

/**
 * The text class provides a set of methods to manipulate
 * text. The methods are static methods.
 */
class text 
{

	public static function addDelimiter(&$item, $key) {
		$item = '\''.$item.'\'';
	}
    /** 
     * Return type of text (wiki, html, etc.)
     *
     * @param string Text to get the type from
     * @return mixed Type or empty string if not found
     */
    public static function getType($text='')
    {
        if (0 == strlen($text)) 
            return '';
        $format = '';
        if (preg_match('/^=([A-Za-z]{4})\s*/', $text, $match)) {
            $format  = trim($match[1]);
        }
        return $format;
    }

    /**
     * Get the raw content of a text.
     * It removes the type of the text if any.
     *
     * @param string Text
     * @return string Raw text
     */

    public static function getRawContent($text='')
    {
        if (0 == strlen($text)) 
            return '';
        if (preg_match('/^=[A-Za-z]{4}\s*/', $text)) {
            $text = substr($text, 5);
        }
        return trim($text);
    }

    /** 
     * Call the wiki parser depending of the content of the string
     *
     * @param string unparsed content
     * @param string output format, ('Html') or 'Text'
     * @return string parsed content
     *
     */
    public static function parseContent($text='', $output='Html')
    {
        if (0 == strlen($text)) return '';
        $output = 'To'.$output;

        $format = 'wiki';
        if (preg_match('/^=([A-Za-z]{4})\s*/', $text, $match)) {
            $text = substr($text, 5);
            $format  = trim($match[1]);
        }
        /*
        $log = date::stamp().' - lib.text::parseContent()'.chr(10);
        $log .= $text . chr(10);
        file_put(SEARCH_LOG, $log);
        */
        if (is_callable(array('text','parse'.$format.$output))) {
            $parsefunc = 'parse'.$format.$output;
        } else {
            $parsefunc = 'parseWikiToHtml';
        }
        //echo $parsefunc;
        $content = call_user_func(array('text', $parsefunc), $text);
        Hook::run('onParseContent', array('text' => &$content, 
                                          'output' => &$output));
        /*
        $log = date::stamp().' - lib.text::parseContent() return'. chr(10);
        $log .= $content.chr(10);
        file_put(SEARCH_LOG, $log);
        */
        return $content;
        
    }

    /**
     * Parse wiki formatted text into XHTML
     *
     * @param string Wiki formatted text
     * @return string XHTML
     */
    public static function parseWikiToHtml($text)
    {
        include_once dirname(__FILE__).'/../extinc/WikiRenderer.lib.php';
        include_once dirname(__FILE__).'/wikirenderer_xhtml.conf.php';
    
        $wkr = new WikiRenderer(new WikiRenderXhtmlConfig(), 'xhtml_');
        return $wkr->render($text);
    }

    public static function parseWikiToText($text)
    {
        include_once dirname(__FILE__).'/../extinc/WikiRenderer.lib.php';
        include_once dirname(__FILE__).'/../extinc/WikiRenderer_w2text.conf.php';
    
        $wkr = new WikiRenderer(new WikiRenderer_w2text());
        return $wkr->render($text);
    }

    public static function parseHtmlToHtml($text)
    {
        while (preg_match('#<xlink\s+id="(.*)"\s+type="(.*)"\s*/>#i', $text, $match) 
               or
               preg_match('#<xlink\s+id="(.*)"\s+type="(.*)"\s*>(.*)</xlink>#i', $text, $match)) {
            $all   = quotemeta($match[0]);
            $id    = $match[1];
            $type  = $match[2];
            $title = '';
            if (isset($match[3])) $title = $match[3];
            $replace = text::xlinkCreate($id, $type, $title);
            $title   = '';
            $text    = preg_replace('#'.$all.'#', $replace, $text);
        }
        return $text;
    }

    public static function parseHtmlToText($text)
    {
        include_once dirname(__FILE__).'/../extinc/class.html2text.php';
        $h2t = new html2text($text);
        return $h2t->get_text();
    }

    /** create a link to a resource in the CMS
     *
     * @return string the HTML tag with the link
     * @param  string id of the resource (art-12, file-2, news-1, etc.)
     * @param  string format of the link 'textlink', 'img' or 'icon'
     * @param  string title for the link
     */
     public static function xlinkCreate($id, $type = 'textlink', $title = '')
    {
        include_once dirname(__FILE__).'/class.manager.php';
        $m = new Manager();
        $r = $m->getResourceByIdentifier($id);
        if ($r->isEmpty()) {
            return '';
        } else {        
            if (strlen($title)) {
                $r->setField('new-title',$title);
            } else {
                $r->setField('new-title', $r->f('title'));
            }
            if (function_exists('text::xlinkCreate'.$type)) {
                $createfunc = 'xlinkCreate'.$type;
            } else {
                $createfunc = 'xlinkCreateTextLink';
            }
            return call_user_func(array('text', $createfunc), $r);
               

            return $createfunc($r);
        }
    }


    public static function xlinkCreateTextLink($res)
    {
        return '<a href="'.$res->getPath().'">'.$res->f('new-title').'</a>';
    }

    /** 
     * hex encode a string not to be 'seen' by a spam system
     *
     * @return string encoded
     * @param  string not encoded
     * @param  bool   is the string for outside a &lt;a ...&gt; link?
     */  
    public static function hexEncode($str, $text=false)
    {
        $encoded = '';
        if ($text) {
            for ($i=0; $i<strlen($str); $i++) {
                if (ord(substr($str, $i, 1)) < 127) {
                    $encoded .= '&#x'.bin2hex(substr($str, $i, 1)).';';
                } else {
                    $encoded .= substr($str, $i, 1);
                }
            }
        } else {
            $encoded = bin2hex($str);
            $encoded = chunk_split($encoded, 2, '%');
            $encoded = '%'.substr($encoded, 0, strlen($encoded) - 1);
        }
        return $encoded;
    } 

    /** 
     * Remove entities of a string
     * 
     * @author Olivier Meunier
     *
     * @param  string String with entities
     * @return string String without entities
     */
    public static function removeEntities($string)
    {
        return html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        /*
        if (strtolower(config::f('encoding')) == 'utf-8') 
            $string = utf8_decode($string);
    
        // Table of codes  130 to 140 and 145 to 156
        $tags = array('�'=>'&sbquo;','�'=>'&fnof;','�'=>'&bdquo;',
                      '�'=>'&hellip;','�'=>'&dagger;', 
                      '�'=>'&Dagger;','�'=>'&circ;','�'=>'&permil;',
                      '�'=>'&Scaron;','�'=>'&lsaquo;','�'=>'&OElig;',
                      '�'=>'&lsquo;','�'=>'&rsquo;','�'=>'&ldquo;',
                      '�'=>'&rdquo;','�'=>'&bull;','�'=>'&ndash;',
                      '�'=>'&mdash;','�'=>'&tilde;','�'=>'&trade;',
                      '�'=>'&scaron;','�'=>'&rsaquo;','�'=>'&oelig;',
                      '�'=>'&Yuml','�'=>'&euro;');
        $vtags = array('�'=>'&#8218;','�'=>'&#402;','�'=>'&#8222;',
                       '�'=>'&#8230;','�'=>'&#8224;','�'=>'&#8225;',
                       '�'=>'&#710;','�'=>'&#8240;','�'=>'&#352;',
                       '�'=>'&#8249;','�'=>'&#338;','�'=>'&#8216;',
                       '�'=>'&#8217;','�'=>'&#8220;','�'=>'&#8221;',
                       '�'=>'&#8226;','�'=>'&#8211;','�'=>'&#8212;',
                       '�'=>'&#732;','�'=>'&#8482;','�'=>'&#353;',
                       '�'=>'&#8250;','�'=>'&#339;','�'=>'&#376;',
                       '�'=>'&#8364;');
    
        $tags = array_merge($tags,get_html_translation_table(HTML_ENTITIES));
        foreach($tags as $k => $v) {
            $ASCIItags[$k] = '&#'.ord($k).';';
        }
    
        $string = str_replace($tags,array_flip($tags),$string);
        $string = str_replace($ASCIItags,array_flip($ASCIItags),$string);
        $string = str_replace(array_values($vtags),array_keys($vtags),$string);
    
        return if_utf8($string);
        */
    }

    /** 
     * Remove the HTML entities of a string to be XML compliant.
     *
     * @author Olivier Meunier
     *
     * @param string String to be converted
     * @param bool Should the output be UTF-8
     * @return string Converted string
     */
    public static function toXML($string,$utf8=true)
    {
        $string = htmlspecialchars(text::removeEntities($string),ENT_NOQUOTES);
        if($utf8) {
            $string = utf8_encode($string);
        }
        return $string;
    }

    /**
     * Convert an URL into words (space delimited).
     *
     * @param string URL
     * @return string Space delimited words
     */
    public static function url2words($url)
    {
        $url = ' '.removeFileExtension($url);
        $url = preg_replace('/[^a-z0-9]/i',' ', $url);
        $url = preg_replace('/[a-z]([A-Z])([A-Z])([a-z]|$)/', " \\1 \\2\\3", $url);
        $url = preg_replace('/([A-Z][a-z0-9]+)/', " \\1", $url);
        $url = preg_replace('/( [0-9]+ )/', ' ', $url);
        return trim(preg_replace('/([A-Z][A-Z]+)/', " \\1", $url));
    }


    /**
     * Convert a string into an URL string.
     *
     * Je n'aime pas �crire du code becomes Je-n-aime-pas-ecrire-du-code
     *
     * @author Olivier Meunier
     *
     * @param string String to convert
     * @param string Separator ('-')
     * @return string URL ready string
     */
    public static function str2url($str, $sep='-')
    {
        $pattern['A'] = '\x{00C0}-\x{00C5}';
        $pattern['AE'] = '\x{00C6}';
        $pattern['C'] = '\x{00C7}';
        $pattern['D'] = '\x{00D0}';
        $pattern['E'] = '\x{00C8}-\x{00CB}';
        $pattern['I'] = '\x{00CC}-\x{00CF}';
        $pattern['N'] = '\x{00D1}';
        $pattern['O'] = '\x{00D2}-\x{00D6}\x{00D8}';
        $pattern['OE'] = '\x{0152}';
        $pattern['S'] = '\x{0160}';
        $pattern['U'] = '\x{00D9}-\x{00DC}';
        $pattern['Y'] = '\x{00DD}';
        $pattern['Z'] = '\x{017D}';
        
        $pattern['a'] = '\x{00E0}-\x{00E5}';
        $pattern['ae'] = '\x{00E6}';
        $pattern['c'] = '\x{00E7}';
        $pattern['d'] = '\x{00F0}';
        $pattern['e'] = '\x{00E8}-\x{00EB}';
        $pattern['i'] = '\x{00EC}-\x{00EF}';
        $pattern['n'] = '\x{00F1}';
        $pattern['o'] = '\x{00F2}-\x{00F6}\x{00F8}';
        $pattern['oe'] = '\x{0153}';
        $pattern['s'] = '\x{0161}';
        $pattern['u'] = '\x{00F9}-\x{00FC}';
        $pattern['y'] = '\x{00FD}\x{00FF}';
        $pattern['z'] = '\x{017E}';
        
        $pattern['ss'] = '\x{00DF}';
        
        foreach ($pattern as $r => $p) {
            $str = preg_replace('/['.$p.']/u',$r,$str);
        }
        
        $str = preg_replace('/[^A-Za-z0-9_\s\'\:\/[\]-]/','',$str);

        $str = strip_tags($str);
        $str = str_replace(array('?','&','#','=','+','<','>'),'',$str);
        $str = str_replace("'",'',$str);
        $str = preg_replace('/[\s]+/',' ',trim($str));
        $str = str_replace('/','-',$str);
        $str = str_replace(' ','-',$str);
        $str = preg_replace('/[-]+/','-',$str);
        
        return $str;
    }

    /** 
     * Clean a string.
     * This function removes more or less everything but do not escape
     * the "'" for DB insertion.
     *
     * @author Olivier Meunier
     * 
     * @param string String to "secure"
     * @param string Secured string
     */
    public static function secure($str)
    {
        $str = trim($str);
        $str = stripslashes($str);
        $str = strip_tags($str);
        $str = htmlspecialchars($str);
        return $str;
    }


    /** 
     * Truncate a string to a certain length if necessary without
     * splitting in the middle of a word and appending the $etc
     * string. Based on smarty modifier.
     *
     * @author   Monte Ohrt <monte at ohrt dot com>
     * @param string
     * @param integer Length
     * @param string etc. string
     * @return string
     */
    public static function truncate($string, $length=80, $etc='...')
    {
        if ($length == 0)
            return '';
        if (strlen($string) > $length) {
            $length -= strlen($etc);
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
            return substr($string, 0, $length).$etc;
        } else {
            return $string;
        }
    }


}
?>
