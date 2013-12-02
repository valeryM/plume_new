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

require_once 'path.php';
require_once $_PX_config['manager_path'].'/prepend.php';
auth::checkAuth(PX_AUTH_ADMIN);

$m = new Manager();
$_px_theme = $m->user->getTheme();

$px_lang = new l10n($m->user->lang);

// Niveau autorisé : Admin ald ROOT
$is_user_root = auth::asLevel(PX_AUTH_ADMIN, $m->user->website, $m->user);

require dirname(__FILE__).'/extinc/class.plugins.php';


# On fait la liste des plugins
$plugins_root = dirname(__FILE__).'/tools/';

$objPlugins = new plugins($plugins_root);
$plugins_list = $objPlugins->getPlugins();

$include = '';

if (!empty($_REQUEST['p']) && !empty($plugins_list[$_REQUEST['p']])
    && $plugins_list[$_REQUEST['p']]['active']) {
	$px_submenu->addItem(__('Back to the tools'), 'tools.php',
                         'themes/'.$_px_theme.'/images/ico_back.png',
                         false);
	$p = $_REQUEST['p'];
	$_px_ptheme = $m->user->getPluginTheme($p);
	ob_start();
	include $plugins_root.$p.'/index.php';
	$include = ob_get_contents();
	ob_end_clean();
}

$px_title = __('Tools and plugins');

include dirname(__FILE__).'/mtemplates/_top.php';
if ($include != '') {	
	echo $include;
} else {
	echo '<h1 id="title_tools">'. __('Tools and plugins')."</h1>\n\n";
	if (count($plugins_list) == 0) {
        echo '<p class="message">'. __('No active or available tools.').'</p>';
    } else {
        echo '<dl class="plugin-list">';
        foreach ($plugins_list as $pname => $pdesc) {
            if ($pdesc['rootonly'] != true or ($is_user_root)) {
                echo '<dt>';
                $ptheme = $m->user->getPluginTheme($pname);
                if (file_exists($plugins_root.$pname.'/themes/'.$ptheme.'/icon.png')) {
                    echo '<img alt="" src="tools/'.$pname.'/themes/'.$ptheme.'/icon.png" /> ';
                }
                echo '<a href="tools.php?p='.$pname.'">';
    			
                if (!empty($pdesc['label'])) {
                    echo $pdesc['label'];
                } else {
                    echo $pdesc['name'];
                }
                echo '</a>';
                echo '</dt>';
    			
                echo '<dd>'.$pdesc['desc'].'</dd>';
            }
        }
        echo '</dl>';
    }
}

include dirname(__FILE__).'/mtemplates/_bottom.php'; 
?>