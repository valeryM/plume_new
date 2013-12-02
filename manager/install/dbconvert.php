<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Plume CMS, a website management application.
# Copyright (C) 2001-2006 Loic d'Anterroches and contributors.
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
if (basename($_SERVER['SCRIPT_NAME']) == 'dbconvert.php') {
    exit;
}
include_once dirname(__FILE__).'/../inc/lib.utils.php';
/**
 * Convert a table from latin1 to utf-8.
 *
 * The fields to be converted are in the array $fields.
 * The primary key against which the update is performed is $primkey.
 */
function convert_table($table, $primkey, $fields)
{
    $err = '';
    if (is_array($primkey)) {
        $primstr = implode(', ', $primkey);
    } else {
        $primstr = $primkey;
    }
    $select = 'SELECT '.$primstr.', '.implode(', ', $fields).' FROM '.$table;
    $con = &pxDBConnect();

    if (($rs = $con->select($select)) === false) {
        $err = $con->error();
        return $select.' - '.$err;
    }
    while (!$rs->EOF()) {
        $update = 'UPDATE '.$table.' SET ';
        $u_fields = array();
        foreach ($fields as $k => $field) {
            $u_fields[] = $field.'=\''.$con->esc(Misc::latin1_utf8($rs->f($field))).'\'';
        }
        $update .= implode(', ', $u_fields);
        if (!is_array($primkey)) {
            $update .= ' WHERE '.$primkey.' = \''
                .$con->esc(Misc::latin1_utf8($rs->f($primkey))).'\'';
        } else {
            $prims = array();
            foreach ($primkey as $k => $field) {
                $prims[] = $field.'=\''.$con->esc(Misc::latin1_utf8($rs->f($field))).'\'';
            }
            $update .= ' WHERE '.implode(' AND ', $prims);
        }
        if ($con->execute($update) === false) {
            $err = $con->error();
            $err = $update.' - '.$err;
            break;
        }
        $rs->moveNext();
    }
    return $err;
}

$tableconvert = __('The table %s has been converted to the utf-8 encoding.');
$tableerrors = __('Errors occured when converting the table %s to the utf-8 encoding:');


$con = pxDBConnect();

$err = convert_table($con->pfx.'users', 
                     'user_id', 
                     array(
                           'user_username',
                           'user_realname',
                           'user_email',
                           'user_pubemail',
                           'user_website',
                           'user_company',
                           'lang_id',
                           'country_id',
                           'user_status',
                           'user_image',
                           'user_icq',
                           'user_aol',
                           'user_yahoo',
                           'user_msn',
                           'user_jabber',
                           'user_signature'));
$g_mess = sprintf($tableconvert, $con->pfx.'users');
$b_mess = sprintf($tableerrors, $con->pfx.'users').' '.$err;
$checklist->addTest('convert-users', (strlen($err) == 0), $g_mess, $b_mess);

$err = convert_table($con->pfx.'articles', 
                     'page_id', 
                     array(
                           'page_title',
                           'page_content',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'articles');
$b_mess = sprintf($tableerrors, $con->pfx.'articles').' '.$err;
$checklist->addTest('convert-articles', (strlen($err) == 0), $g_mess, $b_mess);

$err = convert_table($con->pfx.'categories', 
                     'category_id', 
                     array(
                           'website_id',
                           'category_name',
                           'category_description',
                           'category_keywords',
                           'category_path',
                           'category_template',
                           'category_type',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'categories');
$b_mess = sprintf($tableerrors, $con->pfx.'categories').' '.$err;
$checklist->addTest('convert-categories', (strlen($err) == 0), $g_mess, $b_mess);

$err = convert_table($con->pfx.'categoryasso', 
                     array('category_id', 'identifier'), 
                     array(
                           'identifier',
                           'template',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'categoryasso');
$b_mess = sprintf($tableerrors, $con->pfx.'categoryasso').' '.$err;
$checklist->addTest('convert-categoryasso', (strlen($err) == 0), $g_mess, $b_mess);

$err = convert_table($con->pfx.'comments', 
                     'comment_id', 
                     array(
                           'comment_author',
                           'comment_email',
                           'comment_website',
                           'comment_content',
                           'comment_ip',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'comments');
$b_mess = sprintf($tableerrors, $con->pfx.'comments').' '.$err;
$checklist->addTest('convert-comments', (strlen($err) == 0), $g_mess, $b_mess);

$err = convert_table($con->pfx.'grants', 
                     array('user_id', 'website_id'),
                     array(
                           'website_id',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'grants');
$b_mess = sprintf($tableerrors, $con->pfx.'grants').' '.$err;
$checklist->addTest('convert-grants', (strlen($err) == 0), $g_mess, $b_mess);

$err = convert_table($con->pfx.'links', 
                     'link_id',
                     array(
                           'website_id',
                           'href',
                           'label',
                           'title',
                           'lang',
                           'rel', 	
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'links');
$b_mess = sprintf($tableerrors, $con->pfx.'links').' '.$err;
$checklist->addTest('convert-links', (strlen($err) == 0), $g_mess, $b_mess);

$err = convert_table($con->pfx.'news', 
                     'resource_id',
                     array(
                           'news_titlewebsite',
                           'news_linkwebsite',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'news');
$b_mess = sprintf($tableerrors, $con->pfx.'news').' '.$err;
$checklist->addTest('convert-news', (strlen($err) == 0), $g_mess, $b_mess);

$err = convert_table($con->pfx.'resources', 
                     'resource_id',
                     array(
                           'website_id',
                           'type_id',
                           'identifier',
                           'subject',
                           'creatorname',
                           'creatoremail',
                           'creatorwebsite',
                           'publisher',
                           'lang_id',
                           'title',
                           'description',
                           'path',
                           'size',
                           'version',
                           'metadata',
                           'comment',
                           'misc',
                           'format',
                           'dctype',
                           'dccoverage',
                           'rights',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'resources');
$b_mess = sprintf($tableerrors, $con->pfx.'resources').' '.$err;
$checklist->addTest('convert-resources', (strlen($err) == 0), $g_mess, $b_mess);
$err = convert_table($con->pfx.'search', 
                     array('resource_id', 'website_id'),
                     array(
                           'website_id',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'search');
$b_mess = sprintf($tableerrors, $con->pfx.'search').' '.$err;
$checklist->addTest('convert-search', (strlen($err) == 0), $g_mess, $b_mess);

/*
$err = convert_table($con->pfx.'searchocc', 
                     array('resource_id', 'website_id', 'word_id'),
                     array(
                           'website_id',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'searchocc');
$b_mess = sprintf($tableerrors, $con->pfx.'searchocc').' '.$err;
$checklist->addTest('convert-searchocc', (strlen($err) == 0), $g_mess, $b_mess);
*/
/*
$err = convert_table($con->pfx.'searchwords', 
                     'word_id',
                     array(
                           'word',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'searchwords');
$b_mess = sprintf($tableerrors, $con->pfx.'searchwords').' '.$err;
$checklist->addTest('convert-searchwords', (strlen($err) == 0), $g_mess, $b_mess);
*/

$err = convert_table($con->pfx.'smart404', 
                     array('website_id', 'oldpage'),
                     array(
                           'website_id',
                           'oldpage',
                           'newpage',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'smart404');
$b_mess = sprintf($tableerrors, $con->pfx.'smart404').' '.$err;
$checklist->addTest('convert-smart404', (strlen($err) == 0), $g_mess, $b_mess);

$err = convert_table($con->pfx.'subtypes', 
                     array('subtype_id', 'type_id', 'website_id'),
                     array(
                           'type_id',
                           'website_id',
                           'subtype_name',
                           'subtype_template',
                           'subtype_extra1',
                           'subtype_extra2',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'subtypes');
$b_mess = sprintf($tableerrors, $con->pfx.'subtypes').' '.$err;
$checklist->addTest('convert-subtypes', (strlen($err) == 0), $g_mess, $b_mess);


$err = convert_table($con->pfx.'userprefs', 
                     array('user_id', 'keyname', 'website_id'),
                     array(
                           'website_id',
                           'keyname',
                           'data',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'userprefs');
$b_mess = sprintf($tableerrors, $con->pfx.'userprefs').' '.$err;
$checklist->addTest('convert-userprefs', (strlen($err) == 0), $g_mess, $b_mess);
$err = convert_table($con->pfx.'websites', 
                     'website_id',
                     array(
                           'website_id',
                           'website_name',
                           'website_url',
                           'website_reurl',
                           'website_path',
                           'website_xmedia_reurl',
                           'website_xmedia_path',
                           'website_description',
                           'website_color',
                           )
                     );
$g_mess = sprintf($tableconvert, $con->pfx.'websites');
$b_mess = sprintf($tableerrors, $con->pfx.'websites').' '.$err;
$checklist->addTest('convert-websites', (strlen($err) == 0), $g_mess, $b_mess);

?>
