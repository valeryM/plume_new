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

class Gallery
{

	
    public static function animateSlider($name, $p)
    { 
    	$p=$p[0];
    	$languepreferee = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);

		$lang = $languepreferee[0];
    	$resp = '';
		// Core CSS File.
    	$resp .= '<link rel="stylesheet" href="manager/tools/gallery/slides/slides_default.css" />';
    	$resp .= '<script src="manager/tools/gallery/slides/slides.min.jquery.js" type="text/javascript"></script>';
		$resp .= '<script src="manager/js/ui/jquery-ui.custom.min.js" type="text/javascript"></script>';
		$resp .= '<link rel="stylesheet" type="text/css" href="manager/js/ui/css/cupertino/jquery-ui-1.9.2.custom.min.css" media="screen" />';
        $resp .= '<script type="text/javascript">
        			var options = {"generateNextPrev": true,							
								"next":"slides-next-horizontal",
								"prev":"slides-prev-horizontal",
								"pagination": true,
								"generatePagination": true,
								"slideSpeed": 650,
								"play": 4500,
								"pause" : 300,
								"hoverPause": true,
								"autoHeight": true,
    							"effect":"fade"};
        		
				    $(document).ready(function() {
        						    
				        var ref, element; 
				        var index =0;
			        	$(".gallery").each(function(index) {
			        		// effacer ce qui est à  l intérieur de la balise
			        		element=$(this);
			        		element.html("");
			        		element.addClass("slides-default");
        					element.addClass("slides-imagebox-large");
			        		index += 1;
			        		element.attr("id","gallery-"+index);
        					// merge options with default values
        					var option = eval(element.attr("data-gallery-options"));
        					if (option!=undefined && option.length>0) 
        						option=option[0];
        					else
        						option = new Array();
        					options = $.extend({}, options, option);
        					 
        					// appel du connecteur
			        		$.ajax({
			        			url : "manager/tools/gallery/getGallery.php",
			        			data : {"url": element.attr("data-gallery-url") },
			        			dataType : "html",
			        			type : "post",
			        			success : function (data, status) {
			        				element.html(data);
        							// déclenchement du slider
			        				initSlider(element.attr("id"));
			        			},
			        		});

						});
						
				        		
				    });
   		
				    function initSlider(id) {
				    	$("#"+id).slides({ 
								container: "slides-container",
								generateNextPrev: options["generateNextPrev"],							
								next:options["next"],
								prev:options["prev"],
								pagination: options["pagination"],
								generatePagination: options["generatePagination"],
								slideSpeed: options["slideSpeed"],
								play: options["play"],
								pause : options["pause"],
								hoverPause: options["hoverPause"],
								autoHeight: options["autoHeight"],
        						effect : options["effect"],
								
								animationStart: function(current){
									$(".titreAlaUne").animate({bottom:-35},100);
								},
								animationComplete: function(current){
									$(".titreAlaUne").animate({bottom:30},200);
								},
								slidesLoaded: function() {
									$(".titreAlaUne").animate({bottom:30},200);
								}
								
							});	
				    
				    }
        	</script>';

        if ($p['return'])
        	return $resp;
        else
        	echo $resp;
    }
}

Hook::register('onSlideShow', 'Gallery', 'animateSlider');

?>