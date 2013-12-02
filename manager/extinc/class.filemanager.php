<?php

/****************************************************/
/*  Class FileManager par Krak @ Anduriel
****************************************************/

define ("FTP_CONNECTSSL_FAILED", "<b>Erreur critique:</b> connection au serveur impossible. Vérifiez si l' OpenSSL est installé.");
define ("FTP_CONNECT_FAILED", "<b>Erreur critique:</b> connection au serveur impossible.");
define ("LOGIN_FAILED", "<b>Erreur critique:</b> impossible de se connecter, vérifiez les paramètres d'accès.");
define ("NO_FILE_UPLOADED", "<b>Erreur:</b> le fichier n'a pas été téléchargé sur le serveur.");
define ("ERR_FILESIZEINI", "<b>Erreur:</b> la taille d'un des fichiers dépasse la limite fixée dans votre php.ini.");
define ("ERR_FILESIZE", "<b>Erreur:</b> la taille d'un des fichiers dépasse la limite fixé par le formulaire.");
define ("ERR_PARTIALDL", "<b>Erreur:</b> un des fichiers a été téléchargé partiellement dans le dossier temporaire.");
define ("ERR_NOFILEDL", "<b>Erreur:</b> un des fichiers n'a pas été téléchargé dans le dossier temporaire");
define ("ERR_NOFOLDER", "<b>Erreur:</b> dossier temporaire introuvable.");
define ("ERR_CANTWRITE", "<b>Erreur:</b> impossible d'écrire le fichier dans le dossier temporaire.");
define ("CANT_CHANGEDIR", "<b>Erreur:</b> impossible d'entrer dans le dossier ");
define ("CANT_CREATEDIR", "<b>Erreur:</b> impossible de créer le dossier demandé.");
define ("SSL_NOTINSTALLED", "<b>Erreur:</b> l'ouverture sécurisée SSL n'est pas disponible.");
define ("CANT_DELETEFILE", "<b>Erreur:</b> impossible de supprimer le fichier spécifié.");
define ("INVALID_FILE", "<b>Erreur:</b> fichier spécifié invalide ou introuvable.");
define ("NO_DELETE", "<b>Erreur:</b> fichier non supprimé. Il se peut qu'aucun dossier n'ait été spécifié.");
define ("CANT_CHMOD", "<b>Erreur:</b> impossible de changer le chmod du dossier/fichier spécifié.");
define ("CANT_RENAME", "<b>Erreur:</b> impossible de changer le nom du dossier spécifié.");
define ("CANT_LIST", "<b>Erreur:</b> impossible de lister le répertoire.");

class FileManager  {

	var $envars;
	var $openftp;
	var $file_destination = '.';
	var $force;

	/************************************************************************/
	/* Function d'enregistrement des variables
	 /* setftpvars(urlserver, accessname, acesspass [, mode])
	 /*  - urlserver = url du ftp sans www et sans slasch (ftpperso.free.fr)
	 /*  - accessname = pseudo de connection
	 /*  - accessspass = mot de passe de connection
	 /*  - mode = Tranfert des fichiers: ASCII ou BINARY
	 /***********************************************************************/
	function setftpvars($server, $username, $userpass, $mode="ASCII") {
		$this->envars['ftp_server'] = $server;
		$this->envars['ftp_pass'] = $userpass;
		$this->envars['ftp_login'] = $username;
		$this->envars['ftp_transfert'] = constant('FTP_'.$mode);
	}
	/************************************************************************/
	/* Se place dans un dossier spécifique
	 /* setftpdir(dir, force)
	 /*  - dir = dossier dans lequel charger les fichiers
	 /*  - force = crée le dossier s'il est inexistant
	 /***********************************************************************/
	function setftpdir($dir, $force=false) {
		$this->file_destination = $dir;
		$this->force = $force;
	}
	/************************************************************************/
	/* Créé un dossier
	 /* createdir(dir [, chmod] [, dest])
	 /*  - dir = dossier à crée
	 /*  - chmod = droit d'accès au dossier. Par défaut 0777
	 /*  - dest = true pour créer sur un serveur distant
	 /***********************************************************************/
	function createdir($dir, $chmod=0777, $dest=false) {
		if (!is_dir($dir) && $dest == false) {
			@mkdir($dir, $chmod) or die (CANT_CREATDIR);
		}
		elseif (!is_dir($dir) && $dest != false) {
			@ftp_mkdir($this->openftp, $dir) or die (CANT_CREATDIR);
		}
	}

	public static function recur_mkdir($path)
	{
		$path = preg_split('<([/\\])>', $path, -1, PREG_SPLIT_DELIM_CAPTURE);
		$curr = '';
		for($i = 0; $i < count($path); $i += 2)
		{
			$curr .= $path[$i].$path[$i+1];
			if(!is_dir($curr) && !mkdir($curr)) return false;
		}
		return true;
	}
	/************************************************************************/
	/* Function de connection à un serveur distant
	 /* opensslftp(openmode)
	 /*  - openmode = true pour une ouverture sécurisée SSL
	 /***********************************************************************/
	function openftp($sslopen=false) {
		if ($sslopen == true) {
			if (!function_exists("ftp_ssl_connect")) {
				echo SSL_NOTINSTALLED;
				exit;
			}
			if (!$this->openftp = @ftp_ssl_connect($this->envars['ftp_server'])) {
				echo FTP_CONNECTSSL_FAILED;
				exit;
			}
		} else {
			if (!$this->openftp = @ftp_connect($this->envars['ftp_server'])) {
				echo FTP_CONNECT_FAILED;
				exit;
			}
		}
		@ftp_login($this->openftp, $this->envars['ftp_login'], $this->envars['ftp_pass']) or die (LOGIN_FAILED);
	}
	/************************************************************************/
	/* Charge les fichiers issus d'un formulaire
	 /* uploadfiles(input, destination)
	 /*  - input = nom du champ de type file. Il peut être un tableau.
	 /*  - destination = true pour les charger sur un serveur distant
	 /***********************************************************************/
	function uploadfiles($inputname="", $specialftp="") {
		if (!empty($_FILES[$inputname])) {
			foreach($_FILES[$inputname]['name'] As $key => $file) {
				if ($file != '') {
					$lastfile = ' (Fichier concerné: <b>'.htmlspecialchars($file).'</b>)<br>';
					switch($_FILES[$inputname]['error'][$key]) {
						case 1:
							echo ERR_FILESIZEINI.$lastfile;
							exit;
						case 2:
							echo ERR_FILESIZE.$lastfile;
							exit;
						case 3:
							echo ERR_PARTIALDL.$lastfile;
							exit;
						case 4:
							echo ERR_NOFILEDL.$lastfile;
							exit;
						case 6:
							echo ERR_NOFOLDER.$lastfile;
							exit;
						case 7:
							echo ERR_CANTWRITE.$lastfile;
							exit;
						default: break;
					}
					$filename = basename($_FILES[$inputname]['name'][$key]);
					if (empty($specialftp)) {
						if ($this->force == true) $this->createdir($this->file_destination);
						if (!$is_moved = @move_uploaded_file($_FILES[$inputname]['tmp_name'][$key], 
								$this->file_destination.'/'.$filename)) {
						echo NO_FILE_UPLOADED.$lastfile;
}
					} else {
						$fp = fopen($_FILES[$inputname]['tmp_name'][$key], 'r');
						if ($this->force == true) $this->createdir($this->file_destination, 0777, true);
						@ftp_chdir($this->openftp, $this->file_destination) or die(CANT_CHANGEDIR.$this->file_destination);
						if (!$is_moved = @ftp_fput($this->openftp, $filename, $fp, $this->envars['ftp_transfert'])) {
							echo NO_FILE_UPLOADED.$lastfile;
						}
						else {
							fclose($fp);
						}
					}
				}
			}
		}
	}
	/************************************************************************/
	/* Supprime un fichier ou des fichiers
	 /* deletetfile(file [, destination]])
	 /*  - file = fichier à supprimer. Pour plusieurs fichiers metter un tableau
	 /*    Ce fichier sera supprimé du dossier setftpdir() si le dossier n'est pas
	 spécifié
	 /*    dans un tableau:
	 /*    => array('file1.txt' => 'dir/dir', 'file2.exe' => '../dir2', 'file3.html
	 ')
	 /*  - destination = true pour les supprimer d'un serveur distant
	 /***********************************************************************/
	function deletefile($file, $specialftp="") {
		$lastfile = (!is_array($file)) ? ' (Fichier concerné: <b>'.htmlspecialchars($file).'</b>)<br>' : ' (Tableau de fichier)';
		if (!is_array($file) && is_file($this->file_destination.'/'.$file)) {
			if (empty($specialftp)) {
				return @unlink($this->file_destination.'/'.$file) or die (CANT_DELETEFILE.$lastfile);
			} else {
				return @ftp_delete($this->openftp, $this->file_destination.'/'.$file) or
							die (CANT_DELETEFILE.$lastfile);
			}
		}
		elseif (is_array($file)) {
			foreach($file as $dir => $to_delete) {
				$lastfile = ' (Fichier concerné: <b>'.htmlspecialchars($to_delete).'</b>)<br>';
				$dir = (is_numeric($dir)) ? $this->file_destination : $dir;
				if (is_file($dir.'/'.$to_delete)) {
					if (empty($specialftp)) {
						@unlink($dir.'/'.$to_delete) or die (CANT_DELETEFILE.$lastfile);
					} else {
						@ftp_delete($this->openftp, $dir.'/'.$to_delete) or die (CANT_DELETEFILE.$lastfile);
					}
				} else {
					echo INVALID_FILE.$lastfile;
				}
			}
		}
		else {
			echo NO_DELETE.$lastfile;
		}
	}
	/************************************************************************/
	/* Change le Chmod d'un dossier ou d'un fichier
	 /* changechmoddir(dir, chmod, destination)
	 /*  - dir = dossier dont le chmod est à modifier
	 /*  - chmod = droit d'accès au dossier.
	 /*  - destination = true si le fichier ou dossier est sur un serveur distant
	 /***********************************************************************/
	function changechmod($filedir, $chmod, $specialftp="") {
		if (empty($specialftp)) {
			@chmod($filedir, $chmod) or die (CANT_CHMOD);
		}
		else {
			@ftp_chmod($this->openftp, $filedir, $chmod) or die (CANT_CHMOD);
		}
	}
	/************************************************************************/
	/* Renomme un dossier/fichier
	 /* changenamedir(dirfile, newname, destination)
	 /*  - dirfile = dossier/fichier dont le nm est à modifier
	 /*  - newname = nouveau nom
	 /*  - destination = true si le fichier ou dossier est sur un serveur distant
	 /***********************************************************************/
	function changename($dirfile, $name, $specialftp="") {
		if (empty($specialftp)) {
			@rename($dirfile, $name) or die (CANT_RENAME);
		}
		else {
			@ftp_rename($this->openftp, $dirfile, $name) or die (CANT_RENAME);
		}
	}

	/************************************************************************/
	/* Liste un répertoire
	 /* listrepertory(repertory, destination)
	 /*  - destination = true si le dossier est sur un serveur distant
	 /***********************************************************************/
	function listrepertory($repertory, $specialftp="") {
		if (empty($specialftp)) {
			if ($handle = @opendir($repertory)) {
				while (false !== ($file = @readdir($handle))) {
					if ($file != "." && $file != "..") {
						if (is_file($file)) $prefix = ' (fichier)';
						if (is_dir($file)) $prefix = ' (dossier)';
						echo '- '.$file.$prefix.'<br>';
					}
				}
				closedir($handle);
			} else echo CANT_LIST;
		}
		else {
			$contents = ftp_nlist($this->openftp, '.');
			var_dump($contents);
			foreach ($contents AS $file) {
				if (is_file($file)) $prefix = 'Type: fichier, nom: ';
				if (is_dir($file)) $prefix = 'Type: dossier, nom: ';
				echo $prefix.$file.'<br>';
			}
		}
	}
	
	function listRepertoryArray($repertory, $specialftp="") {
		$dirList = array();
		$i=0;
		if (empty($specialftp)) {
			if ($handle = @opendir($repertory)) {
				while (false !== ($file = @readdir($handle))) {
					if ($file != "." && $file != ".." && substr($file,0,1) != '.' ) {
						if (is_file($file)) $prefix = 'file';
						if (is_dir($file)) $prefix = 'dir';
						//echo '- '.$file.$prefix.'<br>';
						$dirList[$i]['name'] = $file;
						$dirList[$i]['current_dir'] = $repertory;
						$dirList[$i]['type']= $prefix;
						$i++;
					} else if ($file == '..') {
						$dirList[$i]['name'] = $file;
						$dirList[$i]['current_dir'] = __('Parent folder');
						$dirList[$i]['type']= 'dir';
						$i++;
					}
				}
				closedir($handle);
			} else return false;
		} else {
			$contents = ftp_nlist($this->openftp, '.');
			var_dump($contents);
			foreach ($contents AS $file) {
				if (is_file($file)) $prefix = 'Type: fichier, nom: ';
				if (is_dir($file)) $prefix = 'Type: dossier, nom: ';
				//echo $prefix.$file.'<br>';
				$dirList[$i]['name'] = $file;
				$dirList[$i]['current_dir'] = $repertory;
				$dirList[$i]['type']= $prefix;
				$i++;
			}
		}
		return $dirList;
	}
	
}
?>