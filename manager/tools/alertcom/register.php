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
class Alertcom {
	
	public static function getHeader($from, $replyTo) {
		$headers ='From: "Plume Alertcom"<'.$from.">\n";
		$headers .='Reply-To:'.$replyTo."\n";
		$headers .='Content-Type: text/plain; charset="utf-8"'."\n";
		$headers .='Content-Transfer-Encoding: 8bit';
		return $headers;
	}
	
	
	public static function getCommentMessage($p, $status, $website_url) {
		$message = __('This comment has been left on your Plume CMS website:')."\n\n";
		if ($status!=null) $message .= __('Comment status:').' '.$status."\n";
		$message .= __('Author:').' '.$p['ct']->f('comment_author')."\n";
		$message .= __('Email:').' '.$p['ct']->f('comment_email')."\n";
		$message .= __('Website:').' '.$p['ct']->f('comment_website')."\n";
		$message .= __('IP:').' '.$p['ct']->f('comment_ip')."\n\n";
		$message .= __('Comment content')."\n";
		$message .= "====================================================\n";
		$message .= $p['ct']->f('comment_content')."\n";
		$message .= "====================================================\n\n";
		$message .= __('Go to the comments in the Manager:').' '.$website_url;
		return $message;
	}
	
	public static function getWebmasters() {
		
		//Defining Webmaster e-mail
		$webmaster = getWebmaster();
		$webmasterEmail = $webmaster->f('user_email');
		$email_for_sending = $GLOBALS['_PX_config']['email_for_sending_notification'];
		if ($email_for_sending == 'your@mail.com')
			$email_for_sending = $webmasterEmail;
		
		return $email_for_sending;
	}
    /**
     * $p['ct'] contains the comment. 
     *
     */
	public static function onNewComment($name, $p) {
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
// 	      	$email_for_sending = $GLOBALS['_PX_config']['email_for_sending_notification'];
// 	      	if ($email_for_sending == 'your@mail.com')
// 	      	        $email_for_sending = $webmasterEmail;
	      	$email_for_sending = self::getWebmasters();
	      	
	      	if ($GLOBALS['_PX_config']['comment_notification_status'] == 1)
	      	{ //Only the valid (published) comments are notified
				if ($p['ct']->f('comment_status') != PX_RESOURCE_STATUS_JUNK)  {   
					$subject = __('A comment has been left on your Plume CMS site.');

					$headers = self::getHeader($webmasterEmail, $p['ct']->f('comment_email'));
// 					$headers ='From: "Plume Alertcom"<'.$webmasterEmail.">\n";
// 					$headers .='Reply-To:'.$p['ct']->f('comment_email')."\n";
// 					$headers .='Content-Type: text/plain; charset="utf-8"'."\n";
// 					$headers .='Content-Transfer-Encoding: 8bit'; 
			    
					$message = self::getCommentMessage($p, null, $website_comments_url);
// 					$message = __('This comment has been left on your Plume CMS website:')."\n\n";
// 					$message .= __('Author:').' '.$p['ct']->f('comment_author')."\n";
// 					$message .= __('Email:').' '.$p['ct']->f('comment_email')."\n";
// 					$message .= __('Website:').' '.$p['ct']->f('comment_website')."\n";
// 					$message .= __('IP:').' '.$p['ct']->f('comment_ip')."\n\n";
// 					$message .= __('Comment content')."\n";
// 					$message .= "====================================================\n";
// 					$message .= $p['ct']->f('comment_content')."\n";
// 					$message .= "====================================================\n\n";
// 					$message .= __('Go to the comments in the Manager:').' '.$website_comments_url;

					
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

				$headers = self::getHeader($webmasterEmail, $p['ct']->f('comment_email'));
// 				$headers ='From: "Plume Alertcom"<'.$webmasterEmail.">\n";
// 				$headers .='Reply-To:'.$p['ct']->f('comment_email')."\n";
// 				$headers .='Content-Type: text/plain; charset="utf-8"'."\n";
// 				$headers .='Content-Transfer-Encoding: 8bit'; 
			    
				$message = self::getCommentMessage($p, $comment_status, $website_comments_url);
// 				$message = __('This comment has been left on your Plume CMS website:')."\n\n";
// 				$message .= __('Comment status:').' '.$comment_status."\n";
// 				$message .= __('Author:').' '.$p['ct']->f('comment_author')."\n";
// 				$message .= __('Email:').' '.$p['ct']->f('comment_email')."\n";
// 				$message .= __('Website:').' '.$p['ct']->f('comment_website')."\n";
// 				$message .= __('IP:').' '.$p['ct']->f('comment_ip')."\n\n";
// 				$message .= __('Comment content')."\n";
// 				$message .= "====================================================\n";
// 				$message .= $p['ct']->f('comment_content')."\n";
// 				$message .= "====================================================\n\n";
// 				$message .= __('Go to the comments in the Manager:').' '.$website_comments_url;
			     
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
	public static function onCategorySave($name, $p) {
		$cat = $p[0]['cat'];
		$m = $p[0]['m'];
		if (PX_CONFIG_MAIL_ON_CREATE == true) {
			$subject = __('A category has been added to your Plume CMS site.');
         	$webmaster = getWebmaster();
         	$webmasterEmail = $webmaster->f('user_email');
			
         	$email_for_sending = self::getWebmasters();			
			$headers = self::getHeader($webmasterEmail, $webmasterEmail);
			
		}
	}
	
	
	/**
	 * 
	 * @param $art
	 * @param $m
	 * @return unknown_type
	 */
	public static function onArticleSave($name, $p)  {
		$art = $p[0]['art'];
		$m = $p[0]['m'];
        // Send a mail to notify what's adding
        if (PX_CONFIG_MAIL_ON_CREATE == true && $art->isModified==true
        		&& $art->f('status')>=PX_RESOURCE_STATUS_INEDITION)  {
        	$subject = __('A article has been added to your Plume CMS site.');
         	$webmaster = getWebmaster();
         	$webmasterEmail = $webmaster->f('user_email');
        	
        	$email_for_sending = self::getWebmasters();
        	$headers = self::getHeader($webmasterEmail, $webmasterEmail);
        	
        	$author = $art->authors->f('user_realname').' ('.$art->authors->f('user_username').')';
        	
        	$message = __("This article has been added")."\n\n";
        	$message .= __('Author:').' '.$author."\n";
        	$message .= ('Subject:').' '.$art->f('subject')."\n";
        	$message .=('Category:').' '.$art->cats->f('category_name').' ('.$art->cats->f('category_path').')'."\n";
        	$message .= ('Path:').' '.$art->f('path')."\n";
        	$message .= ('Title:').' '.$art->f('title')."\n";        	 
        	$message .= __('Website:').' '.$art->f('website_id');
        	
        	if (!mail($email_for_sending, $subject , $message, $headers)) 
        		echo $m->error('tentive d\'envoi d\'un mail',1);
        }
	}
  
  
	public static function onEventSave($name, $p)  {
		$event = $p[0]['event'];
		$m = $p[0]['m'];
		// Send a mail to notify what's adding
		if (PX_CONFIG_MAIL_ON_CREATE == true && $event->isModified==true
				&& $event->f('status')>=PX_RESOURCE_STATUS_INEDITION) {
			$subject = __('An event has been added to your Plume CMS site.');
 			$webmaster = getWebmaster();
 			$webmasterEmail = $webmaster->f('user_email');
			$email_for_sending = self::getWebmasters();
			$headers = self::getHeader($webmasterEmail, $webmasterEmail);
			
			$author = $event->authors->f('user_realname').' ('.$event->authors->f('user_username').')';
				
			$message = __("This event has been added")."\n\n";
			$message .= __('Author:').' '.$author."\n";
			$message .= ('Subject:').' '.$event->f('subject')."\n";
			$message .=('Category:').' '.$event->cats->f('category_name').' ('.$event->cats->f('category_path').')'."\n";
			$message .= ('Path:').' '.$event->f('path')."\n";
			$message .= ('Title:').' '.$event->f('title')."\n";			
			$message .= __('Website:').' '.$event->f('website_id');
			 
			if (!mail($email_for_sending, $subject , $message, $headers))
				echo $m->error('tentive d\'envoi d\'un mail',1);
		}
	}
	
	public static function onNewsSave($name, $p)  {
		$news = $p[0]['news'];
		$m = $p[0]['m'];
		// Send a mail to notify what's adding
		if (PX_CONFIG_MAIL_ON_CREATE == true && $news->isModified==true
				&& $news->f('status')>=PX_RESOURCE_STATUS_INEDITION){
			$subject = __('A news has been added to your Plume CMS site.');
 			$webmaster = getWebmaster();
 			$webmasterEmail = $webmaster->f('user_email');
			$email_for_sending = self::getWebmasters();
			$headers = self::getHeader($webmasterEmail, $webmasterEmail);
			 
			$author = $news->authors->f('user_realname').' ('.$news->authors->f('user_username').')';
			
			$message = __("This news has been added")."\n\n";
			$message .= __('Author:').' '.$author."\n";
			$message .= ('Subject:').' '.$news->f('subject')."\n";
			$message .=('Category:').' '.$news->cats->f('category_name').' ('.$news->cats->f('category_path').')'."\n";
			$message .= ('Path:').' '.$news->f('path')."\n";
			$message .= ('Title:').' '.$news->f('title')."\n";
			$message .= __('Website:').' '.$news->f('website_id');
			 
			if (!mail($email_for_sending, $subject , $message, $headers))
				echo $m->error('tentive d\'envoi d\'un mail',1);
		}
	}	
}

Hook::register('onNewPublicCommentAfterSave', 'Alertcom', 'onNewComment');
Hook::register('onArticleAfterSave', 'Alertcom', 'onArticleSave');
Hook::register('onEventAfterSave', 'Alertcom', 'onEventSave'); 
Hook::register('onNewsAfterSave', 'Alertcom', 'onNewsSave');
Hook::register('onCategoryAfterSave', 'Alertcom', 'onCategorySave');

?>