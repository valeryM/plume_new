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

require_once 'path.php';
require_once $_PX_config['manager_path'].'/prepend.php';

auth::checkAuth(PX_AUTH_ADMIN);

$m = new Manager();
$_px_theme = $m->user->getTheme();

if (empty($_REQUEST['site_id']) && empty($_REQUEST['op'])) {
    $display_add_site = true;
} else {
    $display_add_site = false;
}
//full right to modify the current site and add. Only the root user.
$is_full_editable = auth::asLevel(PX_AUTH_ROOT); 

if ($is_full_editable) {
	$px_submenu->addItem(__('New site'), 'sites.php?op=add', 
                         'themes/'.$_px_theme.'/images/ico_new.png', 
                         false, $display_add_site);
}
$px_submenu->addItem(__('Back to the list of the sites'), 'sites.php',  
                     'themes/'.$_px_theme.'/images/ico_back.png', false, 
                     !$display_add_site);

/* ========================================================================= *
 *                  Process block                                            *
 * ========================================================================= */

// partial rights to modify the current website, see a partial 
// list of websites (only the one with some rights)
$is_editable = true; 
$update = false;
// switched to true only if the form to create a new site was submitted 
// and the data in the form were ok to start the creation. A page with 
// the log of the creation is then displayed.
$is_new_site = false; 

if (empty($_REQUEST['op']) && empty($_REQUEST['site_id'])) {
	// list the sites
	$px_sites = $m->getSites();
} else {
	// edit a website
	// set default values
	$px_id               = '';
	$px_name             = __('New website name');
	$px_website_address  = '';
	$px_xmedia_name      = 'xmedia';
	$px_website_path     = '';
	$px_description      = __('Description of the __new website__.');
	$px_sitelang         = $m->user->lang;
    $px_comment_status   = 1;
    $px_comment_value = 1;
    $px_comment_support  = 1;
    $px_image_name = '';
	$arry_langs = l10n::getIsoCodes(true);

	if (!empty($_REQUEST['site_id']) && empty($_REQUEST['op'])) {
		// see if the website exists or not
		$px_site = $m->getSites($_REQUEST['site_id']);
		if (strlen($px_site->f('website_id')) == 0) {
			// site does not exist error
			$m->setMessage(__('This site is not available.'));
			header('Location: sites.php');
			exit;
		}
		if (!auth::asLevel(PX_AUTH_ADMIN, $px_site->f('website_id'))) {
			// No rights
			$m->setMessage(__('No rights to edit this website.'));
			header('Location: sites.php');
			exit;
		}
		if (file_exists($_PX_config['manager_path'].'/conf/configweb_'.$px_site->f('website_id').'.php')) {
			include($_PX_config['manager_path'].'/conf/configweb_'.$px_site->f('website_id').'.php');
			if (!is_writable($_PX_config['manager_path'].'/conf/configweb_'.$px_site->f('website_id').'.php')) {
				$is_editable = false;
				$m->setError( __('Error: Permissions of the configuration prevent the addition or modification of the sites.'), 500);
			}
		} else {
			$m->setError(sprintf( __('Error: Configuration file of the website(<strong>%s</strong>) not available.'), files::real_path($_PX_config['manager_path'].'/conf/configweb_'.$px_site->f('website_id').'.php')), 500);
			$is_editable = false;
		}

		if ((false === $m->error(true, false)) && $is_editable) {
			$px_id           = $px_site->f('website_id');
			$px_name         = $px_site->f('website_name');
			$px_website_address = 'http'.(($_PX_website_config['secure']) ? 's': '').'://'.$_PX_website_config['domain'].$_PX_website_config['rel_url'];
			$px_xmedia_name  = trim(str_replace('/', '', substr($_PX_website_config['rel_url_files'], strlen($_PX_website_config['rel_url']))));
			$px_description  = $px_site->f('website_description');
			$px_sitelang     = $_PX_website_config['lang'];
			$px_website_path = substr(files::real_path($_PX_website_config['xmedia_root']), 0, (-1 - strlen($px_xmedia_name)));
            $px_comment_status = $_PX_website_config['comment_default_status'];
            $px_comment_value = $_PX_website_config['comment_default_value'];
            $px_comment_support = $_PX_website_config['comment_support'];
            $px_image_name = $px_site->f('website_img');
			$update = true;
		}
	}
	if (!$is_full_editable && (!empty($_REQUEST['op']) && 'add' == $_REQUEST['op'])) {
		// No rights
		$m->setMessage(__('No rights to add a website.'));
		header('Location: sites.php');
		exit;
	}
	if (!empty($_SESSION['log_new_site']) && (!empty($_REQUEST['op']) && 'log' == $_REQUEST['op'])) {
		$px_new_site = $_SESSION['log_new_site'];
		$is_new_site = true;
	}
}


/*=================================================
 Save the data
=================================================*/
if ($is_editable && (!empty($_POST['save']))) {
	// get data from form
	$px_name = form::getPostField('s_name');
	$px_description = form::getPostField('s_description');
	$px_sitelang = form::getPostField('s_sitelang');
    $px_comment_status =  form::getPostField('s_comment_status');
    $px_comment_value = form::getPostField('s_comment_value');
    $px_comment_support =  form::getPostField('s_comment_support');
	$px_image_name = form::getPostField('s_image_name');
	if ($is_full_editable) {
		$px_website_address = form::getPostField('s_website_address');
		$px_website_path = form::getPostField('s_website_path');
		$px_xmedia_name = form::getPostField('s_xmedia_name');
	}
	$px_log_new_site = ''; //updated through a reference
	if (!empty($px_id)) {
		if (false !== $m->saveSite($px_id, $px_name, $px_description, 
                                   $px_sitelang, $px_website_address, 
                                   $px_website_path, $px_xmedia_name, 
                                   $px_comment_support, $px_comment_status, $px_comment_value,
                                   $px_log_new_site,'', $px_image_name)) {
			$m->setMessage(sprintf(__('Site <strong>%s</strong> successfully saved.'), $px_name));
			header('Location: sites.php');
			exit;
		}
	} else {
		if (false !== $m->saveSite($px_id, $px_name, $px_description, 
                                   $px_sitelang, $px_website_address, 
                                   $px_website_path, $px_xmedia_name, 
                                   $px_comment_support, $px_comment_status, $px_comment_value,
                                   $px_log_new_site, '', $px_image_name)) {
			$m->setMessage(sprintf(__('Site <strong>%s</strong> successfully added.'), $px_name));
            //Save the log in the session
            //the header is to prevent a double "addition".
			$_SESSION['log_new_site'] = $px_log_new_site; 
			header('Location: sites.php?op=log');
			exit;
		}
	}
}

if ($is_editable && (!empty($_POST['delete']))) {
	if (false !== $m->delSite($px_id)) {
		$m->setMessage(__('Site successfully deleted. No file was removed from the document root.'));
		header('Location: sites.php');
		exit;
	}
}

if ($is_editable && strlen(form::getField('switchtheme')) > 0) {
    if (false !== $m->switchSiteTheme($px_id, form::getField('switchtheme'))) {
        //Clean cache when the theme of the current site is changed
        include_once dirname(__FILE__).'/inc/class.cache.php';
        cache::clean(config::f('website_id'));
        // -- 
        $m->setMessage(__('The theme of the site has been successfully changed. You may need to copy the style files in the xmedia folder.'));
		header('Location: sites.php?site_id='.$px_id);
		exit;
	}
}

/* ========================================================================= *
 *          Display block                                                    *
 * ========================================================================= */
/* =================================================  *
 *   Set title of the page, and load common top page  *
 * ================================================== */
$px_title =  __('Sites');
include dirname(__FILE__).'/mtemplates/_top.php';

echo '<h1 id="title_sites">'.__('Sites')."</h1>\n\n";

if($is_new_site) {
/*=================================================
 Show the log of the creation of a new website
=================================================*/
	echo '<h2>'.__('New site')."</h2>\n\n";
	
	echo '<p>'.__('Log of the installation:').'</p>'."\n\n";

	echo $px_new_site;

	echo '<p>'.__('If you see warnings, check them carefully and look at the online documentation to see the impact. You may need to complete the installation manually.').'</p>'."\n\n";

	echo '<p>'.__('To see the new website in the manager, you need to logout and login again.').'</p>'."\n\n";

	echo '<p>'.__('If you are using the nice url option, you need to configure the webserver to use these url with this new website.').'</p>'."\n\n";

} elseif (empty($_REQUEST['op']) && empty($_REQUEST['site_id'])) {
/*=================================================
 Show the list of websites.
=================================================*/
	while(!$px_sites->EOF()) {
		//if (auth::asLevel(PX_AUTH_ADMIN, $px_sites->f('website_id'))) {
			echo '<div class="resourcebox" id="p'.$px_sites->f('website_id').'">';
			echo '<p class="resource_title">';
			echo '<span class="sitename_style"><a href="'.$px_sites->f('website_url').'">'.$px_sites->f('website_name').'</a></span>';
			echo ' <span class="notification">('.$px_sites->f('website_id').')</span> ';
			echo '[<span class="editlink"><a href="sites.php?site_id='.$px_sites->f('website_id').'">'. __('edit').'</a></span>]</p>';
				
			echo "<div class=\"description_style\">\n";
			if ($px_sites->f('website_img')!='' && $px_sites->f('website_img')!=null ) {
				$img_url = 'themes/'.$_px_theme.'/images/'.$px_sites->f('website_img');
				echo '<span style="display:inline;" ><img style="height:25px;" src="'.$img_url.'" alt="image" /></span>';
				echo '<span >&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			}
				
			echo "<span>".$px_sites->f('website_description')."</span>\n";
			echo "</div>\n";
			
			echo '<p class="subtype_style">'.sprintf(__('<a href="%s">Manage the subtypes of resources</a>.'), 'subtypes.php').'</p>'."\n\n";
			echo "\n</div>\n\n";
		//}
		$px_sites->moveNext();
	}
} elseif (($is_editable && !empty($_REQUEST['site_id'])) or ((!empty($_REQUEST['op']) && 'add' == $_REQUEST['op']) && $is_full_editable)) {
/*=================================================
 Show the page to edit/create a website
=================================================*/
	if (!empty($_REQUEST['op']) && 'add' == $_REQUEST['op']) {
		echo '<h2>'. __('New site')."</h2>\n\n";
	}
?>

<form action="sites.php" method="post" id="formPost">
<?php
	if (empty($_REQUEST['op']) or 'add' != $_REQUEST['op']) {
 ?>
	<p class="field"><label class="float" for="s_site_id" style="display:inline"><strong><?php  echo __('Site id:'); ?></strong></label>
	<strong><?php echo $px_id; ?></strong></p>
<?php
	}
?>
  <p class="field"><label class="float" for="s_name" style="display:inline"><strong><?php  echo __('Site name:'); ?></strong></label>
  <?php echo form::textField('s_name', 30, 255, $px_name, '', ''); ?>
  </p>
<?php
// in case of addition of a new website, we ask where the "index.php" folder is
	if ($is_full_editable or (!empty($_REQUEST['op']) && 'add' == $_REQUEST['op'])):  
	?>
  <p class="field"><label class="float" for="s_website_address" style="display:inline"><strong><?php  echo __('Website address:'); ?></strong></label>
  <?php echo form::textField('s_website_address', 50, 255, $px_website_address, '', ''); ?><br />
  <?php  echo __('For example put <em>https://www.mydomain.com/mysite/</em> or <em>http://www.mydomain.com/</em>. Do not forget the <em>http(s)</em>.'); ?>
  </p>

  		<p class="field"><label class="float" for="s_website_path" style="display:inline"><strong><?php  echo __('Path to the document root of the website:'); ?></strong></label>
		<?php echo form::textField('s_website_path', 30, 255, $px_website_path, '', '');
		
		if (!empty($_REQUEST['op']) && 'add' == $_REQUEST['op']):
			echo '<br />'."\n".__('Path on the server side. For example <em>/home/login/www</em> or <em>c:/http</em>. The document root is the folder where the <em>index.php</em> will be placed.');
		endif;

  	echo "\n".'</p>'."\n";
	endif;
?>

<?php if ($is_full_editable): ?>
  <p class="field"><label class="float" for="s_xmedia_name" style="display:inline"><strong><?php  echo __('Name of the file and image folder:'); ?></strong></label>
  <?php echo form::textField('s_xmedia_name', 15, 255, $px_xmedia_name, '', ''); ?><br />
  <?php  echo __('For example put <em>xmedia</em> or <em>documents</em>.'); ?>
  </p>
  <p class="field"><label class="float" for="s_image_name" style="display:inline"><strong><?php  echo __('Name of the website image:'); ?></strong></label>
  <?php echo form::textField('s_image_name', 15, 255, $px_image_name, '', ''); ?>&nbsp;&nbsp;<em><?php echo sprintf(__('In %s'),'manager/themes/'.$_px_theme.'/images/'); ?></em><br />
  <?php echo __('For example put <em>logo_site.png</em>.'); ?>
  </p>
  
<?php endif; ?>

  <p class="field"><label class="float" for="s_sitelang" style="display:inline"><strong><?php  echo __('Main language of the website:'); ?></strong></label>
  <?php echo form::comboBox('s_sitelang', $arry_langs, $px_sitelang); ?>
  </p>

  <p class="field"><label class="float" for="s_description" style="display:inline"><strong><?php  echo __('Description:'); ?></strong></label>
  <?php
  echo form::textArea('s_description', 60, 4, $px_description, '', '');
  ?>
  </p>


  <p class="field"><label class="float" for="s_comment_support" style="display:inline"><strong><?php  echo __('Support of the comments:'); ?></strong></label>
  <?php echo form::comboBox('s_comment_support', 
                            array(__('Comments open') => 1, 
                                  __('Defined for each individual resource') => 2,
                                  __('Comments closed') => 3), $px_comment_support); ?>
  </p>
    <p class="field"><label class="float" for="s_comment_status" style="display:inline"><strong><?php  echo __('Default value for the comment status:'); ?></strong></label>
  <?php echo form::comboBox('s_comment_value', array(__('Comments open') => 1, __('Comments closed') => 3 ), $px_comment_value); ?>
  
  <p class="field"><label class="float" for="s_comment_status" style="display:inline"><strong><?php  echo __('Default status of the comments:'); ?></strong></label>
  <?php echo form::comboBox('s_comment_status', array(__('Need validation') => 5, __('Online') =>1 ), $px_comment_status); ?>

  </p>

	<?php
	if ($update) {
		echo form::hidden('site_id',$px_id);
	    ?><p class="button"> <input name="save" type="submit" class="submit" value="<?php  echo __('Save [s]'); ?>"  accesskey="<?php  echo __('s'); ?>" />&nbsp;
	    <?php
	} else {
		echo form::hidden('op','add');
		echo "\n".'<p>'.__('If all the informations you provided are valid, the system will copy the required files in the right folders. The complete log of those files will be shown to you. No file already available on the system will be overwritten.').'</p>'."\n\n";
		?> <p class="button"> <input name="save" type="submit" class="submit" value="<?php  echo __('Create the new website'); ?>"  accesskey="<?php  echo __('s') ?>" />
		<?php
	}
	?>
	<?php
	if ($update && (false === $m->getEarlierDate('m', '', '', $px_id))) {
		echo '&nbsp;<input name="delete" type="submit" class="submit" '.
		'value="'.__('Delete [d]').'" accesskey="'.__('d').'" onclick="return '.
		'window.confirm(\''.addslashes( __('Are you sure you want to delete this site?')).'\')" />';
	}
	?>
	</p>
</form>
<?php 

 if ($update) {
     // get the available themes
     require_once dirname(__FILE__).'/extinc/class.plugins.php';
     $themes_root = dirname(__FILE__).'/templates/';
     $objPlugins = new plugins($themes_root);
     $themes_list = $objPlugins->getPlugins('theme');

     if (count($themes_list) >= 1) {
         echo '<hr class="soft" />'."\n";
         echo '<h2>'.__('Website Themes').'</h2>'."\n";
         echo '<dl class="themes-list">'."\n";
         reset($themes_list);
         foreach($themes_list as $theme) {
             echo '<dt><img alt="" src="templates/'.$theme['name'].'/preview.png" /> <span class="theme_style">'.$theme['label'].'</span> '.__('by').' <span class="author_style">'.$theme['author'].'</span></dt>'."\n";
             echo '<dd>'.$theme['desc'].' - '.__('version:').' '.$theme['version'];
             if (config::f('theme_id') != $theme['name']) {
                 echo '<br /><a href="sites.php?switchtheme='.$theme['name'].'&amp;site_id='.$px_id.'"><strong>'.__('Use this theme').'</strong></a>';
             }
             echo '</dd>'."\n\n";
         }
         echo '</dl>'."\n";
     }
 }

}

/*=================================================
 Load common bottom page
=================================================*/
include dirname(__FILE__).'/mtemplates/_bottom.php';

?>
