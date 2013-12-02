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

/**
 * Small note on design:
 * 
 * Both links and categories are stored in the same database table.
 * The difference is that 'label' and 'href' fields are empty for categories.
 *
 * This is quite hacky but perfectly fits a simple two level design.
 *
 */ 
 
//	Inclusion des ressources nécessaires
require_once dirname(__FILE__).'/class.ecm.php';
require_once dirname(__FILE__).'/functions.ecm.php';

//	Déclaration des variables
$m = new Manager();
$_px_theme = $m->user->getTheme();
if (!isset($con))
	$con =& pxDBConnect();

$ecm = new ECM($_SESSION['website_id']);
$url = "tools.php?p=ecm";

//	SI le plug-in n'est pas encore installé
//	ALORS lance le process d'installation

if(!$ecm->isInstall())
	include dirname(__FILE__).'/db/db-update.php';

//	Ajoute les actions du menu adéquates
if (!empty($_REQUEST['category_id']) || !empty($_REQUEST['action'])) {
    $display_list_cat = true;
} else {
    $display_list_cat = false;
}
$px_submenu->addItem(__('Categories Manager'), $url, 'tools/ecm/themes/'.$_px_theme.'/icon.png', false, $display_list_cat);
$px_submenu->addItem(__('New category'), $url.'&action=new', 'themes/'.$_px_theme.'/images/ico_new.png', false, !$display_list_cat);

 
echo '<h2>'.__('Categories').'</h2>'."\n\n";

/* ================================================= *
 *           Form to select some resources           *
 * ================================================= */
if (empty($_REQUEST['action']) || ($_REQUEST['action'] != 'new' && $_REQUEST['action'] != 'edit')) {
	echo '<form name="filter" action="'.$url.'" method="get">';
	//echo '<p id="category-select">';
	echo '<input type="hidden" name="p" value="ecm">';
	$valueLocation = '';
	if (!empty($_SESSION['location'])) $valueLocation = $_SESSION['location'];
	if (isset($_REQUEST['location']))  $valueLocation = $_REQUEST['location'];
	//echo $valueLocation;
	/*
	echo '<input type="hidden" name="location" style="height:20px" value="'.$valueLocation.'">';
	echo '<input type="hidden" name="cat_id" value="'.$valueLocation.'">';
	echo '<input type="hidden" name="op" id="op" value="list" />';
	echo '<button class="filterButton"  title="'. __('Apply filter').'" ></button>&nbsp';
	echo '<button class="resetButton" title="'. __('reset the filter').'" ></button>';
	*/
	//echo '</p>';
	$valueLocation = PathSelector::getLocation();
	
	echo PathSelector::getCategoryPathSelector($valueLocation,__('Parent category:'));
	
	echo '</form>';
}
//Get the category id and save it
$cat_id = (!empty($_GET['cat_id'])) ? $_GET['cat_id'] : $m->user->getPref('list_index_cat_id');
$m->user->savePref('list_index_cat_id', $cat_id, $_SESSION['website_id'], true);
if ($cat_id == 'allcat') $cat_id = '';


//	SI on a l'ID de la catégorie sur laquelle agire
//	ET QUE on a reçu une action
if(!empty($_REQUEST['action']))
{
	$cat = new Category();
	$cat->setDefaults($m->user);
	
	if(!empty($_REQUEST['category_id']))
	{
		$cat = $ecm->setCategory($cat, $_REQUEST['category_id']);
		if ($m->loadCategory($cat, $_REQUEST['category_id']) === false)
			$m->setError(__('Error: The requested category is not available.'), 400);
	}
	
		
	if($_REQUEST['action']=='add' || $_REQUEST['action']=='modify')
	{
		$parentid = $cat->f('category_parentid');
		$cat->set(form::getPostField('cat_id'),
					form::getPostField('c_name'),
					form::getPostField('c_description'),
					form::getPostField('c_format'),
					form::getPostField('c_keywords'),
					form::getPostField('c_path'),
					form::getPostField('c_template'),
					3600);
	}
	
	
	//	TEST l'action courante et délanche le process adéquat
	switch($_REQUEST['action'])
	{
		//	Création - Modification d'une catégorie
		case 'renum':
			$ecm->updatePositionAfterInstall($m,$m->getCategories(1,'category_id!=1','ORDER BY category_position, category_path'));
			/*
			if($ecm->updatePositionAfterChange($m,$m->getCategories())!==false)
			{
				$m->setMessage(__('Category successfully add'));
				header('Location: '.$url);
				exit;
			}
			*/
			header('Location: '.$url);
			break;
			
		case 'new':			
		case 'edit':
			 require_once dirname(__FILE__).'/form.inc.ecm.php';
			break;
			
		//	Ajouter une catégorie
		case 'add':
			if ($m->saveCategory($cat)!==false)
			{
				if($ecm->updatePositionAfterChange($cat)!==false)
				{
					$m->setMessage(__('Category successfully add'));
					header('Location: '.$url);
					exit;
				}
			}
			break;
			
		//	Modifier une catégorie
		case 'modify':
			if ($m->saveCategory($cat) !== false)
			{
				if($ecm->isChangeParent($cat, $parentid))
				{
					$ecm->updatePositionAfterChange($cat);
					$cats = $m->getCategories($parentid,'','ORDER BY category_position');
					$ecm->updateChildesPosition($cats);
				}
				$m->setMessage(__('Category successfully modify'));
				header('Location: '.$url);
				exit;
			}
			break;
			
		//	Suppression d'une catégorie
		case 'delete':
			$cats = $m->getCategories($cat->f('category_id'),'','ORDER BY category_position');
			$error=true;
			//	Suppression de la catégorie "mère"
			if ($m->delCategory($cat) !== false)
				$error=false;
			//	Suppression des catégories "enfants"
			if($ecm->deleteCategory($m, $cats))
				$error=false;
			//	Si toutes les suppression de catégorie précédentes on réussi
			//	ALORS redirection + msg de confirmation
			if ($error !== true)
			{
				$m->setMessage(__('The category was successfully deleted.'));
				header('Location: '.$url);
				exit;
			}
			break;
			
		//	Monter la position
		case 'up':
			// Lance une renumérotation du groupe
			//$ecm->updatePositionAfterInstall($m,$cat,$cat->f('category_parentid') );
			if($ecm->upPosition($cat) !== false)
			{
				$m->setMessage(__('The position of the category was successfully up.'));
				header('Location: '.$url);
				exit;
			}
			break;
		//	Baisser la position
		case 'down':
			// Lance une renumérotation du groupe
			//$ecm->updatePositionAfterInstall($m,$cat,$cat->f('category_parentid') );
			if($ecm->downPosition($cat) !== false)
			{
				$m->setMessage(__('The position of the category was successfully down.'));
				header('Location: '.$url);
				exit;
			}
			break;
			
		//	Action "default" : message d'erreur
		default:
			$m->setMessage(__('Invalide action.'));
			header('Location: '.$url);
			break;
	}
}
else
{
	if ($cat_id != '') {
		$cat = new Category();
		$cat = $ecm->setCategory($cat, $cat_id);
		if ($m->loadCategory($cat, $cat_id) === false)
				$m->setError(__('Error: The requested category is not available.'), 400);
		
		$cats = $m->getCategories($cat->f('category_parentid'),'','ORDER BY category_path, category_position');
	} else {
		$cats = $m->getCategories('','','ORDER BY category_path, category_position');
	}
	
	if ($cats->isEmpty())
	{
		echo '<p>'.__('No categories.').'</p>'."\n\n";
	}
	else
	{
		/*
		echo '<script type="text/javascript">'."\n"
			."<!--\n"
			."var js_post_ids = new Array('"
			.implode("','",$cats->getIDs('category_id', 'content'))."');\n"
			."//-->\n"
			."</script>\n";
		*/
		echo '<script type="text/javascript">'."\n"
		."<!--\n"
		."var js_post_ids = new Array('"
		.implode("','",$cats->getIDs('category_id', 'content'))."');\n"
		."var js_levels_ids = new Array('"
		.implode("','",$cats->getIDs('category_id', 'childOf-'))."');\n"
		."//-->\n"
		."</script>\n";
		
		echo '<p class="small"><a href="#" onclick="mOpenClose(js_post_ids,1); return false;">'.__('Show all').'</a>'
			.' - <a href="#" onclick="mOpenClose(js_post_ids,-1); return false;">'.__('Hide all').'</a>';
		echo '&nbsp;&nbsp;<a href="tools.php?p=ecm&action=renum" >'.__('Renum all').'</a> </p>';

		//	Affiche la catégorie mère
		/*
		pxECM::displayCat($m, $cats, '1');
		//	Parcours l'ensemble des autres catégories
		$cats = $m->getCategories('1','','ORDER BY category_position');
		$ecm->listCatsToDisplay($m, $cats);
		*/
		if ($cat_id == '')  {
			//pxECM::displayCat($m, $cats, '0');
			$cats = $m->getCategories(1,'category_id!=category_parentid','ORDER BY category_parentid, category_position');
		} else {
			//pxECM::displayCat($m, $cats, $cat->f('category_parentid'));
			$cats = $m->getCategories($cat->f('category_id'),'','ORDER BY category_parentid, category_position');
		}

		//	Parcours l'ensemble des autres catégories
		$ecm->listCatsToDisplay($m, $cats);
	}
}

if ($_PX_website_config['comment_support'] < 3 ) {
	echo	'<h3>'.__('Usage').'</h3>'.
			'<p>'.__('To replace your categories links list by this one, just put the following code in your template:').'</p>'.
			'<pre>&lt;?php pxECM::primaryCategories($s, $category_id, $return, $order); ?&gt;</pre>'.
			'<ul>'.
				'<li><strong>$s : </strong>(string) \'%s\' '.__('est la chaîne de substitution').'</li>'.
				'<li><strong>$category_id : </strong>(integer) '.__('id de la catégorie dont on souhaite construire le menu. Voir le détail des catégorie pour connaître quel id utiliser.').'</li>'.
				'<li><strong>$return : </strong>(boolean) '.__('variable permettant de spécifier comment on souhaite retourner le résutat de la fonction.').
					'<ul>'.
						'<li>true: '.__('retourne le résultat avec "return $result"').'</li>'.
						'<li>false: '.__('retourne le résultat avec "echo $result"').'</li>'.
					'</ul>'.
				'</lI>'.
				'<li><strong>$order : </strong>'.__('spécifie l\'ordre de trie du menu. Utilise le nom de la colonne de la table ').' "'.$con->pfx.'categories"</li>'.
			'</ul>';
	
	echo	'<h3>'.__('Examples').'</h3>';
	echo	'<h4>&lt;?php pxECM::primaryCategories(\'&lt;ul id="menuTop"&gt;%s&lt;/ul&gt;\'); ?&gt;</h4>'.
			'<p>'.__('construit le menu en incluant un lien "home" retournant à la page d\'accueil').'</p>';
	
	echo	'<h4>&lt;?php pxECM::primaryCategories(\'&lt;ul id="menuTop"&gt;%s&lt;/ul&gt;\', \'0\', false, \'category_position\'); ?&gt;</h4>'.
			'<p>'.__('construit le même menu que précédemment').'</p>';
	
	echo	'<h4>&lt;?php pxECM::primaryCategories(\'&lt;ul id="menuTop"&gt;%s&lt;/ul&gt;\', \'1\'); ?&gt;</h4>'.
			'<p>'.__('construit le même menu que précédemment sans le lien "home"').'</p>';
	
	echo	'<h4>&lt;?php echo pxECM::primaryCategories(\'&lt;ul id="menuTop"&gt;%s&lt;/ul&gt;\', \'2\', true); ?&gt;</h4>'.
			'<p>'.__('construit menu de la catégorie dont l\'id = 2').'</p>';
}
?>