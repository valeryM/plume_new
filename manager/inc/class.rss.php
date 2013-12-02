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



class RSS
{
    /**
     * Action to display the RSS feeds.
     *
     * @param string Server query string
     * @return int Success code
     */
    public static function action($query)
    {
        Hook::register('onInitTemplate', 'RSS', 'hookOnInitTemplate');
        $l10n = new l10n(config::f('lang'));
        $l10n->loadTemplate(config::f('lang'), config::f('theme_id'));
        config::setVar('category_page', 1);
        $category_id = '';
        $path = RSS::parseQueryString($query);
        if ($path != '/') {
            $sql = SQL::getCategoryByPath($path, config::f('website_id'));
            //echo $sql;
            $con =& pxDBConnect();
            if (($cat = $con->select($sql, 'Category')) !== false) {
                if (!$cat->isEmpty()) {
                    $category_id = $cat->f('category_id');
                }
            }
        }
        $GLOBALS['_PX_render']['last'] = '';
        $last =& $GLOBALS['_PX_render']['last']; 
        $GLOBALS['_PX_render']['website'] = '';
        $website =& $GLOBALS['_PX_render']['website']; 

        $cache = new Cache(urlencode('rss').':'.$category_id);
        $cache->setCacheDirectory(config::getCacheDir());
        $cache->debug = config::f('debug');

        header(FrontEnd::getHeader('feed.atom.php'));
        //header('Content-Disposition: attachment; filename="feed.xml"');
        // Load the template
		include config::f('manager_path').'/templates/'
            .config::f('theme_id').'/feed.atom.php';
        return 200;
        
    }

    /**
     * Hook on the initialization of the templates.
     *
     * @param array Default parameters (not used)
     * @return bool Success
     */
    public static function hookOnInitTemplate($param)
    {
        if (config::f('action') == 'RSS') {
            $GLOBALS['_PX_render']['website'] = FrontEnd::getWebsite();
        }
        return true;
    }

    /**
     * Parse query string.
     *
     * @param string Query string
     * @return string Category path
     */
    public static function parseQueryString($query)
    {
		$category = '';
        if (preg_match('#^/feed(.*/)$#i', $query, $match)) {
			$category = $match[1];
		} else {
			$category = '/';
		}
        return $category;
    }

}
?>
