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
# Contributor(s):
# - Kato Fong (kato AT sdf.lonestar.org)
# ***** END LICENSE BLOCK ***** */

include_once dirname(__FILE__).'/functions.php';

/**
 * Alertcom: Send the webmaster an alert mail each time a comment is left on the site.
 */
class Alertcom
{
    /**
     * $p['ct'] contains the comment. 
     *
     */
	public static function onNewComment($name, $p)
   {
   		$p=$p[0];
      if ($GLOBALS['_PX_config']['comment_notification_status'] != 0) {      
      	//Defining the link to go to the comments page in Manager
      	$website_domain = $GLOBALS['_PX_website_config']['domain'];
      	$website_rel = $GLOBALS['_PX_website_config']['rel_url'];
      	$website_secure = $GLOBALS['_PX_website_config']['secure'];
      	if (!$website_secure) {
		      $website_comments_url = 'http://'.$website_domain.$website_rel.'/manager/comments.php';
      	}
      	else {
		      $website_comments_url = 'https://'.$website_domain.$website_rel.'/manager/comments.php';
      	}
      	//Defining Webmaster e-mail
      	$webmaster = getWebmaster();
      	$webmasterEmail = $webmaster->f('user_email');
      	$email_for_sending = $GLOBALS['_PX_config']['email_for_sending_notification'];
      	if ($email_for_sending == 'your@mail.com')
      	        $email_for_sending = $webmasterEmail;
      	
      	if ($GLOBALS['_PX_config']['comment_notification_status'] == 1)
      	{ //Only the valid (published) comments are notified
      	        if ($p['ct']->f('comment_status') != PX_RESOURCE_STATUS_JUNK)
      	        {   
      	                $subject = __('A comment has been left on your Plume CMS site.');
		                          
      	                $headers ='From: "Plume Alertcom"<'.$webmasterEmail.">\n";
      	                $headers .='Reply-To:'.$p['ct']->f('comment_email')."\n";
      	                $headers .='Content-Type: text/plain; charset="utf-8"'."\n";
      	                $headers .='Content-Transfer-Encoding: 8bit'; 
		    
      	                $message = __('This comment has been left on your Plume CMS website:')."\n\n";
      	                $message .= __('Author:').' '.$p['ct']->f('comment_author')."\n";
      	                $message .= __('Email:').' '.$p['ct']->f('comment_email')."\n";
      	                $message .= __('Website:').' '.$p['ct']->f('comment_website')."\n";
      	                $message .= __('IP:').' '.$p['ct']->f('comment_ip')."\n\n";
      	                $message .= __('Comment content')."\n";
      	                $message .= "====================================================\n";
      	                $message .= $p['ct']->f('comment_content')."\n";
      	                $message .= "====================================================\n\n";
      	                $message .= __('Go to the comments in the Manager:').' '.$website_comments_url;
		     
      	                mail($email_for_sending, $subject , $message, $headers);
		       }
      	}
      	else
      	{//All the comments are notified
      	        if ($p['ct']->f('comment_status') == PX_RESOURCE_STATUS_JUNK)
		         $comment_status = __('junk');
		      else
		         $comment_status = __('published');
		         
      	           $subject = __('A comment has been left on your Plume CMS site.');
		                          
      	          $headers ='From: "Plume Alertcom"<'.$webmasterEmail.">\n";
      	          $headers .='Reply-To:'.$p['ct']->f('comment_email')."\n";
      	          $headers .='Content-Type: text/plain; charset="utf-8"'."\n";
      	          $headers .='Content-Transfer-Encoding: 8bit'; 
		    
      	          $message = __('This comment has been left on your Plume CMS website:')."\n\n";
      	          $message .= __('Comment status:').' '.$comment_status."\n";
      	          $message .= __('Author:').' '.$p['ct']->f('comment_author')."\n";
      	          $message .= __('Email:').' '.$p['ct']->f('comment_email')."\n";
      	          $message .= __('Website:').' '.$p['ct']->f('comment_website')."\n";
      	          $message .= __('IP:').' '.$p['ct']->f('comment_ip')."\n\n";
      	          $message .= __('Comment content')."\n";
      	          $message .= "====================================================\n";
      	          $message .= $p['ct']->f('comment_content')."\n";
      	          $message .= "====================================================\n\n";
      	          $message .= __('Go to the comments in the Manager:').' '.$website_comments_url;
		     
      	           mail($email_for_sending, $subject , $message, $headers);
	}
    }
  } // End Method  onNewComment()
  
	/**
	 * 
	 * @param $art
	 * @param $m
	 * @return unknown_type
	 */
	public static function onArticleSave($name,$p)  {
		$p=$p[0];
        	// Send a mail to notify what's adding
        if (PX_CONFIG_MAIL_ON_CREATE == true) 
        	$m->sendEmail('Ajout d\'un article', 'article='.$art->f('resource_id'), $art->f('website_id'),PX_CONFIG_MAIL_LEVEL);
        $m->error('tentive d\'envoi d\'un mail',1);
		
	}
  
  
}

Hook::register('onNewPublicCommentAfterSave', 'Alertcom', 'onNewComment');
Hook::register('onArticleSave', 'Alertcom', 'onArticleSave');  
?>