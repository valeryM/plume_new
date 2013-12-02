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

class pxBOT
{
	var $con;
	var $site_id;
	
	/**
	*	fonction initialisation de la classe pxBOT
	*	@param (string) identifiant du site ("default" par d�faut)
	*/
	function bot($site_id)
	{
		if ($this->con === null)
			$this->con =& pxDBConnect();
		$this->site_id = $site_id;
	}


	//	START Gestion des archives
	

	/**
	*	fonction permettant de r�cup�rer les archives tri�es par ann�es
	*
	*	@param	(string) ordre de tri du r�sultat (desc / asc)
	*	@param	(string) cha�ne de substitution des donn�es de niveau "parent" (<ul></ul>)
	*	@param	(string) cha�ne de substitution des donn�es de niveau "enfants" (<li></li>)
	*	@param	(boolean) d�fini la fa�on dont est retourn� le r�sultat de la fonction (true = string, false = echo string)
	*/
	function archivesListByYear($order='DESC', $list="<ul>%s</ul>", $item="<li>%s</li>", $return=false)
	{
		if($return)
			return pxBOT::archivesList(true, false, false, $order, $return, $list, $item);
		echo pxBOT::archivesList(true, false, false, $order, $return, $list, $item);
	}
	
	/**
	*	fonction permettant de r�cup�rer les archives tri�es par ann�es et par mois
	*
	*	@param	(string) ordre de tri du r�sultat (desc / asc)
	*	@param	(string) cha�ne de substitution des donn�es de niveau "parent" (<ul></ul>)
	*	@param	(string) cha�ne de substitution des donn�es de niveau "enfants" (<li></li>)
	*	@param	(boolean) d�fini la fa�on dont est retourn� le r�sultat de la fonction (true = string, false = echo string)
	*/	
	function archivesListByYearByMonth($order='DESC', $list="<ul>%s</ul>", $item="<li>%s</li>", $return=false)
	{
		if($return)
			return pxBOT::archivesList(true, true, false, $order, $return, $list, $item);
		echo pxBOT::archivesList(true, true, false, $order, $return, $list, $item);
	}
	
	/**
	*	fonction permettant de r�cup�rer les archives tri�es par ann�es, mois et jours
	*
	*	@param	(string) ordre de tri du r�sultat (desc / asc)
	*	@param	(string) cha�ne de substitution des donn�es de niveau "parent" (<ul></ul>)
	*	@param	(string) cha�ne de substitution des donn�es de niveau "enfants" (<li></li>)
	*	@param	(boolean) d�fini la fa�on dont est retourn� le r�sultat de la fonction (true = string, false = echo string)
	*/
	function archivesListByYearByMonthByDay($order='DESC', $list="<ul>%s</ul>", $item="<li>%s</li>", $return=false)
	{
		if($return)
			return pxBOT::archivesList(true, true, true, $order, $return, $list, $item);
		echo pxBOT::archivesList(true, true, true, $order, $return, $list, $item);
	}
	
	
	/**
	*	Fonction permettant de r�cup�rer la listes des archives sous formes de tableau
	*	Option de tri sur les ann�es, les mois et les jours
	*
	*	@param	(boolean) indique si on veut trier les archives sur les ann�es
	*	@param	(boolean) indique si on veut trier les archives sur les mois
	*	@param	(boolean) indique si on veut trier les archives sur les jours
	*	@param	(string) indique l'ordre de tri d�sir� (ascendant / descendant)
	*	@return (string) renvoie une liste non ordonn�e des archives tri�es suivant les crit�res d�sir�s
	*/
	function archivesList($byYear=true, $byMonth=true, $byDay=false, $order='DESC', $return=false, $list="<ul>%s</ul>", $item="<li>%s</li>")
	{
		if(!empty($byYear) || !empty($byMonth) || !empty($byDay))
		{
			$bot = new BOT(config::f('website_id'));
			$res = $bot->getResources($byYear, $byMonth, $byDay, $order);
			
			if($res !== false)
			{
				$result = array();
				while (!$res->EOF() )
				{
					//	TRI sur l'ann�e, le mois et le jour
					if($byYear && $byMonth && $byDay)
						$result[$res->f('myyear')][$res->f('mymonth')][$res->f('myday').' '.$res->f('mydaynb')] = $res->f('count');
					//	TRI su l'ann�e et le mois
					elseif($byYear && $byMonth)
						$result[$res->f('myyear')][$res->f('mymonth')] = $res->f('count');
					//	TRI su l'ann�e
					elseif($byYear)
						$result[$res->f('myyear')] = $res->f('count');
					$res->moveNext();
				}
				
				//	initialisation des variables de subsitution
				$t_list = explode('%s', $list);
				$t_item = explode('%s', $item);
				$listStart = $t_list[0];
				$listEnd = $t_list[1];
				$itemStart = $t_item[0];
				$itemEnd = $t_item[1];
				
				if($return)
					return pxBOT::displayArray($result, $listStart, $listEnd, $itemStart, $itemEnd);
				echo pxBOT::displayArray($result, $listStart, $listEnd, $itemStart, $itemEnd);

			}
			return false;
		}
		return false;
	}

	
	/**
	*	Fonction permettan d'afficher le r�sultat du tableau des archives
	*	Prend en entr�e le tableau des archives
	*
	*	Ex.
	*	<ul>
	*		<li>niveau 1 - position 1</li>
	*		<ul>
	*			<li>niveau 2 - position 1</li>
	*			<ul>
	*				<li>niveau 3 - position 1</li>
	*				<li>niveau 3 - position 2</li>
	*			</ul>
	*			<li>niveau 2 - position 2</li>
	*			<ul>
	*				<li>niveau 3 - position 1</li>
	*				<li>niveau 3 - position 2</li>
	*			</ul>
	*		</ul>
	*	</ul>
	*
	*	@param	(array) tableau des archives (tri�es suivant la m�thode utilis�e: byYear, byMonth, byDay)
	*	@param	(string) cha�ne d'ouverture des �l�ments "parents"
	*	@param	(string) cha�ne de fermeture des �l�ments "parents"
	*	@param	(string) cha�ne d'ouverture des �l�ments "enfants"
	*	@param	(string) cha�ne de fermeture des �l�ments "enfants"
	*	@param	(integer) ToDo
	*	@param	(integer) ToDo
	*	@param	(integer) ToDo
	*	@param	(boolean) ToDo
	*	@param	(string) cha�ne de retour format� suivant les �l�ments "parents" - "enfants" parcourus
	*/
	function displayArray(
							$archives,
							$listStart='<ul>',
							$listEnd='</ul>',
							$itemStart='<li>',
							$itemEnd='</li>',
							$index=0,
							$count=0,
							$parentCount=0,
							$isLast=false,
							&$myTree = null
						)
	{
		$index++;
		$position=0;
		//	Reconstitution des variables de substitutions
		$list = $listStart."\r".'%s'."\r".$listEnd."\r";
		$item = $itemStart.'%s'.$itemEnd."\r";
		foreach($archives as $k=>$v)
		{
			$position++;
			//	SI on est dans un �l�ment array
			if(is_array($v))
			{
				$count=count($v);
				$link = (is_integer($k) ? $k:__($k));
				
				//	SI on est � l'�l�ment root
				//	ALORS stock le nombre d'enfant de cet �l�ment
				if($index==1)
					$parentCount = $count;
					
				//	SI on est � un �l�ment enfant
				//	ET que c'est le premier �l�ment enfant
				//	ALORS ouvre une nouvelle liste (<ul></ul>) et ajoute un �l�ment de liste (<li></li>)
				if($index==2 && $position==1)
					$myTree .=  $listStart."\r".sprintf($item, $link);
				//	SINON ajoute simplement un �l�ment de liste (<li></li>)
				else
					$myTree .= sprintf($item, $link);
					
				//	SI la position de l'�l�m�nt enfant courant = nombre d'enfant de l'�l�ment root
				//	ALORS on est au dernier �l�ment enfant de l'�l�ment root
				if($position == $parentCount)
					$isLast=true;				
				//	Rappel de la fonction r�cursive avec tous les param�tres
				pxBOT::displayArray($v, $listStart, $listEnd, $itemStart, $itemEnd, $index, $count, $parentCount, $isLast, $myTree);
			}
			//	SINON on est dans un �l�ment de liste
			else
			{
				$link = (is_integer($k) ? $k:__($k)).' <span style="color:grey;">('.$v.')</span>';
				//	SI on est au dernier �l�ment parent 
				//	ET que la position du dernier �l�ment enfant = nb d'enfants de l'�l�ment parent
				//	ALORS on est au tout dernier �l�ment de la liste
				if($isLast && ($position==$count))
					$isLast=true;
				
				//	TEST la position de l'�l�ment dans la hi�rarchie
				switch($index)
				{
					//	Element parent
					case 1:
						$myTree .= sprintf($item, $link);
						break;
					//	Element enfant
					case 2:
						if($position == 1)
							$myTree .= $listStart."\r".sprintf($item, $link);
						elseif($position == $count)
							$myTree .= sprintf($item, $link).$listEnd."\r";
						else
							$myTree .= sprintf($item, $link);
						break;
					//	Element enfant-enfant (petit-fils)
					case 3:
						if($position == 1)
						{
							if($position == $count)
								$myTree .= $listStart."\r".sprintf($item, $link).$listEnd."\r";
							else
								$myTree .= $listStart."\r".sprintf($item, $link);
						}
						elseif($position == $count && !$isLast)
							$myTree .= sprintf($item, $link).$listEnd."\r";
						elseif($position == $count && $isLast)
							$myTree .= sprintf($item, $link).$listEnd.$listEnd."\r";
						else
							$myTree .= sprintf($item, $link);
				}
			}
		}
		return "\r".sprintf($list, $myTree.$listEnd)."\r";
	}
	//	END Gestion des archives

	
	/**
	*	Display the list of categories for a breadcrumb. Like
	*	Home >> Subcategory >> Subsubcategory
	*	
	*	@proto function pxSingleCatTree
	*	@param string s substitution string ('<ol>%s</ol>')
	*	@param boolean return Type of return : true return result as a string, false (default) print in stdout
	*/
	function pxSingleCatTree($s='<ol>%s</ol>', $return=false)
	{
		$result = '';
		$remove_numbers = config::fbool('remove_numbers');
		$res = '';
		$categories = array();
		$i=0;
		$categories[$i] = $GLOBALS['_PX_render']['cat'];
		$parentcat    = $GLOBALS['_PX_render']['cat']->f('category_parentid');
		$currentcatid = $GLOBALS['_PX_render']['cat']->f('category_id');
		$i++;
		while ($parentcat !=  $currentcatid) {
			$categories[$i] = FrontEnd::getCategory($parentcat);
			$parentcat    = $categories[$i]->f('category_parentid');
			$currentcatid = $categories[$i]->f('category_id');
			$i++;
		}
		reset($categories);
		
		foreach ($categories as $cat)
		{
			$title = $cat->f('category_name');
			if ($remove_numbers)
				$title = px_removeNumbers($title);
			if($GLOBALS['_PX_render']['cat']->f('category_name') !== htmlspecialchars($title))
				$res = '<li><a href="'.$cat->getPath().'">'.htmlspecialchars($title).'</a> &gt; </li>'.$res."\n";
			else
				$res = '<li><strong>'.htmlspecialchars($title).'</strong></li>'.$res."\n";
		}
		$result = sprintf($s, $res);
		if ($return) return $result;
		echo $result;
	}
	
	/**
	*	Fonction permettant de r�cup�rer l'ensemble d'un article sur une seule page de gabarit
	*
	*	@param	(boolean) d�fini la fa�on dont les donn�es sont renvoy�es (return OU echo)
	*	@return	(string) renvoi la cha�ne de caract�re form�e, renvoy� suivant la m�thode voulue
	*/
	function pxGetAllArticle($getDesc=false, $return=false)
	{
		$myRes = $GLOBALS['_PX_render']['res'];
		$myRes_typeId = $myRes->f('type_id');
		$myRes_id = $myRes->f('resource_id');
		
		$article = new Article();
		if(!$article->load($myRes_id))
			return;
		if(!$article->loadPages())
			return;
		
		$result = ($getDesc ? text::parseContent($article->f('description')) : '');
		while (!$article->pages->EOF())
		{
			$result .=	'<h2>'.$article->pages->f('page_title').'</h2>'.
						$article->getFormattedContent('page_content', 'html', 'pages');
			$article->pages->moveNext();
		}
		if($return)
			return $result;
		echo $result;
	}
	
	/**
	*	Display an ordered list of pages in the article with link to the pages.
	*	The current page is set as ''active'' with the corresponding <li> element
	*	being from the __current__ class.
	*
	*	An output example is:
	*
	*	|<ol>
	*	|<li><a href="/cat/my-article">Page 1</a></li>
	*	|<li class="current"><a href="/cat/my-article2">Page 2</a></li>
	*	|<li><a href="/cat/my-article3">Page 3</a></li>
	*	|</ol>
	*
	*	@proto function pxArtListPages
	*	@param string s Substitution string ('%s')
	*	@param boolean return Type of return : true, return result as a string, false (default) print in stdout
	*/
	function pxArtListPages($s = '%s', $menu = '%s', $return=false)
	{
		print_r( $GLOBALS['_PX_render'] );
		return;
		
		$myRes = $GLOBALS['_PX_render']['res'];
		$myRes_typeId = $myRes->f('type_id');
		$myRes_id = $myRes->f('resource_id');
		
		
		$article = new Article();
		if(!$article->load($myRes_id))
			return;
		if(!$article->loadPages())
			return;
			
		$result = '';
		if ($article->pages->nbRow() > 1) {
			$index = $article->pages->getIndex();
			$article->pages->moveStart();
//			$lp = '<ul id="menuAbout">'."\n";
			$lp = '';
			while (!$article->pages->EOF()) {
				$active = ($index == $article->pages->getIndex()) ? ' class="current"' : '';
				$page = ($article->pages->f('page_number') == 1) ? '' : $article->pages->f('page_number');
				$lp .= '<li'.$active.'><a href="'.$article->getPath().$page.'">'.$article->pages->getTextContent('page_title').'</a></li>'."\n";
				$article->pages->moveNext();
			}
//			$lp .= '</ol>'."\n";
			$article->pages->move($index);
			$result = sprintf($s, sprintf($menu, $lp));
		}
		if ($return) return $result;
		echo $result;
	}
}
?>