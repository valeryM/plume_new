<?php

//require_once 'path.php';
//require_once $_PX_config['manager_path'].'/prepend.php';

class PathSelector {
	
	
	
	public static function getFields($location, $sel) {
		$rep = '<input name="location" type="hidden" style="height:20px" value="'.$location.'">';
		$rep .= '<input type="hidden" name="c_parentid" value="'.$sel.'" onchange="catChangePath(js_pathlist);setPath($(\'#c_name\'));" >';
		$rep .= '<input type="hidden" name="cat_id" value="'.$sel.'" >';
		return $rep;
	}
	
	public static function getCategoryPathSelector($location,$label) {
		global $id_cat;
		$listCat = explode('.',$location);
		if (count($listCat)>0) $id_cat=$listCat[count($listCat)-1];
		//$id_cat = $_GET['cat_id'];
		$rep = '<span align="center"><label for="cat_id" style="display:inline;"><strong>'.$label.' </strong></label>';
		$rep .= '<input name="location" type="hidden" style="height:20px" value="'.$location.'">';
		$rep .= '<input type="hidden" name="cat_id" value="'.$id_cat.'">';
		$rep .= '<button class="filterButton"  title="'. __('Apply filter').'" ></button>&nbsp';
		$rep .= '<button class="resetButton" title="'. __('reset the filter').'" ></button>';	
		$rep .= '</span>';
		
		return $rep;
	}
	
	public static function getScriptloader() {
		global $_PX_website_config;
		
		$rep = '<script type="text/javascript" >'."\n";
		//require dirname(__FILE__).'/../js/pathSelector.js.php';
		// Contruction du script pour le chemin d'accès aux catégories
		if (!empty($_SESSION['valuesPath'])) $valuesPath=$_SESSION['valuesPath'];
		//$valuesPath[]['separator'] = '" "';
		$rep .= '$(function(){
			// function to activate the plugin pathSelector
			// url must have the initial parameter ?
			var param = $("input[name=location]").attr("value");
			var url = "'. $_PX_website_config['rel_url'].'/manager/js/pathSelector/pathSelector.php";
			
			$("input[name=location]").pathSelector(url, '.$valuesPath.') ;
					
			/* Receive notifications when the value of path selector changes.*/
			$("span.pathSelector").bind("mouseout",function(event) {
				//we could show newVal
				var idx = ($("input[name=location]").attr("value"));				
				idx= idx.substring(idx.lastIndexOf(".")+1);
				if (idx=="1") idx = "allcat";
				$("input[name=cat_id]").attr("value",idx) ;
				$("input[name=c_name]").trigger("keyup");
			});
			
			$("form button.filterButton").click(function() {
				var idx = ($("input[name=location]").attr("value"));
				idx= idx.substring(idx.lastIndexOf(".")+1);
				if (idx=="1") idx = "allcat";
				$("input[name=cat_id]").attr("value",idx) ;	
			});
				
			// function for the filter form, reset the filter
			$("form button.resetButton").click(function() {
				$("input[name=location]").attr("value","1");
				$("input[name=cat_id]").attr("value","allcat");
			});
		
			// function for the filter form, into tools ecm
			// reset the filter
			$("form button.resetButtonEcm").click(function() {
				$("input[name=location]").attr("value","1");
				$("input[name=cat_id]").attr("value","allcat");
			});		
			
			$("span.pathSelector").trigger("mouseout");		
		});'."\n";		
		$rep .= '</script>'."\n";
		return $rep;
	}
	
	
	public static function getPath($location, $idx) {
		global $m;
		
		while (($rs = $m->getCategory($idx)) !== false) {
			if (!$rs->EOF() && $rs->f('category_parentid') != $rs->f('category_id') ) {
				$idx = $rs->f('category_parentid');
				$location = $idx . '.' .$location;
			} else break;
		}
		return $location;
	}
	
	public static function getLocation() {
		
		global $m;
		global $cat_id;
		//global $listCat;
		
		$location = '';
		if (!empty($_SESSION['location'])) $location = $_SESSION['location'];
		if (!empty($_GET['location']))  $location = $_GET['location'];
		
		$catparent_id = '';	
		$idx= '';
		
		//Get the category id and save it
		$cat_id = (!empty($_GET['cat_id'])) ? $_GET['cat_id'] : '';
		//$m->user->savePref('list_index_cat_id', $cat_id, $_SESSION['website_id'], true);
		if ($cat_id == 'allcat') $cat_id = '';
		
		//echo 'location:'.$location;
		//echo ' cat_id:'.$cat_id;
		
		if (!empty($_GET['category_id']))
			$catparent_id = $_GET['category_id'];
		
		
		if ($location == '' && $cat_id != '') {

			$location = PathSelector::getPath($cat_id, $cat_id);
			
		} elseif ( $catparent_id != '') {

			$location = PathSelector::getPath('', $catparent_id);

		} else if ($location =='' && $cat_id == '') {
			// Pour les évènements et les news on récupère la catégorie sauvegardée
			$script = basename($_SERVER['SCRIPT_NAME']);
			if ($script == 'events.php') {
				$event_category_id = $m->user->getPref('events_category_id');
				if ( $event_category_id != 'allcat') {
					$idx = $event_category_id;
					$location = PathSelector::getPath($event_category_id, $event_category_id);
				}
			} else if ($script == 'news.php') {
				$news_category_id = $m->user->getPref('news_category_id');
				if ( $news_category_id != 'allcat') {
					$idx = $news_category_id;
					$location = PathSelector::getPath($news_category_id, $news_category_id);
				}
			} else if (isset($_SESSION["location"]) ){
				$location  = $_SESSION["location"];
			}

		}
		
		
		if ($location == '' && empty($_SESSION['location']))  {
			// charger la catégorie "root"
			$location = $m->getCategory('/')->f('category_id');
			//if (!$rsCat->EOF()) {
			//	$location = $rsCat->f('category_id');
			//} else $location = '1';
			//$cat_id = $location;
		}
		
		// Sauvegarde de la localisation pour les pages index et articles
		/*
		if (basename($_SERVER['SCRIPT_NAME']) == 'index.php'
				|| basename($_SERVER['SCRIPT_NAME']) == 'articles.php'
				|| basename($_SERVER['SCRIPT_NAME']) == 'tools.php'
				|| basename($_SERVER['SCRIPT_NAME']) == 'categories.php'
				|| basename($_SERVER['SCRIPT_NAME']) == 'rsslinks.php'
				|| basename($_SERVER['SCRIPT_NAME']) == 'events.php' ) {
			$_SESSION["location"] = $location;
		}
		*/
		if (isset($_GET['p'])) $p =$_GET['p'];
		else $p='';
		if ( $p == 'index' || $p == 'articles'
				|| $p == 'ecm' || $p == 'categories'
				|| $p == 'rsslinks' || $p == 'events' ) {
			$_SESSION["location"] = $location;
		}
		//echo 'location:'.$location;
		//echo ' cat_id:'.$cat_id;
	
		$arrayLevels = explode('.',$location);
		$listCat = array();
		foreach($arrayLevels as $level => $idx) {
			$rsCat = $m->getCategory($idx);
			if (!$rsCat->EOF()) {
				//echo 'trouvé';
				$listCat[] = array("value"=>$rsCat->f('category_id'),"label" => $rsCat->f('category_name'));
			}
		}
		if (count($listCat)==0) {
			$rsCat = $m->getCategories('0');
			if (!$rsCat->EOF()) {
				$listCat[] = array("value"=>$rsCat->f('category_id'),"label" => $rsCat->f('category_name'));
			}
		}
		
		$_SESSION["valuesPath"] = JSONencode(array("initValue"=>$listCat));
		//echo print_r($listCat,true);	
		
		return $location;
	}


	
	public static function getScript($location) {
		global $m;
		
		$categories = $m->getCategories();
		$rep = '<script type="text/javascript">'."\n".
				"var js_pathlist = new Array();\n";
		
		$cat_id = $categories->f('category_id');
		
		if ($cat_id==0 && $location !=='') {
			if (false !== strrpos($location,'.')) {
				$cat_id = substr($location, strrpos($location,'.')+1);
			} else $cat_id = $location;
		}
		
		//$cat_id=$cats->f('category_id');
		$rep .= 'var cat_id='.$cat_id.';'."\n";
		$rep .= 'function setPath(el) { '."\n"
			."\t".'if (el.value != "") {'
			."\t".'if (el.form.cat_id.value == "allcat" || el.form.cat_id.value=="") el.form.cat_id.value=cat_id;'."\n"
			."\t".'	el.form.c_parentid.value=el.form.cat_id.value;'."\n"
			."\t".'	setUrl("c_name", "c_path", "cat", js_pathlist);'."\n"
			.'}}'."\n";
		
		//echo "js_pathlist[0] = '".$cats->f('category_path')."';\n";
		while (!$categories->EOF()) {
			$rep .= "js_pathlist[".$categories->f('category_id')."] = '".$categories->f('category_path')."';\n";
			$categories->moveNext();
		}
		$rep .= "</script>\n";
		
		return $rep;
	}
}