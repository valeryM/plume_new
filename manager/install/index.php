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

require_once dirname(__FILE__).'/prepend.php';

$_px_p = 0; //percentage of wizard done.

/**
 * Overview of the steps.
 *
 * 1- Choose language for installation and/or interface -> go to 9bis if update
 * 2- Check
 * 3- Choose encoding if needed and language for the interface
 * 4- Database info
 * 5- Database install
 * 6- First user
 * 7- First website
 * 8- First post
 * 9- Secure installer
 * 9bis- Update
 * 10- Go to the admin
 */

$installer_langs = l10n::getAvailableLocales('', 'install');
$install_lang = l10n::getAcceptedLanguage($installer_langs);
$encoding = '';
$update = false;
//Check if a global config file is available
if (file_exists($_PX_config['manager_path'].'/conf/config.php')) {
	include_once $_PX_config['manager_path'].'/conf/config.php';
	//overwrite install lang
	$install_lang = $_PX_config['lang'];
    $encoding = $_PX_config['encoding'];
	$update = true;
	$_SESSION['step1'] = true;
}

//Check if language selected.

if (!empty($_POST['lang'])) {
	if (in_array($_POST['lang'], $installer_langs)) {
        $_SESSION['encoding'] = 'utf-8';
		$_SESSION['lang'] = $_POST['lang'];
		$_SESSION['step1'] = true;
		header('Location: check.php');
		exit;
	}
}

//load the language file
$_PX_config['encoding'] = 'utf-8';

$l = new l10n($install_lang, 'install');

//Populate the list of languages
$isocodes = $l->getIsoCodes();
$langs = array();
reset($installer_langs);
foreach ($installer_langs as $iso) {
	$langs[$isocodes[$iso]] = $iso;
}

include dirname(__FILE__).'/_top.php';


echo '<h2>'.__('Welcome on the PLUME CMS installation').'</h2>'."\n\n";

if ($update) {
    if (strtolower($encoding) == 'iso-8859-1') {
        echo '<p>'.__('<strong>Warning</strong>, the upgrade will convert your installation from iso-8859-1 to utf-8, please perform a backup of your database.').'</p>';

    }
	echo '<p>'.sprintf(__('A possible previous installation of PLUME CMS is detected. You are invited to <a href="%s">upgrade your installation</a>.'), 'upgrade.php').'</p>';
} else {
	echo '<p>'.__('Please choose the language of the installation wizard.').'</p>'."\n\n";
	?>
	<form action="index.php" method="post" id="formPost">
		<p class="field">
		<label class="float" for="lang" style="display:inline"><?php  echo __('Language:'); ?></label>
		<?php echo form::combobox('lang', $langs, $install_lang);  ?>
		<input name="next" type="submit" class="submit" value="<?php  echo __('Next'); ?>"  accesskey="n" />
  		</p>
	</form>
	<?php
}

include dirname(__FILE__).'/_bottom.php';
?>