<?php
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
require_once dirname(__FILE__).'/prepend.php';

$_px_p = 100; //percentage of wizard done.

if (empty($_SESSION['step1'])) {
	header('Location: index.php');
	exit;
}
include_once $_PX_config['manager_path'].'/conf/config.php';
$l = new l10n($_PX_config['lang'], 'install');


$checklist = new checklist();


//-- Update of the main configuration file --//
$file = dirname(__FILE__).'/../conf/config.php';
$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('encoding', (string) 'utf-8', 'Encoding of the pages in all PLUME CMS.')) {
	$g_mess = __('The encoding configuration variable has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The encoding configuration variable is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the encoding configuration variable in the configuration file.');
	$test = false;
}
$checklist->addTest('encoding', $test, $g_mess, $b_mess);

$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('log404errors', (bool) false, 'Create a log of the 404 errors')) {
	$g_mess = __('The 404 error logging configuration variable has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The 404 error logging configuration variable is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the 404 error logging configuration variable in the configuration file.');
	$test = false;
}
$checklist->addTest('log404errors', $test, $g_mess, $b_mess);

$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('article_textarea_page', (string) '15', 'Default size of the textarea when adding a page')) {
	$g_mess = __('The default size of the page textarea has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The default size of the page textarea is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the default size of the page textarea in the configuration file.');
	$test = false;
}
$checklist->addTest('article_textarea_page', $test, $g_mess, $b_mess);

$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('article_textarea_description', (string) '7', 'Default size of the description textarea of an article')) {
	$g_mess = __('The default size of the article description textarea has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The default size of the article description textarea is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the default size of the article description textarea in the configuration file.');
	$test = false;
}
$checklist->addTest('article_textarea_description', $test, $g_mess, $b_mess);

$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('news_textarea_content', (string) '12', 'Default size of the content textarea of a news')) {
	$g_mess = __('The default size of the news content textarea has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The default size of the news content textarea is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the default size of the news content textarea in the configuration file.');
	$test = false;
}
$checklist->addTest('news_textarea_content', $test, $g_mess, $b_mess);

$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('category_textarea', (string) '7', 'Default size of the description of a category')) {
	$g_mess = __('The default size of the category description textarea has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The default size of the category description textarea is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the default size of the category description textarea in the configuration file.');
	$test = false;
}
$checklist->addTest('category_textarea', $test, $g_mess, $b_mess);

$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('comment_default_status', (int) 1, 'Default status of a comment, 1: online, 5: waiting for validation')) {
	$g_mess = __('The default status of a comment has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The default status of a comment is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the default status of a comment in the configuration file.');
	$test = false;
}
$checklist->addTest('comment_default_status', $test, $g_mess, $b_mess);

$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('secret_key', (string) Misc::getRandomString(), 'Secret key for your Plume installation, do not give it to anybody!')) {
	$g_mess = __('The secret key has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The secret key is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the secret key in the configuration file.');
	$test = false;
}
$checklist->addTest('secret_key', $test, $g_mess, $b_mess);

//Alertcom update
$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('comment_notification_status', (int) 1, 'Alertcom (comments notification) configuration - 0: disabled ; 1: only published comments notified ; 2: all comments notified')) {
	$g_mess = __('The Alertcom status has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The Alertcom status is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the status of Alertcom in the configuration file.');
	$test = false;
}
$checklist->addTest('comment_notification_status', $test, $g_mess, $b_mess);

$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('email_for_sending_notification', (string) 'your@email.com', 'Email to send the notification to (could be multiple coma seperated)')) {
	$g_mess = __('The Email to send the notification to has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The Email to send the notification to is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the Email to send the notification to in the configuration file.');
	$test = false;
}
$checklist->addTest('secret_key', $test, $g_mess, $b_mess);

//-- Copy the 404.php template if needed for each website --//
/** Kept for users having a really old version.
$u = new User(1); //rootuser
$m = new Manager();
$m->setUser($u);
$test = true;
if (($webs = $m->getSites()) !== false) {
	$src = dirname(__FILE__).'/../templates/_dist/default/templates/404.php';
	while (!$webs->EOF()) {
		$dest = dirname(__FILE__).'/../templates/'.$webs->f('website_id').'/404.php';
		$copy = files::copyfile($src, $dest);
		if (files::is_success($copy)) {
			$w_mess = '';
			$g_mess = sprintf(__('Copy of the template <em>%s</em> to <em>%s</em>'), files::real_path($src), files::real_path($dest));
			$test = true;
		} else {
			$g_mess = '';
			$w_mess = sprintf(__('Error in the copy of the template <em>%s</em> to <em>%s</em>'), files::real_path($src), files::real_path($dest));
			$test = 2;
		}
		$checklist->addTest('404template'.$webs->f('website_id'), $test, $g_mess, '', $w_mess);
		$webs->moveNext();
	}
} else {
	$test = 2;
	$w_mess = __('Unable to get the list of websites to copy the new 404.php template. Please do it manually.');
	$checklist->addTest('404template', $test, '', '', $w_mess);
}
*/

//-- Set the theme_id for each config file --//
$u = new User(1); //rootuser
$m = new Manager();
$m->setUser($u);
$test = true;
if (($webs = $m->getSites()) !== false) {
	while (!$webs->EOF()) {
        $file = dirname(__FILE__).'/../conf/configweb_'.$webs->f('website_id').'.php';
        $cfg = new configfile($file);
        $cfg->prefix = '_PX_website_config';
        $test = true;
        $b_mess = $g_mess = '';
        if ($cfg->addVar('theme_id', (string) $webs->f('website_id'), 'Theme used for the public rendering of the website.')) {
            $g_mess = sprintf(__('The theme id has been set for the website %s.'), $webs->f('website_name'));
            $update = true;
        } else {
            $g_mess = sprintf(__('The theme id was already set for the website %s.'), $webs->f('website_name'));
            $update = false;
        }
        if ($update && !$cfg->saveFile()) {
            $b_mess = sprintf(__('Impossible to set the theme id for the website %s.'), $webs->f('website_name'));
            $test = false;
        }
        $checklist->addTest('theme_id-'.$webs->f('website_id'), $test, $g_mess, $b_mess);
		$webs->moveNext();
    }
}


//-- Set the validation or not of the comments for each config file --//
$u = new User(1); //rootuser
$m = new Manager();
$m->setUser($u);
$test = true;
if (($webs = $m->getSites()) !== false) {
	while (!$webs->EOF()) {
        $file = dirname(__FILE__).'/../conf/configweb_'.$webs->f('website_id').'.php';
        $cfg = new configfile($file);
        $cfg->prefix = '_PX_website_config';
        $test = true;
        $b_mess = $g_mess = '';
        if ($cfg->addVar('comment_default_status', (int) 1, 'Default status of a comment, 1: online 5:waiting for validation.')) {
            $g_mess = sprintf(__('The default comment status has been set for the website %s.'), $webs->f('website_name'));
            $update = true;
        } else {
            $g_mess = sprintf(__('The default comment status was already set for the website %s.'), $webs->f('website_name'));
            $update = false;
        }
        if ($update && !$cfg->saveFile()) {
            $b_mess = sprintf(__('Impossible to set the default comment status for the website %s.'), $webs->f('website_name'));
            $test = false;
        }
        $checklist->addTest('comment_default_status-'.$webs->f('website_id'), $test, $g_mess, $b_mess);
		$webs->moveNext();
    }
}

//-- Set the openness or not of the comments --//
$u = new User(1); //rootuser
$m = new Manager();
$m->setUser($u);
$test = true;
if (($webs = $m->getSites()) !== false) {
	while (!$webs->EOF()) {
        $file = dirname(__FILE__).'/../conf/configweb_'.$webs->f('website_id').'.php';
        $cfg = new configfile($file);
        $cfg->prefix = '_PX_website_config';
        $test = true;
        $b_mess = $g_mess = '';
        if ($cfg->addVar('comment_support', (int) 1, 'Support of the comments: 1 - open, 2 - select per individual resource, 3 - closed.')) {
            $g_mess = sprintf(__('The support of comments has been set for the website %s.'), $webs->f('website_name'));
            $update = true;
        } else {
            $g_mess = sprintf(__('The support of comments was already set for the website %s.'), $webs->f('website_name'));
            $update = false;
        }
        if ($update && !$cfg->saveFile()) {
            $b_mess = sprintf(__('Impossible to set the support of comments for the website %s.'), $webs->f('website_name'));
            $test = false;
        }
        $checklist->addTest('comment_support-'.$webs->f('website_id'), $test, $g_mess, $b_mess);
		$webs->moveNext();
    }
}


//-- Upgrade of the database --//
$con = pxDBConnect();
$xml = implode("\n", file('./db-upgrade.xml'));
$sql = new xmlsql($con, $xml);

//-- MySQL version --//
$rsV = $con->select('SELECT VERSION() AS version');
$mysql_version = preg_replace('/-log$/','',$rsV->f(0));
$extra = '';
if (version_compare($mysql_version, '3.23', '>=')) {
    $extra = ' TYPE=MyISAM';
}
if (version_compare($mysql_version, '4.1', '>=')) {
	$extra = ' ENGINE=MyISAM';
    $charset = 'DEFAULT CHARSET=utf8';
}

$sql->replace('{{TYPE}}', $extra);
$sql->replace('{{PREFIX}}',$_PX_config['db']['table_prefix']);
$sql->replace('{{CHARSET}}',$charset);
$sql->execute($checklist);

$file = dirname(__FILE__).'/../conf/config.php';

//-- Switch from latin1 to utf-8 if needed --//
if (strtolower($_PX_config['encoding']) == 'iso-8859-1') {
    include dirname(__FILE__).'/dbconvert.php';
    $cfg = new configfile($file);
    $cfg->prefix = '_PX_config';
    $test = true;
    $b_mess = $g_mess = '';
    $cfg->editVar('encoding', (string) 'utf-8');
    $g_mess = __('The encoding has been set to utf-8 in the configuration file.');
    if (!$cfg->saveFile()) {
        $b_mess = __('Impossible to set encoding to utf-8 in the configuration file.');
        $test = false;
    }
    $checklist->addTest('secret_key', $test, $g_mess, $b_mess);
}

//-- Store the MySQL version -- //
$cfg = new configfile($file);
$cfg->prefix = '_PX_config';
$test = true;
$b_mess = $g_mess = '';
if ($cfg->addVar('db_version', '4.0', 'Version of the database engine. For compatibility when upgrading from a 1.0.x version of Plume, the version is set to 4.0. Go on the website to see how to take full benefit of the unicode support in MySQL 4.1+')) {
	$g_mess = __('The version of the database engine has been added to the configuration file.');
	$update = true;
} else {
	$g_mess = __('The version of the database engine is already set in the configuration file.');
	$update = false;
}
if ($update && !$cfg->saveFile()) {
	$b_mess = __('Impossible to set the version of the database engine in the configuration file.');
	$test = false;
}
$checklist->addTest('db_version', $test, $g_mess, $b_mess);


$check = $checklist->checkAll();
$m->user->logout();

include dirname(__FILE__).'/_top.php';

echo '<h2>'.__('Upgrade of the installation').'</h2>'."\n\n";

if ($check) {
	echo '<h3>'.__('Congratulations, PLUME CMS has been upgraded!').'</h3>'."\n\n";
} else {
    echo "\n\n" .'<p class="important">'. __('Some errors occured during the upgrade procedure. Please check carefully the messages to see if you need to finish the upgrade manually.') .'</p>'. "\n\n";
}

echo $checklist->getHtml('../themes/default/images');

echo '<p>'.sprintf(__('You can now access the manager. <a href="%s">Access the manager</a>.'), '../login.php?logout=1').'</p>'."\n";

include dirname(__FILE__).'/_bottom.php';
?>
