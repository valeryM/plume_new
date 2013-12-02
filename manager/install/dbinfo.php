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

$_px_p = 30; //percentage of wizard done.

//Get the info for the database.
/*
$_PX_config['db']['db_server']    = 'localhost'; // Server
$_PX_config['db']['db_database']  = 'plume'; // Name of the database
$_PX_config['db']['db_login']     = 'root'; // User/login to the database
$_PX_config['db']['db_password']  = ''; // Password
$_PX_config['db']['db_type']      = 'mysql'; // Type of database engine
$_PX_config['db']['table_prefix'] = 'plume_'; // Prefix on the tables to access them
*/

if (empty($_SESSION['step3'])) {
	header('Location: encoding.php');
	exit;
}

//load the language file
$_PX_config['encoding'] = $_SESSION['manager_encoding'];
$l = new l10n($_SESSION['lang'], 'install');

$px_dbserver = 'localhost';
$px_dbname = 'plume';
$px_dblogin = 'root';
$px_dbpass = '';
$px_dbprefix = 'plume_';
$px_dberror = false;


if (!empty($_POST['dbserver'])) {
	$px_dbserver = (!empty($_POST['dbserver'])) ? $_POST['dbserver'] : '';
	$px_dbname = (!empty($_POST['dbname'])) ? $_POST['dbname'] : '';
	$px_dblogin = (!empty($_POST['dblogin'])) ? $_POST['dblogin'] : '';
	$px_dbpass = (!empty($_POST['dbpass'])) ? $_POST['dbpass'] : '';
	$px_dbprefix = (!empty($_POST['dbprefix'])) ? $_POST['dbprefix'] : '';

	//try a connection and check the results
	$db = new Connection($px_dblogin, $px_dbpass, $px_dbserver, $px_dbname, $px_dbprefix, true);
	if ($db->error()) {
		$px_dberror = true;
		$px_mess = $db->error();
	} else {
        $rsV = $db->select('SELECT VERSION() AS version');
        $mysql_version = preg_replace('/-log$/','',$rsV->f(0));
		$_SESSION['dbserver'] = $px_dbserver;
		$_SESSION['dbname'] = $px_dbname;
		$_SESSION['dblogin'] = $px_dblogin;
		$_SESSION['dbpass'] = $px_dbpass;
		$_SESSION['dbprefix'] = $px_dbprefix;
        $_SESSION['db_version'] = $mysql_version;
		$_SESSION['step4'] = true;
		header('Location: dbinstall.php');
		exit;
	}

}



include dirname(__FILE__).'/_top.php';

if ($px_dberror) {
	echo '<p class="important">'.__('The system was unable to connect to the database, please check the error message and the information you provided to fix the problem.').'</p>'."\n\n";
	echo '<p>'.sprintf(__('Error message: %s'), $px_mess).'</p>'."\n\n";
}
echo '<h2>'.__('Database information').'</h2>'."\n\n";

echo '<p>'.__('The content of the website is saved into a MySQL database. Please provide the necessary data in order to connect to the database.').'</p>'."\n\n";

?>
<form action="dbinfo.php" method="post" id="formPost">

<p class="field"><label for="dbserver"><strong><?php  echo __('Database server:'); ?></strong></label>
<?php echo php_f_textField('dbserver', 30, 255, $px_dbserver); ?>
</p>

<p class="field"><label for="dbname"><strong><?php  echo __('Database name:'); ?></strong></label>
<?php echo php_f_textField('dbname', 30, 255, $px_dbname); ?>
</p>

<p class="field"><label for="dblogin"><strong><?php  echo __('Login to access the database:'); ?></strong></label>
<?php echo php_f_textField('dblogin', 30, 255, $px_dblogin); ?>
</p>

<p class="field"><label for="dbpass"><strong><?php  echo __('Password to access the database:'); ?></strong></label>
<?php echo php_f_textField('dbpass', 30, 255, $px_dbpass); ?>
</p>

<p class="field"><label for="dbprefix"><strong><?php  echo __('Prefix for the tables in the database:'); ?></strong></label>
<?php echo php_f_textField('dbprefix', 30, 255, $px_dbprefix); ?><br />
<span class="small"><?php echo __('Change it only if you have several installations of PLUME CMS with the same database.'); ?>
</p>
<p><?php echo __('In the next step the database will be initialized with the necessary tables for PLUME CMS.'); ?></p>
<p>
<input name="next" type="submit" class="submit" value="<?php  echo __('Next'); ?>"  accesskey="n" />
</p>
</form>
<?php

include dirname(__FILE__).'/_bottom.php';

?>
