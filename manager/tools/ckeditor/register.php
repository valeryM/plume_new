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

class CkEdit
{
    public static function onPrintHeader($name, $p)
    { 
    	$p=$p[0];
        $p['m']->l10n->loadPlugin($p['m']->user->lang, 'visualedit');
        $_px_ptheme = $p['m']->user->getPluginTheme('visualedit');
        $i = strlen($p['m']->user->wdata[$p['m']->user->website]['website_reurl']);
        $base = substr($p['m']->user->wdata[$p['m']->user->website]['website_url'], 0, -$i);

        echo '<script type="text/javascript" src="tools/ckeditor/ckeditor.js"></script>';
		echo '<link href="tools/ckeditor/contents.css" rel="stylesheet" type="text/css"/>';
        echo '<script type="text/javascript">
				    $(document).ready(function() {
        				
				    	if (window.opener && window.opener.the_toolbar)
				        	tb = window.opener.the_toolbar;
				        form = $("#formPost");
				        if (form) {
				        	$("#formPost textarea.ckeditorBar").each(function() {
				        		var editorID = $(this).attr("id");

            					var instance = CKEDITOR.instances[editorID];
            					if (instance) { CKEDITOR.instances[editorID].destroy(); }
        		
								size = $(this).attr("rows")*14 +"px";
								CKEDITOR.replace(editorID,{
								        height: size,
								        filebrowserBrowseUrl : "tools/elfinder/elfinder.php", /*"xmedia.php?mode=popup",*/
								        filebrowserImageBrowseUrl : "tools/elfinder/elfinder.php", /*"xmedia.php?mode=popup",*/
								        filebrowserWindowWidth : "70%",
								        filebrowserWindowHeight : "80%",
								        extraPlugins : "gallery,calendar,pdfviewer", /*,maps",*/ 								        								        
								        } );
        		
        						CKEDITOR.config.resize_dir = "vertical";

								if (editorID == "c_description") {	
									CKEDITOR.config.toolbar_Basic =[ [ "Source", "-", "Bold", "Italic" ] ];
						  			CKEDITOR.config.toolbar = "Basic";
								} 
        		
				        	});
				        }
				        elt = $("#insert-img");
				        if (elt) elt.css("display", "none");
				        		
				    });
        </script>';
    }
}

Hook::register('onPrintHeaderManagerPage2', 'CkEdit', 'onPrintHeader');
Hook::register('onPrintHeaderManagerPopUpPage2', 'CkEdit', 'onPrintHeader'); 
?>