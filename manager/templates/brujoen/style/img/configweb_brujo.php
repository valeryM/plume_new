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

$_PX_website_config['website_id'] = 'wwwleostudercomen';

// $_PX_website_config['document_root'] = ;
$_PX_website_config['xmedia_root'] = 'd:/program files/easyphp1-8/www/plume/en/xmedia';

/* for example '127.0.0.1' if you have 'http://127.0.0.1/plume' */
$_PX_website_config['domain'] = '127.0.0.1';

/* for example '/plume' if you have 'http://127.0.0.1/plume' */
$_PX_website_config['rel_url'] = '/plume/en';

/* for example '/plume/xmedia' if you have 'http://127.0.0.1/plume/xmedia' */
$_PX_website_config['rel_url_files'] = '/plume/en/xmedia';

/* Is the website on a secure server or not */
$_PX_website_config['secure'] = false;

/* Language of the website, 'en', 'fr', 'fr_FR', etc.. */
$_PX_website_config['lang'] = 'en';

/* Theme used for the public rendering of the website. */
$_PX_website_config['theme_id'] = 'default_en';

/* Default status of a comment, 1: online 5:waiting for validation. */
$_PX_website_config['comment_default_status'] = 1;

/* Support of the comments: 1 - open, 2 - select per individual resource, 3 - closed. */
$_PX_website_config['comment_support'] = 1;

?>
