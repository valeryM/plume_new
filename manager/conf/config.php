<?php
date_default_timezone_set('UTC');
// Configuration file. Make the change to match your config

// Database informations
$_PX_config['db']['db_server'] = 'localhost'; // Server

$_PX_config['db']['db_database'] = ''; // Name of the database
$_PX_config['db']['db_login'] = ''; // User/login to the database
$_PX_config['db']['db_password'] = ''; // Password

$_PX_config['db']['db_type']      = 'mysql'; // Type of database engine
$_PX_config['db']['table_prefix'] = 'plume_'; // Prefix on the tables to access them
                                              // (if you want 2 installations of PLUME CMS in the same database)

/* Version of the database engine */
$_PX_config['db_version'] = '5.0.88-log';

// Some other informations
$_PX_config['lang'] = 'fr'; // Default language (fr or en available with default install)
$_PX_config['encoding'] = 'utf-8'; // If one of your sites may use other languages than the
                                        // Western ones, please chose 'UTF-8'. Do not change this
                                        // value without knowing the impact! All your output pages
                                        // will have this encoding.
$_PX_config['content_format'] = 'html'; //default format of the content, including descriptions

// you don't need to look starting here
$_PX_config['debug'] = true; // Display some debug informations (execution time, number of queries)
$_PX_config['log'] = true; // Display some debug informations (execution time, number of queries)

$_PX_config['session_inactive'] = 3600; // Inactivity time of a session before not valid
$_PX_config['url_format'] = 'mod_rewrite'; // use 'mod_rewrite' if you want nice looking URL, check plume-cms.net
                                      // for details about the configuration of mod_rewrite
$_PX_config['max_upload_size'] = 512000; //102400; // maximum size of uploaded file (here 100KB)                     

$_PX_config['level404Ignored'] = 10; // level used by dispatcher to ignore 404 error 
									 // (to be able to check the query before a real 404 error )

/* Create a log of the 404 errors */
$_PX_config['log404errors'] = false;

/* Default status of a comment, 1: online 5:waiting for validation. */
$_PX_config['comment_default_status'] = 5;
$_PX_config['comment_default_value'] = 3;

/* Default size of the textarea when adding a page */
$_PX_config['article_textarea_page'] = '35';

/* Default size of the description textarea of an article */
$_PX_config['article_textarea_description'] = '20';

/* Default size of the content textarea of a news */
$_PX_config['news_textarea_content'] = '30';
$_PX_config['news_textarea_shortcontent'] = '30';
/* Default size of the content textarea of a events */
$_PX_config['events_textarea_content'] = '25';
$_PX_config['events_textarea_shortcontent'] = '25';

/* Default size of the description of a category */
$_PX_config['category_textarea'] = '7';

/* Secret key for your Plume installation, do not give it to anybody! */
$_PX_config['secret_key'] = '';

$_PX_config['akismet_key'] = '';

$_PX_config['typepad_antispam_key'] = '';

/* Alertcom (comments notification) configuration - 0: disabled ; 1: only published comments notified ; 2: all comments notified */
$_PX_config['comment_notification_status'] = 0;

/* Email to send the notification to (could be multiple coma seperated) */
$_PX_config['email_for_sending_notification'] = 'webmster@monsite.fr';
/*
define('PX_AUTH_ROOT',     10);
define('PX_AUTH_ADMIN',     9);
define('PX_AUTH_ADVANCED',  5);
*/
define('PX_CONFIG_MAIL_ON_CREATE',false);
define('PX_CONFIG_MAIL_ON_MODIFY',false);
define('PX_CONFIG_MAIL_LEVEL',9); // PX_AUTH_ROOT,PX_AUTH_ADMIN, PX_AUTH_ADVANCED

/* Mode d'affichage des contenus par défaut (articles / events, news)
 * list : Liste du contenu
 * new : nouveau contenu
 */
$_PX_config['mode_affichage_contenu']='list';

// define the min size for the search word
$_PX_config['search_min_size'] = 5;

include_once('constants.php');

?>
