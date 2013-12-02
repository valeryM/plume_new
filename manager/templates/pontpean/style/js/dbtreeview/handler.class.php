<?php

class MyHandler implements RequestHandler {


	public function handleChildrenRequest(ChildrenRequest $req) {

		//$sm = new SiteMap();
		$attributes = $req->getAttributes();

		if(!isset($attributes['noeud'])) {
			die("error: attribute code not given");
		}
		$parentCode = $attributes['noeud'];
		$nodes = array();
		if($parentCode=='/') {
			// récupère le tableau
			$liste = pxSitemapPrimaryCategory('','');
			foreach ($liste as $ligne) {
				$node = DBTreeView::createTreeNode($ligne['name'], array('noeud'=> $ligne['id'],'loc'=>$ligne['loc']));
				$node->setURL("javascript:alert(\"You clicked on node 1\");");
				$node->setURL($ligne['loc']);
				$node->setOpenIcon(TREEVIEW_IMG_PATH.'ico_folder_open.png');
				$node->setClosedIcon(TREEVIEW_IMG_PATH.'ico_folder_close.png');
				$nodes[] = $node; //add node to nodes array
			}
		} else {
			$liste = pxSitemapCategory($parentCode,'',1);
			foreach ($liste as $ligne) {
				$node = DBTreeView::createTreeNode($ligne['name'], array('noeud'=> $ligne['id'],'loc'=>$ligne['loc']));
				//$node->setURL("javascript:alert(\"You clicked on node 1\");");
				$node->setURL($ligne['loc']);
				$node->setOpenIcon(TREEVIEW_IMG_PATH.'ico_folder_open.png');
				$node->setClosedIcon(TREEVIEW_IMG_PATH.'ico_folder_close.png');
				$nodes[] = $node; //add node to nodes array

			}
			// liste les documents
			$docs = pxSitemapCatContent($parentCode);
			foreach ($docs as $doc) {
				$node = DBTreeView::createTreeNode($doc['name'], array('type'=>$doc['type'])); //array('noeud'=> $doc['id'])
				$node->setHasChildren(false);
				if ($doc['type']=='articles') {
					$ico=TREEVIEW_IMG_PATH.'ico_article.png';
				} elseif ($doc['type']=='news') {
					$ico=TREEVIEW_IMG_PATH.'ico_news.png';
				} elseif ($doc['type']=='events') {
					$ico=TREEVIEW_IMG_PATH.'ico_datetime.png';
				} elseif ($doc['type']=='rsslinks') {
					$ico=TREEVIEW_IMG_PATH.'ico_rss.png';
				} else
					$ico=TREEVIEW_IMG_PATH.'url.gif';
				$node->setClosedIcon($ico);
				$node->setURL(/*$attributes['loc'].*/$doc['loc']);
				$nodes[] = $node; //add node to nodes array
			}
				
		}

		$response = DBTreeView::createChildrenResponse($nodes);
		return $response;
	}
}

?>