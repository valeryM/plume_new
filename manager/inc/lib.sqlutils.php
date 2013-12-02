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

function &pxDBConnect()
{
    global $_PX_db, $_PX_config;
   
    if (isset($_PX_db) && is_resource($_PX_db->con_id))
        return $_PX_db;

    include_once dirname(__FILE__).'/../extinc/class.mysql.php';
    include_once dirname(__FILE__).'/lib.sql.php';

    $_PX_db = new Connection($_PX_config['db']['db_login'], 
                             $_PX_config['db']['db_password'],
                             $_PX_config['db']['db_server'],    
                             $_PX_config['db']['db_database'],
                             $_PX_config['db']['table_prefix'], 
                             $_PX_config['debug'],
                             config::f('db_version'));
    if($_PX_db->error()) {
        header('Content-Type: text/plain');
        echo 'Error MySQL : ' . $_PX_db->error();
        exit;
    }
    return $_PX_db;
}
?>
