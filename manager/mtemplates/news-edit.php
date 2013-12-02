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

if (basename($_SERVER['SCRIPT_NAME']) == 'news-edit.php') exit;

/* =================================================== *
 * Preview of the content if some content is available *
 * =================================================== */
if (strlen($news->getUnformattedContent('description'))) {
	/*
    echo '<div class="preview">';
    
    echo '<h2>'.$news->getTextContent('title').'</h2>';
    echo $news->getFormattedContent('description','html');

    echo "<hr class='invisible' id=\"xxx-prevent\" /></div>\n\n";
    */
    echo '<div id="preview" class="preview" style="display:none">';
	echo '<h2>'.$news->getTextContent('title').'</h2>';
	echo $news->getFormattedContent('description', 'html');
	echo '<br>';
	echo $news->details->f('news_titlewebsite');
	echo '<br>';
	echo $news->details->f('news_linkwebsite');
	
	//echo "<hr class='invisible' id=\"xxx-prevent\"/>";
	echo "</div>\n\n";
    
}

/* ================================================= *
 *  If is editable form to modify the content        * 
 * ================================================= */
$location = PathSelector::getLocation();
if ($is_editable) {

	Hook::run('onPrintHeaderManagerPage2', array('m' => &$m));
	    
    echo '<form action="news.php" method="post" id="formPost" ' .
    	'onsubmit="return (isReady(\'n_title\',\''.__('You need to give a title.').'\') &&' .
    			' isDateGreater(\'n_dt\',\'n_dt_e\',\''.addslashes(__('The off-line date is wrong.')).'\'))">';
    
    echo '<script type="text/javascript">'."\n<!--\n".
        "var js_subtype_ids = new Array('".
        implode("','",$arry_subtypes_extra).
        "');\n//-->\n</script>\n";

    if ($news->cats->nbRow() >= 1) {
        /* ================================================= *
         *  The news is already in a category, propose more  * 
         * ================================================= */
        echo '<fieldset><legend><span class="category_style">'. __('Categories')."</span></legend>\n\n";
        echo "<ol>\n";
        while (!$news->cats->EOF()) {
            //echo '<li>'.$news->cats->f('category_name');
            if ($news->cats->f('categoryasso_type') != PX_RESOURCE_CATEGORY_MAIN) {
            	echo '<li ><span class="categoryList">'.$news->cats->f('category_name'). '</span> ('.$news->cats->f('category_path').')';
            	
                echo ' - <a href=\'news.php?delcat=1&amp;resource_id='
                    .$news->f('resource_id').'&amp;cat_id='
                    .$news->cats->f('category_id').'\' title=\''
                    . __('Remove from this category').'\'>';
                echo '<img src="themes/'.$_px_theme
                    .'/images/delete.png" alt="Delete icon" /></a> ';
                echo ' <a href=\'news.php?addcategory=main&amp;resource_id='
                    .$news->f('resource_id').'&amp;cat_id='
                    .$news->cats->f('category_id').'\' title=\''
                    .__('Set as main category').'\'>';
                echo '<img src="themes/'.$_px_theme
                    .'/images/ico_set_as_home.png" alt="Home icon" /></a> ';
            } else {
            	echo '<li ><span class="categoryMain">'.$news->cats->f('category_name'). '</span> ('.$news->cats->f('category_path').') ';
                echo ' - <img src="themes/'.$_px_theme
                    .'/images/ico_home.png" alt="'.__('Main category')
                    .'" />';
            }
            echo "</li>\n";
            $news->cats->moveNext();
        }
        echo "</ol>\n\n";
        echo '<p><span class="nowrap"><label for="cat_id" '
            .'style="display:inline">'.__('Add in another category').' '
            .$m->HelpLink('news', 'h-category').'</label><br />'."\n";
/*            
        echo form::combobox('cat_id',$arry_cat, '', 1)."\n";
*/        
		//echo '<input size="3" type="hidden" name="cat_id" value="1">';
		//echo '<input size="3" name="location" type="hidden" style="height:20px" value="'.$location.'">';     
		echo PathSelector::getFields($location, $m->user->getPref('article_category_id'));
        echo '<input name="addcategory" tabindex="2" type="submit" '
            .'class="submit" value="'.__('Add').'" />'."\n";
        echo '</span></p></fieldset>'."\n".'<p class="selections">';
    } else {  
        /* ================================================= *
         *  A new news, propose a list of categories         * 
         * ================================================= */
        
        echo '<p class="selections">'."\n";
        echo '<span class="nowrap"><label for="cat_id" '
            .'style="display:inline">'.__('Category').' '
            .$m->HelpLink('news', 'h-category').'</label>'."\n";
/*            
        echo form::combobox('cat_id', $arry_cat, 
                            $news->f('category_id'), 
                            $m->user->getPref('news_category_id'),
                            2).'</span>'."\n";
*/
		if ($location !=='') {
			if (false !== strrpos($location,'.')) {
				$cat_id = substr($location, strrpos($location,'.')+1);
			} else $cat_id = $location;
		}
		
		//echo '<input size="3" name="location" type="hidden" style="height:20px" value="'.$location.'">';
		//echo '<input size="3" type="hidden" name="cat_id" value="'.$cat_id.'">';
		echo PathSelector::getFields($location, $cat_id);
		echo '</span>';     
            
    } 
    /* ================================================= *
     *  Rest of the edition form                         * 
     * ================================================= */
    
    // content format
    echo '<span class="nowrap">';
    //echo "<p>\n";
    echo '<label for="n_content_format" ';
    echo 'style="display:inline">';
    echo __('Format').' ';  
    echo $m->HelpLink('news', 'h-format'); 
    echo '</label> ';
    echo form::combobox('n_content_format', 
                        array('HTML'=>'html','Wiki'=>'wiki'),
                        $news->getContentFormat('description'), 
                        $m->user->getPref('content_format'), 3);
    echo "</span>\n\n";
    //echo "</p>\n";
        
    echo '<span class="nowrap"><label for="n_status" ';
    echo 'style="display:inline">';
    echo __('Status').' '; 
    echo $m->HelpLink('news', 'h-status');
    echo '</label> ';
    echo form::combobox('n_status', $m->getArrayResStatus(), 
                        $news->f('status'), 
                        $m->user->getPref('news_status'), 4);
    echo "</span>\n\n";

    // Comment open or not
    // Show the choice only if possible to choose as defined by
    // the website configuration settings.
    if (config::f('comment_support') == 2) {
        echo '<span class="nowrap"><label for="n_comment_support" ';
        echo 'style="display:inline">';
        echo __('Comments').' ';  
        echo $m->HelpLink('news', 'h-comments'); 
        echo '</label> ';
        $pref = $m->user->getPref('news_comment_support');
        echo form::combobox('n_comment_support', 
                            $m->getArrayCommentSupport(),
                            $news->f('comment_support'), 
                            ($pref ==2 || $pref=='') ? config::f('comment_default_value')  : $pref,
        					4);

        echo "</span>\n\n";
    } else {
        echo form::hidden('n_comment_support', config::f('comment_support'));
    }
    
    // news type, is hidden if only one
    if (count($arry_subtypes) > 1) { 
        echo '<span class="nowrap"><label for="n_subtype" ';
        echo 'style="display:inline">';
        echo __('Type').' '; 
        echo $m->HelpLink('news', 'h-type');
        echo '</label> ';
        echo form::combobox('n_subtype', $arry_subtypes, 
                            $news->f('subtype_id'), '', 5, '',
                            "onchange=\"openCloseBlockIfArray('div_extra','n_subtype',js_subtype_ids,1,-1)\""); 
        echo "</span>\n";
    } else {
        echo form::hidden('n_subtype', array_shift($arry_subtypes));
    }
    echo '<input name="publish" type="submit" class="submit" tabindex="913" ';
    echo 'value="'.__('Save [s]').'" accesskey="'.__('s').'" />';

	// Ajout bouton pour le fil rouge
	/*
	echo '<span  style="position:absolute;left:70%">';
	echo '	<span class="filRouge" >&nbsp;</span>';
	$checked = $news->f('filRouge')== 1 ? true : false;
	echo form::checkbox('filRouge',$news->f('filRouge'),$checked,'','onclick="if (this.checked) this.value=1;"' );
	echo '<label for="filRouge" style="display:inline" >';
	echo __('FilRouge');
	echo '</label>';
	echo '</span>';
	*/   
    // affiche le bouton apercu si le contenu existe
	if (strlen($news->getUnformattedContent('description'))) {    
	    echo '<span class="nowrap" style="position:absolute;left:85%">';
	    echo '	<button class ="previewButton" type="button" onclick="affichePopupApercu();">Aperçu</button>';
	    echo '</span>';    
	}
	
    echo "</p>\n";
    // title
    echo '<p><label for="n_title"><strong>';
    echo __('Title'); 
    echo '</strong> ';
    echo $m->HelpLink('news', 'h-title'); 
    echo '</label> ';
    echo form::textField('n_title', 30, 255, $news->f('title'), 
                         6, 'style="width:100%"'); 
    echo "</p>\n\n";

    echo "<p>\n";
/*
    // insert image or file
    echo '<span id="insert-img" class="right-block"><img src="themes/'.$_px_theme
        .'/images/ico_image.png" alt="" /> ';
    echo '<strong><a href="xmedia.php" accesskey="i" ';
    echo 'onclick="popup(this.href+\'?mode=popup\'); return false;">';
    echo __('Insert an image or a file').'</a></strong></span>'."\n\n";
*/
    // content of the news
    echo '<label for="n_content"><strong>'.__('Content of the news');
    echo '</strong> ';
    echo $m->HelpLink('news', 'h-content'); 
    echo '</label> ';
    echo form::textArea('n_content', 60, 
                        $m->user->getPref('news_textarea_content'), 
                        $news->getUnformattedContent('news_content','details'), 7, 
                        'class="ckeditorBar" style="width:100%"');

    // size control of the content textarea
    /*
    echo '<span id="size-control" class="size-control">'."\n";
    echo '<input type="image" title="'.__('shrink textarea').'" ';
    echo 'name="decrease" value="-" src="themes/'.$_px_theme
        .'/images/ico_shrink.png" accesskey="-" class="size-control" /> ';
    echo '<input type="image" title="'.__('grow textarea').'" ';
    echo 'name="increase" value="+" src="themes/'.$_px_theme
        .'/images/ico_grow.png" accesskey="+" class="size-control" /> ';
    echo "</span>\n\n";
	*/
    echo "</p>\n\n";

    // short content
    echo '<p>';
    echo '<label for="n_shortcontent"><strong>'.__('Short Content').' ';
    echo '</strong></label> ';
    echo form::textArea('n_shortcontent', 60, 
    					$m->user->getPref('news_textarea_shortcontent'), 
                        $news->getUnformattedContent('news_shortcontent','details'), 8, 
                        'class="ckeditorBar" style="width:100%"');
    
    
    echo "</p>\n";
    
    // keywords
    echo '<p><label for="n_subject">'.__('Keywords').' ';
    echo $m->HelpLink('news', 'h-keywords'); 
    echo '</label>';
    echo form::textArea('n_subject', 60, 4, $news->f('subject'), 9,
                        'style="width:100%"');
    echo "</p>\n";


    // associated website and link
    echo '<div id="div_extra">'."\n";
    echo '<p><label for="n_titlewebsite">'.__('Associated website title').' ';
    echo $m->HelpLink('news', 'h-asso-title');
    echo '</label> ';
    echo form::textField('n_titlewebsite', 50, 255, 
                         $news->details->f('news_titlewebsite'), 10, 
                         'style="width:100%"');
    echo "</p>\n";
    echo '<p><label for="n_linkwebsite">'.__('Associated website address').' ';
    echo $m->HelpLink('news', 'h-asso-address'); 
    echo '</label> ';
    echo form::textField('n_linkwebsite', 50, 255, 
                         $news->details->f('news_linkwebsite'), 11, 
                         'style="width:100%"'); 
    echo '</p></div>'."\n\n";


    // script to show hide the extra part
    echo '<script type="text/javascript">'."\n";
    echo "<!--\n";
    echo "openCloseBlockIfArray('div_extra', 'n_subtype', ";
    echo 'js_subtype_ids, 1, -1);'."\n";
    echo "//-->\n";
    echo '</script>'."\n";



    //if ($news->f('resource_id') > 0) {
        echo '<p>'. __('Publication date').' ';
        echo $m->HelpLink('news', 'h-publication-date');
        /*
        echo ' '.form::datetime('n_dt', $news->getArrayDate('publicationdate'),
                                11)."<br />\n";
        */
		$dt_date = $news->getArrayDate('publicationdate');
        if (!is_array($dt_date)) {
            $dt_date = date::explode(date::stamp(),false);
        }		
        echo ' '.form::datetime('n_dt', $dt_date, 13,'',false);
        echo '<script type="text/javascript">';
        echo '$(function() {';
        echo '     $("#n_dt").datepicker({setDate:"'.$dt_date[4].'/'.$dt_date[3].'/'.$dt_date[5].'",dateFormat:"yymmdd"});';
        echo '	   $("#n_dt").change(function() { if ($("#n_dt").val()>$("#n_dt_e").val()) {$("#n_dt_e").val($("#n_dt").val())} });';
        echo '});';
		echo '</script>';
        

        $noenddate_style = ($news->isDateEOT('enddate')) ? 'style="display: none"' : 'style="display:inline"';

        echo '<span class="nowrap">'.
            form::checkbox('n_noenddate', 1, $news->isDateEOT('enddate'), 12, 
                           'onclick="modifDateRefererTo(\'n_noenddate\',\'n_dt_e\');openCloseSpan(\'noenddate\',0);" ')
            .' <label for="n_noenddate" style="display:inline">'
            .__('Do not use an expiration date.').'</label></span>'
            .' <span class="nowrap" id="noenddate" '.$noenddate_style
            .'><br />'. __('Expiration date').' ';
        echo $m->HelpLink('news', 'h-expiration-date');
        /*
        echo ' '.form::datetime('n_dt_e', $news->getArrayDate('enddate'),
                                13)."</span></p>\n\n";
		*/
        $dt_date = $news->getArrayDate('enddate');
        if (!is_array($dt_date)) {
            $dt_date = date::explode(date::stamp(),false);
        }		
        echo ' '.form::datetime('n_dt_e', $dt_date, 13,'',false);
        echo '<script type="text/javascript">';
        echo '$(function() {';
        echo '     $("#n_dt_e").datepicker({setDate:"'.$dt_date[4].'/'.$dt_date[3].'/'.$dt_date[5].'",dateFormat:"yymmdd"});';
        echo '	   $("#n_dt_e").change(function() { if ($("#n_dt").val()>$("#n_dt_e").val()) {$("#n_dt_e").val($("#n_dt").val())} });';
        echo '});';
		echo '</script>';
		echo '</span>';
    //}
    
    // submit buttons
    echo "<p class='button'>\n";
    /*
    echo form::button('submit', 'preview', __('Visualize [v]'), 
                      14, __('v'), 'submit');
    echo "&nbsp; \n";
    */
    echo form::button('submit', 'publish', __('Save [s]'), 
                      15, __('s'), 'submit')."\n";

    if (strlen($news->f('resource_id')) && 
        $news->getContentFormat('description') == 'wiki') {
        echo form::button('submit', 'transform', __('Transform in XHTML [x]'), 
                          16, __('x'), 'submit')."\n";
    }

    if (strlen($news->f('resource_id'))) {
        echo '&nbsp;<input name="delete" type="submit" tabindex="17" class="submit" value="'.__('Delete [d]').'" accesskey="'.__('d').'" onclick="return window.confirm(\''.addslashes( __('Are you sure you want to delete this news?')).'\')" />';
        echo form::hidden('resource_id',$news->f('resource_id'));
    }
?></p>
</form>
<h2><?php  echo __('Online help') ?></h2>
<h3><a onclick="openCloseSpan('wikihelp',0); return false" href="#"><img alt="<?php  echo __('show/hide'); ?>" id="img_wikihelp" src="themes/<?php echo $_px_theme; ?>/images/plus.png" /></a>&nbsp;
<?php  echo __('Wiki syntax'); ?></h3>
<div id="wikihelp" style="display: none;">
<?php echo $m->getHelp('wiki-inline'); ?>
</div>
<script type="text/javascript"><!--
openCloseSpan('wikihelp',-1);
//--></script>

<h3><a onclick="openCloseSpan('htmlhelp',0); return false" href="#"><img alt="<?php  echo __('show/hide'); ?>" id="img_htmlhelp" src="themes/<?php echo $_px_theme; ?>/images/plus.png" /></a>&nbsp;
<?php  echo __('XHTML coding'); ?></h3>
<div id="htmlhelp" style="display: none;">
<?php echo $m->getHelp('html-inline'); ?>
</div>
<script type="text/javascript"><!--
openCloseSpan('htmlhelp',-1);
//-->
</script>
   
<?php 
 if ($_PX_website_config['comment_support'] < 3)  {
    $px_resource_id = $news->f('resource_id');
    if ($px_resource_id > 0) {
        $ct = $news->comments;
        include dirname(__FILE__).'/comments-rlist.php';
    }
 }
} // end of if ($is_editable) 
?>