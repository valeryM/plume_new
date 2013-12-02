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

require_once '../path.php'; //get the path to the manager
require_once $_PX_config['manager_path'].'/inc/class.l10n.php';
require_once $_PX_config['manager_path'].'/inc/class.checklist.php';
require_once $_PX_config['manager_path'].'/inc/class.files.php';
require_once $_PX_config['manager_path'].'/inc/class.manager.php';
require_once $_PX_config['manager_path'].'/inc/class.user.php';
require_once $_PX_config['manager_path'].'/inc/lib.auth.php';
require_once $_PX_config['manager_path'].'/inc/lib.utils.php';
require_once $_PX_config['manager_path'].'/inc/lib.sqlutils.php';
require_once $_PX_config['manager_path'].'/inc/lib.form.php';
require_once $_PX_config['manager_path'].'/extinc/class.mysql.php';
require_once $_PX_config['manager_path'].'/extinc/class.configfile.php';
require_once $_PX_config['manager_path'].'/extinc/lib.form.php';
require_once $_PX_config['manager_path'].'/extinc/class.xmlsql.php';

if(!empty($_GET)) {
	array_walk($_GET,'magicStrip');
}
if(!empty($_POST)) {
	array_walk($_POST,'magicStrip');
}
if(!empty($_REQUEST)) {
	array_walk($_REQUEST,'magicStrip');
}
if(!empty($_COOKIE)) {
	array_walk($_COOKIE,'magicStrip');
}

session_start();

?>