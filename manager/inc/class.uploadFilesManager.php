<?php

require_once 'path.php';
require_once dirname(__FILE__).'/../prepend.php';
require_once config::f('manager_path').'/extinc/class.filemanager.php';

class uploadFilesManager extends FileManager  {
	
	function FileManager()  {
		
	} 
	
	function verifyDirectory($dirPath)  {
		if (!empty($dirPath)) {
			$dirPath = str_replace('\\','',$dirPath);
			$dirPath = str_replace('..','',$dirPath);
			$dirPath = preg_replace( '#[^A-Za-z0-9\-\_\/]#', '', $dirPath);
			$dirPath = preg_replace( '#(/)+#', '/', $dirPath);
			$dirPath = preg_replace( '#^(/)+#', '', $dirPath);
			$dirPath = preg_replace( '#(/)+$#', '', $dirPath);
			if (!empty($dirPath))
			$dirPath .= '/';
		} else {
			$dirPath = '';
		}
		return $dirPath;
	}
	
	function readDirectory($dirPath)  {
		$liste = $this->listRepertoryArray($dirPath);
	}
	
	function createDirectory($dirPath,$new_dir )  {
		
		if (!empty($new_dir)) {
			$m = & new Manager();
			
			$new_dir = trim($new_dir);
			if (preg_match('/[^A-Za-z0-9\-\_]/', $new_dir) or ($new_dir == 'thumb')) {
				$m->setError( __('Error: Invalid folder name, it must contain only letters, digits, "_" and "-".'), 400);
			} else {
				if (@mkdir($dirPath.$new_dir, 0777,true)) {
					$m->setMessage(__('The folder was successfully created, you can already add files and images in.'));
					//header('Location: xmedia.php?dir='.rawurlencode($dirPath.$new_dir).'&mode='.$mode);
					//exit();
				} else {
					$m->setError( __('Error: Impossible to create the new folder.'), 500);
				}
	
			}
		}		
	}
	
	function deleteFile($dirPath, $fileName)  {
		$can_delete = (strlen(fileName) != 0) ? true : false;
		if ( $can_delete)  {
			if (file_exists($dir_Path.$fileName)) {
				return @unlink($dirPath.'/'.$fileName);
			}
		}
	}
	
	function deleteDirectory($dirPath)  {
		
		//$can_delete = true;
		$can_delete = (strlen($dirPath) != 0) ? true : false;
		// scan if one file exist
		if ($is_dir && $can_delete) {
			$D = dir($up_dir.'/'.$dirPath);
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
	
			if (file_exists($up_dir.'/'.$dirPath.'.htaccess'))
			@unlink($up_dir.'/'.$dirPath.'.htaccess');
			if (file_exists($up_dir.'/'.$dirPath.'.htpasswd'))
			@unlink($up_dir.'/'.$dirPath.'.htpasswd');
	
			if (!rmdir($up_dir.'/'.$dirPath)) {
				$m->setError( __('Error: Impossible to delete the folder.'), 500);
			} else {
				$m->setMessage(__('Folder successfully deleted.'));
				$dir = getParentDir($dirPath);
				if (empty($dir)) $dir = '/';
				//header('Location: xmedia.php?dir='.rawurlencode($dir).'&mode='.$mode);
				//exit();
			}
		}		
	}
	
	
	
	function checkRights($dirPath) {
		$is_writable= false;
		$is_dir = true;
		$m = & new Manager();
		// c'est un dossier
		if (is_dir($dirPath)) {
			//avec les droits
			if (is_writable($dirPath)) {
				$is_writable = true;
			} else {
				$m->setError(sprintf(__('Error: The system has no write access to the folder <strong>%s</strong>. Check the permissions.'), $dirPath), 500);
			}
		} else {
			// on tente de créer le dossier avec les droits
			$dir = substr($dirPath, 0,strlen($dirPath)-1);
			if (false === @mkdir($dir,0777,true))  {
				$m->setError(sprintf(__('Error: The folder <strong>%s</strong> does not exist.'), $dirPath), 500);
				$is_dir = false;
			} else {
				$is_writable = true;
			}
		}
		return ($is_dir && $is_writable);		
	}
	
	
	function createThumbnail($fileName, $dirPath='')  {
		$px_file = str_replace('..','',$fileName);
		$px_file = preg_replace( '#(/)+#', '/', $px_file);
		$px_file = preg_replace( '#^(/)+#', '', $px_file);
		
		$m = & new Manager();
		
		if (!file_exists($dirPath.'/'.$px_file)) {
			$m->setError(__('Error: The requested file does not exist.'), 400);
		} else {
			if (!empty($_GET['del']) && $_GET['del'] == 1) {
				if (@unlink($dirPath.'/'.$px_file) === false) {
					$m->setError(__('Error: Impossible to delete the file.'), 500);
				} else {
					@unlink($dirPath.'/thumb/'.md5($px_file).'.jpg');
					$m->setMessage(__('File deleted with success.'));
					header('Location: xmedia.php?dir='.rawurlencode($current_dir).'&mode='.$mode);
					exit();
				}
			} elseif(!empty($_GET['thumb']) && $_GET['thumb'] == 1) {
				$thumb = $dirPath.'/thumb/'.md5($px_file).'.jpg';
				@$thumb = cropImg($dirPath.'/'.$px_file, $thumb, 150, 100);
			}
		}
	}
	
	function uploadFile($dirPath,$fileNames)  {
		$tmp_file = $fileNames['tmp_name'];	//$_FILES['up_file']['tmp_name']
		$file_name = $fileNames['name'];	//$_FILES['up_file']['name']
	
		$m = & new Manager();
		
		if (version_compare(phpversion(),'4.2.0','>=')) {
			$upd_error = $fileNames['error'];		//$_FILES['up_file']['error']
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
			if(!isFileSafe($file_name)) {
				$m->setError(__('Error: Not an authorized type of file. The file name must contain only simple letters, digits, "-", "_" and a recognized extension. No spaces allowed.'), 400);
			} else {
				if (@copy($tmp_file,$dirPath.$file_name)) {
					@chmod($dirPath.$file_name, 0666);
					/* create thumb */
					$thumb = $dirPath.'/thumb/'.$file_name.'.jpg';
					@unlink($thumb); //ensure that the old thumbnail is removed if available
					@$msg=cropImg($dirPath.$file_name, $thumb, 150, 100);
					@chmod($thumb, 0666);
	
					$m->setMessage(__('File successfully uploaded.'));
					//header('Location: xmedia.php?dir='.rawurlencode($dirPath).'&mode='.$mode.'&env='.$env);
					//exit();
				} else {
					$m->setError(__('An error occured during the transfer of the file.'), 500);
				}
			}
		}
			
	}
	
// End of class	
}


?>