<?php

function pxArticlePagesNav($s = '%s', $return=false)  {
	
	$result = '';
	if ($GLOBALS['_PX_render']['art']->pages->nbRow() > 1) {
		$lp = '<div id="navChapitres">'."\n";
		$index = $GLOBALS['_PX_render']['art']->pages->getIndex();
		$GLOBALS['_PX_render']['art']->pages->moveStart();
		$lp .= '<ul>'."\n";
		while (!$GLOBALS['_PX_render']['art']->pages->EOF()) {
			$active = ($index == $GLOBALS['_PX_render']['art']->pages->getIndex()) ? 'current' : '';
			$page = ($GLOBALS['_PX_render']['art']->pages->f('page_number') == 1) ? '' : $GLOBALS['_PX_render']['art']->pages->f('page_number');
			$path = str_replace(' ','-',$GLOBALS['_PX_render']['art']->pages->getTextContent('page_title'));
			$path = str_replace("'",'-',$path);
			$pageTitle = $GLOBALS['_PX_render']['art']->pages->getTextContent('page_title');
			//$lp .= '<li'.$active.'><a href="'.$GLOBALS['_PX_render']['art']->getPath().$page.'">'.$pageTitle.'</a></li>'."\n";
			
			$lp .= '<li style="white-space:nowrap '.$active.'"><a href="#'.$path.'" >'.$pageTitle.'</a></li>'."\n";
			
			$GLOBALS['_PX_render']['art']->pages->moveNext();
		}
		$lp .= '</ul></div>'."\n";
		$GLOBALS['_PX_render']['art']->pages->move($index);
		$idRes = $GLOBALS['_PX_render']['art']->f('resource_id');
		$lp .= '<script type="text/javascript">'."\n";
		$lp .= '	$(document).ready(function() {'."\n";
		$lp .= '		var $tabs'.$idRes.' = $("#tabs-'.$idRes.'").tabs();'."\n";
		$lp .= '		/* si le lien contient un # */'."\n";
		$lp .= '		if (window.location.hash!="") {'."\n";
		$lp .= '			/* sélection de l onglet à afficher */'."\n";
		$lp .= '			var ref = window.location.hash.substring(6);'."\n";
		$lp .= '			$tabs'.$idRes.'.tabs("select", ref); // switch to tab'."\n";
		$lp .= '		}'."\n";
		$lp .= '	});'."\n";
		$lp .= '</script>'."\n";
				
		$result = sprintf($s, $lp);
	}
	
	if ($return) return $result;
	echo $result;
}

function pxArticlePagesContent($s='%s', $return=false) {
	$nbrePage = $GLOBALS['_PX_render']['art']->pages->nbRow();
	$result = '';
	if ($nbrePage >= 1) {
		$lp = '';
		while (!$GLOBALS['_PX_render']['art']->pages->EOF()) {
			$pageTitle = $GLOBALS['_PX_render']['art']->pages->f('page_title');
			$path = str_replace(' ','-',$pageTitle);
			$path = str_replace("'",'-',$path);
			$lp .= '<div id="'.$path.'">';

			$lp .= '<span class="art-title">';
			$lp .= $GLOBALS['_PX_render']['art']->f('title');
			$lp .= '</span>';
			// affiche le titre du chapitre si + d'une page
			if ($nbrePage > 1) {
				$lp .= '&nbsp;-&nbsp;';
				$lp .= '<span class="art-page-title">';
				$lp .= $pageTitle;
				$lp .= '</span>';
			}
			$lp .= '<br/>';
			$lp .= pxArtPageContent(true); 
			
			if ($nbrePage >= 1) {
				$lp .= '</div>';
			}
			
			$GLOBALS['_PX_render']['art']->pages->moveNext();
		}
				
		$result = sprintf($s, $lp);
	}
	
	if ($return) return $result;
	echo $result;	
}

function pxLastResDescription2($s='%s', $limit=0, $return=false)
{
    $result = '';
    if ($limit) {
        $text = text::truncate(trim(text::parseContent($GLOBALS['_PX_render']['last']->f('description'),'Text'), $limit));
        $result = sprintf($s, $text);
    } else {
        $result = trim(text::parseContent($GLOBALS['_PX_render']['last']->f('description'),'Text'));
    }
    if ($return) return $result;
    echo $result;
}

/**
 * 
 * @param string $s (%s) to format the outpur
 * @param integer $limit (all) number of characters
 * @param boolean $return (false) true to return the value,false to output to media
 * @return mixed string or any 
 */
function pxLastNewsShortContent($s='%s', $limit=0, $return=false)
{
    $result = '';
    if ($limit) {
        $text = text::truncate(trim(text::parseContent($GLOBALS['_PX_render']['last']->f('news_shortcontent'), 'Text')), $limit);
        $result = sprintf($s, $text);
    } else {
        $result = trim(text::parseContent($GLOBALS['_PX_render']['last']->f('news_shortcontent'),'Text'));
    }
    if ($return) return $result;
    echo $result;
}

/**
 * Charge les catégories principales
 * Retourne le contenu sous la forme Recordset
 *
 * @return Recordset or False
 */
function pxGetMasterCategoriesByName($name = '', $website='')  {
	if ($website=='') $website = config::f('website_id');
	if ($name !='' && $name!=null)  {
		$con =& pxDBConnect();
		$r = 'SELECT * FROM '.$con->pfx.'categories
	    			WHERE category_name = \''.$name .'\' ';
		if ($website!='')
	    	$r .= 'AND website_id=\''.$con->esc($website).'\' ';
		
		//echo "requete : " . $r;
		if (($rs = $con->select($r,'Category')) !== false) {
			return $rs;
		} else {
			$con->setError('MySQL: '.$con->error(), 500);
			//echo $con->error();
			return false;
		}
	} else {
		return false;
	}
}

/**
 * 
 * @param string $fromCat string to define the list of categories
 * @return array the list of ids and an array of tag for each id
 */
function pxArrayEventsCats($fromCat) {
	// Charge la liste des catégories Evènement
	$arrayItems = explode(',',$fromCat);
	$catList = array();
	foreach($arrayItems as $item) {
		$itemData = explode('=',$item);
		$category = pxgetMasterCategoriesByName($itemData[1],pxWebsiteName('%s',true));
		$catList['ids'][] = $category->f('category_id');
		$catList['idTag'][$category->f('category_id')] = $itemData[0];
	}
	return $catList;
}


/**
 * 
 * Get resources in a category.
 *
 * @param array $idList Category id
 * @param string $website 
 * @return string Ready to use SQL
 */
function pxGetResourcesInCats($idList,$website='')
{
	$con =& pxDBConnect();
	$catsForEvent = implode(',',$idList);
	
	$sql = 'SELECT '.$con->pfx.'categories.category_id, category_path,
					'.$con->pfx.'resources.resource_id,
					'.$con->pfx.'resources.type_id,
					'.$con->pfx.'resources.title,
					'.$con->pfx.'resources.description,
					event_shortcontent shortcontent,
					'.$con->pfx.'resources.path,
				 	event_startdate, event_enddate,
					event_shortcontent, event_content,
					event_startdate startdate, 
					'.$con->pfx.'news.news_titlewebsite,
					'.$con->pfx.'news.news_linkwebsite,
					'.$con->pfx.'news.news_content,	
					'.$con->pfx.'news.news_shortcontent			
				FROM '.$con->pfx.'categories INNER JOIN '.$con->pfx.'categoryasso USING(category_id) 
					INNER JOIN  '.$con->pfx.'resources USING(identifier)
					LEFT JOIN '.$con->pfx.'events  USING(resource_id)
					LEFT JOIN '.$con->pfx.'news  USING(resource_id)
				WHERE '.$con->pfx.'categoryasso.category_id IN ('.$catsForEvent.')';					
							
	if ($website != '') {
		$sql .= "\n".'AND '.$con->pfx.'resources.website_id=\''
				.$con->esc($website).'\'';
	}
	return $sql;
}

function getAllLastEventsFromCategories($listCategories, $timeBefore, $timeAfter, $typeContenu = 'content',$limit = '')  {
	// définit les critères
	$filtreContenu = 'event_shortcontent' ;
	if ($typeContenu == 'content') $filtreContenu = 'event_content';

	$con =& pxDBConnect();

	$r = pxGetResourcesInCats($listCategories, pxWebsiteName('%',true));
	$r .= ' AND '.$filtreContenu.' != \'=html\'
			AND '.$filtreContenu.' != \'=html\n<br />\'
			AND '.$con->pfx.'resources.type_id=\'events\'
			AND status='.PX_RESOURCE_STATUS_VALIDE;

	// restriction sur la date de début
	$r .= " AND ( event_startdate >= '". date('YmdHis',strtotime($timeBefore)) ."' "
			."OR (event_startdate < '". date('YmdHis',strtotime($timeBefore)) ."' "
			."AND event_enddate >= '". date('YmdHis') ."' "
			."AND event_enddate <= '". date('YmdHis',strtotime('+6 months')) ."' ) "
			." )";
	$r .= " AND event_startdate <= '".date('YmdHis', strtotime($timeAfter)) ."' ";
	$r .= ' ORDER BY startdate ASC';
	 
	if ($limit != '')    $r .= ' LIMIT 0,'.$limit;
	 
	//echo $r;
	if (($rs = $con->select($r, 'Events')) !== false) {
		return $rs;
	} else {
		$con->setError('MySQL: '.$con->error(), 500);
		return false;
	}

}

/**
 * 
 * @param string $catName Category name
 * @param integer $taille (1) Font size in em
 * @return string
 */
function pxAfficheTooltipCategories($catName, $taille='1') {
	
	$cat = pxGetMasterCategoriesByName($catName);
	$rep = '';
	//$cat->moveStart();
	//$fctMenuIsActivate= false;
	if (!$cat->EOF()) {
		//echo 'nbrow: '.$cat->nbRow();
		$rep .= '<table id="menu'.$cat->f('category_id'). '" class="menuToolTip" style="display:none;z-index:999;moz-opacity: 0.9;opacity: 0.9;khtml-opacity: 0.9;filter:alpha(opacity=90);" ';
		//$rep .= 'moz-opacity: 0.9;opacity: 0.9;khtml-opacity: 0.9;filter:alpha(opacity=90);" ';
		$rep .= 'cellspacing="4" cellpadding="0">'."\n";
		//echo 'id: '.$cat->f('category_id');
		$childCat = FrontEnd::getCategories($cat->f('category_id'),'ORDER BY category_position');
		if ($childCat===FALSE) return '';
		while (!$childCat->EOF()) {
			$lien = '/?'.$childCat->f('category_path');
			$title = trim(text::parseContent($childCat->f('category_description'),'Text'));
			$rep .= '<tr>'."\n";
			$rep .= '	<td style="white-space:nowrap;">'."\n";
			$rep .= '		<a style="font-size:'.$taille.'em;" title="'.$title.'" href="'.$lien.'" >';
			$rep .= $childCat->f('category_name');
			$rep .= '</a>'."\n";
			$rep .= '</td></tr>'."\n";
			$childCat->moveNext();
		}
		$rep .= '</table>'."\n";
	}
	
	echo $rep;
	
}


function pxResumeRssLinks($path, $cssClass='', $showLogo=true, $showTitle=true) {
	//require_once('plume/manager/tools/simplepie/register.php');

	$resources = pxGetResourceFromLongPath($path);
	$numRes = $resources->f('resource_id');
	$categoryPath = $resources->f('category_path');
	$category = FrontEnd::getCategory($categoryPath);

	$idCat = $category->f('category_id');
	//$res = $this->m->getRsslinks($idCat);
	//echo 'numRes:'.$numRes.' idCat:'.$idCat;
	$res = pxGetRsslinks($numRes,$idCat);
	echo '<div id="FluxRss" >';
	echo '<div class="Widget RssLinks '.$cssClass.'" >';
	if ($showLogo && $res !=false) {
		$url = explode('/',$res->f('rsslink_linkwebsite'));
		$website = $url[0].'//'.$url[2];
			
		echo '<div class="logo" >';
		echo '<a target="_blank" href="'.$website.'" >';
		if ($showTitle ) {
			$titlepage = $res->f('title');
			$titleLink = $res->f('rsslink_titlewebsite');

			if ($titlepage != '')  {
				echo '<span title="'.$titleLink.'">';
				echo $titlepage;
				echo '</span>';
			}

		}
		echo '</a>';			
		echo '</div>';
		echo '<div class="ico_rss"></div>';
	}
	echo pxGetFeed($res->f('rsslink_linkwebsite'),'items:5|showtitle:false|showdesc:false|targetLink:"'.$res->f('title').'"');
	echo '	</div>';
	echo '</div>';
}


/**
 * Charge la ressource pour un chemin (path)
 * @param string $path : Chemin de la ressource
 * @param boolean $online : true to select online resources only
 * @param string $website : name of the website (empty)
 * @param boolean $intoChild : true to load into childs categories
 * @return Recordset
 */
function pxGetResourceFromLongPath($path, $online=true, $website='',$intoChild = false)  {
	if ($website=='') $website = config::f('website_id');
	if (substr($path,-1)!='/') $path .= '/';
	$con =& pxDBConnect();
	
	$matches = explode("/",$path);
	$allPath = array();
	$tmpPath = '/';
	foreach ($matches as $match)  {
		if ($match != '') {
			$tmpPath = $match . '/';
			$allPath[]= "'".$match."'";
		}
		//if ($matches[$i] !='') $tmp_path .= $matches[$i].'/';
	}
	if (count($allPath)==0) return false;
	//echo 'chemins  '.print_r($allPath,true).'<br>';

	if ($intoChild) 
		$pathCriteria = 'LIKE \''.$path.'%\' ';
	else  
		$pathCriteria = '= \''.$path.'\' ';
	
	$r = 'SELECT '.$con->pfx.'resources.*, '.$con->pfx.'categoryasso.*, '.$con->pfx.'categories.* 
    		FROM '.$con->pfx.'resources
    			INNER JOIN '.$con->pfx.'categoryasso USING(identifier)
    			INNER JOIN '.$con->pfx.'categories USING(category_id)  ';
	$r = SQL::getResources(false);
	$r .= 'AND ('.$con->pfx.'categories.category_path '.$pathCriteria;
	$r .= ' OR '.$con->pfx.'resources.path IN ('.$allPath[count($allPath)-1].') )';
	$r .= ' AND '.$con->pfx.'categories.website_id=\''.$con->esc($website).'\' ';
	$r .= ' AND status='.PX_RESOURCE_STATUS_VALIDE;
	if ($online == true) {
		$r .= ' AND (('.$con->pfx.'resources.publicationdate <= '.date::stamp();
		$r .= ' AND '.$con->pfx.'resources.enddate >= '.date::stamp().' ) ';
		$r .= ' OR ('.$con->pfx."resources.type_id = 'events' AND ".$con->pfx.'resources.enddate >= '.date::stamp().') ) ';
	}

	$r .= ' ORDER BY plume_categories.category_path DESC ';

	//echo $r.'<br>';
	if (false !== $rs = $con->select($r, 'Paginator') ) {
		return $rs;
	} else {
		$con->setError('MySQL: '.$con->error(), 500);
		return false;
	}
}

/**
 * Get and return Rss links resources
 * @param int $res_id Resource id
 * @param int $cat_id Category id
 * @param int $limit rows number to return, '' for all
 * @param string $website Website name
 * @return mixed Recordset or False
 */
function pxGetRsslinks($res_id, $cat_id, $limit = '',$website='')  {
	if ($website=='') $website = config::f('website_id');
	$con =& pxDBConnect();
	
	$r = 'SELECT '.$con->pfx.'resources.*, priority , '.$con->pfx.'rsslinks.*
					FROM '.$con->pfx.'resources INNER JOIN '.$con->pfx.'categoryasso
    					ON '.$con->pfx.'resources.identifier='.$con->pfx.'categoryasso.identifier
    					INNER JOIN '.$con->pfx.'rsslinks ON '.$con->pfx.'resources.resource_id='.$con->pfx.'rsslinks.resource_id
    				WHERE '.$con->pfx.'resources.resource_id=\''.$res_id.'\'
    					AND  '.$con->pfx.'categoryasso.category_id=\''.$cat_id.'\'
    					AND website_id=\''.$website.'\' ';

	$r.= ' AND status='.PX_RESOURCE_STATUS_VALIDE;

	//if ($this->availableonline) {
		$r .= ' AND '.$con->pfx.'resources.publicationdate <= '.date::stamp();
		$r .= ' AND '.$con->pfx.'resources.enddate >= '.date::stamp();
	//}
	// limitation du nombre de lignes
	if ($limit != '') {
		$limit = (preg_match('/^[0-9]+$/',$limit)) ? '0,'.$limit : $limit;
		$r .= ' LIMIT '.$limit.' ';
	}
	//echo $r;
	if (($rs = $con->select($r, 'Rsslinks')) !== false) {
		return $rs;
	} else {
		$con->setError('MySQL: '.$con->error(), 500);
		return false;
	}
}



/**
 Display the list of primary categories

 @proto function pxPrimaryCategories
 @param string s Substitution string ('<ul>%s</ul>')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
 */
function pxMyMenuPrimaryCategories($catId,$s='<ul>%s</ul>',$sub = '<li>%s</li>', $return=false, $orderBy = 'category_name')
{
	$ordermanual = config::fbool('order_cat_manual');
	$remove_numbers = config::fbool('remove_numbers');
	$order = 'ORDER BY category_path';
	if ($ordermanual && $orderBy!='') {
		$order = 'ORDER BY '.$orderBy;
	}
	$prim    = FrontEnd::getCategories($catId, $order);

	$cats = '';
	$result = '';
	while (!$prim->EOF()) {
		if ($prim->f('category_path') != '/') {
			$path = $prim->getPath();
			$name = $prim->f('category_name');
			if ($remove_numbers) {
				$name = px_removeNumbers($name);
			}
			$title = htmlspecialchars(text::removeEntities(trim(strip_tags(text::parseContent($prim->f('category_description'),'Text')))));
			$link = '<a id="cat-'.$prim->f('category_id').'" title="'.$title.'" href="'.$path.'">'.htmlspecialchars($name).'</a>'."\n";
			$link .= pxMenuCategory($prim->f('category_id'),'<ul class="subnav" >%s</ul>',$sub,true);
			$cats .= sprintf($sub, $link);
		}
		$prim->moveNext();
	}
	$result = sprintf($s, $cats);

	if ($return) return $result;
	echo $result;
}


/**
 * Affiche le menu des sous rubriques
 * @param Integer $cat : id de la catégorie parente
 * @return html content
 */
function pxAfficheMenuRubrique3($cat='')  {
	$rep = '';
	if ($cat != '') {

		$prim = FrontEnd::getCategories($cat, 'ORDER BY category_position');
		$nbreCat = $prim->nbRowTotal();
		
		$rep .= '<div class="topnav">';
		$rep .= '<ul class="sf-menu topnav">';
		$cpt = 0;
		$largeur = round(1000/9);	

		while (!$prim->EOF()) {
			// récupère la première catégorie
			if (($cpt+1)== $nbreCat ) 
				$last =true;
			else 
				$last=false;
			$rep .= pxGetListRubrique($prim,1,$last);
			$cpt++;
			
			$prim->moveNext();
		} // end while : fin boucle sur la sous-rubrique
	} // end if : fin boucle sur la rubrique
	$rep .= '</div>';
	$url = pxInfo('url',true);
	$rep .='<script src="'.$url.'manager/js/superfish/js/superfish.js"></script>
			<script>
					
    (function($){ //create closure so we can safely use $ as alias for jQuery

		$(document).ready(function(){
        
	        var menuOptions = {
		            delay:       200,                              // less than one second delay on mouseout
		            animation:   {opacity:"show",height:"show"},  // fade-in and slide-down animation
		            speed:       "fast",                          // faster animation speed
		            autoArrows:  false,                           // disable generation of arrow mark-up
		            dropShadows: false                            // disable drop shadows
	        }
	        // initialise plugin
	        $("ul.sf-menu").superfish(menuOptions);
	
		});

	})(jQuery);					

			</script>';

	echo $rep;
}

function pxGetListRubrique($cat, $level = 1, $last=false)  {
	if ($last) $classLast = 'class="lastLevel"'; else $classLast='';
	$prim = FrontEnd::getCategories($cat->f('category_id'), 'ORDER BY category_position');
	$countSubLevels = $prim->nbRowTotal();

	$classLink = '';
	if ($countSubLevels>0) {
		$response = '<li '.$classLast.' >'."\n";
		$classLink = 'class="sf-with-ul"';
	} elseif($classLast!='')
		$response = '<li '.$classLast.' >'."\n";
	else 
		$response = '<li>'."\n";
	
	$itemSub = $cat->f('category_id');

	$lien = '<a '.$classLink.' level="'.$level.'" title="'.trim(text::parseContent($cat->f('category_description'),'Text')).'"';
	$lien .= 'href="/?'.$cat->f('category_path').'" >' . $cat->f('category_name')."\n"; 
	$urlTheme = pxInfo('filesurl',true).'theme/'.pxInfo('theme',true);
	
	if ($countSubLevels>0 && $level>1) {
		$lien .= '<span style="float:right"><img src="'.$urlTheme.'/img/icons/fleche_blanche.png" alt="" /></span>';
	}
	
	$lien .= '</a>'."\n";

	$response .= $lien;
	$closeSubNav = false;
	if ($countSubLevels>0 ) {
		if ($level == 1) {
			$response .= '<ul class="subnav">'."\n";
		} else {
			$response .= '<ul class="subnav sublevel">'."\n";
		}
		$subLevel = $level+1;
		while(!$prim->EOF()) {
			$response .= pxGetListRubrique($prim, $subLevel);
			$prim->moveNext();
		}

		$response .= '</ul>';
	}
	$response .= '</li>';
	return $response;
}

function pxGetArrayEventsCats($fromCat) {
	// Charge la liste des catégories Evènement
	$arrayItems = explode(',',$fromCat);
	$catList = array();
	foreach($arrayItems as $Item) {
		$itemData = explode('=',$Item);
		$category =pxGetMasterCategoriesByName($itemData[1]);
		$catList['ids'][] = $category->f('category_id');
		$catList['idTag'][$category->f('category_id')] = $itemData[0];
	}
	return $catList;
}

/**
 * retourne les évènements publié pour une catégorie
 * @param Integer $cat_id : identifiant de la catégorie
 * @param Integer $limit : nombre maxi d'enregistrents retournés
 * @param Integer $annee : filtre sur l'année
 * @param Integer $mois : filtre sur le mois
 * @return recordset
 */
function pxGetLastEventsFromCat($cat_id, $limit='', $annee='', $mois='',$website='')  {
	if ($website=='') $website = config::f('website_id');
	// définit les critères
	if ($annee == '') $annee = date('Y');
	if ($mois == '') {
		$mois = sprintf('%02s',date('m'));
	} else {
		$mois = sprintf('%02s',$mois);
	}


	$con =& pxDBConnect();

	$r = 'SELECT category_id, '.$con->pfx.'resources.*, priority , '.$con->pfx.'events.*
    		FROM '.$con->pfx.'resources LEFT JOIN '.$con->pfx.'categoryasso
    				ON '.$con->pfx.'resources.identifier='.$con->pfx.'categoryasso.identifier
    				LEFT JOIN '.$con->pfx.'events ON '.$con->pfx.'resources.resource_id='.$con->pfx.'events.resource_id ';
	 
	if (is_array($cat_id)) {
		$r .= 'WHERE category_id IN ('.implode(',',$cat_id).') ';
	} else {
		$r .= 'WHERE category_id='.$cat_id.' ';
	}
	$r .= 'AND website_id=\''.$website.'\' ';
	$r .= ' AND status='.PX_RESOURCE_STATUS_VALIDE;
	
	$condition = ' AND (left(event_startdate,6) = "' . $annee.$mois . '" ';
	$condition .= ' OR left(event_enddate,6) = "'. $annee.$mois .'" )';
	$r .= $condition;

	$r .= ' ORDER BY event_startdate ';
	// limitation du nombre de lignes
	if ($limit != '') {
		$limit = (preg_match('/^[0-9]+$/',$limit)) ? '0,'.$limit : $limit;
		$r .= ' LIMIT '.$limit.' ';
	}
	//echo $r;
	if (($rs = $con->select($r, 'Event')) !== false) {
		return $rs;
	} else {
		$con->setError('MySQL: '.$con->error(), 500);
		return false;
	}

}

/**
 * Construit la liste des évenements, basée sur le calendrier
 * 
 * @param Recordset $catSelected
 * @param Array $catList ['ids','idTag':{id1, id2}]
 * @param integer $Annee Année sélectionnée
 * @param inetger $Mois Mois Sélectionné
 */
function pxRenderEventsContent($catSelected, $catList, $Annee, $Mois, $return=false) {
	global $array_path;
	// récupère la liste des évènements
	$nbreEvent = count($array_path['res']);
	$rep = '';
	
	$last =  pxGetLastEventsFromCat($catList['ids'],'', $Annee, $Mois);

	// Si il y a quelque chose à afficher
	if (false != $last)  {
		$rep .= '<div id="contenus" style="height:650px;display:block;overflow-y:auto;overflow-x:hidden;">';
		$cpt=0;
		$newestEvent = false;
		//echo $Annee.'/'.$Mois;
		while (!$last->EOF())  {
			//echo '<div id="content-'.$cpt.'" class="event '.$catList['idTag'][$last->f('category_id')].'" ><br/>';
			$AnneeMoisEvt = date('Y/m',date::unix($last->f('event_startdate')));
			//echo 'AnneeMois:'.$AnneeMoisEvt;
			// création du tag pour l'evt à venir
			if (!$newestEvent && $Annee == date('Y') && $Mois == date('m')
					&& date('Y/m/d H:i',date::unix($last->f('event_startdate')))> date('Y/m/d H:i') ) {
				$newestEvent  = true;
				$classNewestEvent = 'newestEvent';
			} elseif (!$newestEvent && ($Annee.'/'.$Mois) == $AnneeMoisEvt && $last->f('resource_id')==$array_path['res'][0]) {
				$newestEvent  = true;
				$classNewestEvent = 'newestEvent';
			} else {
				$classNewestEvent = '';
			}
			// règle d'affichage par défaut à la construction du contenu
			$id=$last->f('category_id');
			$style='style="display:none;"';
			if ( $catSelected->f('category_id') == $id) {
			//if ( ($this->sub == $id && $this->rub == '') || $this->rub == $id) {
				$style='';
			}
			$rep .= '<div id="'.$last->f('category_path').$last->f('path').'" class="event '.$catList['idTag'][$id].' '. $classNewestEvent.'" '.$style.' ><br/>';
			// affiche le contenu des évènements
			$rep .= '<span class="art-page-title">';
			$rep .= $last->f('title');
			$rep .= '</span>';
			$rep .= '&nbsp;&nbsp;&nbsp;';
			$rep .= text::parseContent($last->f('event_content'),'Html');
			$rep .= '<hr class="visible" />';
			$rep .= '</div>';

			$last->moveNext();
			$cpt++;
		}
		$rep .= '</div>';
		
		$rep .= '<script type="text/javascript">'."\n";
		$rep .= '	$(document).ready(function() {'."\n";
		$rep .= '		/* si le lien ne contient pas un # */'."\n";
		$rep .= '		if (window.location.hash=="") {'."\n";
		/*
		$rep .= '			var ref = $(".newestEvent");'."\n";
		$rep .= ' console.log(ref);'."\n";
		$rep .= '			var posTop= ref.offset().top -180+ parseInt(ref.css("padding-top"),10) + parseInt(ref.css("margin-top"),10);'."\n";
		$rep .= '			$("#contenus").animate({scrollTop: posTop },"slow"); '."\n";
			*/	
		// n'affiche que le bloc concerné par la valeur sélectionnée
		$rep .= '			var idFilterChecked = $("#"+$("#flt_used").val());'."\n";
		$rep .= '			//var idFilterChecked = $(".flt_agenda:checked");'."\n";
		$rep .= '			idFilterChecked.trigger("click");'."\n";
		$rep .= '			idFilterChecked.attr("checked","checked");'."\n";
		$rep .= '			/* sélection du bloc */'."\n";
		$rep .= '			var $target= $("#contenus");console.log($target);'."\n";
		$rep .= '			$target.scrollTo(".newestEvent");'."\n";
		
		$rep .= '		}'."\n";
		$rep .= '	});'."\n";
		$rep .= '</script>'."\n";
			
		if ($return)
			return $rep;
		else echo $rep;

	} // fin de l'affichage des évènement

}


/**
 * Retourne la liste des mots clés pour une ressource
 * @param $id_res : ID de la resource
 * @param $limit : limitation du nbre de lignes '1,30' par défaut
 * @return recordset
 */
function pxResWordIndex($id_res, $limit='1,30')  {
	$con =& pxDBConnect();
	
	$r = 'SELECT '.$con->pfx.'searchwords.word_id, word, sum(occ) AS occurence
			FROM '.$con->pfx.'searchocc
					INNER JOIN '.$con->pfx.'searchwords USING (word_id)
			WHERE resource_id = '.$id_res .' AND length(word)>5
			GROUP BY '.$con->pfx.'searchwords.word_id, word
			ORDER BY sum(occ) DESC, word ';

	// limitation du nombre de lignes
	if ($limit != '') {
		$limit = (preg_match('/^[0-9]+$/',$limit)) ? '0,'.$limit : $limit;
		$r .= ' LIMIT '.$limit.' ';
	}
	//echo $r;
	if (($rs = $con->select($r)) !== false) {
		$tmp = '';
		while (! $rs->EOF()) {
			$tmp .= $rs->f('word').' ';
			$rs->moveNext();
		}
		return $tmp;
	} else {
		$con->setError('MySQL: '.$con->error(), 500);
		return '';
	}

}



function showXMLPrimaryCategory ( $type='all', $limit='')
{
	$rep = '';
	//if ($this->category_id=='' || $this->category_id==null) $this->category_id=0;
	//$this->m->availableonline=true;
	$list = FrontEnd::getCategories('','ORDER BY category_position');
	while (!$list->EOF()) {

		$rep .= '<url>'."\n";
		$rep .= '<loc>'.$list->getPath().'</loc>';
		$rep .= "\n";
		$rep .= sprintf('<lastmod>%s</lastmod>',date(DATE_ATOM,date::unix($list->f('category_publicationdate'))));
		$rep .= "\n";
		$freqModify = 'weekly';
		$rep .= sprintf('<changefreq>%s</changefreq>',$freqModify);
		$rep .= "\n";
		$priority = '1.0'; // valeur pour les catégories mères
		$rep .= sprintf('<priority>%s</priority>',$priority);
		$rep .= "\n";
		$rep .= '</url>';
		$rep .= "\n";
		$prof = 1;
		if ($type == 'all') {
			$rep .= showXMLCatContent($list, $limit, $prof);
			$rep .= showXMLCategory($list->f('category_id'), $limit, $prof);
		}
		$list->moveNext();
	}
	return $rep;
}

function pxSitemapPrimaryCategory($type='all', $limit='')
{
	$category = array();
	//if ($cat_id=='' ||$cat_id==null) $cat_id=0;

	$list = FrontEnd::getCategories(1,'ORDER BY category_position');
	//exit;
	while (!$list->EOF()) {
		$freqModify = 'weekly';
		$priority = 1; // valeur pour les catégories mères

		$category[]= array(
				'id' => $list->f('category_id'),
				'name' => $list->f('category_name'),
				'loc' => $list->getPath(),
				'lastmod' => $list->f('category_publicationdate'),
				'changefreq' => $freqModify,
				'priority' => $priority
		);
		$prof=1;
		if ($type == 'all') {
			$rep .= pxSitemapCatContent($list, $limit, $prof);
			$rep .= pxSitemapCategory($list->f('category_id'), $limit, $prof);
		}
		$list->moveNext();
	}
	return $category;
}


function pxSitemapXMLCatContent($cat, $limit, $prof)
{
	$cont = '';
	$catId = $cat->f('category_id');
	//$this->m->availableonline=false;
	$res = FrontEnd::getgetOnlineResourcesInCat($catId,$limit); //$this->m->getResourcesFromCat($cat,$limit);

	while (!$res->EOF()) {
		$cont .= '<url>';
		$cont .= "\n";
		$cont .= '<loc>'.$res->getPath().'</loc>';
		$cont .= "\n";
		$cont .= sprintf('<lastmod>%s</lastmod>',date(DATE_ATOM,date::unix($res->f('modifdate'))));
		$cont .= "\n";
		$freqModify = 'daily';
		$cont .= sprintf('<changefreq>%s</changefreq>',$freqModify);
		$cont .= "\n";
		$priority = 1-($prof/10); // valeur pour les catégories mères
		$cont .= sprintf('<priority>%s</priority>',$priority);
		$cont .= "\n";
		$cont .= '</url>';
		$cont .= "\n";

		$res->moveNext();
	}
	return $cont;
}

function pxSitemapCatContent($catId, $limit='', $prof=1)
{
	$content = array();
	//$this->m->availableonline=false;
	$res = FrontEnd::getOnlineResourcesInCat($catId,$limit); // $this->m->getResourcesFromCat($catId,$limit);

	while (!$res->EOF()) {
		$freqModify = 'daily';
		$priority = 1-($prof/10);
		$content[]= array(
				'id' => $res->f('resource_id'),
				'name' => $res->f('title'),
				'loc' => $res->getPath(),
				'type' => $res->f('type_id'),
				'lastmod' => $res->f('publicationdate'),
				'changefreq' => $freqModify,
				'priority' => $priority
		);


		$res->moveNext();
	}
	return $content;
}

function pxSitemapXMLCategory ($catId, $limit, $prof)
{
	$cont = '';
	//$this->m->availableonline=true;
	$list = FrontEnd::getCategories($catId,'ORDER BY category_position');//$this->m->getChildCategoriesFrom($cat);
	while (!$list->EOF()) {
		$cont .= '<url>';
		$cont .= "\n";
		$cont .= '<loc>'.$list->getPath().'</loc>';
		$cont .= "\n";
		$cont .= sprintf('<lastmod>%s</lastmod>',date(DATE_ATOM,date::unix($list->f('category_publicationdate'))));
		$cont .= "\n";
		$freqModify = 'weekly';
		$cont .= sprintf('<changefreq>%s</changefreq>',$freqModify);
		$cont .= "\n";
		$priority = 1-($prof/10); // valeur pour les catégories mères
		$cont .= sprintf('<priority>%s</priority>',$priority);
		$cont .= "\n";
		$cont .= '</url>';
		$cont .= "\n";

		$cont .= pxSitemapXMLCatContent($list, $limit, ($prof+0.5));
		$cont .= pxSitemapXMLCategory($list->f('category_id'), $limit, ($prof+0.5));
		 
		$list->moveNext();
	}
	return $cont;
}


function pxSitemapCategory ($catId, $limit='', $prof = 1)
{
	$category = array();
	//$this->m->availableonline=true;
	$list = FrontEnd::getCategories($catId,'ORDER BY category_position'); //$this->m->getChildCategoriesFrom($cat);
	while (!$list->EOF()) {
		$freqModify = 'weekly';
		$priority = 1-($prof/10); // valeur pour les catégories mères

		$category[]= array(
				'id' => $list->f('category_id'),
				'name' => $list->f('category_name'),
				'loc' => $list->getPath(),
				'lastmod' => $list->f('category_publicationdate'),
				'changefreq' => $freqModify,
				'priority' => $priority
		);
		$list->moveNext();
	}
	return $category;
}
?>