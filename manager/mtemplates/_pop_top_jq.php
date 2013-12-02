<?php
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
# ***** END LICENSE BLOCK *****

if (basename($_SERVER['SCRIPT_NAME']) == '_pop_top.php') exit;

/* Can be include only after the creation of the user ($u) and the manager ($m)
 objects as they are used here. */
header('Content-Type: text/html; charset='.strtolower($GLOBALS['_PX_config']['encoding']));
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php echo strtolower($GLOBALS['_PX_config']['encoding']); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<title><?php echo $px_title; ?> - PLUME CMS</title>
	<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/tools.js"> </script>
	<?php Hook::run('onPrintHeaderManagerPopUpPage2', array('m' => &$m)); ?>
	<link rel="stylesheet" type="text/css"href="<?php echo $_PX_website_config['rel_url'];?>/manager/js/themes/base/jquery.ui.all.css" />
	<link rel="stylesheet" type="text/css" href="themes/<?php echo $_px_theme; ?>/style.css" />
	<!-- Fonctions Jquery ui -->
	<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/js/jquery.last.min.js"></script>
	<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/js/ui/jquery-ui.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/js/jquery-migrate-1.2.1.js"></script>
	<!--  <script type="texte/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/js/xmedia.js" />   -->
	<!-- Fin Fonctions Jquery ui -->
	<script type="text/javascript">
	 
		var pxThemeid = '<?php echo $_px_theme; ?>';

	</script>
	<style type="text/css">
	#gallery {
		float: left;
		width: 65%;
		min-height: 12em;
	}
	
	* html #gallery {
		height: 12em;
	} /* IE6 */
	.gallery.custom-state-active {
		background: #eee;
	}
	
	.gallery li {
		float: left;
		width: 96px;
		padding: 0.4em;
		margin: 0 0.4em 0.4em 0;
		text-align: center;
	}
	
	.gallery li h5 {
		margin: 0 0 0.4em;
		cursor: move;
	}
	
	.gallery li a {
		float: right;
	}
	
	.gallery li a.ui-icon-zoomin {
		float: left;
	}
	
	.gallery li img {
		width: 100%;
		cursor: move;
	}
	
	#trash {
		float: right;
		width: 32%;
		min-height: 18em;
		padding: 1%;
	}
	
	* html #trash {
		height: 18em;
	} /* IE6 */
	#trash h4 {
		line-height: 16px;
		margin: 0 0 0.4em;
	}
	
	#trash h4 .ui-icon {
		float: left;
	}
	
	#trash .gallery h5 {
		display: none;
	}
	
	.ui-icon-zoomin {width =16px;
		height: 17px;
	}
	</style>
	<script type="text/javascript">

	$(function() {
		// there's the gallery and the trash
		var $gallery = $('#gallery_photo'); //, $trash = $('#trash');
		// image preview function, demonstrating the ui.dialog used as a modal window
		
		function viewLargerImage($link) {
			var src; // = $link.attr('href');
			var title; // = $link.attr('alt');
			
			if (!$link.attr('href') ) {		// si c'est pas un balise <a>
				src = $link.attr('src');
				title = $link.attr('alt');
			} else {
				src = $link.attr('href');
				title = $link.siblings('img').attr('alt');
			}
			
			var $modal = $('img[src$="'+src+'"]');
				var img = $('<img alt="'+title+'" width="384" height="288" style="display:none;padding: 8px;" />')
									.attr('src',src).appendTo('body');
				setTimeout(function() {
					img.dialog({
							title: title,
							width: 400,
							modal: true
						});
				}, 1);
	
		}
		
		$('div.icon').click(function(ev) {
			
			var $item = $(this);
			var $target = $(ev.target);
			//alert ($target);
			if ($target.is('a.ui-icon-zoomin') || $target.is('a.icon-zoomin') || $target.is('img.icon-zoomin')) {
				viewLargerImage($target);
				return false;
			} else {
				return true;
			}
	
		});
	
	});
	
	</script>
</head>

<body>
	<div id="main-pop" style="overflow: scroll; height: 650px">
		<div id="content">
			<?php
			if(!empty($_GET['msg'])) {
				echo '<p class="message">'.$_GET['msg'].'</p>';
			}
			if (false !== ($px_error = $m->error(true, false)) )
				echo "\n\n" . $px_error . "\n\n";
			?>