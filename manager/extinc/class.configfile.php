<?php
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
#	loïc d'Anterroches: Modification to manage a different config file format.
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
# ***** END LICENSE BLOCK *****

# Classe de gestion d'un fichier de configuration

class configfile
{
	var $file;
	var $content;
	var $prefix = '_PX_config';

	function configfile($file)
	{
		clearstatcache();
		if (file_exists($file)) {
			$this->file = $file;
			$f = fopen($file, 'r');
			$this->content = fread($f, filesize($file));
			fclose($f);
		} else {
			$this->file = false;
		}
	}

	/** édition d'une variable */
	function editVar($name,$value)
	{
		if ($this->file !== false)
		{
			if (is_array($name)) {
				$temp = '';
				reset($name);
				foreach ($name as $k => $v) {
					$temp .= '[\''.$v.'\']';
				}
				$name = $temp;
			} else {
				$name = "['$name']";
			}
			$match = '/(\$'.preg_quote($this->prefix.$name,'/').')[\s]*=[\s]*(.+);/mU';

			if (preg_match($match,$this->content))
			{
				$value = $this->exp_var($value);
				$replace = '$1 = '.$value.';';

				$this->content = preg_replace($match,$replace,$this->content);
			}
		}
	}

	/** récupération d'une variable sans évaluation (string en sortie)*/
	function getVar($name)
	{
		if ($this->file !== false)
		{
			if (is_array($name)) {
				$temp = '';
				reset($name);
				foreach ($name as $k => $v) {
					$temp .= '[\''.$v.'\']';
				}
				$name = $temp;
			} else {
				$name = "['$name']";
			}

			$match = '/(\$'.preg_quote($this->prefix.$name,'/').')[\s]*=[\s]*(.+);/mU';

			if (preg_match($match,$this->content,$res))
			{
				return trim($res[2]);
			}
		}
		return false;
	}

	/** ajouter une variable */
	function addVar($name, $value, $comment='')
	{
		if (false === $this->getVar($name))
		{
			if (is_array($name)) {
				$temp = '';
				reset($name);
				foreach ($name as $k => $v) {
					$temp .= '[\''.$v.'\']';
				}
				$name = $temp;
			} else {
				$name = "['$name']";
			}
			//the ? > at the end of the file is used as a marker
			$string = '';
			if (!empty($comment)) $string = '/* '.$comment.' */'."\n";
			$value = $this->exp_var($value);
			$string .= '$'.$this->prefix.$name.' = '.$value.';'."\n\n".'?>';
			$this->content = str_replace('?>', $string, $this->content);
			return true;
		}
		return false;
	}


	/** sauvegarde du fichier */
	function saveFile()
	{
		if (($fp = @fopen($this->file,'w')) !== false) {
			if (fwrite($fp,$this->content,strlen($this->content)) !== false) {
				$res = true;
			} else {
				$res = false;
			}
			fclose($fp);
			return $res;
		} else {
			return false;
		}
	}


	/** exportation d'une variable */
	function exp_var($var)
	{
		if (gettype($var) == 'array')
		{
			$arry_res = array();
			foreach ($var as $k => $v) {
				$arry_res[] = $k.' => '.$this->exp_var($v);
			}
			return 'array('.implode(',',$arry_res).')';
		}
		elseif (gettype($var) == 'string')
		{
			return "'".addslashes($var)."'";
		}
		elseif (gettype($var) == 'boolean')
		{
			return ($var) ? 'true' : 'false';
		}
		else
		{
			return $var;
		}
	}
}
?>
