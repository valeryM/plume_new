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
$_px_p = 40; //percentage of wizard done.

if (empty($_SESSION['step4'])) {
	header('Location: dbinfo.php');
	exit;
}

//load the language file
$_PX_config['encoding'] = $_SESSION['manager_encoding'];
$l = new l10n($_SESSION['lang'], 'install');


$_PX_config['db']['db_login']     = $_SESSION['dblogin'];
$_PX_config['db']['db_password']  = $_SESSION['dbpass'];
$_PX_config['db']['db_server']    = $_SESSION['dbserver'];
$_PX_config['db']['db_database']  = $_SESSION['dbname'];
$_PX_config['db']['table_prefix'] = $_SESSION['dbprefix'];
$_PX_config['debug'] = true;
$_PX_config['db_version'] = $_SESSION['db_version'];
$con =& pxDBConnect();

$extra = '';
$charset = '';
$rsV = $con->select('SELECT VERSION() AS version');
$mysql_version = preg_replace('/-log$/','',$rsV->f(0));
if (version_compare($mysql_version, '3.23', '>=')) {
    $extra = ' TYPE=MyISAM';
}
if (version_compare($mysql_version, '4.1', '>=')) {
	$extra = ' ENGINE=MyISAM';
    $charset = 'DEFAULT CHARSET=utf8';
}

$checklist = new checklist();
$xml = implode("\n", file('./db-create.xml'));
$sql = new xmlsql($con, $xml);

$sql->replace('{{TYPE}}', $extra);
$sql->replace('{{PREFIX}}',$_PX_config['db']['table_prefix']);
$sql->replace('{{CHARSET}}',$charset);
$sql->execute($checklist);

$check = $checklist->checkAll();

if($check) {
	$_SESSION['step5'] = true;
}

include dirname(__FILE__).'/_top.php';

echo '<h2>'.__('Database installation').'</h2>'."\n\n";

if(!$check) {
	echo '<p class="important">'.__('Errors during database initialization procedure.').'</p>'."\n\n";
}

echo $checklist->getHtml('../themes/default/images');


if($check) {
	echo '<p>'.sprintf(__('In the next step the first user will be registered to use the system. <a href="%s">Next</a>.'), 'firstuser.php').'</p>'."\n\n";
}
include dirname(__FILE__).'/_bottom.php';
?>
