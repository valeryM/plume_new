<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Teditor, plugin for Plume CMS
# Copyright (C) 2004-2006 Gilles ACCAD.
#
# Teditor is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# Teditor is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

if (basename($_SERVER['SCRIPT_NAME']) == 'teditor.init.php') exit;

// chargement des paramètres du site en cours
if (file_exists($_PX_config['manager_path'].'/conf/configweb_'.$_SESSION['website_id'].'.php')) {
	include($_PX_config['manager_path'].'/conf/configweb_'.$_SESSION['website_id'].'.php');
} else {
	$m->setError(sprintf( __('Error: Configuration file of the website(<strong>%s</strong>) not available.'),$_PX_config['manager_path'].'/conf/configweb_'.$_SESSION['website_id'].'.php'), 500);
}

// chargement du fichier de config sinon copie  vers /conf
if (file_exists($_PX_config['manager_path'].'/conf/configplugin_teditor.php')) {
	 include $_PX_config['manager_path'].'/conf/configplugin_teditor.php';
 } else {
	$src = dirname(__FILE__).'/config.copy.php';
	$dest = $_PX_config['manager_path'].'/conf/configplugin_teditor.php';
	$copy = files::copyfile($src, $dest);
	
	if (!$copy) {
		$m->setError( __('Error: unable to copy configuration file'), 500);		
		exit;
	} else {
        header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/tools.php?p=teditor');
    }
}
?>
