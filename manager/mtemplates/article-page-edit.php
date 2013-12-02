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

if (basename($_SERVER['SCRIPT_NAME']) == 'article-page-edit.php') exit;

/* ===================================================== *
 *  Preview of the content if some content is available  *
 * ===================================================== */
/*
$link = '<a tabindex="1" href="articles.php?resource_id='
	.$ar->f('resource_id').'">'.$ar->f('title').'</a>';
*/
$link = '<b>'.$ar->f('title').'</b> ('.$ar->cats->f('category_path').')&nbsp&nbsp;';

echo '<span class="nowrap" style="position:relative;left:2%">';
if ($ar->pages->f('page_id') > 0) {
		echo ''.sprintf(__('Your are editing page %s of the article: %s.'),
		'<strong>'.$ar->pages->f('page_number').'</strong>', $link).'';
} else {
	echo ''.sprintf(__('Your are adding a page to the article: %s.'), $link)
        .'';
}

echo '	<button class ="actionButton" type="button" onclick="location.replace(\'articles.php?resource_id='.
			$ar->f('resource_id').'\');">'.__('Back to the article').'</button>';
echo '</span>';

echo '<span class="nowrap" style="position:absolute;left:85%">';
echo '	<button class ="previewButton" type="button" onclick="affichePopupApercu();">Aperçu</button>';
echo '</span>';

// preview of the page
if (strlen($ar->getUnformattedContent('page_content', 'pages'))) {
	echo '<div id="preview" class="preview" style="display:none">';

	echo '<h2>'.$ar->getTextContent('page_title', 'pages')
        .'</h2>';
	echo $ar->getFormattedContent('page_content', 'html', 'pages');
	echo "<hr class='invisible' id=\"zoubida\"/></div>\n\n";
}

/* ================================================= *
 *  If is editable form to modify the page content   *
 * ================================================= */
if ($is_editable) {
	Hook::run('onPrintHeaderManagerPage2', array('m' => &$m));
	
    echo '<form action="articles.php" method="post" id="formPost" '
        .'onsubmit="return isReady(\'a_page_title\',\''
        .addslashes(__('The page must have a title.')).'\')">';
    echo "<p class='selections'>\n";
    echo '<span class="nowrap"><label for="a_page_number" '
        .'style="display:inline">'.__('Number of the page').' '
        .$m->HelpLink('article', 'h-page-number').'</label>'."\n";
    echo form::combobox('a_page_number', $ar->getArrayPageList(), 
                        $ar->pages->f('page_number'), '', 2);
    echo '</span>'."\n";

    echo '<span class="nowrap"><label for="a_page_content_format" '
        .'style="display:inline">'.__('Format').' '
        .$m->HelpLink('article', 'h-format').'</label>'."\n";

    echo form::combobox('a_page_content_format', 
                        array('HTML'=>'html','Wiki'=>'wiki'),
                        $ar->getContentFormat('page_content', 'pages'), 
                        $m->user->getPref('content_format'),3)
        .'</span>'."\n";

    echo "</p>\n";
    echo '<p><label for="a_page_title"><strong>'.__('Page title').'</strong> '
        .$m->HelpLink('article', 'h-page-title').'</label>'."\n";
    echo form::textField('a_page_title', 30, 255, 
                         $ar->pages->f('page_title'), 4, 
                         'style="width:100%"').'</p>'."\n";

    echo '<p>'."\n";
    /*
        .'<span id="insert-img" class="right-block"><img src="themes/'.$_px_theme
        .'/images/ico_image.png" alt="" />'
        .'<strong><a href="xmedia.php" accesskey="i" '
        .'onclick="popup(this.href+\'?mode=popup\'); return false;">'
        .__('Insert an image or a file').'</a></strong></span>';
       */
    echo '<label for="a_page_content"><strong>'.__('Page content').'</strong>'
        .$m->HelpLink('article', 'h-page-content').'</label>'."\n";

    echo form::textArea('a_page_content', 60, 
                        $m->user->getPref('article_textarea_page'),
                        $ar->getUnformattedContent('page_content', 'pages'), 5,
                        'class="ckeditorBar" style="width:100%"')."\n";
    /*
    echo '<span id="size-control" class="size-control">'."\n"
        .'<input type="image" title="'.__('shrink textarea').'" '
        .'name="decrease" value="-" src="themes/'.$_px_theme
        .'/images/ico_shrink.png" accesskey="-" class="size-control" />'."\n"
        .'<input type="image" title="'.__('grow textarea').'" '
        .'name="increase" value="+" src="themes/'.$_px_theme
        .'/images/ico_grow.png" accesskey="+" class="size-control" />';
    echo '</span>'."\n";
    */
    echo '</p>'."\n";

    echo '<p><a href="articles.php?resource_id='.$ar->f('resource_id').'">'
        .__('Back to the article').'</a></p>'."\n";

    echo '<p class="button">';
    /*
    echo '<input name="preview" tabindex="6" type="submit" class="submit" '
        .'value="'.__('Visualize [v]').'" accesskey="'.__('v').'" />&nbsp; ';
    */
    echo '<input name="publish" tabindex="7" type="submit" class="submit" '
        .'value="'.__('Save [s]').'" accesskey="'.__('s').'" />'."\n";
    echo form::hidden('op', 'page');
    echo form::hidden('resource_id', $ar->f('resource_id'));
    
    if ($ar->getContentFormat('page_content', 'pages') == 'wiki') {

        echo '&nbsp;<input name="transform" tabindex="8" type="submit" '
            .'class="submit" value="'. __('Transform in XHTML [x]').'" accesskey="'.__('x').'" />';
    }
    
    if ($ar->pages->f('page_id') > 0) {
        echo '&nbsp;<input name="delete" tabindex="9" type="submit" '
            .'class="submit" accesskey="'.__('d').'" value="'.  __('Delete [d]').'" onclick="return '.
            'window.confirm(\''
            .addslashes(__('Are you sure you want to delete this page?'))
            .'\')" />';
        echo form::hidden('a_page_id', $ar->pages->f('page_id'));
    }
    echo '</p>'."\n";
    echo '</form>'."\n";
    echo '<h2>'.__('Online help').'</h2>'."\n";
    echo '<h3><a onclick="openClose(\'wikihelp\',0); return false" '
        .'href="#"><img alt="'.__('show/hide').'" id="img_wikihelp" '
        .'src="themes/'.$_px_theme.'/images/plus.png" style="text-align: middle;" /></a> '
        .__('Wiki syntax').'</h3>'."\n";
    echo '<div id="wikihelp" style="display: none;">'
        .$m->getHelp('wiki-inline').'</div>'."\n";
    echo '<script type="text/javascript"><!-- '."\n"
        .'openClose(\'wikihelp\',-1);'."\n"
        .'//--></script>';
    
    echo '<h3><a onclick="openClose(\'htmlhelp\',0); return false" '
        .'href="#"><img alt="'.__('show/hide').'" id="img_htmlhelp" '
        .'src="themes/'.$_px_theme.'/images/plus.png" style="text-align: middle;" /></a> '
        .__('XHTML coding').'</h3>'."\n";
    echo '<div id="htmlhelp" style="display: none;">'
        .$m->getHelp('html-inline').'</div>'."\n";
    echo '<script type="text/javascript"><!-- '."\n"
        .'openClose(\'htmlhelp\',-1);'."\n"
        .'//--></script>';
}