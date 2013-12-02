<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Plume CMS, a website management application.
# Copyright (C) 2001-2006 Loic d'Anterroches and contributors.
#
# Credits: Olivier Meunier.
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

class VisualEdit
{
    function onPrintHeader($name, $p)
    { 
    	$p=$p[0];
        $p['m']->l10n->loadPlugin($p['m']->user->lang, 'visualedit');
        $_px_ptheme = $p['m']->user->getPluginTheme('visualedit');
        $i = strlen($p['m']->user->wdata[$p['m']->user->website]['website_reurl']);
        $base = substr($p['m']->user->wdata[$p['m']->user->website]['website_url'], 0, -$i);
        echo '<script type="text/javascript" src="tools/visualedit/js/common.js"></script>';
        echo '<link rel="stylesheet" type="text/css" href="tools/visualedit/themes/'.$_px_ptheme.'/jsToolBar.css" />';
        
        echo '<script type="text/javascript" src="tools/visualedit/js/jsToolBar.js"></script>';
        echo '<script type="text/javascript" src="tools/visualedit/js/jsToolBar.wysiwyg.js"></script>';
        echo '<script type="text/javascript" src="tools/visualedit/js/jsToolBar.dotclear.js"></script>';

        echo '<script type="text/javascript"> jsToolBar.prototype.dialog_url = \'popup.php\'; ';
        echo 'jsToolBar.prototype.iframe_css = \'body{font: x-small/1.5em Verdana,Geneva,sans-serif;color : #000;background: #eef3f5; margin: 0;padding : 2px;border: none;}pre, code, kbd, samp {font-family:"Courier New",Courier,monospace;font-size : 1.1em;}code {color : #666;font-weight : bold;}body > p:first-child {margin-top: 0;}\'; ';
        echo 'jsToolBar.prototype.base_url = \''.$base.'\';';
        echo 'jsToolBar.prototype.switcher_visual_title = \''.addslashes(__('visual')).'\'; ';
        echo 'jsToolBar.prototype.switcher_source_title = \''.addslashes(__('source')).'\'; ';
        echo 'jsToolBar.prototype.legend_msg = \''.addslashes(__('You can use the following shortcuts to format your text.')).'\'; ';
	echo 'jsToolBar.prototype.elements.blocks.title = \''.addslashes(__('Block Format')).'\'; ';
	echo 'jsToolBar.prototype.elements.blocks.options.none.label = \''.addslashes(__('-- none --')).'\'; ';
	echo 'jsToolBar.prototype.elements.blocks.options.nonebis.label = \''.addslashes(__('- Block Format -')).'\'; ';
	echo 'jsToolBar.prototype.elements.blocks.options.p.label = \''.addslashes(__('Paragraph')).'\'; ';
	//echo 'jsToolBar.prototype.elements.blocks.options.h1.label = \''.addslashes(__('Header 1')).'\'; ';
	//echo 'jsToolBar.prototype.elements.blocks.options.h2.label = \''.addslashes(__('Header 2')).'\'; ';
	echo 'jsToolBar.prototype.elements.blocks.options.h3.label = \''.addslashes(__('Header 3')).'\'; ';
	echo 'jsToolBar.prototype.elements.blocks.options.h4.label = \''.addslashes(__('Header 4')).'\'; ';
	echo 'jsToolBar.prototype.elements.blocks.options.h5.label = \''.addslashes(__('Header 5')).'\'; ';
	echo 'jsToolBar.prototype.elements.blocks.options.h6.label = \''.addslashes(__('Header 6')).'\'; ';
        echo 'jsToolBar.prototype.elements.strong.title = \''.addslashes(__('Strong emphasis')).'\'; ';
        echo 'jsToolBar.prototype.elements.em.title = \''.addslashes(__('Emphasis')).'\'; ';
        echo 'jsToolBar.prototype.elements.ins.title = \''.addslashes(__('Inserted')).'\'; ';
        echo 'jsToolBar.prototype.elements.del.title = \''.addslashes(__('Deleted')).'\'; ';
        echo 'jsToolBar.prototype.elements.quote.title = \''.addslashes(__('Inline quote')).'\'; ';
        echo 'jsToolBar.prototype.elements.code.title = \''.addslashes(__('Code')).'\'; ';
	echo 'jsToolBar.prototype.elements.span.title = \''.addslashes(__('Generic inline tag')).'\'; ';
        echo 'jsToolBar.prototype.elements.span.style_prompt = \''.addslashes(__('CSS style (leave blank if no style):')).'\'; ';
        echo 'jsToolBar.prototype.elements.br.title = \''.addslashes(__('Line break')).'\'; ';
        echo 'jsToolBar.prototype.elements.blockquote.title = \''.addslashes(__('Blockquote')).'\'; ';
        echo 'jsToolBar.prototype.elements.pre.title = \''.addslashes(__('Preformated text')).'\'; ';
	echo 'jsToolBar.prototype.elements.table.title = \''.addslashes(__('Generate table')).'\'; ';
        echo 'jsToolBar.prototype.elements.div.title = \''.addslashes(__('Generic block tag')).'\'; ';
        echo 'jsToolBar.prototype.elements.div.style_prompt = \''.addslashes(__('CSS style (leave blank if no style):')).'\'; ';
        echo 'jsToolBar.prototype.elements.ul.title = \''.addslashes(__('Unordered list')).'\'; ';
        echo 'jsToolBar.prototype.elements.ol.title = \''.addslashes(__('Ordered list')).'\'; ';
        echo 'jsToolBar.prototype.elements.link.title = \''.addslashes(__('Link')).'\'; ';
        echo 'jsToolBar.prototype.elements.link.href_prompt = \''.addslashes(__('URL?')).'\'; ';
        echo 'jsToolBar.prototype.elements.link.hreflang_prompt = \''.addslashes(__('Language?')).'\'; ';
	echo 'jsToolBar.prototype.elements.removeFormat.title = \''.addslashes(__('Remove text formating')).'\'; ';
        echo 'jsToolBar.prototype.elements.img.title = \''.addslashes(__('External image')).'\'; ';
        echo 'jsToolBar.prototype.elements.img.src_prompt = \''.addslashes(__('URL?')).'\'; ';
        echo 'jsToolBar.prototype.elements.img_select.title = \''.addslashes(__('Image chooser')).'\';  </script>';
        echo '
        <script type="text/javascript">
addLoadEvent(function() {
    if (window.opener && window.opener.the_toolbar)
        tb = window.opener.the_toolbar;
    form = document.getElementById(\'formPost\');
    if (form) {
        var formatField = null;
        var excerptTb2 = null;
        if (form.n_content_format) {
            excerptTb = new jsToolBar(document.getElementById(\'n_content\'));
            excerptTb2 = new jsToolBar(document.getElementById(\'n_shortcontent\'));  
	        formatField = document.getElementById(\'n_content_format\');
        } else if (form.a_description_format) {
            excerptTb = new jsToolBar(document.getElementById(\'a_description\'));  
            formatField = document.getElementById(\'a_description_format\');
        } else if (form.a_page_content_format) {
            excerptTb = new jsToolBar(document.getElementById(\'a_page_content\'));  
            formatField = document.getElementById(\'a_page_content_format\');
        } else if (form.c_format) {
            excerptTb = new jsToolBar(document.getElementById(\'c_description\'));  
            formatField = document.getElementById(\'c_format\');
        } 

        elt = document.getElementById(\'insert-img\');
        if (elt) elt.style.display = \'none\';
        if (formatField) {
            formatField.onchange = function() {
                if (this.value == \'wiki\') {
		            excerptTb.switchMode(this.value);
		            if (excerptTb2 != null) excerptTb2.switchMode(this.value);
                } else {
                    excerptTb.switchMode(\'xhtml\');
                    if (excerptTb2 != null) excerptTb2.switchMode(\'xhtml\');
                }
            };
            excerptTb.draw();
           if (excerptTb2 != null) excerptTb2.draw();
            if (formatField.value == \'wiki\') {
                excerptTb.switchMode(\'wiki\');
                if (excerptTb2 != null) excerptTb2.switchMode(\'wiki\');
            }
        }
    }
});
        </script>';
    }
}

Hook::register('onPrintHeaderManagerPage', 'VisualEdit', 'onPrintHeader');
Hook::register('onPrintHeaderManagerPopUpPage', 'VisualEdit', 'onPrintHeader'); 
?>