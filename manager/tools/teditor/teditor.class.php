<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Teditor, plugin for Plume CMS
# Copyright (C) 2004-2006 Gilles ACCAD.
#
# Teditor is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# Teditor is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

 if (basename($_SERVER['SCRIPT_NAME']) == 'teditor.class.php') exit;

class teditor {
// Tableaux des fichiers
	var $arry_templates_c;
	var $arry_templates_r;
	var $arry_templates_a;
	var $arry_templates_o;
	var $arry_css_f;
// variables de configuration
	var $px_pedition_height;
	var $px_pedition_style;
	var $px_psource_style;
	var $px_pedition_style_width;
	var $px_pedition_style_color;
	var $px_pedition_style_backg;
	var $px_psource_style_nblinecolor;
	var $px_psource_style_nblinebgcolor;
	var $px_pcss_folder;
	var $px_theme_folder;

	//Constructeur
	//initialise les propriétés
	function teditor() {
	global $m, $_PX_pconfig, $_PX_config, $_PX_website_config;

		$this->px_theme_folder = $_PX_config['manager_path'].'/templates/'.$_PX_website_config['theme_id'].'/';
		$this->px_pedition_height = $_PX_pconfig['edition_height'];
		$this->px_pedition_style = $_PX_pconfig['edition_style'];
		$this->px_psource_style = $_PX_pconfig['source-nb_style'];
		$this->px_pedition_style_width = $_PX_pconfig['edition_style']['width'];
		$this->px_pedition_style_color = $_PX_pconfig['edition_style']['color'];
		$this->px_pedition_style_backg = $_PX_pconfig['edition_style']['background'];
		$this->px_psource_style_nblinecolor = $_PX_pconfig['source-nb_style']['color'];
		$this->px_psource_style_nblinebgcolor = $_PX_pconfig['source-nb_style']['background'];
		$this->px_pcss_folder = $_PX_pconfig['css_folder'];
		$this->arry_templates_c = $m->getTemplates('category');
		$this->arry_templates_r = $m->getTemplates('resource');
		$this->arry_templates_cm = $m->getTemplates('comments');		
		$this->arry_templates_a = $m->getTemplates();
		$this->arry_templates_o = array_filter($this->arry_templates_a, array($this, "trie"));
		$this->arry_css_f = $this->getCSS();
	
	}
	
	/** Private
	*
	*/
	// Tri pour extraction des "autres gabarits"
	function trie($var) {
		return ((strpos($var,"resource") === 0) || (strpos($var,"category") === 0) || (strpos($var,"comments") === 0)) ?	false : $var;
	}

	// Recuperation de la liste des fichiers CSS
	function getCSS(){
	$arry_css_files = array();
		if (is_dir($this->px_pcss_folder)) {
			$scan_dir = dir($this->px_pcss_folder);
				while ($file = $scan_dir->read()) {
					if (substr($file,-4,4) == '.css') {
						$arry_css_files[$file] = $file;
					}
				}
			return $arry_css_files;		
		} else {
			return false;
		}
	}
	
	// Definition du chemin vers gabarit a editer
	function getPath($elem){
		(empty($elem)) ? $path_id=$this->px_theme_folder  : $path_id=$this->px_pcss_folder;
		return $path_id;
	}
	
	// Construction de l'URL pour formulaires et header()
	function makeURL() {
		parse_str($_SERVER['QUERY_STRING'], $param);
		
		if (strpos($_SERVER['QUERY_STRING'], "&w=")) { 
			$self = basename($_SERVER['PHP_SELF']).'?p='.$param['p'].'&w='.$param['w'];
		} else {
			$self = basename($_SERVER['PHP_SELF']).'?p='.$param['p'];
		}
		
		return $self;
	}
	
	// Affichage de la liste des templates et CSS
	function listTemplates($template, $type='') {
		global $m;
		
		if (is_array($template) && !empty($template)){
			$tmp  = '';
			$path = $this->getPath($type);
			
			switch(substr($template[key($template)], 0, 3)) {
				case "cat":
					$w = "categ";
					break;
				case "res":
					$w = "res";
					break;
				case "com":
				    $w = "com";
				    break;					
				case "404":	
				case "sea":	
					$w = "other";
					break;				
				default	:
					$w = "css";
			}
		
			if (file_exists($path.$template[key($template)])) {

				$tmp .= '<table style="width:100%;margin:auto;text-align:center;border:1px solid" >'."\n\t"
						.'<caption style="text-align:right;font-style:italic;color:grey">'. __('In:&nbsp;') . $path .'</caption>'				
						.'<tr style="background-color:gray;color:khaki">'."\n\t\t"
						.'<th style="width:30%">'.__('File name')."</th>\n\t\t"
						.'<th style="width:10%">'.__('Size')."</th>\n\t\t"
						.'<th>'.__('Last modification date')."</th>\n\t\t"
						.'<th>'.__('Vizualisation')."</th>\n\t\t"
						.'<th>'.__('Edition')."</th>\n\t\t"
						.'<th>'.__('Suppression')."</th>\n\t"
						."</tr>\n";

				$i = 0;
				foreach ($template as $val) {
				    // Coloration d'une rangee sur deux
					if (($i++) % 2) {
					   $tmp .= "\t".'<tr>'."\n\t\t";
					} else {
					   $tmp .= "\t".'<tr style="background-color:#D9D9F3">'."\n\t\t";
					}
					$format = "%A %e %B %Y";
					if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
						$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
					}
					$tmp .= '<td><strong>'.$val."</strong></td>\n\t\t"
						.'<td>'.prettySize(filesize($path.$val))."</td>\n\t\t";
					$tmp .= '<td>'.strftime ($format, filemtime($path.$val))."</td>\n\t\t";
					$tmp .= '<td><form style="margin:0;padding:0" action="'.$this->makeURL().'&amp;w='.$w.'" method="post" >'."\n\t\t"
						.'<input style="border:none" type="image" src="tools/teditor/themes/'. $m->user->website .'/view.png" name="c_visualiser" id="c_visualiser" value="1" /></td>'."\n\t\t"
						.'<td><input style="border:none" type="image" src="http://'. $_SERVER['HTTP_HOST'] .$GLOBALS['_PX_website_config']['rel_url'].'/manager/themes/'. $m->user->website .'/images/ico_article.png" name="b_template" id="b_template" value="1" />'."\n\t\t"
						.form::hidden('c_template',$val)."</form></td>\n\t"		
						.'<td><form style="margin:0;padding:0" action="'.$this->makeURL().'" method="post" onsubmit="return window.confirm(\''. __('Are you sure you want to delete this file?').'\')">'."\n\t\t"
						.'<input style="border:none" type="image" src="http://'. $_SERVER['HTTP_HOST'] .$GLOBALS['_PX_website_config']['rel_url'].'/manager/themes/'. $m->user->website .'/images/delete.png" name="b_tdelete" id="b_tdelete" value="del" />'."\n\t\t"							
						.form::hidden('c_delete',$path.$val)."\n\t\t"
						."</form></td>\n\t"
						."</tr>\n";
				}			
				
				$tmp .= "</table>\n";
				return $tmp;
			}
		} else {
			return false;
		}
	}
	// Edition template/css
	function frmEditTemplate($file,$type='') {
	global $m;
	
		if ($fileContent = $this->returnFile($file,$type)) {
			$style = $this->getStyle($this->px_pedition_style); 
			//$style='';
			//$fileContent = highlight_string($fileContent,true);
			Hook::run('onCodeHighLigthManager', array('m' => &$m));
			$tmp='';
			//$tmp  = '<script type="text/javascript" src="tools/teditor/functions.js"></script>'."\n";			
			//$tmp .= '<p style="border:1px outset">';
			//$tmp .= '<img src="tools/teditor/themes/'. $m->user->website .'/text_indent.png" alt="'.__('Insert tabulation').'" title="'.__('Insert tabulation').'" onmousedown="btTab(); return false" style="margin:2px;padding:0;vertical-align:bottom" />'."\n";
			//$tmp .= '<img src="tools/teditor/themes/'. $m->user->website .'/select_all.png" alt="'.__('Select all').'" title="'.__('Select all').'" onmousedown="document.getElementById(\'t_content\').select(); return false" style="margin:2px;padding:0;vertical-align:bottom" /></p>'."\n";			
			$tmp .= '<form id="frmEditTemplate" action="'.$this->makeURL().'" method="post">'."\n";
			$tmp .= form::textArea('t_content',60,$this->px_pedition_height,$fileContent,'',$style.' class="codemirror_content '.$type.'"');
			$tmp .= form::button('submit','b_saved',__('Save'),'','','submit');
			$tmp .= form::hidden('h_template',$file);
			$tmp .= "</form>\n";
			return $tmp;
		}
	}
	//Formulaire de creation de gabarit
	function frmCreateTemplate() {
		$arry_filetype = array(__('Resource')=>0,__('Category')=>1,__('Comment')=>2,__('Others')=>3,__('Css')=>4);
		
		$tmp  = '<form action="'.$this->makeURL().'" method="post" style="clear:both">'."\n";
		$tmp .= '<p><fieldset style="border-color:red;"><legend>'. __('Creation').'</legend>'."\n\n";
		$tmp .='<label for="c_tcreation" style="display:inline"><strong>'. __('Name of the new file:').'</strong></label>'."\n";
		$tmp .= form::textField('c_tcreation', 30, 55,'','','class="codemirror_content"')."\n";	
		$tmp .= '<label for="c_filetype" style="display:inline;margin-left:2em"><strong>'. __('Type of the file:').'</strong></label>'."\n";
		$tmp .= form::combobox('c_filetype', $arry_filetype, '','','','','style="margin-right:2em"');			
		$tmp .= '<input type="submit" class="submit" style="padding:0 3em" value="'.__('Save').'" id="b_tcreation" name="b_tcreation" /></fieldset></p>';
		$tmp .= "</form>\n";
		
		return $tmp;
	}		
	
	//Formulaire de configuration de l'interface
	function frmShowConfig(){
		$tmp = '<form action="'.$this->makeURL().'" method="post">'."\n";
		
		$tmp .= '<fieldset style="margin-bottom:1em"><legend>'. __('Edition area').'</legend>'."\n\n";
		$tmp .= '<p class="field"><label class="float" for="c_edition_height" style="display:inline;position:static"><strong>'. __('Height (number of lines):').'</strong></label>'."\n";
		$tmp .=  form::textField('c_edition_height', 5, 2, $this->px_pedition_height)."</p>\n";  
		$tmp .='<p class="field"><label class="float" for="c_edition_style_width" style="display:inline;position:static"><strong>'. __('Area Width:').'</strong><sup>*</sup></label>'."\n";    
		$tmp .=  form::textField('c_edition_style_width', 10, 10, $this->px_pedition_style_width)."</p>\n";
		$tmp .='<p class="field"><label class="float" for="c_edition_style_color" style="display:inline;position:static"><strong>'. __('Text color:').'</strong><sup>*</sup></label>'."\n";    
		$tmp .=  form::textField('c_edition_style_color', 10, 15, $this->px_pedition_style_color)."</p>\n";    
		$tmp .='<p class="field"><label class="float" for="c_edition_style_back" style="display:inline;position:static"><strong>'. __('Background color:').'</strong><sup>*</sup></label>'."\n";    
		$tmp .=  form::textField('c_edition_style_backg', 10, 15, $this->px_pedition_style_backg)."</p>\n";
		$tmp .= '<p><em style="color:red">'.__('*Use CSS values').'</em></p>';	
		$tmp .=  '</fieldset>'."\n"; 
	
		$tmp .= '<fieldset style="margin-bottom:1em"><legend>'. __('Source vizualisation').'</legend>'."\n\n";
		$tmp .='<p class="field"><label class="float" for="c_edition_style_nblinecolor" style="display:inline;position:static"><strong>'. __('Number line color:').'</strong><sup>*</sup></label>'."\n";    
		$tmp .=  form::textField('c_edition_style_nblinecolor', 10, 15, $this->px_psource_style_nblinecolor)."</p>\n";
		$tmp .='<p class="field"><label class="float" for="c_edition_style_nblinebgcolor" style="display:inline;position:static"><strong>'. __('Number line background color:').'</strong><sup>*</sup></label>'."\n";    
		$tmp .=  form::textField('c_edition_style_nblinebgcolor', 10, 15, $this->px_psource_style_nblinebgcolor)."</p>\n";
		$tmp .= '<p><em style="color:red">'.__('*Use CSS values').'</em></p>';	
		$tmp .=  '</fieldset>'."\n";
		
		$tmp .= '<fieldset><legend>'. __('CSS Folder to edit').'</legend>'."\n\n";
		$tmp .='<p class="field"><label class="float" for="c_edition_css_path" style="display:inline;position:static"><strong>'. __('Path to folder:')."</strong></label>\n";    
		$tmp .=  form::textField('c_edition_css_path', 50, 100, $this->px_pcss_folder)."\n";
		$tmp .='<input type="button" id="b_default_path" value="'.__('Get path to &quot;/xmedia/theme/default/&quot;').'" onclick="c_edition_css_path.value=\'' . $GLOBALS['_PX_website_config']['xmedia_root'] .'/theme/default/\'" class="submit" />'."</p>\n";
		$tmp .='<p>' . __('The path must begin and end with a "/"<br />');
		$tmp .=__('Example: <em>/var/www/sites/plume/xmedia/theme/default</em>')."</p>\n";

		$tmp .=  '</fieldset>'."\n";	
		
		$tmp .= form::button('submit','b_saved',__('Save'),'','','submit');
		$tmp .= '</form>'."\n";
		
		return $tmp;    
	}
	
	// Enregistrement de la configuration utilisateur
	function saveConfig() {
		include_once $GLOBALS['_PX_config']['manager_path'].'/extinc/class.configfile.php';
		global $m;
	
		$px_c_height  = (string) $_POST['c_edition_height'];
		$px_c_style_width  = (string) $_POST['c_edition_style_width'];
		$px_c_style_color  = (string) $_POST['c_edition_style_color'];
		$px_c_style_backg = (string) $_POST['c_edition_style_backg'];
		$px_c_lnsource_color = (string) $_POST['c_edition_style_nblinecolor'];
		$px_c_lnsource_backg = (string) $_POST['c_edition_style_nblinebgcolor'];
		$px_c_css_folder = (string) $_POST['c_edition_css_path'];

		$cfg = new configfile($GLOBALS['_PX_config']['manager_path'].'/conf/configplugin_teditor.php');
		
		$cfg->prefix = '_PX_pconfig';
		$cfg->editVar('edition_height',  (string) $px_c_height);
		$cfg->editVar('css_folder',  (string) $px_c_css_folder);
		
		$cfg->prefix = '_PX_pconfig[\'edition_style\']';
		$cfg->editVar('width',  (string) $px_c_style_width);
		$cfg->editVar('color',  (string) $px_c_style_color);
		$cfg->editVar('background',  (string) $px_c_style_backg); 
		
		$cfg->prefix = '_PX_pconfig[\'source-nb_style\']';
		$cfg->editVar('color',  (string) $px_c_lnsource_color);
		$cfg->editVar('background',  (string) $px_c_lnsource_backg);
		
		if (!$cfg->saveFile()) {
			$m->setError(__('Error : Unable to save configuration'),500);	
			exit;
		} else {
			$msg = __('Configuration saved');
			header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/'.$this->makeURL().'&msg='.urlencode($msg));
			exit;   
		}
	}

	
	// Construction des regles de style pour l'editeur
	function getStyle($item){
		$style_str = '';
		
		while (list($key, $val) = each($item)) {
			$style_str .= "$key:$val;";
		}
		
		$style = 'style="'.$style_str.'"';
		return $style;
	}	
	
	// Recuperation du contenu du fichier
	 function returnFile($file,$type='') {
		global $m;
		$content = '';
		$path    = $this->getPath($type);

		if (!@file_exists($path.$file)) {
			$m->setError( __('Error: File does not exist.'), 500);
			return;
		} 

		$content = @join('',@file($path.$file));
	
		if (!$content) {
			$m->setError( __('Error: Unable to read file.'), 500);		
		}
		return $content;
	}

	// Enregistrement du fichier
	function sauve($file,$type) {
		global $m;
		$path = $this->getPath($type);
		$fp   = @fopen($path.$file, 'w+');
		
		if (!@fwrite($fp,$_POST['t_content'])) {
			$m->setError(sprintf(__('Error : File <em>%s</em> not saved'),$file),500);				
		} else {
			@fclose($fp);
			$msg = sprintf(__('File <em>%s</em> saved'),$_POST['h_template']);
			header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/'.$this->makeURL().'&msg='.urlencode($msg));		
		}
	}
	

	//	creation de gabarit
	function createTemplate($file) {
	global $m;
		
		if (ereg('[^A-Za-z0-9_.-]',$file)) {
			$m->setError(__('Error : Invalid template name, it must contain only letters, digits, "_" and "-"'),400);
			return false;
		} elseif ( strpos($file,'.') && ((substr($file, -4, 4) != '.php') && (substr($file, -4, 4) != '.css')) ){
			$m->setError(__('Error : Invalid name extension, use css or php'),400);
		} else {
			if ($_POST['c_filetype'] == 3) {
				(strpos($file,'.')) ? true : $file .= '.css' ;
			} elseif (strpos($file,'.')) {
				$file = $file;
			} else {
				$file .= '.php';
			}
			
			switch ($_POST['c_filetype']) {
				case 0:
					$type = 	'resource';
					$ftmp = $this->px_theme_folder.$type.'_'.$file;
					$fcontent = "<?php \n\n ?>";
					break;
				case 1:
					$type =  'category';
					$ftmp = $this->px_theme_folder.$type.'_'.$file;
					$fcontent = "<?php \n\n ?>";				
					break;
				case 2:
					$type =  'comments';
					$ftmp = $this->px_theme_folder.$type.'_'.$file;
					$fcontent = "<?php \n\n ?>";				
					break;
				case 3:
					$type =  false;
					$ftmp = $this->px_theme_folder.$file;
					$fcontent = "<?php \n\n ?>";				
					break;
				case 4:
					$type =  false;					
					$ftmp = $this->px_pcss_folder.$file;
					$fcontent = "/* CSS */";					
			}

			if (!file_exists($ftmp)) {
				if (@fopen($ftmp, 'w')) {
					$fichier = fopen($ftmp, 'w');
					@fwrite($fichier,$fcontent);
					fclose ($fichier);
					$msg = sprintf(__('File <span style="font-style:italic;">%s</span> created'),(($type) ? $type .= '_' : '').$file);
					header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/'.$this->makeURL().'&msg='.urlencode($msg));
					exit;  
				} else {
					$m->setError(__('Error : The file creation failed'),500);
                }
 		
			} else {
				$m->setError(sprintf(__('Error : File <span style="font-style:italic;">%s</span> already exists'),(($type) ? $type .= '_' : '').$file),500);
			}
		}
	}
	
	//suppression  de gabarit
	function delTemplate($filepath) {
	global $m;
	
		if (!@unlink($filepath))  {
			$m->setError(__('Error : Unable to delete file'),500);			
		} else {
			$file = array_pop(explode('/',$filepath));
			$msg = sprintf(__('File <em>%s</em> deleted'),$file);
			header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/'.$this->makeURL().'&msg='.urlencode($msg));
			exit;   				
		}
	}

	// Coloration syntaxique
	function showSource($filename,$type='') {
		global $m;

		if ($fileContent = $this->returnFile($filename,$type)) {
			$style = $this->getStyle($this->px_psource_style);
			Hook::run('onCodeHighLigthManager', array('m' => &$m));
			$tmp='';	
			$tmp .= '<form id="frmShowTemplate" action="" >'."\n";
			$tmp .= form::textArea('t_content',60,$this->px_pedition_height,$fileContent,'',$style.' class="codemirror_content '.$type.'"');
			$tmp .= "</form>\n";
			return $tmp;
		}
		$m->setError(__('Error : File empty or not readable'),500);
	}
	
	/**
	* Public
	*/
	// Statut du lien de navigation
	function isLnkActive($witch) {
		if (isset($_GET['w'])) {
			return ($_GET['w'] == $witch) ? false : true;
		} else {
			return true;
		}
	}
	
	// Affichage des fichiers
	function showTemplatesList(){
	//global $m;
		$tmp  = '<h3 style="padding:0.5em">'.__('Categories templates')."</h3>\n";
		$tmp .= $this->listTemplates($this->arry_templates_c);
		$tmp .= '<h3 style="border-top:1px solid;padding:0.5em">'.__('Resources templates')."</h3>\n";
		$tmp .= $this->listTemplates($this->arry_templates_r);
		$tmp .= '<h3 style="border-top:1px solid;padding:0.5em">'.__('Comments templates')."</h3>\n";
		$tmp .= $this->listTemplates($this->arry_templates_cm);		
		$tmp .= '<h3 style="border-top:1px solid;padding:0.5em">'.__('Other templates')."</h3>\n";
		$tmp .= $this->listTemplates($this->arry_templates_o);
		$tmp .= '<h3 style="border-top:1px solid;padding:0.5em">'.__('CSS files')."</h3>\n";

		if($this->listTemplates($this->getCSS(),'css')){
			$tmp .= $this->listTemplates($this->getCSS(),'css');
		} else {
			$tmp .= '<p style="color:red">'.__('No CSS files. Check your <a href="tools.php?p=teditor&amp;w=conf">configuration</a> settings')."</p>\n";	
		}
		return $tmp;
	}
	
	//	construction de l'éditeur
	function renderEditor($template_choice,$template_type,$msg){
		global $m;
		$tmp  = '';
		$path = $this->getPath($template_type);
		($template_type == 'css') ? $type = $template_type : $type = '';		
		
		//affichage de la liste des gabarits
		$tmp .= $this->frmListboxTemplates($template_choice);
		
		// visualisation du code source
		if (isset($_POST['c_visualiser'])) {
			//$tmp .= $this->showSource($path.$_POST['c_template'],$type);
			if ((in_array($_POST['c_template'],$this->arry_templates_a)) || (in_array($_POST['c_template'],$this->getCSS()))) {
				$tmp .= $this->showSource($_POST['c_template'],$type);
			} else {
				$m->setError( __('Error: File does not exist.'), 500);
				return false;
			}
			
		} elseif (isset($_POST['b_template'])){
			if ((in_array($_POST['c_template'],$this->arry_templates_a)) || (in_array($_POST['c_template'],$this->getCSS()))) {
				$tmp .= $this->frmEditTemplate($_POST['c_template'],$type);	
			} else {
				$m->setError( __('Error: File does not exist.'), 500);
				return false;			
			}
		} elseif (isset($_POST['b_saved'])) { // sauvegarde du gabarit
			$tmp .= $this->sauve($_POST['h_template'],$type);
		} else {
			$tmp .= '<p>' . __($msg) . "<p>\n";
		}

		return $tmp;
	}
	
	// Formulaire de sélection du gabarit
	function frmListboxTemplates($choice) {

		if (isset($_POST['c_template'])) {
			$selected = $_POST['c_template'];
		} else {
			$selected = '';
		}
		
		$tmp  = '<form action="'.$this->makeURL().'" method="post">'."\n";
		$tmp .= '<p><fieldset><legend>'. __('Selection')."</legend>\n\n";
		$tmp .= '<label for="c_template" style="display:inline"><strong>'. __('Choose a file:')."</strong></label>\n";
		$tmp .= form::combobox('c_template', $choice, $selected,'','','','style="width:10em"');
		$tmp .='<label for="c_visualiser" style="display:inline;margin-left:2em"><strong>'. __('No edition, only visualize:')."</strong></label>\n";    	
		$tmp .= form::checkbox('c_visualiser', '1')."\n";
		$tmp .= '<input type="submit" class="submit" style="margin-left:2em;padding:0 4em" value="'.__('Ok').'" id="b_template" name="b_template" /></fieldset></p>';		
		$tmp .= "\n</form>\n";
		return $tmp;
	}	
}
?>
