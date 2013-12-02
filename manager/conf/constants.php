<?php

/**
 * Définition du paramétrage du site coté Font-end
 * Utilisation dans les classes menuflottant et basicmanagersite
 */
if (!defined('PX_CONFIG_WEBSITE')) define('PX_CONFIG_WEBSITE', 'default');

if (!defined('PX_CONFIG_TIMEBEFORE')) define('PX_CONFIG_TIMEBEFORE', '0 days');
if (!defined('PX_CONFIG_TIMEAFTER')) define('PX_CONFIG_TIMEAFTER', '+6 weeks');
if (!defined('PX_CONFIG_MAX_EVENTS')) define('PX_CONFIG_MAX_EVENTS',5);
if (!defined('PX_CONFIG_MAX_EVENTS_CULTUREL')) define('PX_CONFIG_MAX_EVENTS_CULTUREL',1);

if (!defined('PX_CONFIG_MAIL_ON_CREATE')) define('PX_CONFIG_MAIL_ON_CREATE',true);
if (!defined('PX_CONFIG_MAIL_ON_MODIFY')) define('PX_CONFIG_MAIL_ON_MODIFY',true);
if (!defined('PX_CONFIG_MAIL_LEVEL')) define('PX_CONFIG_MAIL_LEVEL',PX_AUTH_ADMIN); // PX_AUTH_ROOT,PX_AUTH_ADMIN, PX_AUTH_ADVANCED

if (!defined('PX_CONFIG_TEASER_PATH')) define('PX_CONFIG_TEASER_PATH','/xmedia/teaserAccueil');
if (!defined('PX_CONFIG_RATING')) define('PX_CONFIG_RATING',false);


?>
