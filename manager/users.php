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

//To improve security, defining a token
if (empty($_POST['save'])) {
	$token = md5(microtime().config::f('secret_key').$_COOKIE['px_session']);
	$_SESSION['token'] = $token;
}

/* ================================================= *
 *       Generate sub-menu                           *
 * ================================================= */
if (empty($_REQUEST['op']) && empty($_REQUEST['user_id'])) {
    $display_add_user = true;
} else {
    $display_add_user = false;
}

$px_submenu->addItem(__('New author'), 'users.php?op=add',  
                     'themes/'.$_px_theme.'/images/ico_new.png', 
                     false, $display_add_user);
$px_submenu->addItem(__('Back to the list of authors'), 'users.php',  
                     'themes/'.$_px_theme.'/images/ico_back.png', 
                     false, !$display_add_user);

$px_submenu->addItem(__('New users group'), 'users.php?op=addGroups',
		'themes/'.$_px_theme.'/images/ico_groups.png',
		false, true);

/* ====================================================== *
 *                 Process block                          *
 * ====================================================== */

if (empty($_REQUEST['op']) && empty($_REQUEST['user_id'])) { 
    // list the users
    $px_users = $m->getUsers();
} elseif ($_REQUEST['op'] == 'add' || (!empty($_REQUEST['user_id']) && $_REQUEST['op'] == 'edit') ) {
    // edit a user
    // set default values
    $px_is_admin = false; // Has the user the admin level somewhere?
    $px_edit_ok  = false;
    $px_id       = '';
    $px_username = '';   	  
    $px_password = '';  	  
    $px_realname = '';  	  
    $px_email    = '';  
    $px_pubemail = '';  	  
    $px_levels   = array();
    $px_group = 0;
    $px_path_media = '';
    $px_lang = $m->user->getPref('lang');
       
    $arry_levels[__('Administrator')] = PX_AUTH_ADMIN;
    $arry_levels[__('Advanced author')] = PX_AUTH_ADVANCED;
    $arry_levels[__('Intermediate author')] = PX_AUTH_INTERMEDIATE;
    $arry_levels[__('Simple author')] = PX_AUTH_NORMAL;
    $arry_levels[__('No access')]= PX_AUTH_DISABLE;
	$px_res = new Recordset();
	$px_cats = array();
	//$categories = array();
	$categories = $m->getArrayCategories();
	$user_groups = $m->getArrayUserGroups();
    if (!empty($_REQUEST['user_id'])) {
        $px_user = $m->getUserById($_REQUEST['user_id']);
        $px_id       = $px_user->f('user_id');
        $px_username = $px_user->f('user_username');   	  
        $px_realname = $px_user->f('user_realname');  	  
        $px_email    = $px_user->f('user_email');  
        $px_pubemail = $px_user->f('user_pubemail');  	  
        $px_levels   = $px_user->getWebsiteLevels($px_id);
        $px_group 	 = $px_user->f('user_group');
		$px_path_media = $px_user->f('user_path_media');
        $px_lang = $px_user->f('lang_id');
        
        // to get all the resources
        // even in another website 
		$px_res      = $px_user->getListResources(); 
		// to get all the categories allowed
		$px_cats 	 = $px_user->loadArrayCategoriesFromId($px_id);
		
        foreach ($px_user->webs as $site => $score) {
            if ($score >= PX_AUTH_ADMIN) {
                $px_is_admin = true;
                break;
            }
        } 
        reset($px_user->webs);     
        if (auth::asLevel(PX_AUTH_ROOT)
            || !$px_is_admin || $px_id == $m->user->f('user_id')) {
            $px_edit_ok = true;
        }                                                                  
    } else {
        // new user
        $px_edit_ok = true;
    }        
        
} elseif ($_REQUEST['op']=='addGroups' || ($_REQUEST['op'] == 'edit' && !empty($_REQUEST['group_id'])) )  {
	// edit the users groups
	$user_groups = $m->getArrayUserGroups();
	if (auth::asLevel(PX_AUTH_ROOT)
			|| !$px_is_admin || $px_id == $m->user->f('user_id')) {
		$px_edit_ok = true;
	}
	$px_group_id='';
	$px_group_name = '';
	if (!empty($_REQUEST['group_id'])) {
		$res = $m->getUserGroup($_REQUEST['group_id']);
		$px_group_id = $res->f('group_id');
		$px_group_name = $res->f('group_name');
	}
	
}

/* ================================================= *
 *              Save/Add the user                    *
 * ================================================= */
if (!empty($_POST['save'])) {
	if ($_POST['type']=='user') {
	    //Verifying token for security reasons
	    $token = $_POST['token'];
	    if ($token == $_SESSION['token']) {
		    // Populate the list of websites
		    $authwebs = array();
		    // populate the $authwebs with the data from database
		    // so no site can be removed
		    if ($px_id) {
		        foreach ($px_user->webs as $site => $score) {
		            $authwebs[$site] = $score;
		        }
		    }
		    foreach ($m->user->webs as $site => $score) {
				if ($score >= PX_AUTH_ADMIN) {
		    		if (isset($_POST['u_website_'.$site]) 
		                && $_POST['u_website_'.$site] != PX_AUTH_DISABLE) {
		    			$authwebs[$site] = $_POST['u_website_'.$site];
						$px_levels[$site] = $_POST['u_website_'.$site];
		    		} elseif (isset($_POST['u_website_'.$site]) 
		                      && $_POST['u_website_'.$site] == PX_AUTH_DISABLE) {
		    			unset($authwebs[$site]);
						unset($px_levels[$site]);
		            }
				}    
			}
			if(isset($_POST['u_group'])) $px_group=$_POST['u_group'];
			if(isset($_POST['u_path_media'])) $px_path_media=trim($_POST['u_path_media']);
			// si sélection multiple, on récupère le contenu du tableau;
			if (isset($_POST['u_cats'])) {
				if (is_array($_POST['u_cats'])) {
					$u_cats=$_POST['u_cats'];	
				} else {
					$u_cats=array($_POST['u_cats']);
				}
			} else $u_cats=array();
			//echo print_r($u_cats);
		    // now need to be sure that when the user is admin the level is not changed
		    // except the case of the user doing the operation to be root
		    if ($px_id) {
		        if (!auth::asLevel(PX_AUTH_ROOT)) {
		            reset($px_user->webs);
		            foreach ($px_user->webs as $site => $score) {
		                if ($score >= PX_AUTH_ADMIN) {
		                    $authwebs[$site] = $score;
		                }
		            }     
		        }
		    }
		    
		    if ($px_edit_ok) {
		    	$px_username = trim($_POST['u_username']); 
		    	$px_password = trim($_POST['u_password']);
		    	$px_realname = trim($_POST['u_realname']);
		    	$px_email    = trim($_POST['u_email']);
		    	$px_pubemail = trim($_POST['u_pubemail']);
		    	$px_group = trim($_POST['u_group']);
		    	$px_lang = trim($_POST['u_lang']);
		    }
		    if (false !== ($id=$m->saveUser($px_id, $px_username, $px_password, 
		                                    $px_realname, $px_email, $px_pubemail, 
		                                    $authwebs,$px_group,$px_path_media, $px_lang))
		        ) {
		        // suppression des catégories autorisées pour l'utilsateur
		        $m->delUserCats($px_id);
		        // sauvegarde des catégories autorisées
		        foreach($u_cats as $u_cat) {
		        	$m->saveUserCats($px_id, $u_cat);
		        }
				if ($id == $m->user->f('user_id')) {
					header('Location: login.php?logout=1');
					exit; 
				}
				$m->setMessage(__('The author has been successfully saved.'));
				header('Location: users.php');
				exit; 
			}
		
	    }//token verification	
	} elseif ($_POST['type']=='group') {
		// save group
		
		//Verifying token for security reasons
		$token = $_POST['token'];
		if ($token == $_SESSION['token']) {
			// get data and save
			if (isset($_POST['u_group_id'])) {
				$px_group_id = $_POST['u_group_id'];
			} else {
				$px_group_id=0;
			}
			$px_group_name = $_POST['u_group_name'];
			if (false !== ($id=$m->saveGroup($px_group_id, $px_group_name)) ) {
				$m->setMessage(__('The group has been successfully saved.'));
				header('Location: users.php?op=addGroups');
				exit;
			}
		}
	}
} 
/* ================================================= *
 *              Remove a user                        *
 * ================================================= */
if ( !empty($_POST['delete']) && !empty($px_id)) {
	if ($px_id == 1) {
		$m->setError(__('Error: This user cannot be deleted.'), 400);
	} else {
		if ($px_res->nbRow() != 0) {
			$m->setError(__('Error: This user cannot be deleted.'), 400);
		} else {
			if (false !== $m->delUser($px_id)) {
				$m->delUserCats($px_id);
				$m->setMessage(__('Author successfully deleted.'));
				header('Location: users.php');
				exit; 
			}
		}
	}
}

if (isset($_REQUEST['op']) && $_REQUEST['op']=='del' && !empty($_REQUEST['group_id'])) {
	$users = $m->getUsedGroups($_REQUEST['group_id']);
	if ($users->nbRow()>0) {
		$m->setError(__('Error: This user cannot be deleted.'), 400);
	} else {
		if (false !== $m->delGroup($_REQUEST['group_id'])) {
			$m->setMessage(__('The group successfully deleted.'));
			header('Location: users.php?op=addGroups');
			exit;
		}
	}
}

/* =========================================================== *
 *                      Display block                          *
 * =========================================================== */


/* ================================================= *
 *  Set title of the page, and load common top page  *
 * ================================================= */
$px_title =  __('Authors');
include dirname(__FILE__).'/mtemplates/_top.php';

if (empty($_REQUEST['op'])) {
	echo '<h1 id="title_authors">'. __('Authors')."</h1>\n\n";
} elseif ($_REQUEST['op'] == 'addGroups') {
	echo '<h1 id="title_authors">'. __('Users groups')."</h1>\n\n";
} else {
	echo '<h1 id="title_authors">'. __('Authors')."</h1>\n\n";
}
	
if (empty($_REQUEST['op']) && empty($_REQUEST['user_id'])) { 
    // list the users
    while(!$px_users->EOF()) {
        $res = $px_users->getListResources(config::f('website_id'));
        if ($px_users->getWebsiteLevel(config::f('website_id')) > 0) {
            $cancel = '';
        } else {
            $cancel = ' cancel';
        }
        echo '<div class="resourcebox'.$cancel.'" id="p'.$px_users->f('user_id').'">';
		echo "\n<p class='resource_title'>";
        if (($px_users->f('user_id') != 1) || auth::asLevel(PX_AUTH_ROOT)) {
            echo '<span class="author_style"><a href="users.php?op=edit&user_id='.$px_users->f('user_id').'">'.$px_users->f('user_realname').'</a></span>';
        } else {
            echo '<span class="author_style">'.$px_users->f('user_realname').'</span>';
        }
        echo ' ['.$res->nbRow().' '. __('resource(s)').']';
        echo "</p>\n\n";
        echo "\n</div>\n\n";    
        		

		$px_users->moveNext();			
	} 
} elseif ($_REQUEST['op'] == 'add' || (!empty($_REQUEST['user_id']) && $_REQUEST['op'] == 'edit') ) {
    if ($m->user->f('user_id') == $px_id) {
        echo '<p class="message">'. __('Attention, you are modifying your profile. You will be logged out if changes are successfully made.').'</p>'."\n\n";
    }

?>
<h2><?php  echo __('Identity'); ?></h2>
<form action="users.php" method="post" id="formPost">
	<table style="border:0;cellpadding:0;cellspacing:0;width:100%">
		<tr><td >
	<h2><?php  echo __('User'); ?></h2>
  <p class="field"><label class="float" for="u_username" style="display:inline"><span class="login_style"><?php  echo __('Login:'); ?></span></label>
  <?php if ($px_edit_ok) { 
            echo form::textField('u_username', 30, 30, $px_username, '', ''); 
        } else {
            echo $px_username;
        }      
  ?>
  </p>
  
  <p class="field"><label class="float" for="u_realname" style="display:inline"><span class="real_name"><?php  echo __('Name:'); ?></span></label>
  <?php if ($px_edit_ok) { 
            echo form::textField('u_realname', 30, 50, $px_realname, '', ''); 
        } else {
            echo $px_realname;
        }      
  ?>
  </p>

  <p class="field"><label class="float" for="u_email" style="display:inline"><span class="private_mail_style"><?php  echo __('Email <span class="small">(not shown)</span>:'); ?></span></label>
  <?php if ($px_edit_ok) { 
            echo form::textField('u_email', 30, 50, $px_email, '', ''); 
        } else {
            echo $px_email;
        }      
  ?>
  </p>  

  <p class="field"><label class="float" for="u_pubemail" style="display:inline"><span class="public_mail_style"><?php  echo __('Public email:'); ?></span></label>
  <?php if ($px_edit_ok) { 
            echo form::textField('u_pubemail', 30, 50, $px_pubemail, '', ''); 
        } else {
            echo $px_pubemail;
        }      
  ?>
  </p>  

<?php if ($px_edit_ok): ?>
  <p class="field"><label class="float" for="u_password" style="display:inline"><span class="password_style"><?php  echo __('Password:'); ?></span></label>
  <?php echo form::textField('u_password', 30, 50, '', '', ''); ?>
  <br /><span class="notification"><?php  echo __('(keep empty not to change it)'); ?></span></p>    
<?php endif; ?>

<h2><?php  echo __('Levels'); ?></h2>
  <p>
  <?php 
  foreach ($m->user->webs as $site => $score) {
    if ($score >= PX_AUTH_ADMIN) {
	    echo '<p class="field"><label for="u_website_'.$site.'" style="display:inline"><span class="sitename_style">';
        echo sprintf( __('Site <strong>%s</strong>:'), $m->user->wdata[$site]['website_name']);
        echo '</span></label> ';
        if (!isset($px_levels[$site])) $px_levels[$site] = PX_AUTH_DISABLE;
        if ($px_levels[$site] >= PX_AUTH_ADMIN && !$px_edit_ok) {
            echo  __('Administrator');
        } else {
            echo form::combobox('u_website_'.$site, $arry_levels, $px_levels[$site]);
        }
    }    
  }
  if (!empty($px_id)) {
   	 echo form::hidden('user_id',$px_id);
   	 echo form::hidden('op','edit');
  } else { 
	 echo form::hidden('op','add');
  }
  echo form::hidden('type','user');
  echo form::hidden('u_lang',$px_lang);
  //To improve security
  echo form::hidden('token', $token);
?>
  </p>

  </td><td valign="top" align="left">
  <h2><?php echo __('Users group'); ?></h2>
  <p><?php echo form::combobox('u_group',$user_groups,$px_group,'','','','','',false,1);
  	?>
  </p>
  <h2><?php  echo __('Allowed categories'); ?></h2>
  	<p>
	 <?php 
		echo form::combobox('u_cats',$categories,$px_cats,'','','','','',true,15);
		?>
		</p> 	
  </td>
  </tr>
  <tr>
  	<td colspan="2">
		<h2><?php  echo __('Name of the file and image folder:'); ?></h2>
		<p >  <!-- class="field" -->
			<label   for="u_path_media" style="display:inline" > <!-- class="float" -->
				<strong><?php  echo __('Name of the file and image folder:'); ?></strong>
			</label>
			<span><?php echo $_PX_website_config['rel_url_files'].'/'; ?></span>
			<span>
				<?php echo form::textField('u_path_media', 50, 255, $px_path_media, '', ''); ?>
			</span>
			<!-- <br />  -->
		  	<?php  echo __('For example put <em>Login</em> or <em>Name</em>.'); ?>
  		</p>
  	</td>
  </tr>
  <tr><td colspan="2">
  
  <p class="button"> <input name="save" type="submit" class="submit" value="<?php  echo __('Save [s]'); ?>"
  accesskey="<?php  echo __('s'); ?>" />&nbsp;  
  <?php
    if ($px_res->nbRow() == 0 && !empty($px_id)) {
	echo '&nbsp;<input name="delete" type="submit" class="submit" '.
	'value="'.  __('Delete [d]').'" accesskey="'.__('d').'" onclick="return '.
	'window.confirm(\''.addslashes( __('Are you sure you want to delete this author?')).'\')" />';
   
  }
  ?>
  </p>
  </td></tr>
  </table>
</form>
<?php
} elseif ($_REQUEST['op'] == 'addGroups' || ($_REQUEST['op'] == 'edit' && !empty($_REQUEST['group_id'])) )  {
	// Gestion ds groupes d'utilisateurs
?>
	<h2><?php echo __('Users Group List'); ?></h2>
	<table style="border:0;cellpadding:0;cellspacing:0;width:100%">
		<tr>
			<td style="width:150px"></td>
			<td>
				<div>
				<table style="width:500px">
					<thead><tr style="background-color: #E7E7E7;border-bottom: 1px solid #CCCCCC;">
						<th><?php echo __('ID'); ?></th>
						<th><?php echo __('Group name'); ?></th>
						<th><?php echo __('Action'); ?></th>
					</tr></thead>
					<tbody>
						<?php foreach($user_groups as $name=>$id) {?>
						<tr>
							<td style="width:60px;"><?php echo $id; ?></td>
							<td style="width:250px;"><?php echo $name; ?></td>
							<td><?php 
								if (auth::asLevel(PX_AUTH_ROOT)) {
            						echo '<span class="editlink"><a href="users.php?op=edit&group_id='.$id.'">'.__('Edit').'</a></span>';
								}
            					?>
            				&nbsp;&nbsp;&nbsp;
							<?php 
								if (auth::asLevel(PX_AUTH_ROOT)) {
            						echo '<span class="deletelink">&nbsp;&nbsp;<a href="users.php?op=del&group_id='.$id.'">'.__('Delete').'</a></span>';
								}
            					?>
            				</td>
            			</tr>
						<?php } ?>
					</tbody>
				</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div style="height:50px"></div>
				<h2><?php echo ($px_group_id==0) ? __('Add a new group') : __('Edit the group'); ?></h2>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<form action="users.php" method="post" id="formPost">
				<div>
					<span><?php echo __('Group name').' : '?></span>
					<span><?php echo form::textField('u_group_name', 30, 50, $px_group_name, '', 'u_group_name');?></span>
				</div>
				<?php 
				  echo form::hidden('u_group_id',$px_group_id);
				  echo form::hidden('type','group');
				  //To improve security
				  echo form::hidden('token', $token);
				  ?>
				<p class="button">
					<input name="save" type="submit" class="submit" value="<?php  echo __('Save [s]'); ?>" accesskey="<?php  echo __('s'); ?>" />&nbsp;  
				  <?php
						echo '&nbsp;<input name="delete" type="submit" class="submit" '.
						'value="'.  __('Delete [d]').'" accesskey="'.__('d').'" onclick="return '.
						'window.confirm(\''.addslashes( __('Are you sure you want to delete this author?')).'\')" />';
				  ?>
  				</p>
  				</form>
			</td>
		</tr>
	</table>
	
<?php 	
} // fin gestion des groupes

/*=================================================
 Load common bottom page
=================================================*/
include dirname(__FILE__).'/mtemplates/_bottom.php';

?>
