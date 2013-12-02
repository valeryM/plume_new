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

/**
 * Create the path to a resource from a resource object
 *
 * @param  object resource
 * @return string path to the resource
 */
function pathToResource($res)
{
    global $_PX_context, $_PX_config;
    // Absolute path to resource, print it like that.
    if (ereg(':',$res->f('path')))
        return $res->f('path');

    if (isset($_PX_context['out'])) $base = $res->f('website_url');
    else $base = $res->f('website_reurl');
    if ($_PX_config['url_format'] == 'simple') $base .= '/?';
                
    // if type is xmedia the link is special
    if ('xmedia' == $res->f('type_id'))
        return  $base.$res->f('website_xmedia_reurl').$res->f('path');
    if ('news' == $res->f('type_id'))
        return $base.$res->f('category_path').$res->f('resource_id').'-'.textStr2url($res->f('title'));
    // else standard link
    return $base.$res->f('category_path').$res->f('path');
}


/**
 * Create the path to a category from the category object.
 *
 * @param  object category
 * @return string path to the category
 */
function pathToCategory($cat)
{
    global $_PX_context, $_PX_config;
    if (isset($_PX_context['out'])) $base = $res->f('website_url');
    else $base = $res->f('website_reurl');
    if ($_PX_config['url_format'] == 'simple') $base .= '/?';
    return $base.$cat->f('category_path');
}

/**
 * For the article it is possible to have multiple pages
 *
 * @param object resource
 * @param int number of the page
 * @return string path to the article page
 */
function pathToArticlePage($res, $page = 1)
{
    global $_PX_context, $_PX_config;
    if (isset($_PX_context['out'])) $base = $res->f('website_url');
    else $base = $res->f('website_reurl');
    if ($_PX_config['url_format'] == 'simple') $base .= '/?';
    if ($page == 1) $page = '';
    return $base.$res->f('category_path').$res->f('path').$page;
}
?>
