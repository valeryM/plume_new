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

class ecm
{
	var $con;
	var $site_id;
	var $table;
	var $column;
	
	
	/**
	*	constructeur de la class ECM
	*	permet de d�finir les variables de la classe
	*
	*	@param	(str) nom du site courant ("default")
	*/
	function ecm($site_id)
	{
		if ($this->con === null)
			$this->con =& pxDBConnect();
		$this->site_id = $site_id;
		$this->table = $this->con->pfx.'categories';
		$this->column = 'category_position';
	}
	
	
	/****************************************
	#####	START part. INSTALLATION	#####
	****************************************/

	/**
	*	fonction permettant de tester si la plug-in "ecm" est déjà installé ou non
	*
	*	@return	(boolean) renvoie le résultat de la requête (true/false)
	*/
	function isInstall()
	{
		$sql = 'SHOW COLUMNS FROM '.$this->table.' LIKE \''.$this->column.'\'';
		$rs = $this->con->select($sql);
		if($rs->isEmpty())
			return false;
		return true;
	}
	
	
	/**
	*	mise à jour des positions des catégories après installation du plug-in
	*	met à jour les valeurs du champs "category_position" pour l'ensemble des catégories déjà présentes en fonction de leur arborescence
	*
	*	@param	(oject) reférence au manager principal de l'application
	*	@param	(object) référence à l'élément category courant
	*	@param	(int) id de la catégorie parent pour la catégorie courante
	*	@param	(int) valeur de la position de l'enfant au sein de l'arborescence de son parent
	*	@param	(boolean) permet de récupérer le résultat (true/false) de toutes les mises à jour
	*	@return	(boolean) renvoie le résultat des mises à jour (true/false)
	*/
	function updatePositionAfterInstall($m, $cats, $parentId=1, $error=true)
	{
		$index = 1;
		while (!$cats->EOF())
		{
			//	Met à jour la catégorie et récupère l'erreur éventuelle
			$error = $this->updatePosition($cats, $index); // $cats->getIndex()
			//	Récupére la liste des sous-catégorie de la catégorie courante
			$subCats = $m->getCategories($cats->f('category_id'),'','ORDER BY category_position, category_path');
			
			//	SI on a au moins une sous-catégorie
			if($subCats->nbRowTotal() > 0) {
				//	ALORS parcours ce sous-ensemble de façon récursive
				$this->updatePositionAfterInstall($m, $subCats, $cats->f('category_id'), $error);
			}
			//	Passe à l'élément suivant
			$cats->moveNext();
			$index++;
		}
		//	Renvois les erreurs (true/false)
		return $error;
	}
	/************************************
	#####	END part. INSTALLATION	#####
	************************************/
	
	
	/**
	*	fonction permettant de configurer une catégorie
	*	@param	(object) référence à une catégorie vide
	*	@param	(int) id de la catégorie à configurer
	*	@return (object) renvoie la catégorie configurée
	*/
	function setCategory($cat, $category_id)
	{
		$sql = 'SELECT * FROM '.$this->table.' WHERE category_id='.$category_id;
		$rs = $this->con->select($sql);
		if($rs->isEmpty())
			return false;
			
		$data = $rs->getData();
		$cat->load($data[0]['category_id']);
		$cat->set(	$data[0]['category_parentid'],
					$data[0]['category_position'],
					$data[0]['category_name'],
					substr($data[0]['category_description'], 6, strlen($data[0]['category_description'])),
					substr($data[0]['category_description'], 1, 5),
					$data[0]['category_keywords'],
					$data[0]['category_path'],
					$data[0]['category_template'],
					3600);
		return $cat;
	}
	
	
	/**
	*	fonction permettant de définir si on a changé de catégorie parent
	*	fonction utilisé lors de la mise à jour d'une catégorie.
	*
	*	@param	(object) référence à la catégorie courante
	*	@param	(int) id de la catégorie parent AVANT mise à jour de la catégorie
	*	@return (boolean) renvoie le résultat de la comparaison de l'id_parent BEFORe et id_parent AFTER (true / false)
	*/
	function isChangeParent($cat, $parentid)
	{
		return ($cat->f('category_parentid') !== $parentid);
	}
	
	/**
	 * fonction permettant de mettre à jour les éléments enfants
	 * 
	 * @param (object) référence à la catégorie courante
	 * @param (boolean) état du gestionnaire d'erreur
	 * @return (boolean) retour l'état du gestionnaire d'erreur
	 */
	function updateChildesPosition($cats, $hasError=true)
	{
		while(!$cats->EOF())
		{
			if($cats->f('category_position') !== $cats->getIndex())
				$hasError = $this->updatePosition($cats, $cats->getIndex());
			$cats->moveNext();
		}
		return !$hasError;
	}
	
	
	/**
	*	fonction permettant de parcourir l'ensemble des catégories disponibles
	*	fonction récursive qui parcours l'ensemble des catégories disponibles par arborescence
	*
	*	@param	(object) référence au manager
	*	@param	(object) référence à une catégorie
	*	@param	(int) id du parent de l'élément parcouru
	*	@param	(int) position dans l'arborescence de l'élément parcouru 
	*/
	public static function listCatsToDisplay($m, $cats, $parentId=1)
	{
		while (!$cats->EOF())
		{
			$subCats = $m->getCategories($cats->f('category_id'),'','ORDER BY category_parentid, category_position');
			pxECM::displayCat($m, $cats, $parentId, $cats->getIndex());
			
			if($subCats->nbRowTotal() > 0)
				ecm::listCatsToDisplay($m, $subCats, $cats->f('category_id'));
			$cats->moveNext();
		}
	}
		
	
	/**
	*	fonction permettant de mettre à jour les positions des catégorie après l'installation du plug-in
	*	@param	(object) référence à la catégorie à mettre à jour
	*	@param	(int) valeur de la nouvelle position de la catégorie à mettre à jour
	*	@return (boolean) renvois le résultat de la mise à jour (true/false)
	*/
	function updatePosition($cat, $position)
	{
		$sql = 'UPDATE `'.$this->table.'` SET `'.$this->column.'` = \''.$position.'\' WHERE `category_id` = \''.$cat->f('category_id').'\';';
		if(!$this->con->execute($sql))
			return false;
		return true;
	}
	
	
	/**
	*	fonction permettant de récupérer la position du dernier élément d'un élement parent
	*	fonction utilisée lors de l'ajout / modification d'une catégorie. Permet de placer la catégorie en dernière position
	*	
	*	@param	(object) référence à la catégorie à mettre à jour
	*	@return	(boolean) renvois le résultat de la mise à jour (true/false)
	*/
	function updatePositionAfterChange($cat)
	{
		$sql_select =	'SELECT MAX(`'.$this->column.'`) AS `'.$this->column.'`  FROM `'.$this->table.'` WHERE `category_parentid`='.$cat->f('category_parentid').' AND `category_id`!='.$cat->f('category_id');
		//echo $sql_select;
		$rs_select = $this->con->select($sql_select);
		if($rs_select->isEmpty())
			return false;
		$data_select = $rs_select->getData();
		
		return $this->updatePosition($cat, ($data_select[0][$this->column]+1));
	}
	
	
	/**
	*	fonction permettant de monter la position d'une catégorie
	*	La catégorie sélectionnée prend la position de la catégorie précédente
	*		( position < position de la catégorie sélectionnée)
	*	@param	(object) référence à la catégorie à mettre à jour
	*	@return (boolean) renvois le résultat de la mise à jour (true/false)
	*/
	function upPosition($cat)
	{
		if ($cat->f('category_position') == 0) {
			$sql_prevCat = 'SELECT category_id, category_parentid, category_position '.
						'FROM `'.$this->table.'` '.
						'WHERE category_parentid = '.$cat->f('category_parentid').' '.
						'AND category_id < '.($cat->f('category_id')-1);
			
		} else {
			$sql_prevCat = 'SELECT category_id, category_parentid, category_position '.
						'FROM `'.$this->table.'` '.
						'WHERE category_parentid = '.$cat->f('category_parentid').' '.
						'AND category_position < '.($cat->f('category_position')) .' ' .
						' ORDER BY category_position DESC LIMIT 1';
		}

		$rs_prevCat = $this->con->select($sql_prevCat);
		if($rs_prevCat->isEmpty())
			return false;
		$data_prevCat = $rs_prevCat->getData();
		
		// mise à jour de la catégorie sélectionnée avec la position de la catégorie précédente
		$sql_update = 'UPDATE `'.$this->table.'` SET '.$this->column.' = '.$data_prevCat[0]['category_position'].' WHERE category_id = '.$cat->f('category_id').';';
		echo $sql_update;
		if(!$this->con->execute($sql_update))
			return false;

		// mise à jour de la catégorie précédente 
		$sql_update = 'UPDATE `'.$this->table.'` SET '.$this->column.' = '.$cat->f('category_position').' WHERE category_id = '.$data_prevCat[0]['category_id'].';';
		echo $sql_update;
		if(!$this->con->execute($sql_update))
			return false;
		return true;
	}
	
	
	/**
	*	fonction permettant de baisser la position d'une catégorie
	*	La catégorie sélectionnée prend la position de la catégorie suivante
	*		( position > position de la catégorie sélectionnée)
	*	@param	(object) référence à la catégorie à mettre à jour
	*	@return (boolean) renvois le résultat de la mise à jour (true/false)
	*/
	function downPosition($cat)
	{
		// recherche de la catégorie placée au dessous
		$sql_nextCat = 'SELECT category_id, category_parentid, category_position '.
						'FROM `'.$this->table.'` '.
						'WHERE category_parentid = '.$cat->f('category_parentid').' '.
						'AND category_position > '.($cat->f('category_position')) .' '.
						' ORDER BY category_position ASC LIMIT 1';
		
		$rs_nextCat = $this->con->select($sql_nextCat);
		if($rs_nextCat->isEmpty())
			return false;
		$data_nextCat = $rs_nextCat->getData();
		
		// mise à jour de la position de la catégorie sélectionnée
		$sql_update = 'UPDATE `'.$this->table.'` SET '.$this->column.' = '.$data_nextCat[0]['category_position'].' WHERE category_id = '.$cat->f('category_id').';';
		
		if(!$this->con->execute($sql_update))
			return false;
			
		// mise à jour de la position de la catégorie suivante : devient la précédente
		$sql_update = 'UPDATE `'.$this->table.'` SET '.$this->column.' = '.$cat->f('category_position').' WHERE category_id = '.$data_nextCat[0]['category_id'].';';
		if(!$this->con->execute($sql_update))
			return false;
		return true;
	}
	
	
	/**
	*	fonction permettant de supprimer une catégorie.
	*	supprime une catégorie et toutes ses sous-catégories de façon récursive
	*
	*	@param	(object) référence au manager principal
	*	@param	(object) référence à la catégorie courante
	*	@return	(boolean) renvois le résultat de la suppression récursive de la catégorie (true/false)
	*/
	function deleteCategory($m, $cats)
	{
		while (!$cats->EOF())
		{
			//	Génération d'un élément category
			$cat = new Category();
			$cat->setDefaults($m->user);
			$this->setCategory($cat, $cats->f('category_id'));
			if ($m->loadCategory($cat, $cats->f('category_id')) == false)
				return false;
			//	Suppression de la catégorie courante
			if ($m->delCategory($cat) == false)
				return false;
			
			$subCats = $m->getCategories($cats->f('category_id'),'','ORDER BY category_position');
			if($subCats->nbRowTotal() > 0)
				$this->deleteCategory($m, $subCats);
			$cats->moveNext();
		}
		return true;
	}
}
?>