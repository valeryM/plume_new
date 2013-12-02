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

if (basename($_SERVER['SCRIPT_NAME']) == 'comments-rlist.php') exit;

/**
 * @file comments-rlist.php
 *
 * List the comments associated to a resource.
 * Provide the form to modify each comment but also add a new comment.
 *
 * $ct: Stores the list of comments for the given resource.
 */

echo '<hr class="soft" />';
echo '<h2 id="title_comments">'.__('Comments').'</h2>'."\n";
    
if ($ct->isEmpty()) {
    echo '<p id="noresource">'.__('No comments for the moment.').'</p>'."\n\n";
} else {
    $i = 1;
    while (!$ct->EOF()) {
        switch ($ct->f('comment_status')) {
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
        echo '<div class="resourcebox '.$ct_class.'" id="comment'.$ct->f('comment_id').'">'
            .'<a href="#" onclick="openCloseSpan(\'c'.$ct->f('comment_id')
            .'\',0); return false;" title="'.__('Show/hide edition form').'">'
            .'<img src="themes/'.$_px_theme.'/images/plus.png" id="img_c'
            .$ct->f('comment_id').'" class="show_button" alt="'.__('Show/hide edition form')
            .'" /></a>';
         echo $ct_img;
         echo '<p class="infos"><span class="comment_style"><strong>'.__('Comment').' '.$ct->f('comment_id').'</strong></span> '.__('by')
            .' <span class="author_style"><strong>'.$ct->f('comment_author').'</strong></span> '
            .'(<span class="mail_style"><a href="mailto:'.$ct->f('comment_email').'">'
            .$ct->f('comment_email').'</a></span>';
        if (strlen($ct->f('comment_website'))) {
            echo ', <span class="sitename_style"><a href="'.$ct->f('comment_website').'">'
                .$ct->f('comment_website').'</a></span>';
        }
        echo ')</p>'."\n";
        echo '<p class="datetime">'
            .date(__('Y/m/d \a\t H:i:s'), date::unix($ct->f('comment_update')))
            .' '.__('with the IP address:').' '.$ct->f('comment_ip')
            .'</p>'."\n";
        echo '<div class="description_style">'.$ct->getContent().'</div>';
        echo '<div id="c'.$ct->f('comment_id').'" style="display:none;">';
        echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'">'."\n";
        echo '<p><label for="id_c_author_'.$i.'"><span class="author_style"><strong>'.__('Author')
            .'</strong></span></label> ';
        echo form::textField('c_author', 30, 50, $ct->f('comment_author'),
                             '', '', 'id_c_author_'.$i);
        echo '</p>'."\n";
        echo '<p><label for="id_c_email_'.$i.'"><span class="mail_style"><strong>'.__('Email')
            .'</strong></span></label> ';
        echo form::textField('c_email', 30, 50, $ct->f('comment_email'),
                             '', '', 'id_c_email_'.$i);
        echo '</p>'."\n";
        echo '<p><label for="id_c_website_'.$i.'"><span class="sitename_style">'.__('Website').'</span></label> ';
        echo form::textField('c_website', 30, 50, $ct->f('comment_website'),
                             '', '', 'id_c_website_'.$i);
        echo '</p>'."\n";
        echo '<p><label for="id_c_content_'.$i.'"><strong>'.__('Content of the comment');
        echo '</strong> ';
        echo '</label> ';
        echo form::textArea('c_content', 60, 10, $ct->f('comment_content'), 
                            '', 'style="width:50%"', 'id_c_content_'.$i);
        echo '</p>'."\n";
        // Status
        echo '<p><span class="nowrap"><label for="id_c_status_'.$i.'" ';
        echo 'style="display:inline">';
        echo __('Status').' '; 
        echo '</label> ';
        echo form::combobox('c_status', $m->getArrayCommentStatus(), 
                            $ct->f('comment_status'), '', '', '', '',
                            'id_c_status_'.$i); 

        echo "</span></p>\n\n";

        // submit buttons
        echo "<p class='button'>\n";
        echo form::button('submit', 'publish-comment', __('Save'), 
                          '', '', 'submit', 'publish_comment_'.$i)."\n";

        echo '&nbsp;<input name="delete-comment" type="submit" class="submit" value="'.__('Delete').'" onclick="return window.confirm(\''.addslashes( __('Are you sure you want to delete this comment?')).'\')" />';
        echo form::hidden('comment_id',$ct->f('comment_id'), false);
        echo form::hidden('resource_id',$ct->f('resource_id'), false);
        echo '</p>';
        
        echo '</form>'."\n";
        echo '</div>';
        echo "</div>\n\n";    
        $ct->moveNext();
        $i++;
    }
}
    
echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
echo "\n";
echo '<fieldset><legend><span class="comment_style">'. __('Add a comment')."</span></legend>\n\n";
echo '<p><label for="c_author"><span class="author_style"><strong>'.__('Author').'</strong></span></label> ';
echo form::textField('c_author', 30, 50, $ct->f('comment_author'));
echo '</p>'."\n";
echo '<p><label for="c_email"><span class="mail_style"><strong>'.__('Email').'</strong></span></label> ';
echo form::textField('c_email', 30, 50, $ct->f('comment_email'));
echo '</p>'."\n";
echo '<p><label for="c_website"><span class="sitename_style">'.__('Website').'</span></label> ';
echo form::textField('c_website', 30, 50, $ct->f('comment_website'));
echo '</p>'."\n";
echo '<p><label for="c_content"><strong>'.__('Content of the comment');
echo '</strong> ';
echo '</label> ';
echo form::textArea('c_content', 60, 10, 
                    $ct->f('comment_content'), '', 'style="width:50%"');
echo '</p>'."\n";
// Status
echo '<p><span class="nowrap"><label for="c_status" ';
echo 'style="display:inline">';
echo __('Status').' '; 
echo '</label> ';
echo form::combobox('c_status', $m->getArrayCommentStatus(), 
                    $ct->f('comment_status')); 

echo "</span></p>\n\n";

// submit buttons
echo "<p class='button'>\n";
echo form::button('submit', 'publish-comment', __('Save'), 
                  '', '', 'submit')."\n";

echo '&nbsp;<input name="delete-comment" type="submit" class="submit" value="'.__('Delete').'" onclick="return window.confirm(\''.addslashes( __('Are you sure you want to delete this comment?')).'\')" />';
echo form::hidden('comment_id',$ct->f('comment_id'), false);
echo form::hidden('resource_id',$px_resource_id, false);
echo '</p>';
echo '</fieldset>';
echo '</form>'."\n";
?>