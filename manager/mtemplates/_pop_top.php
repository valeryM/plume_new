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
<!-- 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtolower($GLOBALS['_PX_config']['encoding']); ?>" />
-->
	<title><?php echo $px_title; ?> - PLUME CMS</title>
	<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/tools.js"> </script>
	<?php Hook::run('onPrintHeaderManagerPopUpPage2', array('m' => &$m)); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $_PX_website_config['rel_url'];?>/manager/js/themes/base/jquery.ui.all.css" />  
	<link rel="stylesheet" type="text/css" href="<?php echo $_PX_website_config['rel_url'];?>/manager/themes/<?php echo $_px_theme; ?>/style.css" />
	<script type="text/javascript">
  		var pxThemeid = '<?php echo $_px_theme; ?>';  
	</script>
</head>
<body>

<div id="main-pop" style="overflow:auto;height:650px">
<div id="content">

<?php
if(!empty($_GET['msg'])) {
	echo '<p class="message">'.$_GET['msg'].'</p>';
}
if (false !== ($px_error = $m->error(true, false)) )
    echo "\n\n" . $px_error . "\n\n"; 
?>


