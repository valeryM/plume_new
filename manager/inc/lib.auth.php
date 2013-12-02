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

/**
 * Authentication levels.
 */
define('PX_AUTH_ROOT',     10);
define('PX_AUTH_ADMIN',     9);
define('PX_AUTH_ADVANCED',  5);
define('PX_AUTH_INTERMEDIATE',  4);
define('PX_AUTH_NORMAL',    1);
define('PX_AUTH_DISABLE',   0);
/**
 * Authentication class.
 * Manage the login of a user.
 *
 * Note: This class is only composed of static methods thus
 * the "lib" in the filename.
 */
class auth 
{

    /**
     * Check the authentification level and website.
     * If the authentication is not ok, the user is sent to the login page
     * If the authentication is ok, the configuration of the website is loaded,
     * a cookie to remember the website is set and the execution continues.
     * The check is only done on the $_SESSION data.
     *
     * @return void
     * @param  int Level of rights (PX_AUTH_NORMAL)
     */
    public static function checkAuth($level=PX_AUTH_NORMAL)
    {
        if (isset($_GET['wid'])) {
            $website = $_GET['wid'];
        } elseif (isset($_SESSION['website_id'])) {
            $website = $_SESSION['website_id'];
        } elseif (isset($_COOKIE['website_id'])) {
            $website = $_COOKIE['website_id'];
        } else {
            $website = 'default';
        }
        if (empty($_SESSION['webs']) || empty($_SESSION['user_id'])) {
            // No standard session anymore, get from cookie
            if (!auth::getFromCookie())
                auth::goToLoginPage();
        }
        if (!isset($_SESSION['webs'][$website]) or ($_SESSION['webs'][$website] < $level && $_SESSION['user_id'] != 1))
            auth::goToLoginPage();
    
        //Here the session is considered as valid.
        if (!config::loadWebsite($website)) {
            // problem loading the website configuration
            auth::goToLoginPage();
        }
        config::setContext('manager');
        setcookie('website_id', $website, time()+31536000);
    }

    /**
     * Get the session from the cookie.
     *
     * @return bool success
     */
    public static function getFromCookie()
    {
        if (!isset($_COOKIE['px_session'])) {
            return false;
        }
        $check = substr($_COOKIE['px_session'], -32);
        $session_data = substr($_COOKIE['px_session'], 0, strlen($_COOKIE['px_session'])-32);
        if (md5($session_data.config::f('secret_key')) != $check) {
            return false;
        }
        $session_data = base64_decode($session_data);
        list($id, $login, $website, $name) = explode('|', $session_data, 4);
        //logon the user
        if (!auth::login($login, 'dummy', $website, false)) {
            auth::logout();
            return false;
        }
        return true;
    }

    /**
     * Logout a user
     */
    public static function logout()
    {
        setcookie('px_session', '', time()-3600, '/');
        $_SESSION = array();
        $_SESSION['user_id'] = '';
        $_SESSION['webs'] = array();
    }

    /**
     * Check if a user as a given level for a website access.
     * If no website given, get from the session, if not in the session, use
     * 'default' as it is the id of the first website.
     *
     * @param int Right level (PX_AUTH_NORMAL)
     * @param string Website id (false)
     * @param object User ('') if none given, the current session user is tested
     * @return bool Success
     */
    public static function asLevel($level=PX_AUTH_NORMAL, $website=false, $user='')
    {
        if (false === $website) {
            $website = (!empty($_SESSION['website_id'])) ? $_SESSION['website_id'] : 'default';
        }
        if (empty($user)) {
            if ($level == PX_AUTH_ROOT)  {
                return ($_SESSION['user_id'] == 1);
            }
            if (!isset( $_SESSION['webs'][$website])) return false;
            return  ($_SESSION['webs'][$website] >= $level);

        } else {
            // direct check of a user
            if ($user->f('user_id') > 0) {
                $user->loadWebsites();
            }
            if ($level == PX_AUTH_ROOT)  {
                return ($user->f('user_id') == 1);
            }
            if (!isset( $user->webs[$website])) return false;
            return  ($user->webs[$website] >= $level);

        }
    }

    /**
     * Send a "Location:" header to redirect the user
     * to the login page. Abort the script execution.
     */
    public static function goToLoginPage()
    {
        header('Location: '.www::getCurrentFullUrl().'login.php');
        exit;
    }

    /** 
     * Log a user to the system. 
     * If the user has the rights to access the system
     * user data are saved in the session. Some cookies are also set like the
     * language, the website. To be used if the session timeout, so the user is
     * sent back with the right language in the last website.
     *
     * The login scheme is:
     *
     * - check if login/password ok, if not return false
     * - get the authorized websites if no websites return false
     * - get the default website for the user, if not set to the first of the
     * authorized websites and return true
     * - check if the default website is in the list of authorized, if not, 
     * remove it from the user prefs and set the default as the first 
     * authorized website and return true.
     *
     * @see auth::asLevel() to check if the user as the right level afterwards
     *
     * @return bool success
     * @param string Username
     * @param string Password
     * @param string Website id ('')
     * @param bool Password check (true)
     */
    public static function login($user, $pswd, $website='', $checkpass=true)
    {
        if (0 == strlen($user) || 0 == strlen($pswd)) return false;
        if (preg_match('/[^A-Za-z0-9]/', $user)) return false;
        $ok = false;
	
        if ($checkpass == false || User::checkUser($user, $pswd)) {
            $u = new User($user); //load user
            if (!is_array($u->webs)) {
                // no authorized web
                return false;
            }
            if (strlen($website) > 0 && !isset($u->webs[$website])) {
                // the one provided is not good, it can be a fake cookie
                $website = '';
            }
            if (strlen($website) == 0) {
                $website = $u->getPref('default_website', '#all#'); 
                //default website for the user
                if (strlen($website) > 0 && !isset($u->webs[$website])) {
                    // current default is not authorized! remove from prefs
                    $u->removePref('default_website','#all#');
                    $website = '';
                }
            }
            if (strlen($website) == 0) {
                // get a website for the user
                $_tmp = array_keys($u->webs);
                if (count($_tmp) == 0) {
                    return false;
                }
                $website = array_pop($_tmp);
            }
            $u->setWebsite($website);
            //Only after the call of the synchronize function the
            //user is effectively logged in the manager.
            $u->synchronize(PX_USER_SYNCHRO_TO_SESSION);
            $ok = true;
            $session_data = $u->f('user_id').'|'.$u->f('user_username')
                .'|'.$website.'|'.$u->f('user_realname');
            $check = md5(base64_encode($session_data).config::f('secret_key'));
            $session_data = base64_encode($session_data);
            setcookie('px_session', $session_data.$check, time()+1296000, '/');
        }
        return $ok;
    }
}
?>
