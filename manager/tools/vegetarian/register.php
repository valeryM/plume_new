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

/**
 * Vegetarian people don't like spam.
 */
class Vegetarian
{
    /**
     * $p['ct'] contains the comment. 
     */
    public static function onNewPublicComment($name, $p)
    {
    	$p=$p[0];
        if (substr_count(strtolower($p['ct']->f('comment_content')), 'http://') > 3) {
            $p['ct']->setField('comment_status', PX_RESOURCE_STATUS_JUNK);
            return;
        }
        if (false == Vegetarian::checkFormOk($p['ct'])) {
            $p['ct']->setField('comment_status', PX_RESOURCE_STATUS_JUNK);
            return;
        }
        if (config::f('akismet_key') != '' 
            or config::f('typepad_antispam_key') != '') {
            include_once dirname(__FILE__).'/class.microakismet.php';
            if (config::f('typepad_antispam_key') != '') {
                $key = config::f('typepad_antispam_key');
                $host = $key.'.api.antispam.typepad.com';
            } else {
                $key = config::f('akismet_key');
                $host = 'rest.akismet.com';
            }
            $akismet = new MicroAkismet($key, 
                                        'http://'.config::f('domain').config::f('rel_url').'/', 
                                        'PlumeCMS/1.2');
            $akismet->akismet_host= $host;
            $vars = array();
            $vars['user_ip'] = $_SERVER["REMOTE_ADDR"];
            $vars['user_agent']	= $_SERVER["HTTP_USER_AGENT"];
            $vars['comment_author'] = $p['ct']->f('comment_author');
            $vars['comment_author_email'] = $p['ct']->f('comment_email');
            $vars['comment_author_url'] = $p['ct']->f('comment_website');
            $vars['comment_content'] = $p['ct']->f('comment_content');
            if ($akismet->check($vars)) {
                $p['ct']->setField('comment_status', PX_RESOURCE_STATUS_JUNK);
                return;
            }
        }
    }

    /**
     * $p['ct'] contains the comment. 
     *
     * Here we can directly delete comments which are definitely
     * spams.
     */
    public static function onNewPublicCommentAfter($name, $p)
    {
    	$p = $p[0];
        $total = substr_count(strtolower($p['ct']->f('comment_content')), 'http://');
        $total += substr_count(strtolower($p['ct']->f('comment_content')), '[/url]');
        $total += substr_count(strtolower($p['ct']->f('comment_content')), 'a href');
        if ($total > 10 
            && $p['ct']->f('comment_status') == PX_RESOURCE_STATUS_JUNK) {
            $p['ct']->remove();
        }
    }

    /**
     * Check that the form was really sent through the preview form
     * and not through a direct submit.
     *
     * @param Object comment.
     * @param return Bool Ok or not ok
     */
    public static function checkFormOk($ct)
    {
        $twister = form::getPostField('twister');
        if (strlen($twister) == 0) {
            return false;
        }
        // Timestamp field name is a md5 field name based on twister
        $fieldname = md5(config::f('secret_key').$twister);
        $timestamp = form::getPostField($fieldname);
        if (strlen($timestamp) == 0) {
            return false;
        }
        $ctrl_twister = md5($ct->f('comment_ip').$timestamp.config::f('secret_key').$ct->f('resource_id'));
        if ($twister != $ctrl_twister) {
            return false;
        }
        $diff = time() - (int) $timestamp;
        if ($diff < 3 or $diff > 900) {
            return false;
        }
        return true;
    }

    /**
     * Plugin Name: Block-lists anti-spam measures
     * Version: 1.5.1
     * Plugin URI: http://weblog.sinteur.com/index.php?p=8106
     * Description: check if a comment poster is on an open proxy 
     * list, and check if the content contains known spammer domains
     * Author: John Sinteur, with a big thank you to io_error!
     * Author URI: http://weblog.sinteur.com/
     */
   public static  function checkClientIP($spammer_ip)
    {
        $rev = array_reverse(explode('.', $spammer_ip));
        $lookup = implode('.', $rev).'.'.'l1.spews.dnsbl.sorbs.net.';
        if ($lookup != @gethostbyname($lookup)) {
            return false;
        }
        $lookup = implode('.', $rev).'.'.'sbl-xbl.spamhaus.org.';
        if ($lookup != @gethostbyname($lookup)) {
            return false;
        }
        $lookup = implode('.', $rev).'.'.'list.dsbl.org.';
        if ($lookup != gethostbyname($lookup)) {
            return false;
        }
        return true ;
    }
}

Hook::register('onNewPublicCommentBeforeSave', 
               'Vegetarian', 'onNewPublicComment'); 
Hook::register('onNewPublicCommentAfterSave', 
               'Vegetarian', 'onNewPublicCommentAfter'); 

?>