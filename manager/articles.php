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

require_once 'path.php';
require_once $_PX_config['manager_path'].'/prepend.php';
require_once $_PX_config['manager_path'].'/inc/class.article.php';

auth::checkAuth(PX_AUTH_NORMAL);
$is_user_admin = auth::asLevel(PX_AUTH_ADMIN);

$m = new Manager();
$_px_theme = $m->user->getTheme();
$do="";
$old_art=0;
if (!empty($_REQUEST['do'])) $do=$_REQUEST['do'];
if (!empty($_REQUEST['old_art'])) $old_art=$_REQUEST['old_art'];
if ($do=="copy" && $old_art==0)  {
	if (!empty($_REQUEST['resource_id'])) $old_art=$_REQUEST['resource_id'];
}
$param="&do=".$do."&old_art=" .$old_art;

// Default value : Liste events 
//if ($_PX_config['mode_affichage_contenu']=='list' && empty($_REQUEST['op'])) $_REQUEST['op'] = 'list';

// en cas de copie,

/* ================================================= *
 *       Generate sub-menu                           *
 * ================================================= */

$px_submenu->addItem(__('Back to the list of resources'), 'index.php',
                     'themes/'.$_px_theme.'/images/ico_back.png', false);
$px_submenu->addItem(__('New article'), 'articles.php',
		'themes/'.$_px_theme.'/images/ico_new.png', false,
		(!empty($_REQUEST['op'])||empty($_REQUEST['resource_id']))
);
$px_submenu->addItem(__('Article list'), 'articles.php?op=list',
		'themes/'.$_px_theme.'/images/ico_article.png', false);
/*
$px_submenu->addItem('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '', '', false);

$px_submenu->addItem(__('News list'), 'news.php?op=list',
		'themes/'.$_px_theme.'/images/ico_news.png', false);

$px_submenu->addItem(__('Events list'), 'events.php?op=list',
		'themes/'.$_px_theme.'/images/ico_datetime.png', false);

$px_submenu->addItem(__('Rss links list'), 'rsslinks.php?op=list',
		'themes/'.$_px_theme.'/images/rss_edit.png', false);
$px_submenu->addItem(__('Files or images'), 'xmedia.php',
		'themes/'.$_px_theme.'/images/ico_image.png', false);
$_site_url = $m->user->wdata[$GLOBALS['_PX_website_config']['website_id']]['website_url'].'/';

$px_submenu->addItem(__('See the site'), $_site_url,
		'themes/'.$_px_theme.'/images/ico_site.png', false);

*/

/* ========================================================================= *
 *                Process block                                              *
 * ========================================================================= */

/* ===================================================================== *
 *                        add/edit/view an article                       *
 * ===================================================================== */
if (empty($_REQUEST['op']) )  {

	$ar = new Article();

	/* =================================================== *
	 *  Get current article and check if right to edit it  *
	 * =================================================== */
	$is_editable = true;
	//$is_copy=false;
	if (!empty($_REQUEST['resource_id'])) {
		if (false !== $m->loadResource($ar, $_REQUEST['resource_id'])) {
			// check the rights
			if (!$m->asRightToEdit($ar)) {
				$is_editable = false;
			}
			// en cas de copie,
			if ($do=='copy' && $m->asRightToCopy($ar))  {
				// prépare et sauvegarde le nouvel enregistrement
				$ar->setField('resource_id',false);
				$ar->pages->setField('page_id',false);
				$ar->setField('identifier','');					
				$ar->setField('status', PX_RESOURCE_STATUS_INEDITION);
				//$ar->authors->setField('resource_id',false);
				//$ar->authors->setField('user_id',$m->user->getId());
				$ar->cats=new Category(); //->setField('category_id',false);
				$ar->setField('title', __('Copy of'). ' '.$ar->f('title') );
				$is_editable=true;
				unset($_REQUEST['resource_id']);

			}
		} else {
			$m->setError(__('Error: The requested article is not available.'), 400);
			$is_editable = false;
		}
	} else {
		$ar->setDefaults($m->user);
	}
	
	$px_submenu->addItem(__('See the article'), $ar->getPath(),
                         'themes/'.$_px_theme.'/images/ico_article_site.png', 
						false,
						($ar->f('status') == PX_RESOURCE_STATUS_VALIDE) &&
						($ar->f('resource_id') > 0)
						);

	/* ========================== *
	 *  Get values from the form  *
	 * ========================== */
	if ((!empty($_POST['preview']) || !empty($_POST['publish'])
	|| !empty($_POST['transform']) || !empty($_POST['addcategory'])
	|| !empty($_POST['increase']) || !empty($_POST['increase_x'])
	|| !empty($_POST['decrease']) || !empty($_POST['decrease_x'])
	) && $is_editable) {
		$ar->set(form::getPostField('a_title'),
		form::getPostField('a_subject'),
		form::getPostField('a_description'),
		form::getPostField('a_description_format'),
		form::getPostField('a_status'),
		form::getPostField('a_path'),
		form::getDateAndTimeField('a_dt')  /*Publication date */,
		form::getDateAndTimeField('a_dt_e') /*End date */,
		form::getPostField('a_noenddate'),
		form::getPostField('a_comment_support'),
		form::getPostField('a_subtype'));
		//form::getPostField('filRouge'));
		

		if ($ar->f('resource_id') == '') {
			$ar->setField('category_id', form::getPostField('cat_id'));
		}
		if (!empty($_POST['increase']) || !empty($_POST['increase_x'])) {
			$m->user->increase('article_textarea_content');
		}
		if (!empty($_POST['decrease']) || !empty($_POST['decrease_x'])) {
			$m->user->decrease('article_textarea_content');
		}
		if (!empty($_POST['transform'])) {
			
			$ar->setField('description',
                          '=html'."\n"
                          .$ar->getFormattedContent('description','html'));
		}
	}
	
	/* ================================================= *
	 *              Copy a page                    *
	* ================================================= */
	/*
	if (!empty($_GET['do'])) {
		if ($is_editable && $_GET['do']=='copy' && $ar->f('status') != PX_RESOURCE_STATUS_INEDITION) {
	
			if ($ar->f('resource_id')>0) {
				$res_id = $ar->f('resource_id');
				// define new values to prepare recordset
				$ar->setField('resource_id',false);
				$ar->setField('identifier','');
				$ar->setField('status', PX_RESOURCE_STATUS_INEDITION);
	
				$ar->loadPagesFrom($res_id,$ar);
	
				$ar->authors->setField('resource_id',false);
				$ar->authors->setField('user_id',$m->user->getId());
				$ar->cats->setField('identifier',false);
				$ar->cats->setField('category_id',false);
				$ar->setField('title', __('Copy of'). ' '.$ar->f('title') );
				 
				// run save method
				$events->isModified = true;
				if (false !== ($id = $m->saveArticle($ar))) {
					foreach($ar->pages as $page) {
						$page->setField('page_id',false);
						$page->setField('resource_id','id');
						$page->commit();
						$m->saveArticlePage($ar);
					}
					$m->setMessage(__('The article was successfully copied.'));
					header('Location: articles.php?resource_id='.
							$ar->f('resource_id'));
	
				} else {
					$m->setMessage(__('The article could not be copied.'));
					header('Location: articles.php?op=list');
				}
				exit;
			}
		}
	}
	*/

	/* ================================================= *
	 *               Add, edit an article                *
	 * ================================================= */
	if (!empty($_POST['publish']) && $is_editable) {
		if ($ar->f('resource_id') > 0) {
			// edit an article
			if ($m->htmlIsValid($ar->getFormattedContent('description','html')) && false !== ($id = $m->saveArticle($ar))) {
				if ($ar->f('status') != PX_RESOURCE_STATUS_INEDITION) {
					$m->setMessage(__('The article was successfully saved.'));
					header('Location: articles.php?op=list');
				} else {
					header('Location: articles.php?resource_id='.
					$ar->f('resource_id'));
				}
				exit;
			}
		
		} else {
			// add an article
			$px_category_id = form::getPostField('cat_id');
			if ($px_category_id=='allcat') $px_category_id=form::getPostField('location');
			$m->user->savePref('article_category_id', $px_category_id);
			$m->user->savePref('article_status',
				form::getPostField('a_status'));
			$m->user->savePref('content_format',
			form::getPostField('a_description_format'));
			$m->user->savePref('article_subtype',
				form::getPostField('a_subtype'));
			$m->user->savePref('article_comment_support',
				form::getPostField('a_comment_support'));
			$ar->setField('category_id', $px_category_id);
			//$m->setMessage(__('Try to save the article.'));
			if ($m->htmlIsValid($ar->getFormattedContent('description','html')) && false !== ($id = $m->saveArticle($ar))) {
				$m->setMessage(__('The article was successfully saved.'));
				if ($ar->f('status') != PX_RESOURCE_STATUS_INEDITION) {
					header('Location: articles.php?op=list');
				} else {

					if ($ar->pages->nbRow() == 0) {
						$m->setMessage(__('The article was successfully saved.').' '.__('You need now to add the first page of the article. After adding the first page, you can validate and put online the article.'));
						header('Location: articles.php?op=page&resource_id='.$id . $param);
					} else {
						header('Location: articles.php?resource_id='.$id);
					}
				}
				exit;
			}
		}
	}

	/* ================================================= *
	 *               Associate to a category             *
	 * ================================================= */
	if (strlen(form::getField('addcategory'))
	&& strlen(form::getField('cat_id'))
	&& strlen($ar->f('resource_id'))
	&& $is_editable) {

		$px_ar_category_id = form::getField('cat_id');
		if ($px_ar_category_id=='allcat') $px_ar_category_id=form::getPostField('location');
		$m->user->savePref('article_category_id', $px_ar_category_id);

		if ('main' == form::getField('addcategory')) {
			$type = PX_RESOURCE_CATEGORY_MAIN;
		} else {
			$type = PX_RESOURCE_CATEGORY_OTHER;
		}

		if (false !== $m->addResourceInCategory($ar, $px_ar_category_id, $type)) {
			header('Location: articles.php?resource_id='.$ar->f('resource_id'));
			exit;
		}
	}



	/* ================================================= *
	 *              Remove from a category               *
	 * ================================================= */
	else if (strlen(form::getField('delcat'))
	&& strlen($ar->f('resource_id'))
	&& strlen(form::getField('cat_id'))
	&& $is_editable) {

		$px_category_id = form::getField('cat_id');
		if (false !== $m->removeResourceFromCategory($ar, $px_category_id)) {
			header('Location: articles.php?resource_id='.$ar->f('resource_id'));
			exit;
		}
	}

	/* ================================================= *
	 *               Delete the article                  *
	 * ================================================= */
	else if (strlen(form::getPostField('delete')) && $is_editable) {
		if ($m->delArticle($ar) !== false) {
			$m->setMessage(__('The article was successfully deleted.'));
			header('Location: articles.php?op=list');
			exit;
		}
	}

	/* ================================================= *
	 *               Simple visualization                *
	 * ================================================= */
	else if (!empty($_POST['preview'])) {
		$m->check($ar); // Provide the error feedback
	}

	/* ================================================= *
	 *                 Remove a comment                  *
	 * ================================================= */
	if (strlen(form::getField('delete-comment'))
	&& strlen($ar->f('resource_id'))
	&& strlen(form::getField('comment_id'))
	&& $is_editable) {

		$id = form::getField('comment_id');
		if (false !== ($ct = $m->getComment($id, $ar->f('resource_id')))) {
			if (false !== $m->delComment($ct)) {
				$m->setMessage(__('The comment was successfully deleted.'));
				header('Location: articles.php?resource_id='.$ar->f('resource_id'));
				exit;
			}
		}
	}

	/* ================================================= *
	 *                 Add/Edit a comment                *
	 * ================================================= */
	if (strlen(form::getField('publish-comment'))
	&& strlen($ar->f('resource_id'))
	&& $is_editable) {
		$id = form::getField('comment_id');
		$ct = new Comment();
		if ((int) $id > 0) {
			if (false === $ct->load($id)) {
				$m->setError(__('The requested comment does not exist.'));
			}
		}
		if (false === $m->error()) {
			$ct->set(form::getField('c_author'),
			form::getField('c_email'),
			form::getField('c_website'),
			form::getField('c_content'),
			$ar->f('resource_id'),
			($ct->f('comment_ip')) ? $ct->f('comment_ip') : $_SERVER["REMOTE_ADDR"],
			form::getField('c_status'),
			PX_COMMENT_TYPE_NORMAL,
			($ct->f('comment_id')) ? $ct->f('comment_user_id') : $m->user->getId());
			if (false !== $m->saveComment($ct)) {
				$m->setMessage(__('The comment was successfully saved.'));
				header('Location: articles.php?resource_id='.$ar->f('resource_id'));
				exit;
			}
		}
	}


	/* ================================= *
	 *  Get the categories and subtypes  *
	 * ================================= */
	if ($is_editable) {
		$arry_cat = $m->getArrayCategories();
		$arry_subtypes = $m->getSubTypesArray(PX_RESOURCE_MANAGER_ARTICLE);
	}
}

/* =============================================================== *
 *                Add/edit a page                                  *
 * =============================================================== */

if (form::getField('op') == 'page') {
	/* ================================================ *
	 *          Set all the default values              *
	 * ================================================ */
	$ar = new Article();

	/* =================================================== *
	 *  Get current article and check if right to edit it  *
	 * =================================================== */
	$is_editable = true;
	if (!empty($_REQUEST['a_page_id']))  {
		$page_id=$_REQUEST['a_page_id'];
	} else {
		$page_id="";
	}
	if (!empty($do) && !empty($old_art)) {
		$m->loadResource($ar, $_REQUEST['resource_id']);
		$ar->loadPagesFrom($old_art, $ar);
		$page_id = $ar->pages->f('page_id');
		$is_editable=true;
	} else {

		if (!empty($_REQUEST['resource_id'])) {
			if (false !== $m->loadResource($ar, $_REQUEST['resource_id'])) {
				// check the rights
				if (!$m->asRightToEdit($ar)) {
					$is_editable = false;
				}
				// en cas de copie,
			} else {
				$m->setError(__('Error: The requested article is not available.'), 400);
				$is_editable = false;
			}
		} else {
			$m->setError(__('Error: The requested article is not available.'), 400);
			$is_editable = false;
		}
	}
	if ($page_id !== "") {
		// Request a given page.
		if (false === $ar->goToPage($page_id)) {
			$is_editable = false;
			$m->setError(__('Error: The requested page is not available.'), 400);
		}
	} else {
		// Need a new page
		$ar->setDefaults($m->user);
		$ar->pages->insert();
	}

	if ((!empty($_POST['preview'])
	|| !empty($_POST['publish']) || !empty($_POST['transform'])
	|| !empty($_POST['increase']) || !empty($_POST['increase_x'])
	|| !empty($_POST['decrease']) || !empty($_POST['decrease_x']))
	&& $is_editable) {
		/* =============================== *
		 *  Get value from the form        *
		 * =============================== */
		$ar->setPage(form::getField('a_page_id'),
		form::getPostField('a_page_title'),
		form::getPostField('a_page_content'),
		form::getPostField('a_page_content_format'),
		form::getPostField('a_page_number'));

		if (!empty($_POST['increase']) || !empty($_POST['increase_x'])) {
			$m->user->increase('article_textarea_page');
		}
		if (!empty($_POST['decrease']) || !empty($_POST['decrease_x'])) {
			$m->user->decrease('article_textarea_page');
		}

		if (!empty($_POST['transform'])) {
			$ar->pages->setField('page_content',
                                 '=html'."\n"
                                 .$ar->getFormattedContent('page_content',
                                                           'html', 'pages'));
		}

	}

	

	/* ================================================= *
	 *    Add, edit a page - database operations         *
	 * ================================================= */

	if (!empty($_POST['publish']) && $is_editable) {
		if ($ar->pages->f('page_id') > 0) {
			if (false !== ($id = $m->saveArticlePage($ar))) {
				$m->setMessage(__('The page was successfully saved.'));
				header('Location: articles.php?op=page&resource_id='
				.$ar->f('resource_id').'&a_page_id='.$id);
				exit;
			}
		} else {
			$m->user->savePref('content_format',
			form::getPostField('a_page_content_format'));
			if (false !== ($id = $m->saveArticlePage($ar))) {
				$m->setMessage(__('The page was successfully added.'));
				header('Location: articles.php?op=page&resource_id='
				.$ar->f('resource_id').'&a_page_id='.$id);
				exit;
			}
		}
	}

	else if (!empty($_POST['delete'])
				&& $ar->f('resource_id') > 0
				&& $ar->pages->f('page_id') > 0
				&& $is_editable) {
		if (false !== $m->delArticlePage($ar)) {
			$m->setMessage(__('The page was deleted.'));
			header('Location: articles.php?resource_id='.$ar->f('resource_id'));
			exit;
		}
	}

	else if (!empty($_POST['preview'])) {
		$m->checkArticlePage($ar); // Provide the error feedback
	}
	/*Adding Submenu item into the page edit page*/
	if ($ar->pages->f('page_number')>1)
	{ $pagelink = $ar->getpath().$ar->pages->f('page_number'); }
	else
	{ $pagelink = $ar->getpath(); }

	$px_submenu->addItem(__('See the page'), $pagelink,
                         'themes/'.$_px_theme.'/images/ico_article_site.png', 
	false,
	($ar->f('status') == PX_RESOURCE_STATUS_VALIDE) &&
	($ar->f('resource_id') > 0)
	);
}

// end of Add, edit a page

/* =============================================================== *
 *                     List the articles                           *
 * =============================================================== */
if (form::getField('op') == 'list') {

	//Get the category id and save it
	$cat_id = (!empty($_GET['cat_id'])) ? $_GET['cat_id'] : $m->user->getPref('list_articles_cat_id');
	$m->user->savePref('list_articles_cat_id', $cat_id, $_SESSION['website_id'], true);
	if ($cat_id == 'allcat') $cat_id = '';

	//Get the search query
	$px_q = (!empty($_GET['q'])) ? $_GET['q'] : '';

	//Get available months and selected
	list($first, $last, $arry_months) = $m->getArrayMonths('articles', $cat_id);
	$px_m_s = $m->user->getPref('list_articles_month');
	$px_m = (!empty($_GET['m'])) ? $_GET['m'] : ((!empty($px_m_s)) ? $px_m_s : $last);
	$m->user->savePref('list_articles_month', $px_m, $_SESSION['website_id'], true);

	if ($px_m == 'alldate') {
		//$px_m = $first;
		$px_m = '';
		$px_end = date::stamp(0, 1 /*1 month after now*/, 0);
	} else {
		$px_end = date::stamp(0, 1 /*1 month after $px_m */, 0, date::unix($px_m));
	}
	//$fltFilRouge = (!empty($_GET['fltFilRouge'])) ? $_GET['fltFilRouge'] : 0;
	//$m->user->savePref('fltFilRouge', $fltFilRouge, $_SESSION['website_id'], true);
	
	if (empty($px_q)) {
		$res = $m->getResources(''/* All users */, '' /* All status */, $cat_id, 'articles', $px_m /*Date start */, $px_end /*Date end */, '' /* no limit */, 'DESC', false /* online */, true /*by path */);
		//get again as possibly modified because of the 'alldate' case
		$px_m = $m->user->getPref('list_articles_month');
	} else {
		$res = $m->searchResources($px_q, false /*Not only the online resources */, 'articles', 'ResourceSet' /*Class used for the results */);
		//Search is made on all the date and all the categories
		$px_m = 'alldate';
		$cat_id = 'allcat';
	}

	// Categories
	$arry_cat = $m->getArrayCategories(true);
}

/* ========================================================================= *
 *                         Display block                                     *
 * ========================================================================= */

/* --------------------------------------------------- *
 *   Set title of the page, and load common top page   *
 * --------------------------------------------------- */
if (form::getField('op') == 'page') {
	$px_title =  __('Articles') .' - '. sprintf(__('Chapitre %s '), $ar->pages->f('page_number'));
} else {
	$px_title =  __('Articles');
}
include config::f('manager_path').'/mtemplates/_top.php';

echo '<h1 id="title_article">'.$px_title."</h1>\n\n";

if (form::getField('op') == 'page') {
	include config::f('manager_path').'/mtemplates/article-page-edit.php';

} else if (form::getField('op') == 'list') {
	include config::f('manager_path').'/mtemplates/article-list.php';

} else {
	include config::f('manager_path').'/mtemplates/article-edit.php';
}

/* ------------------------------------------------ *
 *           Load common bottom page                *
 * ------------------------------------------------ */
include config::f('manager_path').'/mtemplates/_bottom.php';

?>
