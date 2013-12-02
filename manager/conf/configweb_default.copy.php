<?php
/**
 * @file configweb_default.php
 * Configuration file of a website. Each website has its own. 
 * The name of the file is:
 *         configweb_[id of the website].php
 * At the setup of PLUME CMS the "default" website is created, 
 * so the configweb_default.php file is also created.
 *
 * - The values proposed are calculated dynamically for the case of the default
 * installation with only one website and the 'manager' in a 'manager' 
 * subfolder of the root URL of the website. You can put some hardcoded values.
 */ 

$_PX_website_config['website_id'] = 'default';

// $_PX_website_config['document_root'] = ;
$_PX_website_config['xmedia_root'] = dirname(__FILE__).'/../../xmedia';

/* for example '127.0.0.1' if you have 'http://127.0.0.1/plume' */
$_PX_website_config['domain'] = $_SERVER['HTTP_HOST'];

/* for example '/plume' if you have 'http://127.0.0.1/plume' */
$_PX_website_config['rel_url'] = www::getRelativeUrl();

/* for example '/plume/xmedia' if you have 'http://127.0.0.1/plume/xmedia' */
$_PX_website_config['rel_url_files'] = $_PX_website_config['rel_url'].'/xmedia';

/* Is the website on a secure server or not */
$_PX_website_config['secure'] = false;

/* Language of the website, 'en', 'fr', 'fr_FR', etc.. */
$_PX_website_config['lang'] = 'en_US';

/* Theme used for the public rendering of the website. */
$_PX_website_config['theme_id'] = 'default';

/* Default status of a comment, 1: online 5:waiting for validation. */
$_PX_website_config['comment_default_status'] = 5;

$_PX_website_config['comment_default_value'] = 3;

/* Support of the comments: 1 - open, 2 - select per individual resource, 3 - closed. */
$_PX_website_config['comment_support'] = 2;
/* Alertcom (comments notification) configuration - 0: disabled ; 1: only published comments notified ; 2: all comments notified */
$_PX_config['comment_notification_status'] = 1;

/* Email to send the notification to (could be multiple coma seperated) */
$_PX_config['email_for_sending_notification'] = '';
$_PX_config['order_cat_manual']=true;
