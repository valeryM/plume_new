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

$_px_p = 100; //percentage of wizard done.

if (empty($_SESSION['step7'])) {
	header('Location: firstwebsite.php');
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

$u = new User(1); //user with "root" id
$m = new Manager();
$m->setUser($u);
$error = false;
$error_mess = '';

$source_file      = dirname(__FILE__).'/../conf/config.php-dist';
$destination_file = dirname(__FILE__).'/../conf/config.php';
@copy($source_file, $destination_file);
@chmod($destination_file, 0666);

$cfg = new configfile($destination_file);
$cfg->prefix = "_PX_config['db']";
$cfg->editVar('db_login',     (string) $_PX_config['db']['db_login']);
$cfg->editVar('db_password',  (string) $_PX_config['db']['db_password']);
$cfg->editVar('db_server',    (string) $_PX_config['db']['db_server']);
$cfg->editVar('db_database',  (string) $_PX_config['db']['db_database']);
$cfg->editVar('table_prefix', (string) $_PX_config['db']['table_prefix']);

$cfg->prefix = '_PX_config';
$cfg->editVar('lang', (string) $_SESSION['manager_lang']);
$cfg->editVar('encoding', (string) $_SESSION['manager_encoding']);
$cfg->editVar('debug', false);
$cfg->editVar('secret_key', (string) Misc::getRandomString());
$cfg->editVar('db_version', (string) $_SESSION['db_version']);

if (!$cfg->saveFile()) {
	$error_mess = __('Error: Impossible to create the configuration file.');
	$error = true;
}

include dirname(__FILE__).'/_top.php';

echo '<h2>'.__('End of the installation').'</h2>'."\n\n";

if($error === true) {
	echo '<p class="important">'.__('Errors during the creation of the configuration file. Check the online documentation to get the solution!').'</p>'."\n\n";
    echo "\n\n" . $error_mess . "\n\n";
} else {
	echo '<h3>'.__('Congratulations, PLUME CMS has been installed!').'</h3>'."\n\n";
}

echo '<p>'.__('The very last step is your work. You just have to create a new article or news into you website.').'</p>'."\n";
echo '<p>'.sprintf(__('You can now access the manager with the login and password you provided before. <a href="%s">Access the manager</a>.'), '../login.php?logout=1').'</p>'."\n";

include dirname(__FILE__).'/_bottom.php';
?>
