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

$m->l10n->loadPlugin($m->user->lang, 'searchmng');
$con =& pxDBConnect();
$env  = (!empty($_GET['env'])) ? intval($_GET['env']) : 1;

/*==============================================================================
 Functions needed for the lists
==============================================================================*/
require_once $_PX_config['manager_path'].'/extinc/class.lum.php';
require_once $_PX_config['manager_path'].'/inc/class.search.php';

function line_res($data, $i)
{
	global $_px_ptheme,$env;
	$reindex = sprintf('<a href="tools.php?p=searchmng&amp;op=index&amp;env='.$env.'&amp;id=%s" title="'.__('Index the resource').'"><img src="tools/searchmng/themes/'.$_px_ptheme.'/index.png" alt=" "/></a>',
		urlencode($data['identifier']));
	if (strlen($data['title']) > 50) {
		$data['title'] = '<span title="'.$data['title'].'">'.substr($data['title'],0,47).'...</span>';
	}
	$strings['ok'] = __('Up to date');
	$strings['old'] = __('Index too old');
	$strings['not'] = __('Not indexed');

	$status = '<img src="tools/searchmng/themes/'.$_px_ptheme.'/icon_%s.png" alt="%s"/>';
	$data['status'] = sprintf($status, $data['status'], $strings[$data['status']]);
	return '<tr><td>'.$data['status'].'</td><td>'.$data['title'].'</td><td>'.$data['date'].'</td><td>'.$data['total'].'</td><td>'.$reindex.'</td></tr>'."\n";
}


/*==============================================================================
 Process block
==============================================================================*/
$s = new Search($con, $m->user->website);

//-- Index a resource if asked --//
if (!empty($_GET['op']) && 'index' == $_GET['op'] && !empty($_GET['id'])) {
	list($type, $id) = split('-', $_GET['id']);
	if ('art' == $type) {
		$type = 'Article';
	} else if ('events' == $type)  {
		$type = "Events";
	} else $type = 'News';
	$id = trim($id);
	if (false !== ($res = $m->getResourceByIdentifier($id, $type))) {
		if ($id == $res->f('resource_id')) {
			$m->indexRemove($res);
			if (false !==  $m->indexResource($res) ) {
				$msg =  __('The resource has been successfully indexed.');
				//$msg = 'contenu: '.html_entity_decode($res->getAsString());
				header('Location: tools.php?p=searchmng&env='.$env.'&msg='.urlencode($msg));
				exit;
			}
		}
	}
} else if (!empty($_GET['op']) && 'index' == $_GET['op'] && empty($_GET['id'])) {
// index all the resources if asked
	if (false !== $res = $m->getResources('', '', '', '', '', '', '', '', true)) {//		getResourcesFromPath('/') ) {
		//Charge la liste des resources
		$cpt = 0;
		$ok = 0;
		$msg = '';
		while (!$res->EOF())  {
			$cpt++;
			$id = $res->f('resource_id');
			$type = $res->f('type_id');
			if  ($type == 'articles') {
				$class = 'Article';
			} else if ($type == 'events') {
				$class = 'Events';
			} else $class = 'News';
			// charge l'objet ressource correspondant
			if (false !== ($resObj = $m->getResourceByIdentifier($id, $class))) {
				// demande l'indexation
				if (false !== $m->indexResource($resObj))  {
					//echo 'resource :'.$id. ' indexée<br>';
					$ok++;
				} else $msg .= 'Ressource : '.$id. ' non indexée';
			}
			$res->moveNext();
		}
		$msg .= $ok .'/'.$cpt.' ressources indexée(s)<br>';
		header('Location: tools.php?p=searchmng&env='.$env.'&msg='.urlencode($msg));
		//exit;
	}
		
}

//-- Clean the index if asked --//
if (!empty($_GET['op']) && 'clean' == $_GET['op']) {
	if (false !== ($words = $s->clean_index())) {
		$msg =  sprintf(__('The index has been cleaned with <b>%s</b> unused word(s) removed.'), $words);
		header('Location: tools.php?p=searchmng&msg='.urlencode($msg));
		exit;
	}
}



$rs = $s->get_indexed_resources_stats($m->user->website);
$i = 0;
$rs_list = array();
while (($rs !== false) && !$rs->EOF()) {
	$indextime = date::unix($rs->f('lastindex'));
	$modiftime = date::unix($rs->f('modifdate'));
	if (0 == $indextime) {
		$rs_list[$i]['status'] = 'not';
	} elseif ($indextime < $modiftime) {
		$rs_list[$i]['status'] = 'old';
	} else {
		$rs_list[$i]['status'] = 'ok';
	}
	$rs_list[$i]['date'] = date(__('Y/m/d&\nb\sp;H:i:s'), $indextime);
	$rs_list[$i]['title'] = $rs->f('title');
	$rs_list[$i]['identifier'] = $rs->f('identifier');
	$rs_list[$i]['total'] = $rs->f('nbindex');
	$i++;
	$rs->moveNext();
}

/*==============================================================================
 Display block
==============================================================================*/
?>
<h1><?php  echo __('Search Manager'); ?></h1>

<h2><?php  echo __('Indexed resources'); ?></h2>
<?php

$objLum = new lum($env, 'line_res', $rs_list, 0, 20);

$objLum->htmlHeader = '<table class="clean-table">'."\n".'<tr><th>'.__('Status').'</th><th>'.__('Resource title').'</th><th>'.__('Last indexation').'</th><th colspan="2">'.__('Number').'</th></tr>'."\n";

$objLum->htmlLineStart = '';
$objLum->htmlColStart = '';
$objLum->htmlColEnd = '';
$objLum->htmlLineEnd = '';
$objLum->htmlFooter = '</table>'."\n";

$objLum->htmlLinksStart = '<p class="small">';
$objLum->htmlLinksEnd = '</p>';

$objLum->htmlCurPgStart = '<strong>';
$objLum->htmlCurPgEnd = '</strong>';

$objLum->htmlPrev =  __('&laquo; previous page');
$objLum->htmlNext =  __('&raquo; next page');
$objLum->htmlPrevGrp = '...';
$objLum->htmlNextGrp = '...';

$objLum->htmlEmpty = '<p><strong>'. __('No resources.').'</strong></p>';
$objLum->htmlLinksLib =  __('page(s):');

echo $objLum->drawLinks();
echo $objLum->drawPage();
echo $objLum->drawLinks();
$status = '<img src="tools/searchmng/themes/'.$_px_ptheme.'/icon_%s.png" alt="%s"/>';
?>
<ul class="checklist">
<li><?php echo sprintf($status,'ok',__('Up to date')).' '.__('Up to date'); ?></li>
<li><?php echo sprintf($status,'old',__('Index too old')).' '.__('Index too old'); ?></li>
<li><?php echo sprintf($status,'not',__('Not indexed')).' '.__('Not indexed'); ?></li>
</ul>

<h2><?php  echo __('Index maintenance'); ?></h2>
<?php $url = 'tools.php?p=searchmng&amp;op=index'; ?>
<p><?php echo sprintf(__('<a href="%s">Index all the resources</a>. This operation may take a long time and is database intensive.'), $url); ?></p>
<p></p>
<?php $url = 'tools.php?p=searchmng&amp;op=clean'; ?>
<p><?php echo sprintf(__('<a href="%s">Clean the index of unused words</a>. This operation may take a long time and is database intensive.'), $url); ?></p>
