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

class Codemirror
{
    public static function onCodeHighLigth($name, $p)
    { 
    	$p=$p[0];
        $p['m']->l10n->loadPlugin($p['m']->user->lang, 'visualedit');
        $_px_ptheme = $p['m']->user->getPluginTheme('visualedit');
        $i = strlen($p['m']->user->wdata[$p['m']->user->website]['website_reurl']);
        $base = substr($p['m']->user->wdata[$p['m']->user->website]['website_url'], 0, -$i);
		$path = $p['m']->user->wdata[$p['m']->user->website]['website_reurl'];
		
		echo '<link href="'.$path.'/manager/tools/codemirror/lib/codemirror.css" rel="stylesheet" type="text/css"/>';
        echo '<script type="text/javascript" src="'.$path.'/manager/tools/codemirror/lib/codemirror.js"></script>';
        echo '<script type="text/javascript" src="'.$path.'/manager/tools/codemirror/lib/util/matchbrackets.js"></script>';
        echo '<script type="text/javascript" src="'.$path.'/manager/tools/codemirror/mode/htmlmixed/htmlmixed.js"></script>';
        echo '<script type="text/javascript" src="'.$path.'/manager/tools/codemirror/mode/xml/xml.js"></script>';
        echo '<script type="text/javascript" src="'.$path.'/manager/tools/codemirror/mode/javascript/javascript.js"></script>';
        echo '<script type="text/javascript" src="'.$path.'/manager/tools/codemirror/mode/css/css.js"></script>';
        echo '<script type="text/javascript" src="'.$path.'/manager/tools/codemirror/mode/clike/clike.js"></script>';
        echo '<script type="text/javascript" src="'.$path.'/manager/tools/codemirror/mode/php/php.js"></script>';
		echo '<style type="text/css"> .CodeMirror {font-size:13px; border-top: 1px solid black; border-bottom: 1px solid black;border-left: 1px solid silver; border-right: 1px solid silver; height: 500px;} </style>';
		echo '<link href="'.$path.'/manager/tools/codemirror/theme/eclipse.css" rel="stylesheet" type="text/css"/>';
        echo '<script type="text/javascript">
				    $(document).ready(function() {
        				
				        form = $("#frmEditTemplate");
				        if (form) {
				        	$("#frmEditTemplate textarea.codemirror_content").each(function() {
								var fileMode;
				        		if ($(this).hasClass("css")) fileMode = "text/css";
				        		else fileMode = "application/x-httpd-php";
				        	
        						var editor = CodeMirror.fromTextArea(this, {
									lineNumbers: true,
									matchBrackets: true,
									mode: fileMode,
									indentUnit: 4,
									indentWithTabs: true,
									enterMode: "keep",
									tabMode: "shift",
									theme: "eclipse",
									continuousScanning: 500,
									}); 
									
								form.submit(function() {
				        			editor.save();				        		
				        		});
				        	});
				        	
				        }

				        form2 = $("#frmShowTemplate");
				        if (form2) {
				        	$("#frmShowTemplate textarea.codemirror_content").each(function() {
								var fileMode;
				        		if ($(this).hasClass("css")) fileMode = "text/css";
				        		else fileMode = "application/x-httpd-php";
				        		
        						var editor = CodeMirror.fromTextArea(this, {
									lineNumbers: true,
									readOnly: true,
									matchBrackets: true,
									mode: fileMode,
									indentUnit: 4,
									indentWithTabs: true,
									enterMode: "keep",
									tabMode: "shift",
									theme: "eclipse",
									continuousScanning: 500,
									}); 
									
				        	});
				        	
				        }
				        		
				    });
        </script>';
    }
}

Hook::register('onCodeHighLigthManager', 'Codemirror', 'onCodeHighLigth');

?>