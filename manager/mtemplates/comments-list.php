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

if (basename($_SERVER['SCRIPT_NAME']) == 'comments-list.php') exit;
if ($ct->isEmpty()) {
    echo '<p class="noresource">'.__('No comments.').'</p>'."\n\n";
} else {
    while (!$ct->EOF()) {
        switch ((int) $ct->f('comment_status')) {
        case PX_RESOURCE_STATUS_OFFLINE:
            $ct_class = 'cancel';
            $ct_img = '<img src="themes/'.$_px_theme
                .'/images/check_off.png" alt="'.__('Comment off-line')
                .'" class="status" />';
            break;
        case PX_RESOURCE_STATUS_VALIDE:
            $ct_class = 'published';
            $ct_img = '<img src="themes/'.$_px_theme
                .'/images/check_on.png" alt="'.__('Comment on-line')
                .'" class="status" />';
            break;
        case PX_RESOURCE_STATUS_JUNK:
            $ct_class = 'cancel';
            $ct_img = '<img src="themes/'.$_px_theme
                .'/images/ico_bin.png" alt="'.__('Comment is junk')
                .'" class="status" />';
            break;
        case PX_RESOURCE_STATUS_TOBEVALIDATED:
        default:
            $ct_class = 'published';
            $ct_img = '<img src="themes/'.$_px_theme
                .'/images/check_wait.png" alt="'
                .__('Comment waiting for validation').'" class="status" />';
            break;
        }
        echo '<div class="resourcebox '.$ct_class.'" id="c'.$ct->f('comment_id').'">';
        echo $ct_img;
        //Link for editing the resource
        $editreslink = $ct->f('type_id').'.php?resource_id='.$ct->f('resource_id');
        echo '<p class="resource_title"><span class="comment_style"><a href="'.$editreslink.'#comment'.$ct->f('comment_id').'" title="'.__('Comment').'">#'.$ct->f('comment_id').'</a></span> ';
        echo __('To').' <em><a href="'.$editreslink.'" title="'.__('Resource').'">'.$ct->f('title').'</a></em> - '.__('by').' <strong>'.$ct->f('comment_author').'</strong><br />'."\n";
        echo '<strong>'.date(__('Y/m/d \a\t H:i:s'), date::unix($ct->f('comment_update')))
            .'</strong></p>'."\n";
        echo '<div id="content'.$ct->f('comment_id').'">';
        echo $ct->getContent();
        if ($can_deleted_comments) {
            echo '<form method="post" action="comments.php">';
            echo '<p class="button"><input name="delete-comment" type="submit" class="submit" value="'.__('Delete').'" onclick="return window.confirm(\''.addslashes( __('Are you sure you want to delete this comment?')).'\')" />';
            echo form::hidden('comment_id',$ct->f('comment_id'), false);
            echo form::hidden('resource_id',$ct->f('resource_id'), false);
            if (strlen(form::getField('op'))>0) {
                echo form::hidden('op', 'all', false);
            }
            echo '</p>';
            echo '</form>'."\n";
        }
        echo "<hr class='invisible' /></div></div>\n\n";    
        $ct->moveNext();
    }

    if ($can_deleted_comments && strlen(form::getField('op'))>0) {
        echo '<p>'.__('Delete all the junk comments listed here.').'</p>';
        echo '<form method="post" action="comments.php">';
        echo '<p class="button"><input name="delete-comment" type="submit" class="submit" value="'.__('Delete the junk listed here').'" onclick="return window.confirm(\''.addslashes( __('Are you sure you want to delete all the comments marked as junk?')).'\')" />';
        echo form::hidden('delete-comments', '1', false);
        $ct->moveStart();
        echo form::hidden('max_id', $ct->f('comment_id'), false);
        $ct->moveEnd();
        echo form::hidden('min_id', $ct->f('comment_id'), false);
        echo '</p>';
        echo '</form>'."\n";
    }
}
    
    
?>
