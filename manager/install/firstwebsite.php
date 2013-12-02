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
require_once dirname(__FILE__).'/prepend.php';

$_px_p = 60; //percentage of wizard done.

if (empty($_SESSION['step6'])) {
	header('Location: firstuser.php');
	exit;
}

//load the language file
$_PX_config['encoding'] = $_SESSION['manager_encoding'];
$l = new l10n($_SESSION['manager_lang'], 'install');
$l->loadDomain($_SESSION['manager_lang']); //load the general lang file

$_PX_config['db']['db_login']     = $_SESSION['dblogin'];
$_PX_config['db']['db_password']  = $_SESSION['dbpass'];
$_PX_config['db']['db_server']    = $_SESSION['dbserver'];
$_PX_config['db']['db_database']  = $_SESSION['dbname'];
$_PX_config['db']['table_prefix'] = $_SESSION['dbprefix'];
$_PX_config['debug'] = true;
$_PX_config['db_version'] = $_SESSION['db_version'];

$u = new User(1); //user with "root" id
//$u->set($_SESSION['username']);
//$u->lang = $u->getPref('lang');
$m = new Manager();
$m->setUser($u);
$error = false;
$error_mess = '';
//$m->l10n->loadDomain($_SESSION['lang'], 'install');

$px_name             = __('New website name');
$s = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '';
$px_website_address  = 'http'.$s.'://'.$_SERVER['SERVER_NAME'].www::getRelativeUrl();
$px_xmedia_name      = 'xmedia';
$px_website_path     = files::real_path(dirname(__FILE__).'/../../');
$px_description      = __('Description of the new website.');

$show_log = false;
if (!empty($_SESSION['log_new_site']) && !empty($_REQUEST['op'])) {
	$show_log = true;
	$px_log_new_site = $_SESSION['log_new_site'];
	$_px_p = 80;
}

if (!empty($_POST['s_save'])) {
	$px_name             = (!empty($_POST['s_name'])) ? $_POST['s_name'] : $px_name;
	$px_website_address  = (!empty($_POST['s_website_address'])) ? $_POST['s_website_address'] : $px_website_address;
	$px_xmedia_name      = (!empty($_POST['s_xmedia_name'])) ? $_POST['s_xmedia_name'] : $px_xmedia_name;
	$px_website_path     = (!empty($_POST['s_website_path'])) ? $_POST['s_website_path'] : $px_website_path;
	$px_description      = (!empty($_POST['s_description'])) ? $_POST['s_description'] : $px_description;
	$px_id = '';
	$px_log_new_site = '';
	$px_sitelang = $_SESSION['manager_lang'];
	if (false !== $m->saveSite($px_id, $px_name, $px_description, $px_sitelang, $px_website_address, $px_website_path, $px_xmedia_name, 1, 1, 1, $px_log_new_site, 'default')) {
		$msg =  sprintf(__('Site <strong>%s</strong> successfully added.'), $px_name);
		$_SESSION['log_new_site'] = $px_log_new_site; //Save the log in the session, the header is to prevent a double "addition".
		$_SESSION['step7'] = true;
		$_SESSION['s_name'] = $px_name;
		$_SESSION['s_website_address'] = $px_website_address;
		$_SESSION['s_xmedia_name'] = $px_xmedia_name;
		$_SESSION['s_website_path'] = $px_website_path;
		$_SESSION['s_description'] = $px_description;
		header('Location: firstwebsite.php?op=log&msg='.urlencode($msg));
		exit;
	} else {
		$error = true;
		$error_mess = $m->error(true, false);
	}
}
include dirname(__FILE__).'/_top.php';

echo '<h2>'.__('First website creation').'</h2>'."\n\n";

if (!$show_log) {

if($error === true) {
	echo '<p class="important">'.__('Errors during the installation of the first website. Please check the error messages.').'</p>'."\n\n";
    echo "\n\n" . $error_mess . "\n\n";
}

echo '<p>'.__('Simply provide the necessary information so the system can create the default website for you.').' '.__('Normally you do not have to change anything except the name and the description.').'</p>'."\n";
?>


<form action="firstwebsite.php" method="post" id="formPost">

<p class="field"><label class="float" for="s_name" style="display:inline"><strong><?php  echo __('Site name:'); ?></strong></label>
<?php echo php_f_textField('s_name', 30, 255, $px_name, '', ''); ?>
</p>
<p class="field"><label class="float" for="s_website_address" style="display:inline"><strong><?php  echo __('Website address:'); ?></strong></label>
<?php echo php_f_textField('s_website_address', 50, 255, $px_website_address, '', ''); ?></p>
<p>
<?php  echo __('For example put <em>https://www.mydomain.com/mysite/</em> or <em>http://www.mydomain.com/</em>. Do not forget the <em>http(s)</em>.'); ?>
</p>
<p class="field"><label class="float" for="s_website_path" style="display:inline"><strong><?php  echo __('Path to the document root of the website:'); ?></strong></label>
<?php echo php_f_textField('s_website_path', 50, 255, $px_website_path, '', ''); ?></p>
<p>
<?php 	echo __('Path on the server side. For example <em>/home/login/www</em> or <em>c:/http</em>. The document root is the folder where the <em>index.php</em> will be placed.'); ?>
</p>

<p class="field"><label class="float" for="s_xmedia_name" style="display:inline"><strong><?php  echo __('Name of the file and image folder:'); ?></strong></label>
<?php echo php_f_textField('s_xmedia_name', 15, 255, $px_xmedia_name, '', ''); ?></p>
<p>
<?php  echo __('For example put <em>xmedia</em> or <em>documents</em>.'); ?>
</p>

<p>
<label for="s_description"><strong><?php  echo __('Description:'); ?></strong></label>
<?php  echo php_f_textArea('s_description', 60, 4, htmlspecialchars($px_description), '', '');  ?>
</p>

<p>
<?php echo __('If all the informations you provided are valid, the system will copy the required files in the right folders. The complete log of those files will be shown to you. No file already available on the system will be overwritten.'); ?>
</p>

<p> <input name="s_save" type="submit" class="submit" value="<?php  echo __('Create the website'); ?>"  accesskey="s" />

<?php
} else { //show the log of the installation

	echo '<p>'.__('Log of the installation:').'</p>'."\n\n";

	echo $px_log_new_site;

	echo '<p>'.__('If you see warnings, check them carefully and look at the online documentation to see the impact. You may need to complete the installation manually.').'</p>'."\n\n";

	echo '<p>'.sprintf(__('In the next step the global configuration will be created. <a href="%s">Next</a>.'), 'configfile.php').'</p>'."\n\n";
}

include dirname(__FILE__).'/_bottom.php';
?>
