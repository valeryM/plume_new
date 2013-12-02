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



class ElFinderTool
{
    public static function onShowDirectoryContent($name, $p)
    { 
    	$p=$p[0];
    	$lang = $p['m']->user->lang;
        $p['m']->l10n->loadPlugin($lang, 'elfinder');
        $_px_ptheme = $p['m']->user->getPluginTheme('visualedit');
        $i = strlen($p['m']->user->wdata[$p['m']->user->website]['website_reurl']);
        $base = substr($p['m']->user->wdata[$p['m']->user->website]['website_url'], 0, -$i);
		$path = $p['m']->user->wdata[$p['m']->user->website]['website_reurl'];
		
		echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$path.'/manager/tools/elfinder/css/elfinder.min.css">';
		echo '<script type="text/javascript" src="'.$path.'/manager/tools/elfinder/js/elfinder.min.js"></script>';
		
		echo '<!-- Mac OS X Finder style for jQuery UI smoothness theme (OPTIONAL) -->';
		echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$path.'/manager/tools/elfinder/css/theme.css">';
		echo '<script type="text/javascript" src="'.$path.'/manager/tools/elfinder/js/i18n/elfinder.'.$lang.'.js"></script>';
		
        echo '<script type="text/javascript">
				    $(document).ready(function() {
				    
				    	var opts = {
				    		lang: "'.$lang.'",      // language (OPTIONAL)
							url : "'.$path.'/manager/tools/elfinder/php/connector.php",  //  connector URL (REQUIRED)
							/*tmbUrl:*/
							commands : [
								"reload", "home", "up", "back", "forward", "quicklook", 
								"download", "rm", "duplicate", "rename", "mkdir", "mkfile", "upload", "copy", 
								"cut", "paste", "extract", "archive", "search", "info", "view", "help",
								"resize", "sort"],
							commandsOptions: {
								getfile : {
									// send only URL or URL+path if false
									onlyURL  : true,							
									// allow to return multiple files info
									multiple : false,							
									// allow to return folders info
									folders  : false,
									// action after callback (close/destroy)
									oncomplete : "close",
								},
							},
							uiOptions : {
								// toolbar configuration
								toolbar : [
									["back", "forward"],
									["reload"],
									["home", "up"],
									["mkdir", "mkfile", "upload"],
									[/*"open",*/ "download", "getfile"],
									["info"],
									["quicklook"],
									["copy", "cut", "paste"],
									["rm"],
									["duplicate", "rename", "edit", "resize"],
									["extract", "archive"],
									["search"],
									["view"],
									["help"]
								],
							},
							contextmenu : {
								// navbarfolder menu
								navbar : [/*"open", "|",*/ "copy", "cut", "paste", "duplicate", "|", "rm", "|", "info"],
							
								// current directory menu
								cwd    : ["reload", "back", "|", "upload", "mkdir", "mkfile", "paste", "|", "info"],
							
								// current directory file menu
								files  : [
									"getfile", "|",/*"open",*/ "quicklook", "|", "download", "|", "copy", "cut", "paste", "duplicate", "|",
									"rm", "|", /*"edit",*/ "rename", "resize", "|", "archive", "extract", "|", "info"
								]
							},
							handlers : {
								// auto resize files on upload
								upload : function(event, instance) {
									var uploadedFiles = event.data.added;
									var archives = ["application/zip", "application/x-gzip", "application/x-tar", "application/x-bzip2"];
									for (i in uploadedFiles) {
										var file = uploadedFiles[i];
										if (jQuery.inArray(file.mime, archives) >= 0) {
											instance.exec("extract", file.hash);
										}
										/*
										if (file.size> maxFileSize ) {
											file.tmb="1";
										}
										*/
									}
								},
								/*
								select : function(event, elfinderInstance) {
						            //console.log(event.data);
						            //console.log(event.data.selected); // selected files hashes list
						        },
						        */
						        /*
								open   : function(event) { 
									//console.log(event.data.options);
    							},
    							*/
							},
							allowShortcuts : false,
							loadTmbs : 2,
							debug : ["error","warning", "event-destroy"],
							/* Callback function for "getfile" command. Required to use elFinder with WYSIWYG editors, external callbacks.
							For more info how to use this function refer to wiki WYSIWYG integrations examples
							*/
							/*
							getFileCallback : function() {}, 
							*/	
							resizable : false,
							height:550,				
						    };
						var elf = $("#elfinder").elfinder(opts).elfinder("instance");		
				        		
				    });
        	</script>';
        //echo '<!-- Element where elFinder will be created (REQUIRED) -->';
		echo '<div id="elfinder"></div>';
        
    }
    
    
}

Hook::register('onShowDirectoryContentManager', 'ElFinderTool', 'onShowDirectoryContent');

?>