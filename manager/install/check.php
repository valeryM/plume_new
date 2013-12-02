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
require_once dirname(__FILE__).'/prepend.php';

$_px_p = 10; //percentage of wizard done.



if (empty($_SESSION['step1'])) {
    header('Location: index.php');
    exit;
}

/**
 * load the language file
 */
$_PX_config['encoding'] = $_SESSION['encoding'];
$l = new l10n($_SESSION['lang'], 'install');

/* Check of the php installation */

//Check the php version
$checklist = new checklist('php', version_compare(phpversion(),'4.1','>='),
                           sprintf(__('The installed PHP version is: %s.'), phpversion()),
    sprintf(__('The installed PHP version is: %s. PHP 4.1 or higher is required.'), phpversion()));
//Check the support of MySQL
$checklist->addTest('mysql', function_exists('mysql_connect'),
    __('The MySQL module is available.'),
    __('The MySQL module is not available.'));
//Check the support of the output buffer functions
$checklist->addTest('ob', function_exists('ob_start'),
    __('The output buffering functions are available.'),
    __('The output buffering functions are not available.'));
//Check the support of gd for thumbnails
//Note: the library version is not tested as the output buffering functions are needed.
//I may get the results of the test 'ob' and then if ok, check the version.
$checklist->addTest('gd', (function_exists('imagecreatefromjpeg')) ? 1 : 2,
    __('The GD library is available.'),
    '', /* no error in test only warning */
    __('The GD library is not available. Thumbnails will not be created automatically.'));
//Need the XML module for the SQL xml class
$checklist->addTest('xmlparser', function_exists('xml_parser_create'),
    __('The XML module is available.'),
    __('The XML module is not available.'));


/* Check of the read/write of the files */
// Files/folders to check:
// xmedia and xmedia/thumb
// conf folder and files in
// cache folder
// templates
// tools (warning only for the update manager)
$rp = $_PX_config['manager_path'];
//Configuration folder
$checklist->addTest('conf-folder', is_writable($rp.'/conf/'),
    sprintf(__('The system can write in the configuration folder: %s'), files::real_path($rp.'/conf/')),
    sprintf(__('The system cannot write in the configuration folder: %s. Check the permissions.'), files::real_path($rp.'/conf/')));
//Cache folder
$checklist->addTest('cache-folder', is_writable($rp.'/cache/'),
    sprintf(__('The system can write in the cache folder: %s'), files::real_path($rp.'/cache/')),
    sprintf(__('The system cannot write in the cache folder: %s. Check the permissions.'), files::real_path($rp.'/cache/')));
//Template folder
$checklist->addTest('templates-folder', is_writable($rp.'/templates/'),
    sprintf(__('The system can write in the template folder: %s'), files::real_path($rp.'/templates/')),
    sprintf(__('The system cannot write in the template folder: %s. Check the permissions.'), files::real_path($rp.'/templates/')));
//document root
$checklist->addTest('root-folder', is_writable($rp.'/../'),
    sprintf(__('The system can write in the document folder: %s'), files::real_path($rp.'/../')),
    sprintf(__('The system cannot write in the document folder: %s. Check the permissions.'), files::real_path($rp.'/../')));
//xmedia path and thumbnail path
$checklist->addTest('xmedia-folder', is_writable($rp.'/../xmedia/'),
    sprintf(__('The system can write in the document folder: %s'), files::real_path($rp.'/../xmedia/')),
    sprintf(__('The system cannot write in the document folder: %s. Check the permissions.'), files::real_path($rp.'/../xmedia/')));
$checklist->addTest('theme-folder', is_writable($rp.'/../xmedia/theme/'),
    sprintf(__('The system can write in the theme folder: %s'), files::real_path($rp.'/../xmedia/theme/')),
    sprintf(__('The system cannot write in the theme folder: %s. Check the permissions.'), files::real_path($rp.'/../xmedia/theme/')));
$checklist->addTest('thumb-folder', (is_writable($rp.'/../xmedia/thumb/')) ? 1 : 2,
    sprintf(__('The system can write in the thumbnail folder: %s'), files::real_path($rp.'/../xmedia/thumb/')),
    '', /*no error but warning */
    sprintf(__('The system cannot write in the thumbnail folder: %s. Check the permissions or you will not be able to have automatic creation of the thumbnails.'), files::real_path($rp.'/../xmedia/thumb/')));

$check = $checklist->checkAll();
if ($check) {
    $_SESSION['step2'] = true;
}
include dirname(__FILE__).'/_top.php';

echo '<h2>'.__('Check of the PLUME CMS support by the system').'</h2>'."\n\n";

echo $checklist->getHtml('../themes/default/images');

if ($check) {
    echo '<p>'.__('Congratulations, your system supports PLUME CMS. Warnings will not prevent your use of PLUME CMS.').'</p>'."\n";
    echo '<p>'.__('No irreversible action is taken during the installation process. You can revert your choices from the manager at a later time.').'</p>'."\n";


    echo '<p>'.sprintf(__('<a href="%s">Continue with the choice of languages</a>.'), 'encoding.php').'</p>'."\n";

} else {
    echo '<p>'.__('Some errors prevent the installation of PLUME CMS on your system. You can fix them and restart the wizard afterwards.').'</p>'."\n\n";
}
include dirname(__FILE__).'/_bottom.php';

?>