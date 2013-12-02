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
require_once dirname(__FILE__).'/prepend.php';
auth::checkAuth(PX_AUTH_NORMAL);

$m = new Manager();
$_px_theme = $m->user->getTheme();
$m->user->load();
//require_once config::f('manager_path').'/extinc/class.lum.php';
//require_once config::f('manager_path').'/extinc/lib.image.php';

if (false === config::loadWebsite($_SESSION['website_id'])) {
	$m->setError(sprintf(__('Error: Configuration file of the website (<strong>%s</strong>) not available. Please check your installation.'), $_SESSION['website_id']), 500);
}

/* ================================================= *
 *       Generate sub-menu                           *
* ================================================= */
$px_submenu->addItem(__('Back to the list of resources'), 'index.php',
		'themes/'.$_px_theme.'/images/ico_back.png', false);
/*
$px_submenu->addItem(__('Files or images'), 'xmedia.php',
		'themes/'.$_px_theme.'/images/ico_image.png', false);
*/
//$px_submenu->addItem('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '', '', false);

$px_submenu->addItem(__('News list'), 'news.php?op=list',
		'themes/'.$_px_theme.'/images/ico_news.png', false);

$px_submenu->addItem(__('Article list'), 'articles.php?op=list',
		'themes/'.$_px_theme.'/images/ico_article.png', false);

$px_submenu->addItem(__('Events list'), 'events.php?op=list',
		'themes/'.$_px_theme.'/images/ico_datetime.png', false);

$px_submenu->addItem(__('Rss links list'), 'rsslinks.php?op=list',
		'themes/'.$_px_theme.'/images/rss_edit.png', false);
/*
$px_submenu->addItem(__('List the types'), 'subtypes.php',
		'themes/'.$_px_theme.'/images/ico_subtype.png', false);
*/
$_site_url = $m->user->wdata[$GLOBALS['_PX_website_config']['website_id']]['website_url'].'/';
$px_submenu->addItem(__('See the site'), $_site_url,
		'themes/'.$_px_theme.'/images/ico_site.png', false);


/**
 * for the moment the logic is here, completely based on the work done in
 * dotclear with extension to almost all the kind of files, but will need
 * to move it into classes at some point.
 */
//$px_gd_version = gd_version();
$mode = (!empty($_REQUEST['mode'])) ? htmlspecialchars($_REQUEST['mode']) : '';
//$env  = (!empty($_GET['env'])) ? $_GET['env'] : 1;
$env = (!empty($_GET['env'])) ? intval($_GET['env']) : 1;

// CKEDITOR : Variables passées depuis une boite de dialogue / Explorer le serveur
// Required: anonymous function number as explained above.
//[CKEditor],[CKEditorFuncNum],[langCode]
$Editor = (!empty($_REQUEST['CKEditor'])) ? $_REQUEST['CKEditor'] : '' ;
$funcNum = (!empty($_REQUEST['CKEditorFuncNum'])) ? $_REQUEST['CKEditorFuncNum'] : '' ;
$langCode = (!empty($_REQUEST['langCode'])) ? $_REQUEST['langCode'] : '' ;
$vars ='';
if ($mode!='') $vars.= '&amp;mode='.$mode;
if ($Editor!='') $vars .= '&amp;CKEditor='.$Editor;
if ($funcNum!='') $vars .= '&amp;CKEditorFuncNum='.$funcNum;
if ($langCode !='') $vars .= '&amp;langCode='.$langCode;

$is_writable = false;
$is_dir = true;

if (true === $m->error()) {
	die();
	exit;
}
/*
$up_dir = config::f('xmedia_root');

//set the current dir and mode from the session if info available
if (empty($_REQUEST['dir'])) {
	if (!empty($_SESSION['xmedia_current_dir'])) {
		$current_dir = $_SESSION['xmedia_current_dir'];
	} else {
		$current_dir = $m->user->getPref('xmedia_current_dir');
	}
	if ($mode=='popup' ) $dir = "/";
	if (!empty($current_dir)) {
		// the current dir is coming from the session,
		// set the $env from it or set it to 1
		$env  = ($m->user->getPref('xmedia_current_dir')!=='') ? $m->user->getPref('xmedia_current_dir') : 1;
		//$env = $current_dir;
		//we need to ensure a "correct" request URI to work with the lum class
		if ('.php' == substr($_SERVER['REQUEST_URI'], strlen($_SERVER['REQUEST_URI'])-4, 4))
		$_SERVER['REQUEST_URI'] .= '?';
		$_SERVER['REQUEST_URI'] = str_replace('?','?dir='.$current_dir.'&env='.$env.'&',$_SERVER['REQUEST_URI']);
	}
} else {
	$current_dir = $_REQUEST['dir'];
	
	// contrôle de sécurité : un simple utilisateur ne peut accéder qu'a ses sous-dossiers. 
	if (!auth::asLevel(PX_AUTH_ADVANCED))  {
		$base_dir = $m->user->getPref('xmedia_current_dir');
		// le chemin demande doit être un sous dossier du chemin de base
		if (substr($current_dir,0,strlen($base_dir))!== $base_dir) {
			$current_dir=$base_dir;
		}
	}
	if (empty($env)) $env=1;
}
*/
/*
if (!empty($current_dir)) {
	$current_dir = str_replace('\\','',$current_dir);
	$current_dir = str_replace('..','',$current_dir);
	$current_dir = preg_replace( '#[^A-Za-z0-9\-\_\/]#', '', $current_dir);
	$current_dir = preg_replace( '#(/)+#', '/', $current_dir);
	$current_dir = preg_replace( '#^(/)+#', '', $current_dir);
	$current_dir = preg_replace( '#(/)+$#', '', $current_dir);
	if (!empty($current_dir))
		$current_dir .= '/';
} else {
	$current_dir = '';
}
*/
/* check rights */
/*
if (is_dir($up_dir.'/'.$current_dir)) {
	if (is_writable($up_dir.'/'.$current_dir)) {
		$is_writable = true;
	} else {
		$m->setError(sprintf(__('Error: The system has no write access to the folder <strong>%s</strong>. Check the permissions.'), config::f('rel_url_files').'/'.$current_dir), 500);
	}
} else {
	// on tente de créer le dossier avec les droits
	$dir = substr($up_dir.'/'.$current_dir, 0,strlen($up_dir.'/'.$current_dir)-1);
	if (false === @mkdir($dir,0777))  {
		$m->setError(sprintf(__('Error: The folder <strong>%s</strong> does not exist.'), config::f('rel_url_files').'/'.$current_dir), 500);
		$is_dir = false;
	} else {
		$is_writable = true;
	}
}
*/
//endif; //end of if (false === $m->error()):
/*
if ($is_dir) {
	$_SESSION['xmedia_current_dir'] = $current_dir;
	//$m->user->savePref('xmedia_current_dir', $current_dir, $_SESSION['website_id'], true);
} else {
	// TODO : génération d'un dossier par défaut : $m->user->f('user_username').
	//$m->user->savePref('xmedia_current_dir', '/', $_SESSION['website_id'], true);
	//$current_dir = $_SESSION['website_id'].'/';
}
*/
/* modification of the file */
/*
if (!empty($_GET['file']) && (false === $m->error())) {
	$px_file = str_replace('..','',$_GET['file']);
	$px_file = preg_replace( '#(/)+#', '/', $px_file);
	$px_file = preg_replace( '#^(/)+#', '', $px_file);

	if (!file_exists($up_dir.'/'.$px_file)) {
		$m->setError(__('Error: The requested file does not exist.'), 400);
	} else {
		if (!empty($_GET['del']) && $_GET['del'] == 1) {
			if (@unlink($up_dir.'/'.$px_file) === false) {
				$m->setError(__('Error: Impossible to delete the file.'), 500);
			} else {
				@unlink($up_dir.'/thumb/'.$px_file.'.jpg');	//.md5($px_file).'.jpg');
				$m->setMessage(__('File deleted with success.'));
				header('Location: xmedia.php?dir='.rawurlencode($current_dir).$vars);
				exit();
			}
		} elseif(!empty($_GET['thumb']) && $_GET['thumb'] == 1) {
			$thumb = $up_dir.'/thumb/'.$px_file.'.jpg';		//.md5($px_file).'.jpg';
			@$thumb = cropImg($up_dir.'/'.$px_file, $thumb, 150, 100);
		}
	}
	
}
*/
/* Upload of the file */
/*
if ($is_writable && !empty($_FILES['up_file']) && (false === $m->error())) {
	$tmp_file = $_FILES['up_file']['tmp_name'];
	$file_name = $_FILES['up_file']['name'];

	if (version_compare(phpversion(),'4.2.0','>=')) {
		$upd_error = $_FILES['up_file']['error'];
	} else {
		$upd_error = 0;
	}

	if ($upd_error != 0) {
		switch ($upd_error) {
			case 1:
			case 2:
				$m->setError(__('Error: The size of the file excess the maximum size.'), 400);
				break;
			case 3:
				$m->setError(__('Error: File not fully transfered.'), 400);
				break;
			case 4:
				$m->setError(__('Error: No file.'), 400);
				break;
		}
	} elseif(!file_exists($tmp_file)) {
		$m->setError(__('Error: No file.'), 400);
	} elseif(filesize($tmp_file) > config::f('max_upload_size')) {
		$m->setError(__('Error: The size of the file excess the maximum size.'), 400);
	} else {
		$file_name = preg_replace( '#(\s+)#', '_', $file_name);
		if (!isFileSafe($file_name)) {
			$file_name = safeFile($file_name);
		}	
		//if(!isFileSafe($file_name)) {
		//	$m->setError(__('Error: Not an authorized type of file. The file name must contain only simple letters, digits, "-", "_" and a recognized extension. No spaces allowed.'), 400);
		//} else {
			if (@copy($tmp_file,$up_dir.'/'.$current_dir.$file_name)) {
				@chmod($up_dir.'/'.$current_dir.$file_name, 0666);
				//create thumb 
	
				$thumb = $up_dir.'/thumb/'.$current_dir.$file_name.'.jpg';		//md5($current_dir.$file_name).'.jpg';
				@unlink($thumb); //ensure that the old thumbnail is removed if available
				@$msg=cropImg($up_dir.'/'.$current_dir.$file_name, $thumb, 150, 100);
				@chmod($thumb, 0666);

				$m->setMessage(__('File successfully uploaded.'));
				header('Location: xmedia.php?dir='.rawurlencode($current_dir)
				.$vars.'&env='.$env);
				exit();
			} else {
				$m->setError(__('An error occured during the transfer of the file.'), 500);
			}
		//}
	}
	
}
*/
/* Create a subfolder */
/*
if ($is_writable && !empty($_POST['create']) && (false === $m->error()) 
				&& (auth::asLevel(PX_AUTH_ADVANCED))  ) {
	
	if (!empty($_POST['new_folder'])) {
		$new_dir = trim($_POST['new_folder']);
		if (preg_match('/[^A-Za-z0-9\-\_]/', $new_dir) or ($new_dir == 'thumb')) {
			$m->setError( __('Error: Invalid folder name, it must contain only letters, digits, "_" and "-".'), 400);
		} else {
			$tmp = $up_dir.'/'.$current_dir;
			if (@mkdir($up_dir.'/'.$current_dir.$new_dir, 0705)) {	// $up_dir.'/'.$current_dir.$new_dir
				$m->setMessage(__('The folder was successfully created, you can already add files and images in.'));
				header('Location: xmedia.php?dir='.rawurlencode($current_dir.$new_dir).$vars);
				exit();

			} else {
				$m->setError( __('Error: Impossible to create the new folder.'), 500);
			}

		}
	}
	
}

if ($is_writable && !empty($_GET['file']) && !empty($_GET['del']) && (false === $m->error()) 
				&& (auth::asLevel(PX_AUTH_ADVANCED))  ) {
	uploadFilesManager::deleteFile($up_dir.'/'.$current_dir,$_GET['file']);
}
*/					

/* Delete a subfolder */
/*
if ($is_writable && !empty($_REQUEST['delfolder']) && (false === $m->error())
&& (auth::asLevel(PX_AUTH_ADVANCED))) {

	$can_delete = true;
	$can_delete = (strlen($current_dir) != 0) ? true : false;
	// scan if one file exist
	if ($is_dir && $can_delete) {
		$D = dir($up_dir.'/'.$current_dir);
		while(false !== ($entry = $D->read())) {
			if ($entry != '.' && $entry != '..' && $entry != '.htaccess' && $entry != '.htpasswd') {
				$can_delete = false;
				break;
			}
		}
		$D->close();
	}

	if ($can_delete) {
		// brut force delete of possible files

		if (file_exists($up_dir.'/'.$current_dir.'.htaccess'))
		@unlink($up_dir.'/'.$current_dir.'.htaccess');
		if (file_exists($up_dir.'/'.$current_dir.'.htpasswd'))
		@unlink($up_dir.'/'.$current_dir.'.htpasswd');

		if (!rmdir($up_dir.'/'.$current_dir)) {
			$m->setError( __('Error: Impossible to delete the folder.'), 500);
		} else {
			$m->setMessage(__('Folder successfully deleted.'));
			$dir = getParentDir($current_dir);
			if (empty($dir)) $dir = '/';
			header('Location: xmedia.php?dir='.rawurlencode($dir).$vars);
			exit();
		}
	}
	
}
*/

/*=============================================================================
 Display block
 =============================================================================*/

/*=================================================
 Set title of the page, and load common top page
 =================================================*/

$px_title =  __('Files and images');

if ($mode == 'popup') {
	include dirname(__FILE__).'/mtemplates/_pop_top_jq.php';
} else {
	// sub menu
	//$px_submenu->addItem( __('Back to the list of resources'),'index.php','themes/'.$_px_theme.'/images/ico_back.png',false);

	include dirname(__FILE__).'/mtemplates/_top_jq.php';
}

echo '<h1 id="title_files">'. __('Files and images')."</h1>\n\n";

?>

<?php /*if($is_writable) : ?>

<form enctype="multipart/form-data" action="xmedia.php" method="post"
	onsubmit="return isReady('up_file','<?php echo __('Error: No file.') ?>')">
<fieldset><legend><?php   echo __('Add a file to the current folder'); ?></legend>
<p><label for="up_file"><?php  echo sprintf( __('Choose a file (maximum size %s)'), prettySize($_PX_config['max_upload_size'])); ?>
<?php echo $m->HelpLink('xmedia', 'h-add-file'); ?></label> <input
	name="up_file" id="up_file" type="file" /> <input type="hidden"
	name="MAX_FILE_SIZE"
	value="<?php echo $_PX_config['max_upload_size']; ?>" /> <input
	type="hidden" name="mode" value="<?php echo $mode; ?>" /> <input
	type="hidden" name="env" value="<?php echo $env; ?>" /> <input
	type="hidden" name="dir" value="<?php echo $current_dir; ?>" /></p>
<p><input class="submit" type="submit"
	value="<?php echo  __('Add the file'); ?>" /></p>
</fieldset>
</form>
<?php endif;?>

<h2><?php echo __('Your files'); ?></h2>

<?php
*/
/* Start populating the list of files and folders */
/*
$img_list = array();
$rep_list = array();

if ($is_dir) {
	$D = dir($up_dir.'/'.$current_dir);
	$i = 0;
	$j = 0;
	//Get the parent folder to go "up" one level.
	if (strlen($current_dir) > 0) {
		$parent_dir = getParentDir($current_dir);
		// pour les utilisateurs avancé, autorise l'accès à la racine
		if (auth::asLevel(PX_AUTH_ADVANCED) && $parent_dir == '') {
			$parent_dir = '/';
		} 

		if (!empty($parent_dir)) { 
			$rep_list[$j]['url'] = $parent_dir;	
			$rep_list[$j]['name'] = __('Parent folder');
			$rep_list[$j]['current_dir'] = $parent_dir;
			$rep_list[$j]['type'] = 'updir';
			$j++;
		}
	}
	while(false !== ($entry = $D->read()))
	{
		if($entry != '.' && $entry != '..' && $entry != '.htaccess' && $entry != '.htpasswd') {
			if (!is_dir($up_dir.'/'.$current_dir.$entry)) {
				$img_list[$i]['url'] = config::f('rel_url_files').'/'.$current_dir.$entry; 	
				$img_list[$i]['name'] = $entry;
				$img_list[$i]['current_dir'] = $current_dir;
				$img_list[$i]['type'] = 'file';
				$i++;
			} elseif ($entry != 'thumb') {
				$rep_list[$j]['url'] = config::f('rel_url_files').'/'.$current_dir.$entry;	
				$rep_list[$j]['name'] = $entry;
				$rep_list[$j]['current_dir'] = $current_dir.$entry;
				$rep_list[$j]['type'] = 'dir';
				$j++;
			}
		}
	}
	$D->close();
}
*/
//echo print_r($rep_list);
/* End populating the list of files and folders */


Hook::run('onShowDirectoryContentManager', array('m' => &$m));

/* Line to display a file */
/*
function line_file($data,$i)
{
	global $mode, $up_dir, $_PX_website_config, $m,
	$px_gd_version, $envf, $_px_theme;
	global $funcNum, $Editor, $langCode, $vars;

	//echo 'Lum::line_file';
	// 4 kinds of entry: "up" folder, folder, image and normal file
	// 2 modes: popup and normal
	$is_dir = false;
	$is_file = false;
	$is_image = false;
	$is_updir = false;
	$url = $data['url'];
	$name = $data['name'];
	$cur_dir = $data['current_dir'];

	switch ($data['type']) {
		case 'file':
			$is_image = isImage($name);
			$is_file = !$is_image;
			$ext = getFileExtension($name);
			break;
		case 'dir':
			$is_dir = true;
			break;
		case 'updir':
			$is_updir = true;
			break;
	}

	$res = ''; //final string to be displayed.

	//contains the height/width of the image or the size of
	// the file, nothing if an image and impossible to get the size
	$file_size = '';

	$create_thumb = ''; //link to create the thumbnail
	$icons_dir = config::f('manager_path').'/themes/'
	.$_px_theme.'/images/icons/';

	$delete_link = '<a  href="xmedia.php?dir='.rawurlencode($cur_dir)
	.'&amp;file='.rawurlencode($cur_dir.$name).'&amp;del=1'
	.$vars.'&amp;env='.$envf.'" title="'. __('Delete')
	.'" onclick="return window.confirm(\''
	. __('Are you sure you want to delete this file?')
	.'\')"><img src="themes/'.$_px_theme.'/images/delete.png" alt="'
	.__('Delete').'" /></a>';

	// Get size of the file in $file_size format ready to be displayed
	if ($is_image) {
		$siz = @getimagesize(config::f('xmedia_root').'/'.$cur_dir.$name);
		if ($siz !== false && 'html' == $m->user->getPref('content_format')) {
			//HTML format
			$file_size = __('<strong>size</strong>:').' '.$siz[3];
		} elseif ($siz !== false) {
			//wiki format
			$file_size = __('<strong>size</strong>:').' '.$siz[0].'x'.$siz[1];
		}
	} elseif ($is_file) {
		//size in kb
		$file_size = __('<strong>size</strong>:').' '
		.prettySize(filesize(config::f('xmedia_root').'/'
		.$cur_dir.$name));
	}

	if ($mode == 'popup' && ($is_image || $is_file)) {
		//call from the popup
		$act = ($is_image) ? 'img' : 'file';
		
		$action_link = '<a href="#" '
				.'onclick="insertUrlImage(window.opener.document,\''.$url
				.'\',\''.$act.'\',\''
				.addslashes(__('Title of the file or image:')).'\','.$funcNum.'); '
				.'window.close(); return false;">%s</a>';
				
		$action_zoom = '<a class="ui-icon-zoomin" href="'.$url.'"></a>';
	} elseif ($is_dir || $is_updir) {
		
		$slash = ($is_dir) ? '/' : '';
		$action_link = '<a href="xmedia.php?dir='.$cur_dir.$slash
				.$vars.'">%s</a>';
	} else {
		//call from the "normal" list of files page
		$action_link = '<a class="icon-zoomin" href="'.$url.'">%s</a>';
		$action_zoom = '<a class="ui-icon-zoomin" href="'.$url.'"></a>';
		//$action_link = '<a class="icon-zoomin" href="'.$url.'">Zoom</a>';

	}

	// create the thumbnail link
	if ($is_file || $is_image) {
		if (file_exists($up_dir.'/thumb/'.$cur_dir.$name.'.jpg')) {	//.md5($cur_dir.$name).'.jpg')
			//thumbnail exists
			$th =  '<img class="icon-zoomin" class="thumbnail" alt="'.$name .'" src="'
			.www::getManagedWebsiteUrl()
			.config::f('rel_url_files').'/thumb/'.$cur_dir.$name.'.jpg" alt="" />';	//.md5($cur_dir.$name)

		} elseif ($is_image && $px_gd_version) {
			//image without thumbnail
			$img=$url;
			//$th = sprintf($action_link, '<img class="icon-zoomin" width="50px" class="thumbnailicon" alt="'.$name .'" src="'.$img.'" />');
			$th = '<img class="icon-zoomin" width="50px" class="thumbnailicon" alt="'.$name .'" src="'.$img.'" />';
			//$th = $action_zoom . '<img class="thumbnailicon" alt="'.$name .'" src="'.$img.'" />';
			//$img = config::f('rel_url_files').'/thumb/'.md5($cur_dir.$name);
			//$th = sprintf($action_link, '<img class="thumbnailicon" alt="'.$name .'" src="'.$img.'" />');
			$create_thumb = '<a href="xmedia.php?dir='
						.rawurlencode($cur_dir).'&amp;file='
						.rawurlencode($cur_dir.$name).'&amp;thumb=1'
						.$vars.'&amp;env='.$envf.'" title="'
						.__('Try to create the thumbnail').'"><img src="themes/'
						.$_px_theme.'/images/ico_createthumb.png" alt="'
						.__('Try to create the thumbnail').'" /></a>';
		} else {
			//normal file
			$ext = getFileExtension($name);
			if (file_exists($icons_dir.$ext.'-dist.png')) {
				$img = 'themes/'.$_px_theme.'/images/icons/'.$ext.'-dist.png';
			} else {
				$img = 'themes/'.$_px_theme.'/images/icons/default-dist.png';
			}
			$th = sprintf($action_link,
                          '<img class="icon-zoomin" class="thumbnailicon" src="'.$img
						  .'" alt="" />');
		}
		//$action_zoom = sprintf($action_zoom,'<img class="ui-icon-zoomin" src="/plume/manager/themes/'. $_px_theme.'/images/ico_search.png" width="20px"');
		$res = '<div class="icon">'."\n"
					.'<p class="legend action">'.$action_zoom.'  ' .$create_thumb.'  '.$delete_link.'</p>'
					."\n".'<p class="icon">'.$th.'</p>'."\n".'<p class="legend">'
					.sprintf($action_link,$name).'<br />'."\n".$file_size.'</p>'."\n"
					.'</div>'."\n";
	} elseif ($is_dir || $is_updir) {
		$ico = ($is_dir) ? 'ico_folder.png' : 'ico_folder_up.png';
		$img = 'themes/'.$_px_theme.'/images/'.$ico;
		$th = sprintf($action_link,
                      '<img class="thumbnailicon" src="'.$img.'" alt="" />');
		$res = '<div class="icon">'."\n"
		.'<p class="icon">'.$th.'</p>'."\n"
		.'<p class="legend">'.$name.'</p>'."\n"
		.'</div>'."\n";

	}
	return $res;
}
*/
/*
// Display the files and folders
$img_list = array_merge($rep_list, $img_list);
$objLum = new lum($env, 'line_file', $img_list, 0, '','',6);
$m->user->savePref('xmedia_current_dir', $objLum->env, $_SESSION['website_id'], true);

$objLum->htmlHeader = '<div class="spacer"> &nbsp; </div>';
$objLum->htmlLineStart = '';
$objLum->htmlColStart = '';
$objLum->htmlColEnd = '';
$objLum->htmlLineEnd = '';
$objLum->htmlFooter = '<div class="spacer"> &nbsp; </div>';

$objLum->htmlLinksStart = '<p class="notification">';
$objLum->htmlLinksEnd = '</p>';

$objLum->htmlCurPgStart = '<strong>';
$objLum->htmlCurPgEnd = '</strong>';

$objLum->htmlPrev = __('&laquo; previous page');
$objLum->htmlNext = __('&raquo; next page');
$objLum->htmlPrevGrp = '...';
$objLum->htmlNextGrp = '...';

$objLum->htmlEmpty = '<div class="spacer"> &nbsp; </div><div class="icon"><p class="icon"><strong>'.__('No file for the moment.').'</strong></p></div><div class="spacer"> &nbsp; </div>';
$objLum->htmlLinksLib = __('page(s):');
*/
/*
if ($is_dir) {
	$parent_dir = getParentDir(config::f('rel_url_files').'/'.$current_dir);
	if (empty($parent_dir)) $parent_dir = '/';

	echo '<p class="small">';
	if (strlen($current_dir) > 0) {
		if ($m->user->getWebsiteLevel($m->user->website) >= PX_USER_LEVEL_ADVANCED) {

			echo '<a  href="xmedia.php?dir='.$rep_list[0]['current_dir'].'&amp;mode='.$mode
			.'" title="'.__('Go one folder up.').'"><img src="themes/'
			.$_px_theme.'/images/ico_folder_up_small.png" alt=" " /></a> ';


		} else {
			echo '<img src="themes/'.$_px_theme.'/images/dossier.png" width="19" height="16" alt=" " />&nbsp;';
		}
	}
	echo sprintf(__('You are in the folder <strong>%s</strong>'),
				config::f('rel_url_files').'/'.$current_dir);
	echo '</p>';
	echo $objLum->drawLinks()."\n\n";
	echo '<div id="gallery_photo">';
	echo $objLum->drawPage()."\n\n";

	echo '</div>';
	echo $objLum->drawLinks()."\n\n";

	echo '<p class="small">';
	if (strlen($current_dir) > 0) {
		if ($m->user->getWebsiteLevel($m->user->website) >= PX_USER_LEVEL_ADVANCED) {
			echo '<a href="xmedia.php?dir='.$rep_list[0]['current_dir'].'&amp;mode='.$mode
			.'" title="'.__('Go one folder up.').'"><img src="themes/'
			.$_px_theme.'/images/ico_folder_up_small.png" alt=" " /></a> ';
		} else {
			echo '<img src="themes/'.$_px_theme.'/images/dossier.png" width="19" height="16" alt=" " />&nbsp;';
		}
	}
	echo sprintf(__('You are in the folder <strong>%s</strong>'), config::f('rel_url_files').'/'.$current_dir);

	if (count($rep_list) == 1
		&& count($img_list) == 1
		&& $rep_list[0]['type'] == 'updir'
		&& $is_writable
		&& false === $m->error()
		&& auth::asLevel(PX_AUTH_ADVANCED) 	) {

		echo ' <strong><a href="xmedia.php?dir='
					.rawurlencode($current_dir).'&amp;delfolder=1&amp;mode='
					.$mode.'" '
					.'onclick="return window.confirm(\''
					.__('Are you sure you want to delete this folder?').'\')" title="'
					.__('Delete this folder').'"><img src="themes/'.$_px_theme
					.'/images/delete.png" alt=" " /></a></strong>';
	}
	echo '</p>';

	if ($is_writable &&  (false === $m->error())
			&& (auth::asLevel(PX_AUTH_ADVANCED)) ) {
		?>
<form action='xmedia.php' method='post'><input type='hidden' name='dir'
	value='<?php echo $current_dir; ?>' /> <input type='hidden' name='mode'
	value='<?php echo $mode; ?>' />
<p><span class="nowrap"><label for="new_folder" style="display: inline"><?php  echo __('Create a sub-folder'); ?>
<?php echo $m->HelpLink('xmedia', 'h-sub-folder'); ?></label> <?php echo form::textField('new_folder', 15, 30, '', '', ''); ?>
<input name="create" type="submit" class="submit"
	value="<?php  echo __('Create'); ?>" /> </span></p>
</form>
<?php

	}
}
*/
?>

<?php
if ($mode == 'popup') {
	include dirname(__FILE__).'/mtemplates/_pop_bottom.php';
} else {
	include dirname(__FILE__).'/mtemplates/_bottom.php';
}
?>