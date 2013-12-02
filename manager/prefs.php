<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
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

auth::checkAuth(PX_AUTH_NORMAL);
$m = new Manager();
$_px_theme = $m->user->getTheme();

/* ================================================== *
 *                  Process block                     *
 * ================================================== */

$px_password = '';
$px_realname = $m->user->f('user_realname');
$px_email    = $m->user->f('user_email');
$px_pubemail = $m->user->f('user_pubemail');
$px_lang     = $m->user->getPref('lang');

// get the available languages
$localedir = dirname(__FILE__).'/locale/';
$arry_lang = array();
$arry_codes = l10n::getIsoCodes();
$west_codes = l10n::getIsoWestern();
$D = dir($localedir);
while(false !== ($entry = $D->read())) {
	if (is_dir($localedir.$entry) && preg_match('/^[a-z]{2}$/', $entry)) {
		if ('utf-8' == strtolower($_PX_config['encoding']) or in_array($entry, $west_codes)) {
			$label = $arry_codes[$entry];
			$arry_lang[$label] = $entry;
		}
	}
}
unset($arry_codes, $west_codes); //big arrays not needed anymore
$D->close();
$arry_lang['English'] = 'en';


// get the available themes
require dirname(__FILE__).'/extinc/class.plugins.php';
$themes_root = dirname(__FILE__).'/themes/';

$objPlugins = new plugins($themes_root);
$themes_list = $objPlugins->getPlugins('theme');


/*=================================================
 Save/Add the user
=================================================*/
if (!empty($_POST['save'])) {
    // Save the prefs
	$px_id       = $m->user->f('user_id');
	$px_username = $m->user->f('user_username');
	$px_password = trim($_POST['u_password']);
	$px_realname = trim($_POST['u_realname']);
	$px_email    = trim($_POST['u_email']);
	$px_pubemail = trim($_POST['u_pubemail']);
	$px_lang = trim($_POST['u_lang']);

	$ok = $m->user->savePref('lang', $px_lang, '#all#');
	if ($ok !== true) {
		$m->setError($ok, 500);
	}
	if (($ok === true) && false !== ($id = $m->saveUser($px_id, $px_username, $px_password, $px_realname, $px_email, $px_pubemail,null,0,'', $px_lang))) {
        $m->setMessage(__('Preferences successfully saved.'));
        header('Location: prefs.php');
        exit;
	}
}

if (!empty($_GET['switchtheme'])) {
	if (!empty($themes_list[trim($_GET['switchtheme'])])) {
		//theme exists, can be used
		$ok = $m->user->savePref('theme', trim($_GET['switchtheme']), '#all#');
		if ($ok !== true) {
			$m->setError($ok, 500);
		} else {
			$m->setMessage(__('Theme successfully changed.'));
			header('Location: prefs.php');
			exit;
		}
	}

}


/* ============================= *
 *       Display block           *
 * ============================= */
$px_title =  __('Preferences');
include dirname(__FILE__).'/mtemplates/_top.php';

echo '<h1 id="title_prefs">'. __('Preferences')."</h1>\n\n";

?>
<h2><?php  echo __('Identity'); ?></h2>
<form action="prefs.php" method="post" id="formPost" class="prefs_style">
  <p class="field"><label class="float" for="u_realname" style="display:inline"><span class="real_name"><?php  echo __('Name:'); ?></span></label>
  <?php echo form::textField('u_realname', 30, 50, $px_realname, '', ''); ?>
  </p>

  <p class="field"><label class="float" for="u_email" style="display:inline"><span class="private_mail_style"><?php  echo __('Email <span class="small">(not shown)</span>:'); ?></span></label>
  <?php echo form::textField('u_email', 30, 50, $px_email, '', ''); ?>
  </p>

  <p class="field"><label class="float" for="u_pubemail" style="display:inline"><span class="public_mail_style"><?php  echo __('Public email:'); ?></span></label>
  <?php echo form::textField('u_pubemail', 30, 50, $px_pubemail, '', ''); ?>
  </p>

  <p class="field"><label class="float" for="u_password" style="display:inline"><span class="password_style"><?php  echo __('Password:'); ?></span></label>
  <?php echo form::textField('u_password', 30, 50, '', '', ''); ?>
  <br /><?php  echo __('(keep empty not to change it)'); ?></p>

  <p class="field"><label class="float" for="u_lang" style="display:inline"><?php  echo __('Interface language:'); ?></label>
    <?php  echo form::combobox('u_lang', $arry_lang, $px_lang); ?></p>

<?php if (false && count($m->user->webs)): ?>
  <p class="field"><label class="float" for="u_default" style="display:inline"><?php  echo __('Default website to edit:'); ?></label>
<?php
reset($m->user->webs);
$arry_websites = array();
foreach ($m->user->webs as $site => $score) {
	$arry_websites[$m->user->wdata[$site]['website_name']] = $site;
}
echo form::combobox('u_default', $arry_websites, $px_default);
?><br />
  <span class="notification"><?php  echo __('If saved in a cookie, the last edited website is proposed after login.'); ?></span>
  </p>
<?php endif; ?>
  <p class="button">
  <input name="save" type="submit" class="submit" value="<?php  echo __('Save [s]'); ?>" accesskey="<?php  echo __('s'); ?>" />
  </p>
</form>
<?php
/*
if (count($themes_list) > 1):
?>

<hr class="soft" />

<h2><?php  echo __('Manager Themes'); ?></h2>

<dl class="themes-list">

<?php

	reset($themes_list);
	foreach($themes_list as $theme) {
		echo '<dt><img alt="" src="themes/'.$theme['name'].'/preview.png" /> <span class="theme_style">'.$theme['label'].'</span> '.__('by').' <span class="author_style">'.$theme['author'].'</span></dt>'."\n";
		echo '<dd>'.$theme['desc'].' - '.__('version:').' '.$theme['version'];
		if ($_px_theme != $theme['name']) {
			echo '<br /><a href="prefs.php?switchtheme='.$theme['name'].'"><strong>'.__('Use this theme').'</strong></a>';
		}
		echo '</dd>'."\n\n";
	}
	echo '</dl>'."\n";

endif;
?>
</dl>
<?php 
*/
/*=================================================
 Load common bottom page
=================================================*/
include dirname(__FILE__).'/mtemplates/_bottom.php';

?>
