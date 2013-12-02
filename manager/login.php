<?php
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

require_once 'path.php';
require_once $_PX_config['manager_path'].'/prepend.php';

if (!empty($_GET['logout'])) {
    auth::logout();
    header('Location: index.php');
    exit;
}


$errorinlogin = false;
$website = '';
if (isset($_POST['username']) && isset($_POST['password'])) {
    if (!empty($_COOKIE['website_id'])) $website = $_COOKIE['website_id'];
    if (auth::login($_POST['username'], $_POST['password'], $website)) {
        header('Location: index.php');
        exit;
    } 
    $errorinlogin = true;
    $_COOKIE['website_id'] = '';
}
if (isset($_POST['username'])) $px_username = text::secure($_POST['username']);
else $px_username = '';
$px_password = '';

if (empty($_POST['username']) && !empty($_GET['username'])) $px_username = text::secure($_GET['username']);
if (empty($_POST['password']) && !empty($_GET['password'])) $px_password = text::secure($_GET['password']);

$locales = l10n::getAvailableLocales();

if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], $locales)) $lang = $_COOKIE['lang'];
else $lang = l10n::getAcceptedLanguage($locales);

$l = new l10n($lang);

// Here you can consider that username/password was not good, or not given.
header('Content-Type: text/html; charset='.strtolower(config::f('encoding')));
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
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtolower(config::f('encoding')); ?>" />
 -->
	<title>PLUME CMS</title>
	<link rel="stylesheet" type="text/css" href="themes/default/style.css" />
</head>
<body>

<div style="text-align:center;">
<div>
	<h1><img src="themes/default/images/logo.png" alt="PLUME CMS" /></h1>
</div>
<div>
	<?php 
	// get all websites informations
	// and try to define image
	$rs = FrontEnd::getWebsites();
	while (!$rs->EOF()) {
		if ($rs->f('website_img')!='') {
			if (file_exists('themes/default/images/'.$rs->f('website_img'))) 
				echo '<img style="width:200px" src="themes/default/images/'.$rs->f('website_img').'" alt="logo du site '.$rs->f('website_id').'" />';
		}
		$rs->moveNext();
	}
	
	?>
</div>
<form action="login.php" method="post">
<div class="login">
<h2><?php echo __('Manager Access'); ?></h2>

<?php
if ($errorinlogin)
{
    echo '<p><strong>'.__('There is an error with your login or your password.').'</strong></p>';
    $px_password = '';
}
?>

<p><label for="username"><span class="author_style"><strong><?php echo __('Login:'); ?></strong></span></label>
<input name="username" id="username" type="text" maxlength="32"
value="<?php echo $px_username; ?>" tabindex="1"/></p>

<p><label for="password"><span class="password_style"><strong><?php echo __('Password:'); ?></strong></span></label>
<input name="password" id="password" type="password" value="<?php echo $px_password; ?>" tabindex="2" /></p>

<p><input class="submit" type="submit" value="<?php echo __('Ok'); ?>" /></p>

<p><?php echo __('You need to accept the cookies to access the manager.'); ?></p>

</div>
</form>
</div>

</body>
</html>