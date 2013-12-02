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
error_reporting(E_ALL);
session_start();

include_once dirname(__FILE__).'/conf/config.php';
include_once dirname(__FILE__).'/inc/class.config.php';
include_once dirname(__FILE__).'/inc/class.hook.php';
include_once dirname(__FILE__).'/inc/class.manager.php';
include_once dirname(__FILE__).'/inc/class.resource.php';
include_once dirname(__FILE__).'/inc/class.category.php';
include_once dirname(__FILE__).'/inc/class.resourceset.php';
include_once dirname(__FILE__).'/inc/class.l10n.php';
include_once dirname(__FILE__).'/inc/lib.auth.php';
include_once dirname(__FILE__).'/inc/lib.sql.php';
include_once dirname(__FILE__).'/inc/lib.sqlutils.php';
include_once dirname(__FILE__).'/inc/lib.text.php';
include_once dirname(__FILE__).'/inc/lib.utils.php';
include_once dirname(__FILE__).'/inc/class.files.php';
include_once dirname(__FILE__).'/inc/class.dispatcher.php';
include_once dirname(__FILE__).'/extinc/class.menu.php';
include_once dirname(__FILE__).'/inc/lib.form.php';
include_once dirname(__FILE__).'/inc/lib.pathSelector.php';
include_once dirname(__FILE__).'/inc/lib.frontend.php';

//require_once dirname(__FILE__).'/class.l10n.php';

include_once dirname(__FILE__).'/inc/class.basicmanager.php';
include_once dirname(__FILE__).'/inc/class.user.php';
include_once dirname(__FILE__).'/inc/class.article.php';
include_once dirname(__FILE__).'/inc/class.news.php';
include_once dirname(__FILE__).'/inc/class.rsslinks.php';
include_once dirname(__FILE__).'/inc/class.events.php';
include_once dirname(__FILE__).'/inc/class.mail.php';
include_once dirname(__FILE__).'/inc/class.website.php';
//include_once dirname(__FILE__).'/tools/htmlValidator/Services/W3C/HTMLValidator.php';

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

if(function_exists('ini_set')) {
    @ini_set('session.use_cookies','1');
    @ini_set('session.use_only_cookies','1');
    @ini_set('session.use_trans_sid','0');
    @ini_set('url_rewriter.tags','');
}

// Create menus
$px_menu = new menu('menu');
$px_submenu = new menu('submenu',' ',' | ');

// Set the context of operation
config::setContext('manager'); 

Dispatcher::loadControllers();
?>