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

auth::checkAuth(PX_AUTH_NORMAL);

$m = new Manager();
$_px_theme = $m->user->getTheme();

$mode = (!empty($_REQUEST['mode'])) ? htmlspecialchars($_REQUEST['mode']) : '';
$px_chapter = (!empty($_REQUEST['c'])) ? $_REQUEST['c'] : '';
$px_plugin = (!empty($_REQUEST['p'])) ? $_REQUEST['p'] : '';

/* ================================================= *
 *             title of the page                     *
 * ================================================= */

$px_title =  __('Online help'); // used in _top.php

if ($mode == 'popup') {
    include dirname(__FILE__).'/mtemplates/_pop_top.php';
    echo '<p class="notification" style="text-align: right;"><a href="#" onclick="window.close(); return false;">'.__('Close this window').'</a></p>';
} else {
    include dirname(__FILE__).'/mtemplates/_top.php';
    echo '<h1 id="title_help">'. __('Online help').'</h1>'."\n\n";
}
$help = '';
if ('' != $px_chapter) {
    $help = $m->getHelp($px_chapter, $px_plugin, true);
}
if ('' == $help) {
    $px_chapter = '';
}

if ('' != $px_chapter) {
    //display the requested help
    echo '<h2>'.$help[1].'</h2>'."\n\n";
    echo $help[2];
}

//display the list of help chapters
echo '<h3>'.__('Help topics').'</h3>';
$chapters = $m->getHelpChapters();
echo '<ul>'."\n";
foreach ($chapters as $c) {
    echo '<li><a href="help.php?c='.$c[0].'&amp;mode='.$mode.'">'.$c[1].'</a></li>'."\n";
}
echo '</ul>'."\n";

echo '<h3>'. __('More help').'</h3>'."\n\n";
echo '<p>'. __('You can read the <a href="http://pxsystem.sourceforge.net/documentation.html">complete online documentation</a>.').'</p>'."\n\n";
echo '<p>'. __('You can also ask your questions to the community on the <a href="http://pxsystem.sourceforge.net/participate.html">forum</a>.').'</p>'."\n\n";



if ($mode == 'popup') {
    echo '<p class="notification" style="text-align: right;"><a href="#" onclick="window.close(); return false;">'.__('Close this window').'</a></p>';
    include dirname(__FILE__).'/mtemplates/_pop_bottom.php';
} else {
    include dirname(__FILE__).'/mtemplates/_bottom.php';
}
?>