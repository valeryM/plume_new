<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
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
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */
/*
$art, $res, $news etc...
-> points to $GLOBALS['_PX_render']['art']
                                   ['res']
                                   ['news']
                                   ['cat']
*/
/**
 @proto doc
 
 !!! Introduction
 
 The PLUME CMS template system is not the simplest, but if read the 
 documenation and study a little the current templates, you will quickly 
 figure out how to use it.
 
 The system simply use PHP functions to display the data. Sometimes the use of
 the [PHP|http://www.php.net] [sprintf|http://www.php.net/sprintf] function to
 format data.

*/ 

/**
 @proto doc
 
 !!! Informative functions
 
 These are functions to display information like the name of the website
 but not within a loop.
*/ 
  
 
/**
 Display some information about the website.
 The __name__ parameter can take the following values:
 
 * name : name of the website
 * url : relative url of the website
 * fullurl : full url of the website
 * filesurl : relative url to the files and images of the website  
 * lang : the lang of the website
 * namexml : name utf-8 encoded
 * theme : the name for selected theme

Example:

|<a href="<?php pxInfo('url'); ?>"><?php pxInfo('name'); ?></a>
 
 @proto function pxInfo 
 @param string name Property to display ('name')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxInfo($name='name', $return=false)
{
    switch ($name) {
    case 'fullurl':
        $result = 'http://'.config::f('domain').config::f('rel_url').'/'; 
        break;
    case 'url':
        $result = config::f('rel_url').'/'; 
        break;
    case 'filesurl':
        $result = config::f('rel_url_files').'/'; 
        break;
    case 'lang':
        $result = str_replace('_', '-', config::f('lang')); 
        break;
    case 'description':
        $result = $GLOBALS['_PX_render']['website']->f('website_description'); 
        break;
    case 'namexml':
        $result = text::toXML($GLOBALS['_PX_render']['website']->f('website_name'));
        break;
    case 'encoding':
        $result = strtolower(config::f('encoding')); 
        break;
    case 'email_site' :
    	$result = config::f('email_for_sending_notification');
    	break;
    case 'theme':
    	$result = config::f('theme_id');
    	break;
    case 'website_name':
    	$result = $GLOBALS['_PX_render']['website']->f('website_name');
    	break;
    default:
    	if (isset($GLOBALS['_PX_render']['website']))
        	$result = $GLOBALS['_PX_render']['website']->f('website_name');
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Generate the 'link' links just need to put it in the head of the
 template.

|<head>
|<?php pxHeadLinks(); ?>
|</head>

 @proto function pxHeadLinks
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxHeadLinks($return=false)
{
    $remove_numbers = (config::f('remove_numbers')) ? true : false;
    if ((int) config::f('res_per_page') > 0) {
        $res_per_page = (int) config::f('res_per_page');
    } else {
        $res_per_page = 0;
    }
    $result = '';
    if (config::f('action') == 'Category') {
        // Navigation links to the resources in the current page
        $GLOBALS['_PX_render']['res']->move((config::f('category_page') - 1) * $res_per_page);
        while (!$GLOBALS['_PX_render']['res']->EOF()) {
            $title = $GLOBALS['_PX_render']['res']->getTextContent('title');
            if ($remove_numbers) $title = px_removeNumbers($title);
            $result.= '<link rel="section" href="'
                .$GLOBALS['_PX_render']['res']->getPath()
                .'" title="'.$title.'" />'."\n";
            $GLOBALS['_PX_render']['res']->moveNext();
        }
        $GLOBALS['_PX_render']['res']->move((config::f('category_page') - 1) * $res_per_page);

        // Keywords
        $result.= '<meta name="keywords" content="'.htmlspecialchars($GLOBALS['_PX_render']['cat']->f('category_keywords')).'" />'."\n";

    } elseif (config::f('action') == 'Article') {
        $result = '<meta name="keywords" content="'
            .htmlspecialchars($GLOBALS['_PX_render']['art']->f('subject')
                              .', '.$GLOBALS['_PX_render']['art']->f('category_keywords')).'" />'."\n";
    } elseif (config::f('action') == 'News') {
        $result = '<meta name="keywords" content="'
            .htmlspecialchars($GLOBALS['_PX_render']['news']->f('subject')
                              .', '.$GLOBALS['_PX_render']['news']->f('category_keywords')).'" />'."\n";
    } elseif (config::f('action') == 'Events') {
        $result = '<meta name="keywords" content="'
            .htmlspecialchars($GLOBALS['_PX_render']['events']->f('subject')
                              .', '.$GLOBALS['_PX_render']['events']->f('category_keywords')).'" />'."\n";
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the list of primary categories

 @proto function pxPrimaryCategories
 @param string s Substitution string ('<ul>%s</ul>')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
 */
function pxMenuPrimaryCategories($s='<ul>%s</ul>',$sub = '<li>%s</li>', $return=false, $orderBy = 'category_name')
{
	$ordermanual = config::fbool('order_cat_manual');
	$remove_numbers = config::fbool('remove_numbers');
	$order = 'ORDER BY category_path';
	if ($ordermanual && $orderBy!='') {
		$order = 'ORDER BY '.$orderBy;
	}
	$rootcat = FrontEnd::getCategory('/');
	$prim    = FrontEnd::getCategories($rootcat->f('category_id'), $order);

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
 Display the list of primary categories

 @proto function pxPrimaryCategories
 @param string s Substitution string ('<ul>%s</ul>')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
 */
function pxMenuCategory($idCat,$s='<ul>%s</ul>',$sub = '<li>%s</li>', $return=false, $orderBy = 'category_name')
{
	$ordermanual = config::fbool('order_cat_manual');
	$remove_numbers = config::fbool('remove_numbers');
	$order = 'ORDER BY category_path';
	if ($ordermanual && $orderBy!='') {
		$order = 'ORDER BY '.$orderBy;
	}
	//$rootcat = FrontEnd::getCategory('/');
	$prim    = FrontEnd::getCategories($idCat, $order);

	$cats = '';
	$result = '';
	while (!$prim->EOF()) {
		if ($prim->f('category_path') != '/') {
			$path = $prim->getPath();
			$name = $prim->f('category_name');
			if ($remove_numbers) {
				$name = px_removeNumbers($name);
			}
			$link = '<a id="cat-'.$prim->f('category_id').'" href="'.$path.'">'.htmlspecialchars($name).'</a>'."\n";
			// get the child-categories
			$link .= pxMenuCategory($prim->f('category_id'),$s,$sub,$return);
			$cats .= sprintf($sub, $link);
			
		}
		$prim->moveNext();
	}
	if ($cats!='') $result = sprintf($s, $cats);

	if ($return) return $result;
	echo $result;
}


/**
 Return the array of categories

 @proto function pxArrayCategory
 @param integer idCat Category id
 @param string orderBy (ORDER BY category_name) 
 */
function pxArrayCategory($idCat,$orderBy = 'ORDER BY category_name')
{
	$ordermanual = config::fbool('order_cat_manual');
	$remove_numbers = config::fbool('remove_numbers');
	$order = 'ORDER BY category_path';
	if ($ordermanual && $orderBy!='') {
		$order = $orderBy;
	}
	$prim    = FrontEnd::getCategories($idCat, $order);
	
	$cats = array();
	// boucle sur les catégories
	while (!$prim->EOF()) {
		if ($prim->f('category_path') != '/') {
			$path = $prim->f('category_path');
			$name = $prim->f('category_name');
			if ($remove_numbers) {
				$name = px_removeNumbers($name);
			}
			$cats[]=array(
					'id' => $prim->f('category_id'),
					'path' => $path,
					'url' => $prim->getPath(),
					//'title' => Text::parseHtmlToText($prim->f('category_description')),
					'title' => trim(text::parseContent($prim->f('category_description'),'Text')),
					'name' => $name,
					'sublevel' => pxArrayCategory($prim->f('category_id'),$orderBy),
					);				
		}
		$prim->moveNext();
	}
	return $cats;
}


/**
 Generate the RSS sequence description

 @proto function pxRssSeq
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxRssSeq($return=false)
{
    $result = '';
    $index = $GLOBALS['_PX_render']['last']->getIndex();
    while (!$GLOBALS['_PX_render']['last']->EOF()) {
        $result.= '<rdf:li rdf:resource="'
            .$GLOBALS['_PX_render']['last']->getPath('fullurl').'" />'."\n";
        $GLOBALS['_PX_render']['last']->moveNext();
    }
    $GLOBALS['_PX_render']['last']->move($index);
    
    if ($return) return $result;
    echo $result;
}

/**
 Generate the RSS item sequence

 @proto function pxRssItems
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxRssItems($return=false)
{
    $result = '';    //global $_PX_website_config, $_PX_config;
    $index = $GLOBALS['_PX_render']['last']->getIndex();
    $remove_numbers = config::fbool('remove_numbers');
    while (!$GLOBALS['_PX_render']['last']->EOF()) {
        $content = htmlspecialchars(text::removeEntities(text::parseContent($GLOBALS['_PX_render']['last']->f('description'))));
        $title = htmlspecialchars(text::removeEntities($GLOBALS['_PX_render']['last']->f('title')));
        if ($remove_numbers) {
            $title = px_removeNumbers($title);
        }
        $result.= '<item rdf:about="'.$GLOBALS['_PX_render']['last']->getPath('fullurl')
            .'">'."\n".'  <title>'.$title."</title>\n".'  <link>'; 
        $result.= pxLastResPath('fullurl', true);  
        $result.= "</link>\n".'  <dc:date>'; 
        $result.= pxLastResDateModification('%Y-%m-%dT%H:%M:%S+00:00', 
                                            '%s', false, true); 
        $result.= "</dc:date>\n";
        $result.= '<description>'.$content.'</description>'."\n";
        $result.= '</item>'."\n";
        $GLOBALS['_PX_render']['last']->moveNext();
    }
    $GLOBALS['_PX_render']['last']->move($index);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the title of the current category.

 @proto function pxSingleCatTitle
 @param string s Substitution string ('%s - ')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxSingleCatTitle($s='%s - ', $return=false)
{
	$title='';
	if (isset($GLOBALS['_PX_render']['cat']))
    	$title = $GLOBALS['_PX_render']['cat']->f('category_name');
    if (config::fbool('remove_numbers')) 
        $title = px_removeNumbers($title);
    $result = sprintf($s, htmlspecialchars($title));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the description of the current category.

 @proto function pxSingleCatDescription
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxSingleCatDescription($return=false)
{
	if (! isset($GLOBALS['_PX_render']['cat']))
		return false;
	
    $result = text::parseContent($GLOBALS['_PX_render']['cat']->f('category_description'));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the category path

 @proto function pxSingleCatPath
 @param string s Substitution string ('%s')
 @param bool Get the feed path (false)
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxSingleCatPath($s='%s', $feed=false, $return=false)
{
    $path = $GLOBALS['_PX_render']['cat']->getPath('', $feed);
    $result = sprintf($s, $path);
    
    if ($return) return $result;
    echo $result;
}

/**
 Give the number of resources in the current category, 
 when displaying a category page
 
 @proto function pxSingleCatNbResources
 @param string no String for no resources ('no resources')
 @param string one String for 1 resource ('1 resource')
 @param string more Substitution string for 2 or more resources ('%s resources')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxSingleCatNbResources($no='no resources', $one='1 resource', 
                                $more='%s resources', $return=false)
{
    switch ($GLOBALS['_PX_render']['res']->NbRow()) {
    case 0: 
        $result = $no; 
        break;
    case 1: 
        $result = $one; 
        break;
    default: 
        $result = sprintf($more, $GLOBALS['_PX_render']['res']->nbRow()); 
        break;
    }
    if ($return) return $result;
    echo $result;
}

/**
 Get the number of resources in the current category, when displaying a 
 category page. If displaying a resource page, give the number of resources 
 in the current category of the resource. Usefull in __if__ statements.

 @proto function pxSingleCatGetNbResources
 @return integer Number of resources
*/
function pxSingleCatGetNbResources()
{
    return $GLOBALS['_PX_render']['res']->NbRowTotal();
}

/**
 Give a link to the previous/next page if available
  
 @proto function pxSingleCatNextPage
 @param int dir Direction -1 previous, (1) next
 @param string s substitution string ('%s')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxSingleCatNextPage($dir=1, $s='%s', $return=false)
{
    $total = $GLOBALS['_PX_render']['res']->NbRowTotal();
    $path = $GLOBALS['_PX_render']['cat']->getPath();
    $page = config::f('category_page');
    $nbres = config::f('res_per_page');
    $result = '';
    if ($dir == -1 && $page > 1) {
        $page--;
        if (1 == $page) {
            $result = sprintf($s, $path);
        } else {
            $result = sprintf($s, $path.'index'.$page);
        }
    } elseif ($dir == 1 && ($total > ($page * $nbres))) {
        $page++;
        $result = sprintf($s, $path.'index'.$page);
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Give the list of pages of a category with links like
 search engines. Looks like ''Pages: Prev 1, 2, 3, 4, 5 Next''.

 @proto function pxSingleCatListPages
 @param string s Substitution string ('<p>Pages: %s</p>')
 @param string prev Previous page string ('Prev')
 @param string next Next page string ('Next')
 @param string sep Separator between page numbers (', ')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxSingleCatListPages($s='<p>Pages: %s</p>', $prev='Prev', 
                              $next='Next', $sep=', ', $return=false)
{
    $result = '';
    $total = $GLOBALS['_PX_render']['res']->NbRowTotal();
    $activepage  = config::f('category_page');
    $nbres = config::fint('res_per_page');
    $path = $GLOBALS['_PX_render']['cat']->getPath();
    $pages = array();
    $list = '';
    $previouspage = '';
    $nextpage = '';
    $nbpages = (int) ceil($total/$nbres);
    if ($nbpages > 1) {
        for ($i=1;$i<=$nbpages;$i++) {
            $class = '';
            if ($i == $activepage) $class = 'class="current" ';
            $url = $path;
            if ($i > 1) $url .= 'index'.$i;
            $pages[]='<a '.$class.'href="'.$url.'">'.$i.'</a>';
        }
        $list = join($sep, $pages);
        //Previous page
        if ($activepage > 1) {
            if (2 == $activepage) {
                $previouspage = '<a href="'.$path.'">'.$prev.'</a>';
            } else {
                $temp = $activepage - 1;
                $previouspage = '<a href="'.$path.'index'.$temp.'">'
                    .$prev.'</a>';
            }
        }
        //Next page
        if ($total > ($activepage * $nbres)) {
            $temp = $activepage + 1;
            $nextpage = '<a href="'.$path.'index'.$temp.'">'.$next.'</a>';
        }
        $result = sprintf($s, $previouspage.' '.$list.' '.$nextpage);
    }
    
    if ($return) return $result;
    echo $result;
}


/**
 Display the list of categories for a breadcrumb. Like
 Home >> Subcategory >> Subsubcategory

 @proto function pxSingleCatTree
 @param string s substitution string ('<ol>%s</ol>')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
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
    foreach ($categories as $cat) {
        $title = $cat->f('category_name');
        if ($remove_numbers) $title = px_removeNumbers($title);
        $res = '<li><a href="'.$cat->getPath().'">'.htmlspecialchars($title)
            .'</a></li>'."\n".$res;
    }
    $result = sprintf($s, $res);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the parent name cat, only if not in homepage

 @proto function pxParentCatTitle
 @param string s Substitution string ('%s - ')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxParentCatTitle($s='%s - ', $return=false)
{
    $title = $GLOBALS['_PX_render']['pcat']->f('category_name');
    if (config::fbool('remove_numbers')) {
        $title = px_removeNumbers($title);
    }
    $result = sprintf($s, htmlspecialchars($title));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the parent description cat, only if not in homepage

 @proto function pxParentCatDescription
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxParentCatDescription($return=false)
{
    $result = text::parseContent($GLOBALS['_PX_render']['pcat']->f('category_description'));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the parent category path, only if not in homepage

 @proto function pxParentCatPath
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxParentCatPath($s='%s', $return=false)
{
    $result = sprintf($s, $GLOBALS['_PX_render']['pcat']->getPath());
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the list of primary categories

 @proto function pxPrimaryCategories
 @param string s Substitution string ('<ul>%s</ul>')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxPrimaryCategories($s='<ul>%s</ul>', $return=false)
{
    $ordermanual = config::fbool('order_cat_manual');
    $remove_numbers = config::fbool('remove_numbers');
    $order = 'ORDER BY category_path';
    if ($ordermanual) {
        $order = 'ORDER BY category_name';
    }
    $rootcat = FrontEnd::getCategory('/');
    $prim    = FrontEnd::getCategories($rootcat->f('category_id'), $order);

    $cats = '';
    while (!$prim->EOF()) {
        if ($prim->f('category_path') != '/') {
            $path = $prim->getPath();
            $name = $prim->f('category_name');
            if ($remove_numbers) {
                $name = px_removeNumbers($name);
            }
            $cats .= '<li><a href="'.$path.'">'.htmlspecialchars($name)
                .'</a></li>'."\n";
        }
        $prim->moveNext();
    }
    $result = sprintf($s, $cats);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the list of subcategories in the form of an unordered list.
 
 @proto function pxSubCategories
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/ 
function pxSubCategories($s='%s', $return=false)
{
    $remove_numbers = config::fbool('remove_numbers');
    $order = 'ORDER BY category_path';
    if (config::fbool('order_cat_manual')) {
        $order = 'ORDER BY category_name';
    }
    $subcat = FrontEnd::getCategories($GLOBALS['_PX_render']['cat']->f('category_id'), $order);
    $result = '';
    if ($subcat->NbRow() > 0) {
        $ls = "<ul>\n";
        while (!$subcat->EOF()) {
            $name = $subcat->f('category_name');
            if ($remove_numbers) {
                $name = px_removeNumbers($name);
            }
            $ls .= '<li><a href="'.$subcat->getPath().'">'
                .htmlspecialchars($name).'</a></li>'."\n";
            $subcat->moveNext();
        }
        $ls .= "</ul>\n";
        $result = sprintf($s, $ls);
    }
    if ($return) return $result;
    echo $result;
}

/**
 Display the query string.
 
 To be used only in the search template.

 @proto function pxSearchQuery
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxSearchQuery($s= '%s', $return=false)
{
	$query = trim(config::f('query_string')); //query_string_origin
    $result = sprintf($s, htmlspecialchars($query));
    
    if ($return) return $result;
    echo $result;
}


/**
 @proto doc
 
 !!! Functions in loops
 
 It is possible to run functions within loops to get access to specific data from
 the elements in the loops. 
 
 !! The $res loop

 This loop is available in the categories. It is used to display the last 
 resources of a category. For example:

|<?php while (!$res->EOF()): ?>
|   <div class="resource">
|   <h2><a href="<?php pxResPath(); ?>"><?php pxResTitle(); ?></a></h2>
|   <?php pxResDescription(); ?>
|   </div>
|<?php 
|$res->moveNext(); 
|endwhile; ?>
 
*/ 


/**
 Display the title of the resource. 

 @proto function pxResTitle
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxResTitle($s='%s', $return=false)
{
    $title = $GLOBALS['_PX_render']['res']->getTextContent('title');
    if (config::fbool('remove_numbers')) {
        $title = px_removeNumbers($title);
    }
    $result = sprintf($s, $title);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the path to the resource.

 @proto function pxResPath
 @param string type 'fullurl' give path with http:// ('relative')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxResPath($type='relative', $return=false)
{
    $result = $GLOBALS['_PX_render']['res']->getPath($type);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the description of the resource.

 If a limit is given, the description is converted into raw text and then the limit is applied.

 @proto function pxResDescription
 @param string s Substitution string ('%s')
 @param int limit Number of words (characters for the moment) to limit the description
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxResDescription($s='%s', $limit=0, $return=false)
{
    $result = '';
    //echo $GLOBALS['_PX_render']['res']->f('resource_id');
    //return;
    if ($limit) {
        $text = text::truncate(text::parseContent($GLOBALS['_PX_render']['res']->f('description'), 'text'), $limit);
        $result = sprintf($s, $text);
    } else {
        $result = text::parseContent($GLOBALS['_PX_render']['res']->f('description'));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the list of categories in which the ressource is.

 The list is not an HTML list, it is to be used as sentence like
 "Category one, category two and category tree" The category names
 are linked to the category pages.

 @proto function pxResCategories
 @param string s Substitution string ('%s')
 @param string p1 First delimiters (', ')
 @param string p2 Last delimiter (' and ')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxResCategories($s='%s', $p1=', ', $p2=' and ', $return=false)
{
    $remove_numbers = config::fbool('remove_numbers');
    $cat = $GLOBALS['_PX_render']['res']->cur->cats;
    $nr = $cat->nbRow();
    $i = 1;
    $link = '<a href="%s">%s</a>';
    $res = '';
    while (!$cat->EOF()) {
        $title = $cat->f('category_name');
        if ($remove_numbers) $title = px_removeNumbers($title);
        $res .= sprintf($link, $cat->getPath(), htmlspecialchars($title));
        if ($nr >= 2 && ($i < ($nr - 1))) {
            $res .= trim($p1).'&nbsp;';
        }
        if ($nr >= 2 && ($i == ($nr - 1))) {
            $res .= '&nbsp;'.trim($p2).'&nbsp;';
        }
        $i++;
        $cat->moveNext();
    }
    $result = sprintf($s, $res);
    
    if ($return) return $result;
    echo $result;
}


/**
 Display the name of the author

 @proto function pxResAuthor
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxResAuthor($return=false)
{
	if (isset($GLOBALS['_PX_render']['res']))
    	$result = $GLOBALS['_PX_render']['res']->extf('authors', 'user_realname');
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the author public email if available.

 @proto function pxResAuthorEmail
 @param string s Substitution string ('%s')
 @param string encoding Encoding for a mailto ('link') or for display 'text'
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxResAuthorEmail($s = '%s', $encoding = 'link', $return=false)
{
    $result = '';
    $text = ($encoding == 'link') ? false : true;
    if (strlen($GLOBALS['_PX_render']['res']->extf('authors', 'user_pubemail')) > 0) {
        $result = sprintf($s, text::hexEncode($GLOBALS['_PX_render']['res']->extf('authors', 'user_pubemail'), $text));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the modification date of the resource. Only if newer than the publication date.

 @proto function pxResDateModification
 @param string dateformat Format of the date ('%Y-%d-%mT%T+00:00')
 @param string s Substitution string ('%s')
 @param mixed ifmodified Time in minutes between publication date and modification to display, false to always display it (false)
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxResDateModification($dateformat='%Y-%m-%dT%H:%M:%S+00:00', $s='%s', 
                               $ifmodified=false, $return=false)
{
    $result = '';
    if (false !== $ifmodified) {
        $ifmodified = $ifmodified * 60;
        $md = date::unix($GLOBALS['_PX_render']['res']->f('modifdate'));
        $pd = date::unix($GLOBALS['_PX_render']['res']->f('publicationdate'));
        if ($md >  ($pd + $ifmodified))
            $result = sprintf($s, strftime($dateformat, $md));
    } else {
        $result = sprintf($s, strftime($dateformat , 
                                  date::unix($GLOBALS['_PX_render']['res']->f('modifdate'))));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the publication date of the resource.

 @proto function pxResDatePublication
 @param string dateformat Format of the date ('%Y-%d-%mT%T+00:00')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxResDatePublication($dateformat = '%Y-%m-%dT%H:%M:%S+00:00', $return=false)
{
    $result = strftime($dateformat , date::unix($GLOBALS['_PX_render']['res']->f('publicationdate')));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the score of the resource for this research.

 To be used only in the search template.

 @proto function pxResSearchScore
 @param string s Substitution string ('%01.2f%%')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxResSearchScore($s= '%01.2f%%', $return=false)
{
    if (!isset($GLOBALS['_PX_render']['search_max_occ'])) {
        $GLOBALS['_PX_render']['search_max_occ'] = (float) $GLOBALS['_PX_render']['res']->f('score');
    }
    $score = (float) $GLOBALS['_PX_render']['res']->f('score') / $GLOBALS['_PX_render']['search_max_occ'] * 100.0;
    $result = sprintf($s, $score);
    
    if ($return) return $result;
    echo $result;
}


/**
 Display the associated link and title if available. When getting the 
 resources, if the 'news' type is chose as link can be associated to
 news, this is the way to get it back if available.

 @proto function pxResAssociatedLink
 @param string s Substitution string ('<a href="%1$s">%2$s</a>')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxResAssociatedLink($s='<a href="%1$s">%2$s</a>', $return=false)
{
    $result = '';
    if ($GLOBALS['_PX_render']['res']->f('type_id') == 'news') {
        $GLOBALS['_PX_render']['res']->cur->loadDetails();
        if (strlen($GLOBALS['_PX_render']['res']->cur->details->f('news_titlewebsite')) > 0) {
            $result = sprintf($s, $GLOBALS['_PX_render']['res']->cur->details->f('news_linkwebsite'), $GLOBALS['_PX_render']['res']->cur->details->f('news_titlewebsite'));
        }
    }
    if ($return) return $result;
    echo $result;
}

/**
 Display the number of comments of the resources.

 @proto function pxResCountComments
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxResCountComments($return=false)
{
    $result = $GLOBALS['_PX_render']['res']->cur->countComments();
    if ($return) return $result;
    echo $result;
}

function pxResCommentAvailable() {
	if ($GLOBALS['_PX_render']['res']->f('comment_support') != 1) 
		return false;
	else
		return true;
}


/**
 @proto doc

 !! The $last loop

 This loop is available in all the templates, you just have to initialize it
 before use:

|<?php pxGetLastResources(); ?>
|<?php while (!$last->EOF()): ?>
|    <p><a href="<?php pxLastResPath(); ?>"><?php pxLastResTitle(); ?></a></p>
|<?php
|$last->moveNext();
|endwhile; ?>

*/

/**
 * Get the list of last resources and put them in $last
 * Must be run before using the $last loop
 *
 * @proto function pxGetLastResources
 * @param int limit Number of last resources (5)
 * @param string type Type of resources ('') for all, 'news', 'events','rsslinks' or 'articles'
 * @param mixed category Category path or id ('')
 * @param boolean Set true if to get the online resources
 * @param boolean return Type of return : true, return result as a recordset, false (default) save in globals var
 */
function pxGetLastResources($limit=5, $type='', $category='',  $online = false, $return=false, $order='ORDER BY %sresources.publicationdate DESC') 
{
	if (!$online) 
		$result = FrontEnd::getResources($category,$limit, $type, 1);
	else 
		$result = FrontEnd::getOnlineResourcesInCat($category, '', $limit, $type,1, $order);
				//'ORDER BY %sresources.path');
   $GLOBALS['_PX_render']['last'] = $result;
        
    if ($return) return $result;
}

/**
 * Get the list of last resources by subtype and put them in $last
 * Must be run before using the $last loop
 *
 * @proto function pxGetLastResourcesBySubType
 * @param int limit Number of last resources (5)
 * @param int subtype Subtype id ('')
 * @param int category Category id ('')
 * @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxGetLastResourcesBySubType($limit=5, $subtype='', $category='', $return=false) 
{
    $result = $GLOBALS['_PX_render']['last'] = FrontEnd::getResources($category, 
                        $limit, '', $subtype, 1);
    if ($return) return $result;
}


function pxGetLastModification($dateformat='%Y-%m-%dT%H:%M:%S+00:00', $s='%s', $return = false)  {
	$GLOBALS['_PX_render']['lastModif']= FrontEnd::getLastModifOfResources();
    $result = sprintf($s, strftime($dateformat, date::unix($GLOBALS['_PX_render']['lastModif']->f('datemodif'))));
    if ($return) return $result;
    echo $result;	
}

/**
 Display the title of the resource.

 @proto function pxLastResTitle
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxLastResTitle($s='%s', $return=false)
{
    $remove_numbers = (config::f('remove_numbers')) ? true : false;
    $title = $GLOBALS['_PX_render']['last']->getTextContent('title');
    if ($remove_numbers) 
        $title = px_removeNumbers($title);
    $result = sprintf($s, $title);
    if ($return) return $result;
    echo $result;
}

/**
 Display the path to the resource.

 @proto function pxLastResPath
 @param string type If 'fullurl' give path with http:// ('relative')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxLastResPath($type='relative', $return=false)
{
    $result = $GLOBALS['_PX_render']['last']->getPath($type);
    
    if ($return) return $result;
    echo $result;
}

/**
 * 
 * get the type of resource (event, article, news).
 * @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 * @return String : the type of the resource
 */
function pxLastResType($return=false)  {
	$result = $GLOBALS['_PX_render']['last']->f('type_id');
	if ($return) return $result;
	echo $result;
}

/**
 * 
 * get the name of type of resource (event, article, news).
 * @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 * @return String : the name of type of the resource
 */
function pxLastResTypeName($return=false)  {
	$result = $GLOBALS['_PX_render']['last']->f('type_id');
	if ($result=='events') {
		$result='Evènements';
	} else if ($result=='articles') {
		$result ='Articles';
	} else if ($result=='news') {
		$result = 'Brèves';
	} else $result ='';
	if ($return) return $result;
	echo $result;
}

/**
 Display the description of the resource.

 @proto function pxLastResDescription
 @param string s Substitution string ('%s')
 @param int limit Number of words (characters for the moment) to limit the description
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxLastResDescription($s='%s', $limit=0, $return=false)
{
    $result = '';
    if ($limit) {
        $text = text::truncate(text::parseContent($GLOBALS['_PX_render']['last']->f('description'), 'text'), $limit);
        $result = sprintf($s, $text);
    } else {
        $result = text::parseContent($GLOBALS['_PX_render']['last']->f('description'),'Text');
    }
    $result = html_entity_decode($result,ENT_QUOTES,'UTF-8');
    if ($return) return $result;
    echo $result;
}

function pxLastResDescriptionHtml($s='%s', $limit=false, $return=false)
{
    $result = '';
    $content ='';
    
    if ($GLOBALS['_PX_render']['last']->f('page_title') )  { //($this->pxLastResType(true) == 'articles') {
    	//$content = '<h3>' .$GLOBALS['_PX_render']['last']->f('page_title');
    	//$content = $GLOBALS['_PX_render']['last']->f('page_content');
    } else {
    	$content= $GLOBALS['_PX_render']['last']->f('description');
    }
    
    if ($limit) {
        $text = text::truncate(text::parseContent($content, 'html'), $limit);
        $result = sprintf($s, $text);
    } else {
        $result = text::parseContent($content,'html');
    }
    $result = htmlspecialchars($result,ENT_COMPAT,'UTF-8');
    if ($return) return $result;
    echo $result;
}

/**
 Display the name of the author

 @proto function pxLastResAuthor
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxLastResAuthor($return=false)
{
    $result = $GLOBALS['_PX_render']['last']->extf('authors', 'user_realname');
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the author public email if available.

 @proto function pxLastResAuthorEmail
 @param string s Substitution string ('%s')
 @param string encoding Encoding for a mailto ('link') or for display 'text'
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxLastResAuthorEmail($s='%s', $encoding='link', $return=false)
{
    $result = '';
    $text = ($encoding == 'link') ? false : true;
    if (strlen($GLOBALS['_PX_render']['last']->extf('authors', 'user_pubemail')) > 0) {
        $result = sprintf($s, text::hexEncode($GLOBALS['_PX_render']['last']->extf('authors', 'user_pubemail'), $text));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the modification date of the resource.

 @proto function pxLastResDateModification
 @param string dateformat Format of the date ('%Y-%d-%mT%T+00:00')
 @param string s Substitution string ('%s')
 @param mixed ifmodified Time in minutes between publication date and modification to display, false to always display it (false)
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxLastResDateModification($dateformat='%Y-%m-%dT%H:%M:%S+00:00', 
                                   $s='%s', $ifmodified=false, $return=false)
{
    $result = '';
    if ($ifmodified !== false) {
        $pd = date::unix($GLOBALS['_PX_render']['last']->f('publicationdate'));
        $md = date::unix($GLOBALS['_PX_render']['last']->f('modifdate'));
        $ifmodified = 60 * (int) $ifmodified;
        if ($md > ($pd + $ifmodified)) {
            $result = sprintf($s, strftime($dateformat, $md));
        }
    } else {
        $result = sprintf($s, strftime($dateformat, date::unix($GLOBALS['_PX_render']['last']->f('modifdate'))));
    }
    if ($return) return $result;
    echo $result;
}

/**
 Display the publication date of the last resource.

 @proto function pxLastResDatePublication
 @param string dateformat Format of the date ('%Y-%d-%mT%T+00:00')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxLastResDatePublication($dateformat='%Y-%m-%dT%H:%M:%S+00:00', $return=false)
{
    $result = strftime($dateformat , date::unix($GLOBALS['_PX_render']['last']->f('publicationdate')));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the associated link and title if available. When getting the last
 resources, if the 'news' type is chose as link can be associated to
 news, this is the way to get it back if available.

 @proto function pxLastResAssociatedLink
 @param string s Substitution string ('<a href="%1$s">%2$s</a>')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxLastResAssociatedLink($s='<a href="%1$s">%2$s</a>', $return=false)
{
    $result = '';
    if ($GLOBALS['_PX_render']['last']->f('type_id') == 'news') {
        $GLOBALS['_PX_render']['last']->cur->loadDetails();
        if (strlen($GLOBALS['_PX_render']['last']->cur->details->f('news_titlewebsite')) > 0) {
            $result = sprintf($s, $GLOBALS['_PX_render']['last']->cur->details->f('news_linkwebsite'), $GLOBALS['_PX_render']['last']->cur->details->f('news_titlewebsite'));
        }
    }
    if ($return) return $result;
    echo $result;
}

/**
 Display the category in which the last ressource is.

 @proto function pxLastResCategory
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxLastResCategory($s='%s', $return=false)
{
	$remove_numbers = config::fbool('remove_numbers');
	$cat = $GLOBALS['_PX_render']['last']->cur;
	$i = 1;
	$res = '';
	$title = $cat->f('category_name');
	if ($remove_numbers) $title = px_removeNumbers($title);
	$result = sprintf($s, $title);

	if ($return) return $result;
	echo $result;
}


/**
 Display the list of categories in which the last ressource is.

 The list is not an HTML list, it is to be used as sentence like
 "Category one, category two and category tree" The category names
 are linked to the category pages.

 @proto function pxLastResCategories
 @param string s Substitution string ('%s')
 @param string p1 First delimiters (', ')
 @param string p2 Last delimiter (' and ')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxLastResCategories($s='%s', $p1=', ', $p2=' and ', $return=false)
{
    $remove_numbers = config::fbool('remove_numbers');
    $cat = $GLOBALS['_PX_render']['last']->cur->cats;
    $nr = $cat->nbRow();
    $i = 1;
    $link = '<a href="%s">%s</a>';
    $res = '';
    while (!$cat->EOF()) {
        $title = $cat->f('category_name');
        if ($remove_numbers) $title = px_removeNumbers($title);
        $res .= sprintf($link, $cat->getPath(), htmlspecialchars($title));
        if ($nr >= 2 && ($i < ($nr - 1))) {
            $res .= $p1;
        }
        if ($nr >= 2 && ($i == ($nr - 1))) {
            $res .= $p2;
        }
        $i++;
        $cat->moveNext();
    }
    $result = sprintf($s, $res);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the number of comments of the resources.

 @proto function pxLastResCountComments
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxLastResCountComments($return=false)
{
    $result = $GLOBALS['_PX_render']['last']->cur->countComments();
    if ($return) return $result;
    echo $result;
}



/**
 @proto doc

 !!! The resource functions

 These are functions to display information about the current resource in the
 page. For the moment the resource can be either an __article__ or a __news__.

 !! The article functions

 These functions are to be used in the ''article'' templates.

*/


/**
 Display the title of the article.

 @proto function pxArtTitle
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxArtTitle($s='%s', $return=false)
{
    $title = $GLOBALS['_PX_render']['art']->getTextContent('title');
    if (config::fbool('remove_numbers')) 
        $title = px_removeNumbers($title);
    $result = sprintf($s, $title);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the description of the article.

 @proto function pxArtDescription
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxArtDescription($return=false)
{
    $result = text::parseContent($GLOBALS['_PX_render']['art']->f('description'));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the name of the author

 @proto function pxArtAuthor
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxArtAuthor($return=false)
{
    $result = $GLOBALS['_PX_render']['art']->authors->f('user_realname');
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the path to the article.

 @proto function pxArtPath
 @param string type 'fullurl' give path with http:// ('relative')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxArtPath($type='relative', $return=false)
{
    $result = $GLOBALS['_PX_render']['art']->getPath($type);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the author public email if available.

@proto function pxArtAuthorEmail
@param string s Substitution string ('%s')
@param string encoding Encoding for a mailto ('link') or for display 'text'
@param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxArtAuthorEmail($s='%s', $encoding='link', $return=false)
{
    $result = '';
    $text = ($encoding == 'link') ? false : true;
    if (strlen($GLOBALS['_PX_render']['art']->authors->f('user_pubemail')) > 0) {
        $result = sprintf($s, text::hexEncode($GLOBALS['_PX_render']['art']->authors->f('user_pubemail'), $text));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the creation date of the article.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxArtDateCreation
 @param string dateformat Format of the date ('%A %e %B %Y')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxArtDateCreation($dateformat='%A %e %B %Y', $return=false)
{
    $result = strftime($dateformat , date::unix($GLOBALS['_PX_render']['art']->f('creationdate')));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the publication date of the article.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxArtDatePublication
 @param string dateformat Format of the date ('%A %e %B %Y')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxArtDatePublication($dateformat='%A %e %B %Y', $return=false)
{
    $result = strftime($dateformat , date::unix($GLOBALS['_PX_render']['art']->f('publicationdate')));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the modification date of the article. Only if newer than the
 publication date.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxArtDateModification
 @param string dateformat Format of the date ('%A %e %B %Y - %T ')
 @param string s Substitution ('Modified the %s.')
 @param mixed ifmodified Time in minutes between publication date and modification to display, false to always display it (false)
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxArtDateModification($dateformat='%A %e %B %Y', 
                               $s='Modified the %s.', $ifmodified=false, $return=false)
{
    $result = '';
    if (false !== $ifmodified) {
        $ifmodified = 60 * $ifmodified;
        $md = date::unix($GLOBALS['_PX_render']['art']->f('modifdate'));
        $pb = date::unix($GLOBALS['_PX_render']['art']->f('publicationdate'));
        if ($md > ($pb + $ifmodified)) {
            $result = sprintf($s, strftime($dateformat, $md));
        }
    } else {
        $result = sprintf($s, strftime($dateformat, date::unix($GLOBALS['_PX_render']['art']->f('modifdate'))));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the date of end of availaibility of the article. Only if end date.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxArtDateEnd
 @param string dateformat Format of the date ('%A %e %B %Y - %T ')
 @param string s Substitution ('End the %s.')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxArtDateEnd($dateformat='%A %e %B %Y', $s='End the %s.', $return=false)
{
    $result = '';
    $y = substr($GLOBALS['_PX_render']['art']->f('enddate'), 0, 4);
    if ((int)$y < 9999) {
        $result = sprintf($s, strftime($dateformat , date::unix($GLOBALS['_PX_render']['art']->f('enddate'))));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the keywords or subject of the article

 @proto function pxArtKeywords
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxArtKeywords($s='%s', $return=false)
{
    $result = '';
    $keywords = trim($GLOBALS['_PX_render']['art']->f('subject'));
    if (strlen($keywords) > 0) {
        $result = sprintf($s, $keywords);
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the list of categories in which the article is.

 The list is not an HTML list, it is to be used as sentence like
 "Category one, category two and category tree" The category names
 are linked to the category pages.

 @proto function pxArtCategories
 @param string s Substitution string ('%s')
 @param string p1 First delimiters (', ')
 @param string p2 Last delimiter (' and ')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxArtCategories($s='%s', $p1=', ', $p2=' and ', $return=false)
{
    $remove_numbers = config::fbool('remove_numbers');
    $cat = $GLOBALS['_PX_render']['art']->cats;
    $nr = $cat->nbRow();
    $i = 1;
    $link = '<a href="%s">%s</a>';
    $res = '';
    while (!$cat->EOF()) {
        $title = $cat->f('category_name');
        if ($remove_numbers) $title = px_removeNumbers($title);
        $res .= sprintf($link, $cat->getPath(), htmlspecialchars($title));
        if ($nr >= 2 && ($i < ($nr - 1))) {
            $res .= $p1;
        }
        if ($nr >= 2 && ($i == ($nr - 1))) {
            $res .= $p2;
        }
        $i++;
        $cat->moveNext();
    }
    $result = sprintf($s, $res);
    
    if ($return) return $result;
    echo $result;
}

/**
 Return true if the current page is the first page of the article

 @proto function pxArtPageIsFirst
 @return bool __true__ if the current page is the first, else __false__
*/
function pxArtPageIsFirst()
{
    return ((int)$GLOBALS['_PX_render']['art']->pages->f('page_number') == 1);
}

/**
 Display the title of the current article page.

 @proto function pxArtPageTitle
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxArtPageTitle($s='%s', $return=false)
{
    $result = $GLOBALS['_PX_render']['art']->pages->f('page_title');
    $result = str_replace('&', '&amp;', $result);    
    $result = sprintf($s, $result);
    if ($return) return $result;
    echo $result;
}

/**
 Display the number of comments of the article.

 @proto function pxArtCountComments
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxArtCountComments($return=false)
{
    $result = $GLOBALS['_PX_render']['art']->countComments();
    if ($return) return $result;
    echo $result;
}

/**
 Display an address link to edit the current resource.

@proto function pxResEditLink
@param string s Substitution string ('%s')
@param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/

function pxGetResType()
{
    return $GLOBALS['_PX_render']['res']->f('type_id');
}

function pxResEditLink($s='%s', $return=false)
{
    require_once dirname(__FILE__).'/lib.auth.php';
    require_once dirname(__FILE__).'/class.user.php';
    if (auth::getFromCookie())
    {
        $result = "";

        $type = $GLOBALS['_PX_render']['res']->f('type_id');
        if ($type == "articles")
	{$result = "articles";
	$res = 'art';}
        else if ($type == "news")
	{$res = 'news';
	$result = "news";}

        $id = $GLOBALS['_PX_render'][$res]->f('resource_id');
        $url = pxInfo('fullurl', true)."manager";
        $result = "$url/$result.php?resource_id=$id";
        $result = sprintf($s, $result);

        if ($return) return $result;
        echo $result;
    }
}

/**
 Display the content of the current article page.

 @proto function pxArtPageContent
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxArtPageContent($return=false)
{
    $result = text::parseContent($GLOBALS['_PX_render']['art']->pages->f('page_content'));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display an ordered list of pages in the article with link to the pages.
 The current page is set as ''active'' with the corresponding <li> element
 being from the __current__ class.

 An output example is:

|<ol>
|<li><a href="/cat/my-article">Page 1</a></li>
|<li class="current"><a href="/cat/my-article2">Page 2</a></li>
|<li><a href="/cat/my-article3">Page 3</a></li>
|</ol>

 @proto function pxArtListPages
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxArtListPages($s = '%s', $return=false)
{
    $result = '';
    if ($GLOBALS['_PX_render']['art']->pages->nbRow() > 1) {
        $index = $GLOBALS['_PX_render']['art']->pages->getIndex();
        $GLOBALS['_PX_render']['art']->pages->moveStart();
        $lp = '<ol>'."\n";
        while (!$GLOBALS['_PX_render']['art']->pages->EOF()) {
            $active = ($index == $GLOBALS['_PX_render']['art']->pages->getIndex()) ? ' class="current"' : '';
            $page = ($GLOBALS['_PX_render']['art']->pages->f('page_number') == 1) ? '' : $GLOBALS['_PX_render']['art']->pages->f('page_number');
            $lp .= '<li'.$active.'><a href="'.$GLOBALS['_PX_render']['art']->getPath().$page.'">'.$GLOBALS['_PX_render']['art']->pages->getTextContent('page_title').'</a></li>'."\n";
            $GLOBALS['_PX_render']['art']->pages->moveNext();
        }
        $lp .= '</ol>'."\n";
        $GLOBALS['_PX_render']['art']->pages->move($index);
        $result = sprintf($s, $lp);
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Give a link to the previous/next page of an article if available
  
 @proto function pxArtNextPage
 @param int dir Direction -1 previous, (1) next
 @param string s substitution string ('%s')
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxArtNextPage($dir=1, $s='%s', $return=false)
{
    $total = $GLOBALS['_PX_render']['art']->pages->nbRow();
    $path = $GLOBALS['_PX_render']['art']->getPath();
    $page = $GLOBALS['_PX_render']['art']->pages->f('page_number');
    $result = '';
    if ($dir == -1 && $page > 1) {
        $page--;
        if (1 == $page) {
            $result = sprintf($s, $path);
        } else {
            $result = sprintf($s, $path.$page);
        }
    } elseif ($dir == 1 && ($total > $page)) {
        $page++;
        $result = sprintf($s, $path.$page);
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 @proto doc

 !! The comments functions

 These functions are to be used in the ''news'' and ''article'' templates.

*/

/**
 Return true if one can post a comment to the current resource.

 @proto function pxCtEnabled
 @return bool True if possible to post a comment

*/
function pxCtEnabled()
{
    return $GLOBALS['_PX_render']['ct_enabled'];
}


/**
 Display the author of the comment.

 @proto function pxCtAuthor
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxCtAuthor($s='%s', $return=false)
{
    $result = sprintf($s, htmlspecialchars($GLOBALS['_PX_render']['ct']->f('comment_author')));
    if ($return) return $result;
    echo $result;
}

/**
 Display the email of the author of the comment.

 @proto function pxCtEmail
 @param string s Substitution string ('%s')
 @param string encoding Encoding for a mailto ('link') or for display 'text'
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxCtEmail($s='%s', $encoding='link', $return=false)
{
    $text = ($encoding == 'link') ? false : true;
    $result = '';
    if (strlen($GLOBALS['_PX_render']['ct']->f('comment_email'))) {
        $result = sprintf($s, text::hexEncode($GLOBALS['_PX_render']['ct']->f('comment_email'), $text));
    }
    if ($return) return $result;
    echo $result;
}

/**
 Display the web of the author of the comment.

 @proto function pxCtWeb
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxCtWeb($s='%s', $return=false)
{
    $result = '';
    if (strlen($GLOBALS['_PX_render']['ct']->f('comment_website'))) {
        $result = sprintf($s, htmlspecialchars($GLOBALS['_PX_render']['ct']->f('comment_website')));
    }
    if ($return) return $result;
    echo $result;
}

/**
 Display the content of the comment.

 @proto function pxCtContent
 @param string Substitution string ('<span class="px-comment">%s</span>')
 @param string Format of the content ('safe') or 'textarea'
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxCtContent($s='<span class="px-comment">%s</span>', $format='safe',
                     $return=false)
{
    $result = '';
    $content = $GLOBALS['_PX_render']['ct']->getContent($format);
    if (strlen($content) > 0) {
        $result = sprintf($s, $content);
    }
    if ($return) return $result;
    echo $result;
}

/**
 Display the comment errors if any.

 @proto function pxCtErrors
 @param string Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxCtErrors($s='%s', $return=false)
{
    if (false !== ($result = $GLOBALS['_PX_render']['ct']->error(true, false))) {
        $result = sprintf($s, $result);
        if ($return) return $result;
        echo $result;
    }
}

/**
 Display the action to post a comment.

 @proto function pxCtAction

*/
function pxCtAction()
{
    $url = pxInfo('url', true);
    $base = '';
    if (config::f('url_format') == 'simple') {
        $base .= '?/';
    }
    echo $url.$base.'comments/'.$GLOBALS['_PX_render']['res_id'].'/';
}

/**
 Display the redirection URL

 @proto function pxCtRedirect

*/
function pxCtRedirect($return=false)
{
    if (isset($GLOBALS['_PX_render']['ct_redirect'])) {
        echo $GLOBALS['_PX_render']['ct_redirect'];
    } else {
        echo www::getRequestUri();
    }
}

/**
 Display some special fields to kill a little more spam.

 @proto function pxCtSpamControl
*/
function pxCtSpamControl()
{
    $twister = md5($_SERVER["REMOTE_ADDR"].time().config::f('secret_key').$GLOBALS['_PX_render']['res_id']);
    echo '<input type=\'hidden\' name=\'twister\' value=\''.$twister.'\' />';
    $fieldname = md5(config::f('secret_key').$twister);
    echo '<input type=\'hidden\' name=\''.$fieldname.'\' value=\''.time().'\' />';    
}


/**
 @proto doc

 !! The news functions

 These functions are to be used in the ''news'' templates.

*/

/**
 Display the title of a news.

 @proto function pxNewsTitle
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxNewsTitle($s='%s', $return=false)
{
    $result = $GLOBALS['_PX_render']['news']->getTextContent('title');
    if (config::fbool('remove_numbers')) {
       $result = px_removeNumbers($result);
    }
    $result = sprintf($s, $result);
    if ($return) return $result;
    echo $result;
}

/**
 Display the content of a news.

 @proto function pxNewsContent
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxNewsContent($return=false)
{
    $result = text::parseContent($GLOBALS['_PX_render']['news']->f('description'));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the keywords or subject of the news.

 @proto function pxNewsKeywords
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxNewsKeywords($s = '%s', $return=false)
{
    $result = '';
    $keywords = trim($GLOBALS['_PX_render']['news']->f('subject'));
    if (strlen($keywords) > 0) {
        $result = sprintf($s, $keywords);
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the creation date of the news.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxNewsDateCreation
 @param string dateformat Format of the date ('%A %e %B %Y')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxNewsDateCreation($dateformat='%A %e %B %Y', $return=false)
{
    $result = strftime($dateformat, date::unix($GLOBALS['_PX_render']['news']->f('creationdate')));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the publication date of the news.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxNewsDatePublication
 @param string dateformat Format of the date ('%A %e %B %Y')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxNewsDatePublication($dateformat='%A %e %B %Y', $return=false)
{
    $result = strftime($dateformat, date::unix($GLOBALS['_PX_render']['news']->f('publicationdate')));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the modification date of the news. Only if newer than the
 publication date.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxNewsDateModification
 @param string dateformat Format of the date ('%A %e %B %Y - %T ')
 @param string s Substitution ('Modified the %s.')
 @param mixed ifmodified Time in minutes between publication date and modification to display, false to always display it (false)
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxNewsDateModification($dateformat='%A %e %B %Y', $s='Modified the %s.',
                                    $ifmodified=false, $return=false)
{
    $result = '';
    if (false !== $ifmodified) {
        $ifmodified = $ifmodified * 60;
        $md = date::unix($GLOBALS['_PX_render']['news']->f('modifdate'));
        $pd = date::unix($GLOBALS['_PX_render']['news']->f('publicationdate'));
        if ($md > ($ifmodified + $pd)) {
            $result = sprintf($s, strftime($dateformat, $md));
        }
    } else {
        $result = sprintf($s, strftime($dateformat, date::unix($GLOBALS['_PX_render']['news']->f('modifdate'))));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the date of end of availaibility of the news. Only if end date.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxNewsDateEnd
 @param string dateformat Format of the date ('%A %e %B %Y - %T ')
 @param string s Substitution ('End the %s.')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxNewsDateEnd($dateformat='%A %e %B %Y', $s='End the %s.', $return=false)
{
    $result = '';
    $y = substr($GLOBALS['_PX_render']['news']->f('enddate'),0,4);
    if ((int)$y < 9999) {
        $result = sprintf($s, strftime($dateformat , date::unix($GLOBALS['_PX_render']['news']->f('enddate'))));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the name of the author

 @proto function pxNewsAuthor
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxNewsAuthor($return=false)
{
    $result = $GLOBALS['_PX_render']['news']->authors->f('user_realname');
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the author public email if available.

 @proto function pxNewsAuthorEmail
 @param string s Substitution string ('%s')
 @param string encoding Encoding for a mailto ('link') or for display 'text'
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxNewsAuthorEmail($s='%s', $encoding='link', $return=false)
{
    $result = '';
    $text = ($encoding == 'link') ? false : true;
    if (strlen($GLOBALS['_PX_render']['news']->authors->f('user_pubemail')) > 0) {
        $result = sprintf($s, text::hexEncode($GLOBALS['_PX_render']['news']->authors->f('user_pubemail'), $text));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the path to the news.

 @proto function pxNewsPath
 @param string type 'fullurl' give path with http:// ('relative')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxNewsPath($type='relative', $return=false)
{
    $result = $GLOBALS['_PX_render']['news']->getPath($type);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the list of categories in which the news is.

 The list is not an HTML list, it is to be used as sentence like
 "Category one, category two and category tree" The category names
 are linked to the category pages.

 @proto function pxNewsCategories
 @param string s Substitution string ('%s')
 @param string p1 First delimiters (', ')
 @param string p2 Last delimiter (' and ')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxNewsCategories($s='%s', $p1=', ', $p2=' and ', $return=false)
{
    $cat = $GLOBALS['_PX_render']['news']->cats;
    $nr = $cat->nbRow();
    $i = 1;
    $link = '<a href="%s">%s</a>';
    $res = '';
    $remove_numbers = config::fbool('remove_numbers');
    while (!$cat->EOF()) {
        $title = $cat->f('category_name');
        if ($remove_numbers) $title = px_removeNumbers($title);
        $res .= sprintf($link, $cat->getPath(), htmlspecialchars($title));
        if ($nr >= 2 && ($i < ($nr - 1))) {
            $res .= $p1;
        }
        if ($nr >= 2 && ($i == ($nr - 1))) {
            $res .= $p2;
        }
        $i++;
        $cat->moveNext();
    }
    $result = sprintf($s, $res);
    
    if ($return) return $result;
    echo $result;
}


/**
 Display the associated link and title if available. 

 @proto function pxNewsAssociatedLink
 @param string s Substitution string ('<a href="%1$s">%2$s</a>')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxNewsAssociatedLink($s='<a href="%1$s">%2$s</a>', $return=false)
{
    $result = '';
    if ($GLOBALS['_PX_render']['news']->f('type_id') == 'news') {
        $GLOBALS['_PX_render']['news']->loadDetails();
        if (strlen($GLOBALS['_PX_render']['news']->details->f('news_titlewebsite')) > 0) {
            $result = sprintf($s, $GLOBALS['_PX_render']['news']->details->f('news_linkwebsite'), $GLOBALS['_PX_render']['news']->details->f('news_titlewebsite'));
        }
    }
    if ($return) return $result;
    echo $result;
}

/**
 Display the number of comments of the news.

 @proto function pxArtCountComments
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxNewsCountComments($return=false)
{
    $result = $GLOBALS['_PX_render']['news']->countComments();
    if ($return) return $result;
    echo $result;
}


/**
 @proto doc

 !! The events functions

 These functions are to be used in the ''events'' templates.

*/

/**
 Display the calendat widget for the events
 
 @proto function pxEventsCalendarWidget
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
 
 */
function pxEventsCalendarWidget($return = false) {
	$resp = Hook::run('onEventCalendarList', array('m' => null, 'return'=>$return));
}

function pxFullCalendar($return = false) {
	$resp = Hook::run('onFullCalendarShow', array('m' => null, 'return'=>$return));
}

function pxGallery($return = false)  {
	$resp = Hook::run('onSlideShow',array('m'=>null, 'return'=>$return));
}

function pxEventsList($year='',$month='',$day='', $limit=15) {
	return FrontEnd::getEventsResources('',$limit,$year,$month,$day);
}

function pxPdfViewer($return = false) {
	$resp = Hook::run('onPdfViewDoc', array('m' => null, 'return'=>$return));
}

/**
 Display the title of a events.

 @proto function pxEventsTitle
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxEventsTitle($s='%s', $return=false)
{
    $result = $GLOBALS['_PX_render']['events']->getTextContent('title');
    if (config::fbool('remove_numbers')) {
       $result = px_removeNumbers($result);
    }
    $result = sprintf($s, $result);
    if ($return) return $result;
    echo $result;
}

/**
 Display the content of a events.

 @proto function pxEventsContent
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxEventsContent($return=false)
{
    $result = text::parseContent($GLOBALS['_PX_render']['events']->f('description'));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the content of a events.

 @proto function pxEventsShortContent
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxEventsShortContent($return=false)
{
    $result = text::parseContent($GLOBALS['_PX_render']['events']->f('event_shortcontent'));
    
    if ($return) return $result;
    echo $result;
}
/**
 Display the keywords or subject of the events.

 @proto function pxNewsKeywords
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxEventsKeywords($s = '%s', $return=false)
{
    $result = '';
    $keywords = trim($GLOBALS['_PX_render']['m']->events->f('subject'));
    if (strlen($keywords) > 0) {
        $result = sprintf($s, $keywords);
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the creation date of the events.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxEventsDateCreation
 @param string dateformat Format of the date ('%A %e %B %Y')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxEventsDateCreation($dateformat='%A %e %B %Y', $return=false)
{
    $result = strftime($dateformat, date::unix($GLOBALS['_PX_render']['events']->f('creationdate')));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the publication date of the event.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxEventsDatePublication
 @param string dateformat Format of the date ('%A %e %B %Y')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

*/
function pxEventsDatePublication($dateformat='%A %e %B %Y', $return=false)
{
    $result = strftime($dateformat, date::unix($GLOBALS['_PX_render']['events']->f('publicationdate')));
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the modification date of the events. Only if newer than the
 publication date.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxNewsDateModification
 @param string dateformat Format of the date ('%A %e %B %Y - %T ')
 @param string s Substitution ('Modified the %s.')
 @param mixed ifmodified Time in minutes between publication date and modification to display, false to always display it (false)
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxEventsDateModification($dateformat='%A %e %B %Y', $s='Modified the %s.',
                                    $ifmodified=false, $return=false)
{
    $result = '';
    if (false !== $ifmodified) {
        $ifmodified = $ifmodified * 60;
        $md = date::unix($GLOBALS['_PX_render']['events']->f('modifdate'));
        $pd = date::unix($GLOBALS['_PX_render']['events']->f('publicationdate'));
        if ($md > ($ifmodified + $pd)) {
            $result = sprintf($s, strftime($dateformat, $md));
        }
    } else {
        $result = sprintf($s, strftime($dateformat, date::unix($GLOBALS['_PX_render']['events']->f('modifdate'))));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the date of end of availaibility of the event. Only if end date.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxNewsDateEnd
 @param string dateformat Format of the date ('%A %e %B %Y - %T ')
 @param string s Substitution ('End the %s.')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxEventsDateEnd($dateformat='%A %e %B %Y', $s='End the %s.', $return=false)
{
    $result = '';
    $y = substr($GLOBALS['_PX_render']['events']->f('enddate'),0,4);
    if ((int)$y < 9999) {
        $result = sprintf($s, strftime($dateformat , date::unix($GLOBALS['_PX_render']['events']->f('enddate'))));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the name of the author

 @proto function pxEventsAuthor
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxEventsAuthor($return=false)
{
    $result = $GLOBALS['_PX_render']['events']->authors->f('user_realname');
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the author public email if available.

 @proto function pxEventsAuthorEmail
 @param string s Substitution string ('%s')
 @param string encoding Encoding for a mailto ('link') or for display 'text'
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxEventsAuthorEmail($s='%s', $encoding='link', $return=false)
{
    $result = '';
    $text = ($encoding == 'link') ? false : true;
    if (strlen($GLOBALS['_PX_render']['events']->authors->f('user_pubemail')) > 0) {
        $result = sprintf($s, text::hexEncode($GLOBALS['_PX_render']['events']->authors->f('user_pubemail'), $text));
    }
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the path to the events.

 @proto function pxEventsPath
 @param string type 'fullurl' give path with http:// ('relative')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxEventsPath($type='relative', $return=false)
{
    $result = $GLOBALS['_PX_render']['events']->getPath($type);
    
    if ($return) return $result;
    echo $result;
}


/**
 Display the list of categories in which the events is.

 The list is not an HTML list, it is to be used as sentence like
 "Category one, category two and category tree" The category names
 are linked to the category pages.

 @proto function pxEventsCategories
 @param string s Substitution string ('%s')
 @param string p1 First delimiters (', ')
 @param string p2 Last delimiter (' and ')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
*/
function pxEventsCategories($s='%s', $p1=', ', $p2=' and ', $return=false)
{
    $cat = $GLOBALS['_PX_render']['events']->cats;
    $nr = $cat->nbRow();
    $i = 1;
    $link = '<a href="%s">%s</a>';
    $res = '';
    $remove_numbers = config::fbool('remove_numbers');
    while (!$cat->EOF()) {
        $title = $cat->f('category_name');
        if ($remove_numbers) $title = px_removeNumbers($title);
        $res .= sprintf($link, $cat->getPath(), htmlspecialchars($title));
        if ($nr >= 2 && ($i < ($nr - 1))) {
            $res .= $p1;
        }
        if ($nr >= 2 && ($i == ($nr - 1))) {
            $res .= $p2;
        }
        $i++;
        $cat->moveNext();
    }
    $result = sprintf($s, $res);
    
    if ($return) return $result;
    echo $result;
}


/**
 Display the number of comments of the news.

 @proto function pxArtCountComments
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxEventsCountComments($return=false)
{
    $result = $GLOBALS['_PX_render']['events']->countComments();
    if ($return) return $result;
    echo $result;
}




/**
 @proto doc

 !! The rsslinks functions

 These functions are to be used in the ''news'' templates.

 */

/**
 Display the title of a events.

 @proto function pxEventsTitle
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

 */
function pxRsslinksTitle($s='%s', $return=false)
{
	$result = $GLOBALS['_PX_render']['rsslinks']->getTextContent('title');
	if (config::fbool('remove_numbers')) {
		$result = px_removeNumbers($result);
	}
	$result = sprintf($s, $result);
	if ($return) return $result;
	echo $result;
}

/**
 Display the content of a events.

 @proto function pxEventsContent
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxRsslinksContent($return=false)
{
	$result = text::parseContent($GLOBALS['_PX_render']['rsslinks']->f('description'));

	if ($return) return $result;
	echo $result;
}

/**
 Display the content of a rsslinks.

 @proto function pxRsslinksShortContent
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxRsslinksShortContent($return=false)
{
	$result = text::parseContent($GLOBALS['_PX_render']['rsslinks']->f('event_shortcontent'));

	if ($return) return $result;
	echo $result;
}
/**
 Display the keywords or subject of the rsslinks.

 @proto function pxNewsKeywords
 @param string s Substitution string ('%s')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxRsslinksKeywords($s = '%s', $return=false)
{
	$result = '';
	$keywords = trim($GLOBALS['_PX_render']['rsslinks']->f('subject'));
	if (strlen($keywords) > 0) {
		$result = sprintf($s, $keywords);
	}

	if ($return) return $result;
	echo $result;
}

/**
 Display the creation date of the rsslinks.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxRsslinksDateCreation
 @param string dateformat Format of the date ('%A %e %B %Y')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

 */
function pxRsslinksDateCreation($dateformat='%A %e %B %Y', $return=false)
{
	$result = strftime($dateformat, date::unix($GLOBALS['_PX_render']['rsslinks']->f('creationdate')));

	if ($return) return $result;
	echo $result;
}

/**
 Display the publication date of the event.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxRsslinksDatePublication
 @param string dateformat Format of the date ('%A %e %B %Y')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout

 */
function pxRsslinksDatePublication($dateformat='%A %e %B %Y', $return=false)
{
	$result = strftime($dateformat, date::unix($GLOBALS['_PX_render']['rsslinks']->f('publicationdate')));

	if ($return) return $result;
	echo $result;
}

/**
 Display the modification date of the rsslinks. Only if newer than the
 publication date.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxNewsDateModification
 @param string dateformat Format of the date ('%A %e %B %Y - %T ')
 @param string s Substitution ('Modified the %s.')
 @param mixed ifmodified Time in minutes between publication date and modification to display, false to always display it (false)
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxRsslinksDateModification($dateformat='%A %e %B %Y', $s='Modified the %s.',
		$ifmodified=false, $return=false)
{
	$result = '';
	if (false !== $ifmodified) {
		$ifmodified = $ifmodified * 60;
		$md = date::unix($GLOBALS['_PX_render']['rsslinks']->f('modifdate'));
		$pd = date::unix($GLOBALS['_PX_render']['rsslinks']->f('publicationdate'));
		if ($md > ($ifmodified + $pd)) {
			$result = sprintf($s, strftime($dateformat, $md));
		}
	} else {
		$result = sprintf($s, strftime($dateformat, date::unix($GLOBALS['_PX_render']['rsslinks']->f('modifdate'))));
	}

	if ($return) return $result;
	echo $result;
}

/**
 Display the date of end of availaibility of the event. Only if end date.

 The substitution string for the date is directly given to
 [strftime|http://www.php.net/strftime]

 @proto function pxNewsDateEnd
 @param string dateformat Format of the date ('%A %e %B %Y - %T ')
 @param string s Substitution ('End the %s.')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxRsslinksDateEnd($dateformat='%A %e %B %Y', $s='End the %s.', $return=false)
{
	$result = '';
	$y = substr($GLOBALS['_PX_render']['rsslinks']->f('enddate'),0,4);
	if ((int)$y < 9999) {
		$result = sprintf($s, strftime($dateformat , date::unix($GLOBALS['_PX_render']['rsslinks']->f('enddate'))));
	}

	if ($return) return $result;
	echo $result;
}

/**
 Display the name of the author

 @proto function pxRsslinksAuthor
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxRsslinksAuthor($return=false)
{
	$result = $GLOBALS['_PX_render']['rsslinks']->authors->f('user_realname');

	if ($return) return $result;
	echo $result;
}

/**
 Display the author public email if available.

 @proto function pxRsslinksAuthorEmail
 @param string s Substitution string ('%s')
 @param string encoding Encoding for a mailto ('link') or for display 'text'
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxRsslinksAuthorEmail($s='%s', $encoding='link', $return=false)
{
	$result = '';
	$text = ($encoding == 'link') ? false : true;
	if (strlen($GLOBALS['_PX_render']['rsslinks']->authors->f('user_pubemail')) > 0) {
		$result = sprintf($s, text::hexEncode($GLOBALS['_PX_render']['rsslinks']->authors->f('user_pubemail'), $text));
	}

	if ($return) return $result;
	echo $result;
}

/**
 Display the path to the rsslinks.

 @proto function pxRsslinksPath
 @param string type 'fullurl' give path with http:// ('relative')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxRsslinksPath($type='relative', $return=false)
{
	$result = $GLOBALS['_PX_render']['rsslinks']->getPath($type);

	if ($return) return $result;
	echo $result;
}


/**
 Display the list of categories in which the rsslinks is.

 The list is not an HTML list, it is to be used as sentence like
 "Category one, category two and category tree" The category names
 are linked to the category pages.

 @proto function pxRsslinksCategories
 @param string s Substitution string ('%s')
 @param string p1 First delimiters (', ')
 @param string p2 Last delimiter (' and ')
 @param boolean return Type of return : true, return result as a string, false (default) print in stdout
 */
function pxRsslinksCategories($s='%s', $p1=', ', $p2=' and ', $return=false)
{
	$cat = $GLOBALS['_PX_render']['rsslinks']->cats;
	$nr = $cat->nbRow();
	$i = 1;
	$link = '<a href="%s">%s</a>';
	$res = '';
	$remove_numbers = config::fbool('remove_numbers');
	while (!$cat->EOF()) {
		$title = $cat->f('category_name');
		if ($remove_numbers) $title = px_removeNumbers($title);
		$res .= sprintf($link, $cat->getPath(), htmlspecialchars($title));
		if ($nr >= 2 && ($i < ($nr - 1))) {
			$res .= $p1;
		}
		if ($nr >= 2 && ($i == ($nr - 1))) {
			$res .= $p2;
		}
		$i++;
		$cat->moveNext();
	}
	$result = sprintf($s, $res);

	if ($return) return $result;
	echo $result;
}


/**
 Display the number of comments of the news.

 @proto function pxArtCountComments
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
 */
function pxRsslinksCountComments($return=false)
{
	$result = $GLOBALS['_PX_render']['rsslinks']->countComments();
	if ($return) return $result;
	echo $result;
}





/**
 * Display the content of a category
 *
 * @credits Nicolas LASSALLE.
 *
 * @param String category the current category to display
 * @param int limit Number of last resources (10)
 * @param string type Type of resources ('' or 'all') for all, 'news' or 'articles'
 */
function pxSitemapShowCatContent($vals, $type, $limit)
{
    $cat = $vals['id'];
    $res = FrontEnd::getResources($cat, $limit, $type);
    
    if ($res->EOF()) {
        $path = $vals['path'];
        echo sprintf('<li class="docs-'.$res->f('type_id').'"><a href="%s">%s</a></li>'."\n", $path,
                     __('Show the category content'));
    } else {
        while (!$res->EOF()) {
            echo sprintf('<li class="docs-'.$res->f('type_id').'"><a href="%s">%s</a></li>'."\n", $res->getPath(), 
                         px_removeNumbers($res->getTextContent('title')));
         $res->moveNext();
        }
    }
}


/**
 * Returns subcategories of a Category
 *
 * @credits Nicolas LASSALLE.
 *
 * @param int category the category we want the subdirectories
 *
 * @return array
 */
function pxSitemapCategoryList($categoryId)
{
	$rep = array();
	$ordermanual = config::fbool('order_cat_manual');
	$remove_numbers = config::fbool('remove_numbers');
	$order = 'ORDER BY category_path';
	if ($ordermanual) {
		$order = 'ORDER BY category_position';
	}
	$cat    = FrontEnd::getCategory($categoryId);

	if (!$cat->EOF()) {
		if ($cat->f('category_path') != '/') {
			$path = $cat->getPath();
			$name = $cat->f('category_name');
			if ($remove_numbers) {
				$name = px_removeNumbers($name);
			}
			$rep = array('path' => $path,
					'name' => $name,
					'desc' => $cat->f('category_description'),
					'parentid' => $cat->f('category_parentid'),
					'id' => $cat->f('category_id'));
		}
	}
	return $rep;
}


/**
 * Returns subcategories of a Category
 * 
 * @credits Nicolas LASSALLE.
 *
 * @param int category the category we want the subdirectories
 *
 * @return array
 */
function pxSitemapCategoriesList($categoryId)
{
    $list = array();
    $ordermanual = config::fbool('order_cat_manual');
    $remove_numbers = config::fbool('remove_numbers');
    $order = 'ORDER BY category_path';
    if ($ordermanual) {
        $order = 'ORDER BY category_position';
    }
    $prim    = FrontEnd::getCategories($categoryId, $order);

    $cats = '';
    while (!$prim->EOF()) {
        if ($prim->f('category_path') != '/') {
            $path = $prim->getPath();
            $name = $prim->f('category_name');
            if ($remove_numbers) {
                $name = px_removeNumbers($name);
            }
            $list[htmlspecialchars($name)] = array('path' => $path, 
                                                   'desc' => $prim->f('category_description'),
                                                   'parentid' => $prim->f('category_parentid'),
                                                   'id' => $prim->f('category_id'));
        }
        $prim->moveNext();
    }
    return $list;
}


/**
 * return the content of a category title without the html tag 
 * (no <hx> or <li>)
 *
 * @credits Nicolas LASSALLE.
 *
 * @return the title to display
 */
function pxSitemapGetCatTitle($name, $vals, $primary=false) 
{
    $rootcat = FrontEnd::getCategory('/');
    $rootcatid = $rootcat->f('category_id');
    if ($primary) {
        return sprintf('<div class="primaryCatTitle"><a href="%s">%s</a>'."\n".'<span>%s</span></div>'."\n",
                       $vals['path'], $name,
                        strip_tags(trim(text::parseContent($vals['desc']))),$name);
    } else {
        return sprintf('<div class="catTitle"><a href="%s">%s</a>'."\n".'<span>%s</span></div>'."\n",
                       $vals['path'], $name,
                        strip_tags(trim(text::parseContent($vals['desc']))),$name);
    }
}

/**
 * Display a sitemap for a category
 *
 * @credits Nicolas LASSALLE.
 *
 * @param int catId the current category to display
 * @param string type Type of resources ('' or 'all') for all, 'news' or 'articles'
 * @param int limit Number of last resources (10)
 */
function pxSitemapShowCategory ($catId, $type='', $limit=10) 
{
    $list = pxSitemapCategoriesList($catId); 
    while (list($name, $vals) = each ($list)) {
        echo sprintf('<li class="subcatlism">%s '."\n",  pxSitemapGetCatTitle($name, $vals)); 
        echo '<ul class="nodeco">'."\n";
        pxSitemapShowCatContent($vals, $type, $limit);
        pxSitemapShowCategory($vals['id'], $type, $limit);
        echo '</ul>'."\n";
        echo '</li>'."\n";
    }
}

/**
 * Display a sitemap for a primary category
 *
 * @credits Nicolas LASSALLE.
 *
 * @param String category the current category to display
 * @param int limit Number of last resources (10)
 * @param string type Type of resources ('' or 'all') for all, 'news' or 'articles'
 */
function pxSitemapShowPrimaryCategory ($category, $type='', $limit=10) 
{
    $catInfo = pxSitemapCategoryList($category);
    echo sprintf('%s'."\n",  pxSitemapGetCatTitle($catInfo['name'], $catInfo,true));
    echo '<ul class="primaryList">'."\n";
    pxSitemapShowCatContent($catInfo, $type, $limit);
    pxSitemapShowCategory($catInfo['id'], $type, $limit);
    echo '</ul>'."\n";
    /*
    while (list($name, $vals) = each ($list)) {
        echo sprintf('%s'."\n",  pxSitemapGetCatTitle($name, $vals,true));
        echo '<ul class="primaryList">'."\n";
        pxSitemapShowCatContent($vals, $type, $limit);
        pxSitemapShowCategory($vals['id'], $type, $limit);
        echo '</ul>'."\n";
    }
    */
}


/**
 * Display the sitemap
 *
 * @credits Nicolas LASSALLE.
 *
 * @param int limit Number of last resources (10)
 * @param string type Type of resources ('' or 'all') for all, 'news' or 'articles'
 */
function pxShowSitemap($type='all', $limit='10') 
{
    if ($type == 'all') {
        $type = '';
    }
    $rootcat = FrontEnd::getCategory('/');
    pxSitemapShowPrimaryCategory ($rootcat->f('category_id'), $type, $limit, 0);
}


/**
 Remove the numbers at the start of a string

 @param string string
 @return string
 @private
*/
function px_removeNumbers($string)
{
    return preg_replace('/^\s*\d+\.\s*/', '', $string);
}




/**
 * Initialisation of the template, it loads all the other librairies. 
 * Needed only if the page is not cached, else we try to load the minimum.
 *
 * @param string Parameters for the template
 */
function pxTemplateInit($params='')
{
    $aparams = explode('|', $params);
    foreach ($aparams as $param) {
        if (strpos($param, ':')) {
            list($key, $val) = explode(':', $param);
            config::setVar(trim($key), trim($val));
        } else {
            config::setVar(trim($param), true);
        }
    }

    setlocale(LC_ALL, strtolower(config::f('lang')));
    Hook::run('onInitTemplate');
}


function pxWebsiteName($s = '%s', $return=false) {
	$website = FrontEnd::getWebsite();
	if (!$website->EOF()) {
		if($return)
			return $website->f('website_id');
		else
			echo sprintf($s,$website->f('website_id'));
	}
}

/**
 * Set of methods useful for the action method of the resource classes.
 *
 * All the methods are 'standalone' methods.
 */
class FrontEnd
{
    /**
     * Get the resources for the category.
     *
     * Set the correct position with respect
     * to the number of resources in one page.
     *
     * @param int Category id ('') default is current category
     * @param int Number of resources per page (10)
     * @param string Type of resources to limit to ('')
     * @param int Page number for the pagination (1)
     * @param string Optional order ('ORDER BY %sresources.publicationdate DESC')
     * @return mixed false or ResourceSet
     */
    public static function getResources($category='', $limit=10, $type='', $page=1,
                          $order='ORDER BY %sresources.publicationdate DESC')
    {
        if ('' == $category) {
            $sql = SQL::getResources();
        } else {
            $sql = SQL::getResourcesInCat($category);
        }
        $con =& pxDBConnect();
		$order = sprintf($order, $con->pfx);

        $sql .= ' AND '.$con->pfx.'resources.website_id=\''
            .$con->esc(config::f('website_id')).'\''."\n";
        $sql .= ' AND '.$con->pfx.'resources.status=\''
            .PX_RESOURCE_STATUS_VALIDE.'\''."\n";
        
        if ('' != $type) {
        	if (strpos($type,',')!== false) {
        		$typeList= explode(',',$type);
        		function addDelimiter(&$item, $key) {
        			$item = '\''.$item.'\'';
        		}
        		array_walk($typeList, 'addDelimiter');

        		$type = implode(',',$typeList);
        		$sql .= ' AND '.$con->pfx.'resources.type_id IN ('
        				.$type.')'."\n";
        	} else {
        		$sql .= ' AND '.$con->pfx.'resources.type_id=\''
                	.$con->esc($type).'\' '."\n";
        	}
        }
        
        $sql .= ' AND ( ';
        $sql .= '('.$con->pfx.'resources.type_id = \'events\' ) '; //AND '.$con->pfx.'resources.enddate >= '.date::stamp().'
        $sql .= ' OR ('.$con->pfx.'resources.publicationdate <= '.date::stamp();
        $sql .= ' AND '.$con->pfx.'resources.enddate >= '.date::stamp().') )';
        $sql .= ' '.$order;
		
        if (($rs = $con->select($sql, 'Paginator', $limit, $page)) === false) {
            $GLOBALS['_PX_render']['error']->setError('MySQL: '.$con->error(), 
                                                      500);
            return false;
        }
        return $rs;
    }

    /**
     * Get the online resources in a cat and them sub categories
     * @param mixed Category path or id
     * @param mixed Resource path or id
     * @param int Number of resources per page (10)
     * @param string Type of resources to limit to ('')
     * @param int Page number for the pagination (1)
     * @param string Optional order ('ORDER BY %sresources.publicationdate DESC')
     * @return mixed false or ResourceSet
     */
    public static function getOnlineResourcesInCat($cat, $res, $limit=10, $type='', $page=1,
                          $order='ORDER BY %sresources.publicationdate DESC') {
    	
    	$con =& pxDBConnect();
    	$sql = SQL::getOnlineResourceInCat($res, $cat, $con->esc(config::f('website_id')));
    	if ('' != $type) {
    		if (strpos($type,',')!== false) {
    			$typeList= explode(',',$type);
    			function addDelimiter(&$item, $key) {
    				$item = '\''.$item.'\'';
    			}
    			array_walk($typeList, 'addDelimiter');
    		
    			$type = implode(',',$typeList);
    			$sql .= ' AND '.$con->pfx.'resources.type_id IN ('
    					.$type.') '."\n";
    		} else {
    			$sql .= ' AND '.$con->pfx.'resources.type_id=\''
    					.$con->esc($type).'\' '."\n";
    		}

    	}

    	$order = sprintf($order, $con->pfx);
    	$sql .= ' '.$order;
    	//echo $sql;
    	if (($rs = $con->select($sql, 'Paginator', $limit, $page)) === false) {
    		$GLOBALS['_PX_render']['error']->setError('MySQL: '.$con->error(),
    				500);
    		return false;
    	}
    	return $rs;
    }
    
    
    /**
     * Get the events resources
     * @param int Category id
     * @param int Number of resources to return
     * @param int Year
     * @param int Month
     * @param int Day
     * @param string Order of the result
     * @return mixed false or a recordset
     */
    public static function getEventsResources($category='', $limit='', $year='', $month='', $day='',  $order='ORDER BY %sevents.event_startdate ASC') {
    	if ('' == $category) {
    		$sql = SQL::getResources();
    	} else {
    		$sql = SQL::getResourcesInCat($category);
    	}

    	$con =& pxDBConnect();
    	$order = sprintf($order, $con->pfx);
    	
    	$sql .= ' AND '.$con->pfx.'resources.website_id=\''
    					.$con->esc(config::f('website_id')).'\''."\n";
    	$sql .= ' AND '.$con->pfx.'resources.status=\''
    					.PX_RESOURCE_STATUS_VALIDE.'\''."\n";
    	$sql .= ' AND '.$con->pfx.'resources.type_id=\'events\' '."\n";

    	if ($year == false || $year =='false' || $year=='') $year = date('Y');
    	if ($month == '' || $month == 'false' || $month == false) $month = date('m');
    	$month = substr('00'.$month, -2);
   	
    	$sql .= ' AND '.$con->pfx.'events.event_startdate <= '.$year.$month.'31000000 ';
    	$sql .= ' AND '.$con->pfx.'events.event_enddate >= '.$year.$month.'01000000';

    	$sql .= ' '.$order;
    	
    	if ($limit != '') 
    		$sql .= ' LIMIT '.$limit;
     	//echo $sql;
    	if (($rs = $con->select($sql)) === false) {
    		$GLOBALS['_PX_render']['error']->setError('MySQL: '.$con->error(), 500);
    		return false;
    	}
    	return $rs;
    }
    
    /**
     * 
     * Get the last date of modification from the resources of a website
     * @param String website id (optionnal)
     * @return result
     */
    public static function getLastModifOfResources($website = '')  {
    	$con = & pxDBConnect();
        if ('' == $website) {
            $website = config::f('website_id');
        }
    	
    	$sql = SQL::getLastModif($website);
        if (($rs = $con->select($sql, 'Category')) !== false) {
            return $rs;
        } else {
            $GLOBALS['_PX_render']['error']->setError('MySQL: '
                                                      .$this->con->error(), 
                                                      500);
            return false;
        }    
    }

    /** 
     * Get a category by id or path.
     *
     * @param mixed Id as int or path as string
     * @return mixed Category or false
     */
    public static function getCategory($cat)
    {
        if (preg_match('/^[0-9]+$/', $cat)) {
            $sql = SQL::getCategoryById($cat);
        } else {
            $sql = SQL::getCategoryByPath($cat, config::f('website_id'));
        }

        $con =& pxDBConnect();
        if (($rs = $con->select($sql, 'Category')) !== false) {
            return $rs;
        } else {
            $GLOBALS['_PX_render']['error']->setError('MySQL: '
                                                      .$this->con->error(), 
                                                      500);
            return false;
        }
    }

    /**
     * Get categories.
     *
     * Limit to ones have a given parent, order in a given order.
     *
     * @param int Parent id ('') no limit by default
     * @param string Order ('ORDER BY category_path')
     * @return Category or false
     */
    public static function getCategories($parentid='', $order='ORDER BY category_path')
    {
        $con =& pxDBConnect();
        $sql = 'SELECT * FROM '.$con->pfx.'categories '
            .'LEFT JOIN '.$con->pfx.'websites 
             ON '.$con->pfx.'websites.website_id='
            .$con->pfx.'categories.website_id '
            .'WHERE '.$con->pfx.'categories.website_id=\''
            .$con->esc(config::f('website_id')).'\'';
        if (!empty($parentid)) {
            $sql .= ' AND category_parentid=\''.$con->esc($parentid).'\'';
        }
        $sql .= ' AND category_path NOT LIKE \'%/\\_%\'';
        $sql .= ' '.$order;
        
        if (($rs = $con->select($sql, 'Category')) !== false) {
            return $rs;
        } else {
            $GLOBALS['_PX_render']['error']->setError('MySQL: '.$con->error(), 500);
            echo $con->error();
            return false;
        }

    }



    /**
     * Get the website data.
     *
     * @param string Website id ('') current by default
     * @return mixed RecordSet or false in case of error
     */
    public static function getWebsite($website='')
    {
        if ('' == $website) {
            $website = config::f('website_id');
        }
        $sql = SQL::getWebsite($website);
        $con =& pxDBConnect();
        if (($rs = $con->select($sql)) !== false) {
            return $rs;
        } else {
            $GLOBALS['_PX_render']['error']->setError('MySQL: '.$con->error(), 500);
            return false;
        }
    }

    
    /**
     * Get the websites data.
     *
     * @return mixed RecordSet or false in case of error
     */
    public static function getWebsites()
    {
    	$sql = SQL::getWebsites();
    	$con =& pxDBConnect();
    	if (($rs = $con->select($sql)) !== false) {
    		return $rs;
    	} else {
    		$GLOBALS['_PX_render']['error']->setError('MySQL: '.$con->error(), 500);
    		return false;
    	}
    }
    
    
	/**
     * Get extra header from the template extension.
     *
     * @param string Template file
     * @return string Extra header to send
     */
	public static function getHeader($template)
	{
		$ext = strtolower(substr(strrchr($template, '.'), 1));
		$encoding = strtolower(config::f('encoding'));
		$headers = array(
		  'htm'   => 'text/html; charset='.$encoding,
		  'html'  => 'text/html; charset='.$encoding,
		  'php'   => 'text/html; charset='.$encoding,
		  'xhtml' => 'application/xhtml+xml; charset='.$encoding,
		  'txt'   => 'text/plain; charset='.$encoding,
		  'rss'   => 'text/xml; charset='.$encoding,
		  'rdf'   => 'text/xml; charset='.$encoding,
		  'xml'   => 'text/xml; charset='.$encoding,
          'atom'  => 'application/atom+xml; charset=utf-8',
		);
		if (!empty($headers[$ext])) {
			return sprintf('Content-Type: %s', $headers[$ext]);
		}
		return '';
	}
}

/**
 Display the description of the current category in the meta description.

 @proto function pxMetasDescription
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxMetasDescription($return='')
{
    $result = strip_tags(text::parseContent($GLOBALS['_PX_render']['cat']->f('category_description')),"");
    $result = str_replace("\n","",$result);
    
    if ($return) return $result;
    echo $result;
}

/**
 Display the translated string according to the langague set up in manager.
 If no translation founds, return the string parameter.

 @proto function pxTrans
 @param boolean return Type of return : true return result as a string, false (default) print in stdout
*/
function pxTrans($str, $return=false)
{
    $result = __($str);
    
    if ($return) return $result;
    echo $result;
}
?>
