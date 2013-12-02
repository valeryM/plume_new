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

if (basename($_SERVER['SCRIPT_NAME']) == 'prepend.php') exit;

include_once $_PX_config['manager_path'].'/conf/config.php';
include_once $_PX_config['manager_path'].'/inc/class.config.php';
include_once $_PX_config['manager_path'].'/inc/lib.text.php';
include_once $_PX_config['manager_path'].'/inc/class.hook.php';
include_once $_PX_config['manager_path'].'/inc/class.dispatcher.php';
include_once $_PX_config['manager_path'].'/inc/class.rss.php';
include_once $_PX_config['manager_path'].'/inc/class.search.php';
include_once $_PX_config['manager_path'].'/inc/class.error404.php';
include_once $_PX_config['manager_path'].'/inc/class.category.php';
include_once $_PX_config['manager_path'].'/inc/class.resource.php';
include_once $_PX_config['manager_path'].'/inc/class.news.php';
include_once $_PX_config['manager_path'].'/inc/class.events.php';
include_once $_PX_config['manager_path'].'/inc/class.rsslinks.php';
include_once $_PX_config['manager_path'].'/inc/class.article.php';
include_once $_PX_config['manager_path'].'/inc/class.resourceset.php';
include_once $_PX_config['manager_path'].'/inc/lib.frontend.php';
include_once $_PX_config['manager_path'].'/inc/lib.sql.php';
include_once $_PX_config['manager_path'].'/inc/class.paginator.php';
include_once $_PX_config['manager_path'].'/inc/class.cache.php';
include_once $_PX_config['manager_path'].'/inc/class.sitemap.php';
include_once $_PX_config['manager_path'].'/inc/class.l10n.php';

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
?>