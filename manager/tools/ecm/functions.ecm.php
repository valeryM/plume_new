<?php
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
# The Initial Developer of the Original Code is
# Olivier Meunier.
# Portions created by the Initial Developer are Copyright (C) 2003
# the Initial Developer. All Rights Reserved.
#
# Contributor(s):
# - Sebastien Fievet
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

class pxECM
{
	public static function primaryCategories($s='<ul>%s</ul>', $category_id=false, $return=false, $order='category_path ASC, category_position ASC')
	{
		$remove_numbers = config::fbool('remove_numbers');
		$orderBy = 'ORDER BY '.$order;
		$menu = "\n\t\t";
		
		$rootcat = FrontEnd::getCategory('/');
		
		if($category_id)
			$cats = FrontEnd::getCategories($category_id, $orderBy);
		else
		{
			$cats    = FrontEnd::getCategories($rootcat->f('category_id'), $orderBy);
			
			if($GLOBALS['_PX_render']['cat']->f('category_id') == $rootcat->f('category_id'))
				$style = 'mainMenu_selected';
		
			$menu .= '<li'.(!empty($style) ? ' class="'.$style.'"':'').'>'.
						'<a href="'.pxInfo('url', true).'">'.htmlspecialchars($rootcat->f('category_name')).'</a>'.
					'</li>'."\n";
		}
		

		while (!$cats->EOF())
		{
			if ($cats->f('category_path') != '/')
			{
				$path = $cats->getPath();
				$name = $cats->f('category_name');
				$hasSub=false;
				if ($remove_numbers)
					$name = px_removeNumbers($name);
				if(isset($GLOBALS['_PX_render']['cat']))
				{
					if($GLOBALS['_PX_render']['cat']->f('category_id')==$cats->f('category_id') || $GLOBALS['_PX_render']['cat']->f('category_parentid')==$cats->f('category_id'))
					{
						$style = ($category_id ? 'subMenu_selected':'mainMenu_selected');
						$hasSub=true;
					}
					else
						$style = '';
				}
				
				$menu	.=	"\t\t".
							'<li'.(!empty($style) ? ' class="'.$style.'"':'').'>'.
								'<a href="'.$path.'">'.htmlspecialchars($name).'</a>';

				$subCats = FrontEnd::getCategories($cats->f('category_id'), $orderBy);
				//	SI on a au moins une sous-catégorie
				if($subCats->nbRowTotal() > 0 && $hasSub)
					$menu .= "\n\t\t\t".pxECM::primaryCategories('<ul class="subMenu">%s</ul>', $cats->f('category_id'), true, $order);

				$menu	.=	'</li>'."\n";
			}
			$cats->moveNext();
		}
		$result = sprintf($s."\n", $menu."\t");
		
		if ($return) return $result;
		echo $result;
	}
	
	
	/**
	*	Fonction permettant d'afficher une catégorie 
	*	fonction utilisée lors de l'appel à la fonction listCats()
	*	Les catégorie sont construites au format html <div></div>
	*
	*	@param	variable globale contenant le thème courant (str)
	*	@param	référence à l'élément "catégorie" courant (object)
	*	@param	id du parent de l'élément parcouru (int)
	*	@param	position de l'élément parcouru dans l'arborescence. (int)
	*/
	public static function displayCat($m, $cats, $parentId=1)
	{
		$_px_theme = $m->user->getTheme();
		echo	'<div class="line" id="p'.$cats->f('category_id').'" style="margin-left: 0">'.
					'<p>'.
						'<a href="#" onclick="openCloseSpan(\'content'.$cats->f('category_id').'\',0); return false;">'.
							'<img src="themes/'.$_px_theme.'/images/plus.png" id="img_content'.$cats->f('category_id').'" alt="'. __('show/hide').'" />'.
						'</a> ';
		echo		'<a href="index.php?cat_id='.$cats->f('category_id').'"><strong>'.$cats->f('category_name').'</strong></a>'.
					'<span class="small">'.$cats->f('category_path').'</span>'.
					' [<a href="tools.php?p=ecm&action=edit&category_id='.$cats->f('category_id').'"><strong>'. __('edit').'</strong></a>]';
		
		echo '<span class="small">&nbsp;(Position : '.$cats->f('category_position').' )</span>';

		//	SI la catégorie courante n'est pas la catégorie mère (home)
		//	ALORS on peut afficher les actions disponibles
		if($parentId!='')
		{
			//	Supprimer la catégorie
			pxECM::iconDeleteCat($_px_theme, $cats);
			//$this->iconDeleteCat($_px_theme, $cats);
			//	SI la cat�gorie courante n'est pas la première catégorie de la liste
			if($cats->getIndex() > 0)
			{
				// 	ALORS on peut forcément monter la catégorie, car elle a une catégorie avant elle
				//$this->iconUpCat($_px_theme, $cats);
				pxECM::iconUpCat($_px_theme, $cats);
				//	SI la cat�gorie courante n'est pas la dernière de la liste
				if($cats->getIndex()+1 < $cats->nbRow())
					//	ALORS on peut baisser la catégorie, car elle a une autre cat�gorie apr�s elle
					//$this->iconDownCat($_px_theme, $cats);
					pxECM::iconDownCat($_px_theme, $cats);
			}
			//	SI la cat�gorie courante n'est pas la dernière de sa catégorie
			else if($cats->nbRow()>1)
				//	ALORS on peut baisser la catégorie, car elle a une autre catégorie après elle
				pxECM::iconDownCat($_px_theme, $cats);
				//$this->iconDownCat($_px_theme, $cats);
		}

		echo		"</p>\n\n";
		echo		'<div id="content'.$cats->f('category_id').'" style="display:none;">';
		echo			'<p>'.text::parseContent($cats->f('category_description'))."</p>\n";
		echo			"<p>".
							"<span class='small'>". __('Id to use in the templates:').' '.$cats->f('category_id')."</span>".
						"</p>\n";
		echo			"<hr class='invisible' />".
					"</div>".
				"</div>\n\n";
		
		//	SI l'élément courant est un élément "enfant"
		//	ALORS on lui applique un style pour le d�caler par rapport � son �l�ment "parent"
		if($parentId !='')
		{
			echo	'<script type="text/javascript" id="tmpJavascript'.$cats->f('category_id').'">'."\n".
						'var reg=new RegExp("[0-9]+","g");'."\n".
						'if (document.getElementById(\'p\'+'.$parentId.')) { '.
						'	parentMargin = reg.exec(document.getElementById(\'p\'+'.$parentId.').getAttribute(\'style\'));'."\n".
						'	newMargin = parseInt(parentMargin)+2'."\n".
						'	document.getElementById(\'p\'+'.$cats->f('category_id').').setAttribute(\'style\',\'margin-left:\'+newMargin+\'em\')'."\n".
						'}'.
					'</script>'."\n";
		}
	}
	
	
	/**
	*	fonction permettant d'afficher un bouton "supprimer"
	*	bouton "delete" permettant de supprimer une cat�gorie
	*
	*	@param	variable globale contenant le nom du thème utilisé (str)
	*	@param	référence à l'élément "catégorie" courant (object)
	*/
	public static function iconDeleteCat($_px_theme, $cats)
	{
		echo	'<a href="tools.php?p=ecm&action=delete&category_id='.$cats->f('category_id').'" onclick="return window.confirm(\''.addslashes( __('Are you sure you want to delete this category?')).'\')">'.
					'<img src="tools/ecm/themes/'.$_px_theme.'/images/delete.png" alt="'.__('delete').'" title="'.__('Delete the category').' '.$cats->f('category_name').'" style="float: right;/* margin: -1em 0.5em 0 0;*/" />'.
				'</a>';
	}
	
	
	/**
	*	fonction permettant d'afficher un bouton "monter"
	*	bouton "up" permettant de monter la position d'une cat�gorie
	*
	*	@param	variable globale contenant le nom du th�me utilis� (str)
	*	@param	référence à l'élément "catégorie" courant (object)
	*/
	public static function iconUpCat($_px_theme, $cats)
	{
		echo 	'<a href="tools.php?p=ecm&action=up&category_id='.$cats->f('category_id').'">'.
					'<img src="tools/ecm/themes/'.$_px_theme.'/images/arrow_up.png" alt="'.__('up').'" title="'.__('up position').'"  style="float: right;/* margin: -1em 0.5em 0 0;*/" />'.
				'</a>';
	}
	
	
	/**
	*	fonction permettant d'afficher un bouton "baisser"
	*	bouton "down" permettant de baisser la position d'une cat�gorie
	*
	*	@param	variable globale contenant le nom du th�me utilis� (str)
	*	@param	référence à l'élément "catégorie" courant (object)
	*/
	public static function iconDownCat($_px_theme, $cats)
	{
		echo 	'<a href="tools.php?p=ecm&action=down&category_id='.$cats->f('category_id').'">'.
					'<img src="tools/ecm/themes/'.$_px_theme.'/images/arrow_down.png" alt="'.__('down').'" title="'.__('down position').'"  style="float: right; /*margin: -1em 0.5em 0 0;*/" />'.
				'</a>';
	}
}
?>