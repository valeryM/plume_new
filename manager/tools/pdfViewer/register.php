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

class PdfViewer
{

    public static function onEventsShow($name, $p)
    { 
    	$p=$p[0];
		
    	$resp = '';
		// script ref
		$resp .= '<script type="text/javascript" src="manager/tools/pdfViewer/pdfobject.js"></script>';
		$resp .= '<style> 
				.normalPdfZoom {width:280px; height:370px;} 
				.bigPdfZoom {width:500px; height:720px;}
				.button-zoom {display:inline;width:40px;font-size:0.9em;text-align:center;cursor:pointer;}
				</style>';
        $resp .= '<script type="text/javascript">
        			
				    $(document).ready(function() {
						var idx = 0;
	        			$(".pdfviewer").each(function(index) {
        					$(this).attr("id","pdfDoc_"+idx);
        					$(this).addClass("ui-widget-content normalPdfZoom");        		 			        						
							$(this).attr("style","");
        					var height = $(this).attr("data-pdfviewer-height");
        					var width = $(this).attr("data-pdfviewer-width");
        					$(this).attr("style","width:"+width+"px; height:"+height+"px;");
        					$(this).html("");
				        	var myPDF = new PDFObject({
				        		url: $(this).attr("data-pdfviewer-url"),
				        		pdfOpenParams: { view: "FitB", pagemode: "bookmarks", scrollbars: "1", toolbar: "1", statusbar: "1", messages: "1", navpanes: "1" }
				        	}).embed($(this).attr("id"));
        					$(this).after("<div id=\"button_"+idx+"\" onclick=\"zoomPdfViewer(\'#pdfDoc_"+idx+"\')\" class=\"button-zoom ui-state-default ui-corner-all\">Zoom</div>");
							$(this).after("<a href=\""+$(this).attr("data-pdfviewer-url")+"\" target=\"_blank\" class=\"button-zoom ui-state-default ui-corner-all\">Ouvrir</a>&nbsp;");
        					idx++;
        		 		});
      									        		
				    });
        		
        		    function zoomPdfViewer(elem){
        				if ($(elem).hasClass("normalPdfZoom")) {
							$(elem+".normalPdfZoom").switchClass( "normalPdfZoom", "bigPdfZoom", 1000 );
        					return false;
        				} else if ($(elem).hasClass("bigPdfZoom")) {
							$(elem+".bigPdfZoom").switchClass( "bigPdfZoom", "normalPdfZoom", 1000 );
							return false;
        				}
    				} 
        	</script>';      	
        	
        if ($p['return'])
        	return $resp;
        else
        	echo $resp;
    }
}

Hook::register('onPdfViewDoc', 'PdfViewer', 'onEventsShow');

?>