<?php
/*
# ***** BEGIN LICENSE BLOCK *****
# Version: MPL 1.1/GPL 2.0/LGPL 2.1
#
# The contents of this file are subject to the Mozilla Public License Version
# 1.1 (the "License"); you may not use this file except in compliance with
# the License. You may obtain a copy of the License at
# http://www.mozilla.org/MPL/
#
# Software distributed under the License is distributed on an "AS IS" basis,
# WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
# for the specific language governing rights and limitations under the
# License.
#
# The Original Code is DotClear Weblog.
#
# The Initial Developer of the Original Code is
# Olivier Meunier.
# Portions created by the Initial Developer are Copyright (C) 2003
# the Initial Developer. All Rights Reserved.
#
# Contributor(s):
#	Extended by loic d'Anterroches
#
# Alternatively, the contents of this file may be used under the terms of
# either the GNU General Public License Version 2 or later (the "GPL"), or
# the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
# in which case the provisions of the GPL or the LGPL are applicable instead
# of those above. If you wish to allow use of your version of this file only
# under the terms of either the GPL or the LGPL, and not to allow others to
# use your version of this file under the terms of the MPL, indicate your
# decision by deleting the provisions above and replace them with the notice
# and other provisions required by the GPL or the LGPL. If you do not delete
# the provisions above, a recipient may use your version of this file under
# the terms of any one of the MPL, the GPL or the LGPL.
#
# ***** END LICENSE BLOCK ***** */

/*
Classe de gestion des plugins et des th�mes
*/

class plugins extends CError
{
    var $name = '';
    var $list = array(); //simple list of plugins, used to do a batch load
    			    //of the lang files.

	function plugins($location)
	{

        if (is_dir($location)) {
			$this->location = $location.'/';
		} else {
			$this->location = NULL;
		}
	}
	
	/*
	Obtenir les plugins et les infos de ces plugins
	*/
	function getPlugins($root='plugin',$active_only=true)
	{
		include_once dirname(__FILE__).'/../inc/class.l10n.php';
		$l = new l10n(config::f('locale_lang'));
		$res = $tmp = array();

		if (($list_files = $this->_readDir()) !== false)
		{
			foreach ($this->list as $pl) {
				$l->loadPlugin(config::f('locale_lang'), $pl);
			}
			foreach ($list_files as $entry => $pfile)
			{
				$desc = implode('',file($pfile));
				
				if (preg_match('/<'.$root.'(.*)>(.*)<\/'.$root.'>/msU',$desc,$matches))
				{	
					$this->_xmlParser($matches[1],$matches[2],$tmp[$entry],$active_only);
				}
			}
			
			/* On supprime les �l�ments NULL */
			foreach ($tmp as $k => $v) {
				if (is_array($v)) {
					$res[$k] = $v;
				}
			}
			ksort($res);
			return $res;
		}
		else
		{
			return false;
		}
	}
	
	/* Installation d'un plugin */
	function install($url)
	{
		$dest = $this->location.'/'.basename($url);
        $this->name = basename($url,'.ext.gz');
        
		if (($err = $this->_copyRemote($url,$dest)) !== true)
		{
			return $err;
		}
		else
		{
            if (!function_exists('gzfile')) {
                return  __('The automatic install is not available on your system, do the manual install.');
            }
			if (($content = @implode('',@gzfile($dest))) === false) {
                return  __('Impossible to open the file');
			} else {
				if (($list = unserialize($content)) === false)
				{
                    return  __('Invalid plugin');
				}
				else
				{
					if (is_dir($this->location.'/'.$list['name']))
					{
						if ($this->_deldir($this->location.'/'.$list['name']) === false)
						{
                            return  __('Impossible to delete the existing plugin');
						}
					}
					
					foreach ($list['dirs'] as $d)
					{
						$d = str_replace('..','',$d);
                        mkdir ($this->location.'/'.$d);
						@chmod($this->location.'/'.$d,0777);
					}
					
					foreach ($list['files'] as $f => $v)
					{
						$f = str_replace('..','',$f);
                        $v = base64_decode($v);
                        if (preg_match('/[A-Za-Z]{2}\_[A-Za-Z]{2}/',$f)) {
                            if (!file_exists(dirname(__FILE__).'/../locale/'.$f)) {
						        mkdir (dirname(__FILE__).'/../locale/'.$f);
						        @chmod(dirname(__FILE__).'/../locale/'.$f,0777);                                
                            }
     						$fp = fopen(dirname(__FILE__).'/../locale/'.$f.'/'.$this->name.'.php','w');
    						fwrite($fp,$v,strlen($v));
    						fclose($fp);
                        } else {
    						$fp = fopen($this->location.'/'.$f,'w');
    						fwrite($fp,$v,strlen($v));
    						fclose($fp);
                        }
					}

					unlink ($dest);
				}
			}
		}
		return true;
	}

	/* Lecture d'un répertoire à la recherche des desc.xml */
	function _readDir()
	{
		if ($this->location === NULL) {
			return false;
		}

		$res = array();

		$d = dir($this->location);

		# Liste du r�pertoire des plugins
		while (($entry = $d->read()) !== false)
		{
			if ($entry != '.' && $entry != '..' &&
			is_dir($this->location.$entry) && file_exists($this->location.$entry.'/desc.xml'))
			{
				$res[$entry] = $this->location.$entry.'/desc.xml';
				$this->list[] = $entry;
			}
		}

		return $res;
	}

	/* Analyse des information plugin/theme */
	function _xmlParser($attr,$content,&$res,$active_only=true)
	{
		# Vérification du nom
		if (preg_match('/name="(.+)"/msU',$attr,$name))
		{
			# Actif
			if (preg_match('/active="(true|yes|1)"/msU',$attr)) {
				$active = true;
			} else {
				$active = false;
			}

			if (!$active && $active_only) {
				return true;
			}

			$res['active'] = $active;

			# Nom
			$res['name'] = __(trim($name[1]));

            # Root only
			if (preg_match('/rootonly="(true|yes|1)"/msUi',$attr,$rootonly)) {
				$res['rootonly'] = true;
			} else {
                $res['rootonly'] = false;
            }

			# Version
			if (preg_match('/version="(.*)"/msU',$attr,$version)) {
				$res['version'] = trim($version[1]);
			}

			# Auteur
			if (preg_match('/<author>(.+)<\/author>/msU',$content,$author)) {
				$res['author'] = trim($author[1]);
			}

            # Label
			if (preg_match('/<label>(.+)<\/label>/msU',$content,$label)) {
				$res['label'] = __(trim($label[1]));
			}

			# Description
			if (preg_match('/<desc>(.+)<\/desc>/msU',$content,$description)) {
				$res['desc'] = __(trim($description[1]));
			}

		}
	}
	
	# Copier d'un fichier binaire distant
	function _copyRemote($src,$dest)
	{
		if (($fp1 = @fopen($src,'r')) === false)
		{
            return  __('An error occured during the download of the file.');
		}
		else
		{
			if (($fp2 = @fopen($dest,'w')) === false)
			{
				fclose($fp1);
				return  __('An error occured when writing the file.');
			}
			else
			{
				while (($buffer = fgetc($fp1)) !== false) {
					fwrite($fp2,$buffer);
				}
				fclose($fp1);
				fclose($fp2);
				return true;
			}
		}
	}

	function _deldir($dir)
	{
		$current_dir = opendir($dir);
		while($entryname = readdir($current_dir))
		{
			if(is_dir($dir.'/'.$entryname) and ($entryname != '.' and $entryname!='..'))
			{
				$this->_deldir($dir.'/'.$entryname);
			}
			elseif($entryname != '.' and $entryname!='..')
			{
				if (@unlink($dir.'/'.$entryname) === false) {
					return false;
				}
			}
		}
		closedir($current_dir);
		if (@rmdir($dir) === false) {
			return false;
		}
	}
}
?>
