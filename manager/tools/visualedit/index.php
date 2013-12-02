<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Plume CMS, a website management application.
# Copyright (C) 2001-2005 Loic d'Anterroches and contributors.
# 
# Credits: Olivier Meunier
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


require_once dirname(__FILE__).'/../../path.php';
require_once $_PX_config['manager_path'].'/prepend.php';
auth::checkAuth(PX_AUTH_NORMAL);

$m = new Manager();
$_px_theme = $m->user->getTheme();
$m->l10n->loadPlugin($m->user->lang, 'visualedit');
$px_title = __('Add a link');


/* Can be include only after the creation of the user ($u) and the manager ($m)
   objects as they are used here. */
header('Content-Type: text/html; charset='.strtolower($GLOBALS['_PX_config']['encoding']));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php echo $px_title; ?> - PLUME CMS</title>
  <script type="text/javascript" src="../../tools.js"> </script>
  <script type="text/javascript">
  addLoadEvent(function() {
      if (window.opener && window.opener.the_toolbar)
          tb = window.opener.the_toolbar;
  });
  </script>
  <script type="text/javascript" src="js/common.js"></script>
  <script type="text/javascript" src="js/popup_link.js"></script>
  <link rel="stylesheet" type="text/css" href="../../themes/<?php echo $_px_theme; ?>/style.css" />
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtolower($GLOBALS['_PX_config']['encoding']); ?>" />
  <script type="text/javascript">
  <!--  
  var pxThemeid = '<?php echo $_px_theme; ?>';  
  //-->
  </script>
</head>
<body>
<div id="main-pop">
<div id="content">


<?php
if(!empty($_GET['msg'])) {
	echo '<p class="message">'.$_GET['msg'].'</p>';
}
if (false !== ($px_error = $m->error(true, false)) )
    echo "\n\n" . $px_error . "\n\n"; 

echo '<p style="text-align: right;"><a href="#" onclick="window.close(); return false;">'.__('Close this window').'</a></p>';

$href = !empty($_GET['href']) ? $_GET['href'] : '';
$title = !empty($_GET['title']) ? $_GET['title'] : '';
$hreflang = !empty($_GET['hreflang']) ? $_GET['hreflang'] : '';

echo
'<form id="link-insert-form" action="#" method="get">'.
'<p><label class="required" title="'.__('Required field').'">'.__('Link URL:').' '.
form::textField('href', 35, 255, $href).'</label></p>'.
'<p><label>'.__('Link title:').' '.
form::textField('title',35,255,$title).'</label></p>'.
'<p><label>'.__('Link language:').' '.
form::textField('hreflang',5,5,$hreflang).'</label></p>'.
'</form>'.
'<p><a href="#" id="link-insert-cancel">'.__('cancel').'</a> - '.
'<strong><a href="#" id="link-insert-ok">'.__('insert link').'</a></strong></p>';

echo '<p style="text-align: right;"><a href="#" onclick="window.close(); return false;">'.__('Close this window').'</a></p>';
include dirname(__FILE__).'/../../mtemplates/_pop_bottom.php';

?>