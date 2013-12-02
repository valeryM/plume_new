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
# Kevyn Lebouille
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

/*
Classe LUM
*/

class lum
{

	//Déclaration des variables
	var $env;
	var $nb_per_page;
	var $nb_pages_per_group;
	var $nb_colonnes;
	var $arryValues;
	var $func_name;
	var $varPage;
	
	//Constructeur
	function lum($env, $func_name, $arryValues, $virtual=0, $nb_per_page='', $nb_pages_per_group='', $nb_colonnes='')
	{
		$this->init();
		
		$this->func_name = $func_name;
		$this->arryValues = $arryValues;
		$this->virtual = $virtual;
		
		if($nb_per_page != '') {
			$this->nb_per_page = $nb_per_page; 
		}
		
		if($nb_pages_per_group != '') {
			$this->nb_pages_per_group = $nb_pages_per_group; 
		}
		
		if($nb_colonnes != "") {
			$this->nb_colonnes = $nb_colonnes;
		}
		
		//Nombre d'éléments du tableau
		if (!$this->virtual)
		{
			$this->nb_elements = count($arryValues);
		}
		else
		{
			$this->nb_elements = $this->virtual;
		}
		
		//Nombre de pages possibles
		$this->nb_pages = ceil($this->nb_elements/$this->nb_per_page);
		//On vérifie que env ne sort pas du nombre de pages
		//if (is_int($env) && $env <= $this->nb_pages) {
		if ($env <= $this->nb_pages && $env != "" ) 	{
			$this->env = $env;
		} else {
			$this->env = 1;
		}
		//echo 'nb pages='.$this->nb_pages.' env:'.$env;
		//Nombre de groupes
		$this->nb_groups = ceil($this->nb_pages/$this->nb_pages_per_group);
		
		//Index de début de page
		$this->index_start = ($this->env-1)*$this->nb_per_page;
		
		//Index de fin de page
		$this->index_end = $this->index_start+$this->nb_per_page-1;
		if($this->index_end >= $this->nb_elements)
		{
			$this->index_end = $this->nb_elements-1;
		}
		
		//Index du groupe en cours
		$this->env_group = ceil($this->env/$this->nb_pages_per_group);
		
		//Index de la première page du groupe
		$this->index_group_start = ($this->env_group-1)*$this->nb_pages_per_group+1;
		if ($this->index_group_start <= 0) $this->index_group_start = 1;
		
		//Index de la dernière page du groupe
		$this->index_group_end = $this->index_group_start+$this->nb_pages_per_group-1;
		if($this->index_group_end > $this->nb_pages)
		{
			$this->index_group_end = $this->nb_pages;
		}
	}
	
	# Initialisation
	function init()
	{
		//Déclaration des variables
		$this->nb_per_page = 30;
		$this->nb_pages_per_group = 10;
		$this->nb_colonnes = 2;
		$this->arryValues = array();
		$this->func_name = NULL;
		$this->varPage = 'env';
		
		//Formatage HTML
		$this->htmlLegende = '';
		//$this->htmlHeader = '<ul id="icon gallery" class="gallery ui-helper-reset ui-helper-clearfix">' ;
			
		$this->htmlHeader = '<table cellpadding="0" cellspacing="0" width="100%" border="1">';
		$this->htmlLineStart = '<tr>';	// '<tr>';
		$this->htmlColStart = '<td>'; //'<li class="icon ui-widget-content ui-corner-tr">';	//<td>';
		$this->htmlColEnd = '<td>';	//</li>';	//		</td>';
		$this->htmlLineEnd = '<tr>';	//</tr>';
		$this->htmlFooter = '</table>';
		//$this->htmlFooter = '</ul>';
		$this->htmlLinksStart = '<p>';
		$this->htmlLinksEnd = '</p>';
		
		$this->htmlCurPgStart = '<span class="lumActive"><b>';
		$this->htmlCurPgEnd = '</b></span>';
		
		$this->htmlPrev = '&lt;page préc.';
		$this->htmlNext = 'page suiv.&gt;';
		$this->htmlPrevGrp = '...';
		$this->htmlNextGrp = '...';
		
		$this->htmlEmpty = '<p><b>Aucun résultat</b></p>';
		
		$this->htmlLinksLib = 'page(s) : ';
	}
	
	
	function drawPage()
	{
		$htmlres = NULL;
		//echo 'Lum::drawpage ';
		if($this->virtual)
			$index = 0;
		if(count($this->arryValues) >0)
		{
			$htmlres .= $this->htmlLegende;
			$htmlres .= $this->htmlHeader;
			$line_num = 0;
			//echo 'Lum::drawpage start='.$this->index_start.' - end='.$this->index_end;
			for($i=$this->index_start; $i<=$this->index_end; $i++)
			{
				$func_name = $this->func_name;
				//echo 'Lum::drawpage '.$func_name;
				if(!$this->virtual)
					$index = $i;
				
				$base_index = $i-$this->index_start;
				
				//*
				if(($base_index+$this->nb_colonnes)%$this->nb_colonnes == 0)
				{
					$htmlres .= $this->htmlLineStart;
					$line_num++;
				}
				//*/
				//echo 'valeur : '.print_r($this->arryValues[$index],true);
				$htmlres .= $this->htmlColStart;
				if(isset($this->arryValues[$index]))  {
					//echo 'Lum::drawpage Appel '.func_name.'('.$this->arryValues[$index].')';
					$htmlres .= $func_name($this->arryValues[$index],$i);
				}
				$htmlres .= $this->htmlColEnd;
				
				//*
				if($i==$this->index_end)
				{
					$rest_cols = ($line_num*$this->nb_colonnes)-1-$base_index;
					for($j=0;$j<$rest_cols;$j++)
						$htmlres .= $this->htmlColStart.'&nbsp;'.$this->htmlColEnd;
				}
				
				if(($base_index+1)%$this->nb_colonnes == 0 || $i==$this->index_end)
					$htmlres .= $this->htmlLineEnd;
				//*/
				
				$index++;
			}
			
			$htmlres .= $this->htmlFooter;
		}
		else
		{
			$htmlres .= $this->htmlEmpty;
		}
		
		return $htmlres;
	}
	
	
	function setURL($pageNum)
	{
		$strLink = $_SERVER['REQUEST_URI'];
		//echo 'start: '.$strLink.'<br>';
		//Suppression de l'information de session
		//if(ereg(session_name().'='.session_id().'([&]){1}',$strLink))
		if(preg_match('#'.session_name().'='.session_id().'([&]){1}#',$strLink))
				//$strLink = ereg_replace(session_name()."=".session_id().'([&]){1}','',$strLink);
				$strLink = preg_replace(session_name()."=".session_id().'([&]){1}','',$strLink);				
		else				
				//$strLink = ereg_replace('([?&]){1}'.session_name().'='.session_id(),'',$strLink);
				$strLink = preg_replace('`([?&]){1}'.session_name().'='.session_id().'`','',$strLink);
		
		
		//if(ereg('([?&]){1}'.$this->varPage.'=([0-9])+',$strLink))
		if(preg_match('`([?&]){1}'.$this->varPage.'=([0-9])+`',$strLink))
		{
			//$strLink = ereg_replace('([?&]){1}'.$this->varPage.'=([0-9])+', '\\1'.$this->varPage.'='.$pageNum, $strLink);
			$strLink = preg_replace('`([?&]){1}'.$this->varPage.'=([0-9])+`', '\\1'.$this->varPage.'='.$pageNum, $strLink);
		}
		else
		{
			//if(ereg('\?',$strLink))
			if(preg_match('`\?`',$strLink))
			{
				$strLink .= '&'.$this->varPage.'='.$pageNum;
			}
			else
			{	
				$strLink .= '?'.$this->varPage.'='.$pageNum;
			}			
		}	
		//echo 'end: '.$strLink.'<br>';
		return str_replace('&','&amp;',$strLink);
	}
	
	
	function drawLinks()
	{
		//Création des liens
		//echo 'Lum::drawlinks ';
		$htmlLinks = '';
		$htmlPrev = '';
		$htmlNext = '';
		$htmlPrevGrp = '';
		$htmlNextGrp = '';
		//echo '1 :'.$current_dir;

		if ($this->index_group_start ==$this->index_group_end )  {
			$htmlres="1";
			return $this->htmlLinksStart.$this->htmlLinksLib.$htmlres;
			exit;
		}
		for($i=$this->index_group_start; $i<=$this->index_group_end; $i++)
		{
			if($i == $this->env)
			{
				$htmlLinks .= $this->htmlCurPgStart.$i.$this->htmlCurPgEnd;
			}
			else
			{
				$htmlLinks .= '<a href="'.$this->setURL($i).'">'.$i.'</a>';
			}
			
			if($i != $this->index_group_end)
			{
				$htmlLinks .= '-';
			}
		}
		
		//Page pr�c�dente
		if($this->env != 1)
		{
			$htmlPrev = '<a href="'.$this->setURL($this->env-1).'">';
			$htmlPrev .= $this->htmlPrev;
			$htmlPrev .= '</a>&nbsp;';
		}
		
		//Page suivante
		if($this->env != $this->nb_pages)
		{
			$htmlNext = '&nbsp;<a href="'.$this->setURL($this->env+1).'">';
			$htmlNext .= $this->htmlNext;
			$htmlNext .= '</a>';
		}
		
		//Groupe pr�c�dent
		if($this->env_group != 1)
		{
			$htmlPrevGrp = '&nbsp;<a href="'.$this->setURL($this->index_group_start - $this->nb_pages_per_group).'">';
			$htmlPrevGrp .= $this->htmlPrevGrp;
			$htmlPrevGrp .= '</a>&nbsp;';
		}
		
		if($this->env_group != $this->nb_groups)
		{
			$htmlNextGrp = '&nbsp;<a href="'.$this->setURL($this->index_group_end+1).'">';
			$htmlNextGrp .= $this->htmlNextGrp;
			$htmlNextGrp .= '</a>&nbsp;';
		}
		
		$htmlres =	$this->htmlLinksStart.
					$this->htmlLinksLib.
					$htmlPrev.
					$htmlPrevGrp.
					$htmlLinks.
					$htmlNextGrp.
					$htmlNext.
					$this->htmlLinksEnd;
		
		if(count($this->arryValues)) {
			return $htmlres; }			
	}
	
	//M�thode de d�bugage
	function debug()
	{
		return '<pre>'.
		'Nombre d\'éléments par page ........ '.$this->nb_per_page."\n".
		'Nombre de pages par groupe ......... '.$this->nb_pages_per_group."\n".
		'Nombre de colonnes ................. '.$this->nb_colonnes."\n".
		'Nombre d\'éléments ................. '.$this->nb_elements."\n".
		'Nombre de pages .................... '.$this->nb_pages."\n".
		'Nombre de groupes .................. '.$this->nb_groups."\n\n".
		'Index de départ .................... '.$this->index_start."\n".
		'Index de fin ....................... '.$this->index_end."\n".
		'Groupe en cours .................... '.$this->env_group."\n".
		'Index de la première page du groupe  '.$this->index_group_start."\n".
		'Index de la dernière page du groupe  '.$this->index_group_end."\n".
		'</pre>';
	}
}//Fin de la classe
?>
