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

include_once dirname(__FILE__).'/class.htaccess.php';

/**
 * Load the language object for the plugin: 
 *   $m->user->lang : current language of the user.
 *   'htaccess' : the current plugin.
 */ 
$m->l10n->loadPlugin($m->user->lang, 'htaccess');

/* load the config file of the current website. Note that the error message uses
the language file from the manager, as this error can be found somewhere else */
/*
if (file_exists($_PX_config['manager_path'].'/conf/configweb_'.$_SESSION['website_id'].'.php')) {
    include($_PX_config['manager_path'].'/conf/configweb_'.$_SESSION['website_id'].'.php');
} else {
    $m->setError(sprintf( __('Error: Configuration file of the website(<strong>%s</strong>) not available.'),$_PX_config['manager_path'].'/conf/configweb_'.$_SESSION['website_id'].'.php'), 500);
}
*/

/* =================================================================
 *                   Process block
 * =================================================================
 */
$is_writable = false;
$is_dir      = true;
$rep_list    = array();
$is_secured  = false;
$px_users    = array();
$px_zonename = '';
$err         = '';
$px_password = '';
$px_user     = '';

/* find the folder to secure */
if (false === $m->error()):
$up_dir = config::f('xmedia_root'); 
if (!empty($_REQUEST['dir'])) {
	$current_dir = str_replace('\\','',$_REQUEST['dir']);
    $current_dir = str_replace('..','',$current_dir);
    $current_dir = preg_replace( '#(/)+#', '/', $current_dir); 
    $current_dir = preg_replace( '#^(/)+#', '', $current_dir); 
	$current_dir = preg_replace( '#(/)+$#', '', $current_dir);
	if (!empty($current_dir)) 
		$current_dir .= '/'; 
} else {
	$current_dir = '';
}
/* check rights on the folder */
if(is_dir($up_dir.'/'.$current_dir))
{
	if (is_writable($up_dir.'/'.$current_dir)) {
		$is_writable = true;
	} else {
        $m->setError(sprintf( __('Error: The system has no write access to the folder <strong>%s</strong>. Check the permissions.'),$_PX_website_config['rel_url_files'].'/'.$current_dir), 500);
		
	}
}
else {
    $m->setError(sprintf( __('Error: The folder <strong>%s</strong> does not exist.'),$_PX_website_config['rel_url_files'].'/'.$current_dir), 500);
	$is_dir = false;
}

if ($is_dir) {
    $D = dir($up_dir.'/'.$current_dir);
    while(false !== ($entry = $D->read())) {
    	if (is_dir($up_dir.'/'.$current_dir.$entry) && ($entry != 'thumb') && $entry != '.' && $entry != '..') {
        	$rep_list[] = $entry;
    	}
    }
	$D->close();
}

if ($current_dir != '') {
	$ht = new htaccess();
	$is_secured = $ht->isSecured($up_dir.'/'.$current_dir);
	if ($is_secured) {
		$px_users = $ht->getUsers();
		$px_zonename = $ht->getZoneName();
	}		
}

/* create a new zone */
if ($is_dir && $is_writable && $current_dir != '' && !empty($_POST['createzone'])) {
	if (!empty($_POST['new_zone'])) {
		$zone = str_replace('"','',$_POST['new_zone']);
		$ht = new htaccess();
		$ht->setAuthName($zone);
		$ht->setFHtaccess($up_dir.'/'.$current_dir.'.htaccess');
		$ht->setFPasswd($up_dir.'/'.$current_dir.'.htpasswd');
		$ht->addLogin();
		$msg =  __('Zone successfully created.');
		header('Location: tools.php?p=htaccess&dir='.rawurlencode($current_dir).'&msg='.rawurlencode($msg));
		exit();
	}
}

/* add a user for this zone */
if ($is_dir && $is_writable && $current_dir != '' && !empty($_POST['adduser'])) {
	$px_password = trim($_POST['new_password']);
	$px_user     = trim($_POST['new_user']);
	if (preg_match('/\s/', $px_password) or preg_match('/\s/', $px_user) 
        or preg_match('/[^0-9a-zA-Z]/', $px_password)
        or preg_match('/[^0-9a-zA-Z]/', $px_user)
	    or strlen($px_password) == 0 or strlen($px_user) == 0) {
		$err =  __('Username and password must contain only letters and digits without spaces.');
	
	} else {
		$ht = new htaccess();
		$ht->setFHtaccess($up_dir.'/'.$current_dir.'.htaccess');
		$ht->setFPasswd($up_dir.'/'.$current_dir.'.htpasswd');
		if (false !== $ht->addUser($px_user, $px_password)) {
    		$msg =  __('User successfully added.');
    		header('Location: tools.php?p=htaccess&dir='.rawurlencode($current_dir).'&msg='.rawurlencode($msg));
    		exit();
			
		} else {
			$err =  __('Error while adding the user.');
		}
	}
}

/* delete a user of this zone */
if ($is_dir && $is_writable && $current_dir != '' && !empty($_REQUEST['u'])) {
	$px_user = trim($_REQUEST['u']);
    $ht = new htaccess();
    $ht->setFHtaccess($up_dir.'/'.$current_dir.'.htaccess');
    $ht->setFPasswd($up_dir.'/'.$current_dir.'.htpasswd');
    if (false !== $ht->delUser($px_user)) {
        $msg =  __('User successfully deleted.');
        header('Location: tools.php?p=htaccess&dir='.rawurlencode($current_dir).'&msg='.rawurlencode($msg));
        exit();
    } else {
	    $err =  __('Error while deleting the user.');
    }
}

/* delete a zone */
if ($is_dir && $is_writable && $current_dir != '' && !empty($_REQUEST['del'])) {
    $ht = new htaccess();
    $ht->setFHtaccess($up_dir.'/'.$current_dir.'.htaccess');
    $ht->setFPasswd($up_dir.'/'.$current_dir.'.htpasswd');
    $ht->delLogin();
    $msg =  __('Zone successfully deleted.');
    header('Location: tools.php?p=htaccess&dir='.rawurlencode($current_dir).'&msg='.rawurlencode($msg));
    exit();
}

endif; //end of if (false === $m->error()):


if ($err != '') {
	echo '<div class="erreur"><p><strong>'. __('Error(s):').'</strong></p>'.$err.'</div>';
}

/*==============================================================================
 Display block
==============================================================================*/
?>


<h1><?php  echo __('Files and images access manager'); ?></h1>

<?php
/* display the list of the subdirectories */
if ($is_dir) {

	echo '<p>'.sprintf( __('You are in the folder <strong>%s</strong>'), $_PX_website_config['rel_url_files'].'/'.$current_dir).'</p>'."\n\n";
	
	/* propose to do something only if writable and not the root document folder */
	if ($is_writable && ($current_dir != '') && $is_secured) {
		/* secured, show the list of users, the name of the zone, propose to delete the protection */
		echo '<p class="button">'.sprintf( __('Zone <strong>%s</strong>.'), $px_zonename);
		echo ' [<strong><a href="tools.php?p=htaccess&amp;dir='.rawurlencode($current_dir).'&amp;del=1" '.
			'onclick="return window.confirm(\''. __('Are you sure you want to delete this zone ?').'\')">'. __('Delete this zone').'</a></strong>]';
		echo '</p>';
		if (count($px_users)) {
			echo '<p><strong>'. __('List of the users').'</strong></p>'."\n";
			echo '<ul>'."\n";
    		reset($px_users); 
    		while (list ($k, $v) = each ($px_users))  {
    			echo '<li>'.$v.' <a href="tools.php?p=htaccess&amp;dir='.$current_dir.'&amp;u='.$v.'" onclick="return window.confirm(\''. __('Are you sure you want to delete this user?').'\')"><img src="tools/htaccess/themes/'.$_px_ptheme.'/delete.png" alt="'. __('Delete').'" /></a></li>'."\n";
    		}	
			echo '</ul>'."\n";			
		}
		/* propose to add a user */
		?>
		
		<form action='tools.php' method='POST'>
		<input type='hidden' name='dir' value='<?php echo $current_dir; ?>' />
		<input type='hidden' name='p' value='htaccess' />
		<p>
        <span class="nowrap"><label for="new_user"
        style="display:inline"><?php  echo __('User to add:'); ?></label>
        <?php echo form::textField('new_user', 8, 8, $px_user, '', ''); ?>
		<label for="new_password"
        style="display:inline"><?php  echo __('Password:'); ?></label>
        <?php echo form::textField('new_password', 15, 15, $px_password, '', ''); ?>
        <input name="adduser" type="submit" class="submit" value="<?php  echo __('Add'); ?>" />
        </span></p>
  		</form>				
		<?php
	} elseif ($is_writable && ($current_dir != '') && !$is_secured) {
		/* propose to add a protected zone */
		?>
		<form action='tools.php' method='POST'>
		<input type='hidden' name='dir' value='<?php echo $current_dir; ?>' />
		<input type='hidden' name='p' value='htaccess' />
		<p>
        <span class="nowrap"><label for="new_zone"
        style="display:inline"><?php  echo __('New protected zone:'); ?></label>
        <?php echo form::textField('new_zone', 30, 30, '', '', ''); ?>
        <input name="createzone" type="submit" class="submit" value="<?php  echo __('Create the zone'); ?>" />
        </span></p>
  		</form>
		
		
		
		<?php
	
	}
	
	echo '<div class="resourcebox">';		
	echo '<h2>'. __('Navigation').'</h2>'."\n\n";
		
	$open = false;
	if (strlen($current_dir) > 0) {
		$open = true;
		$parent_dir = getParentDir($current_dir);
		echo '<ul class="folders"><li><a href="tools.php?p=htaccess&amp;dir='.$parent_dir.'">..</a></li>'."\n";
	}
	if (count($rep_list)) {
		if (!$open) {
			echo '<ul class="folders">'."\n";
			$open = true;
		}
		reset($rep_list); 
		while (list ($k, $v) = each ($rep_list))  {
			echo '<li><a href="tools.php?p=htaccess&amp;dir='.$current_dir.$v.'/">'.$v.'</a></li>'."\n";
		}			
	}
	if ($open) echo "</ul>\n\n";
	echo '</div>';

}	
?>
