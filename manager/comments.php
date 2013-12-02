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
require_once dirname(__FILE__).'/prepend.php';
require_once dirname(__FILE__).'/inc/class.comment.php';

auth::checkAuth(PX_AUTH_NORMAL);
$is_user_admin = auth::asLevel(PX_AUTH_ADMIN);
$can_deleted_comments = auth::asLevel(PX_AUTH_ADVANCED);

$m = new Manager();
$_px_theme = $m->user->getTheme();

$px_submenu->addItem(__('Latest online comments'), 'comments.php',
                     'themes/'.$_px_theme.'/images/ico_comments_online.png', 
                     false, 'all' == form::getField('op'));
$px_submenu->addItem(__('Latest comments'), 'comments.php?op=all',
                     'themes/'.$_px_theme.'/images/ico_last_comments.png', 
                     false, 'all' != form::getField('op'));

/* ================================================= *
 *                 Remove a comment                  *
 * ================================================= */
if ($can_deleted_comments 
    && strlen(form::getField('delete-comment')) 
    && strlen(form::getField('resource_id')) 
    && strlen(form::getField('comment_id'))) {
    $id = form::getField('comment_id');
    if (false !== ($ct = $m->getComment($id, form::getField('resource_id')))) {
        if (false !== $m->delComment($ct)) {
            $m->setMessage(__('The comment was successfully deleted.'));
            $what = (strlen(form::getField('op'))>0) ? '?op=all' : '';
            header('Location: comments.php'.$what);
            exit;
        }
    }
}

if ($can_deleted_comments 
    && strlen(form::getPostField('delete-comments')) 
    && strlen(form::getPostField('max_id')) 
    && strlen(form::getPostField('min_id'))) {
    $min = (int) form::getPostField('min_id');
    $max = (int) form::getPostField('max_id');
    $ct = $m->getComments('', 15, PX_RESOURCE_STATUS_JUNK);
    while (!$ct->EOF()) { 
        if ($ct->f('comment_id') >= $min and $ct->f('comment_id') <= $max) {
            $m->delComment($ct);
        }
        $ct->moveNext();
    }
    $m->setMessage(__('The junk comments were successfully deleted.'));
    header('Location: comments.php?op=all');
    exit;
}

if (empty($_REQUEST['op'])) {
    $ct = $m->getComments('', 15, PX_RESOURCE_STATUS_VALIDE);
} elseif ('all' == form::getField('op')) {
    // get last 15 comments for the current website in 
    // reverse chronological order
    $ct = $m->getComments('', 15);
}

if ('all' != form::getField('op')) {
    $px_title = __('Latest online comments');
} else {
    $px_title = __('Latest comments');
}

include dirname(__FILE__).'/mtemplates/_top.php';

echo '<h1 id="title_comments">'.$px_title.'</h1>'."\n\n";

include dirname(__FILE__).'/mtemplates/comments-list.php';

include dirname(__FILE__).'/mtemplates/_bottom.php';
