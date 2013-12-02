<?php
if ($cache->processPage(180)):
   
pxTemplateInit('order_res_manual|res_per_page:10|remove_numbers');
//pxGetLastResources();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php pxInfo('encoding'); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<?php require(dirname(__FILE__).'/inc/head-link.php'); ?>
	<?php require(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.Description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.Creator" content="<?php pxResAuthor(); ?>" />
	<meta name="DC.Date.modified" content="<?php pxResDateModification('%Y-%m-%d'); ?>" />
	<meta name="DC.source" content="<?php pxResPath('fullurl')?>" scheme="URI" />
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?> (<?php echo $GLOBALS['_PX_render']['cat']->f('category_path'); ?>)" />
	<?php 
	$keywords = pxSingleCatTitle('%s',true).' '.pxSingleCatPath('%s',false,true). ' ';
	while (!$res->EOF() ) {
		$keywords .= pxResWordIndex($res->f('resource_id'),'1,10');
		$res->moveNext();
	}
	$res->moveStart();
	?>
	<meta name="DC.Keywords" content="<?php echo $keywords; ?>" />
</head>

<body class="category">

	<div id="page">
		<?php require dirname(__FILE__).'/inc/banner2.php'; ?>
		<?php require dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">

			<div id="mainleft">
				<div id="content">
					<?php 
					$restmp = &$GLOBALS['_PX_render']['res']->arry_data;
						// No resources in current category ?
						if(count($restmp)==0) {
							?>
							<h2 class="art-title">Liste des rubriques et documents disponibles</h2>
							<div id="sitemap">
							<?php 	
								pxSitemapShowPrimaryCategory($GLOBALS['_PX_render']['cat']->f('category_id'));
							?>
							</div>
							<hr class="invisible"/>
							<?php 
						} else {

							for ($i=0; $i<count($restmp);$i++) {
								?>
							    <div class="resource">
							    <?php if ($restmp[$i]['type_id']!='articles') {?>
								    <h2 class="resource_title">
								    	<?php echo text::parseContent($restmp[$i]['title']); //pxResTitle('%s'); ?>
								    </h2>
								<?php } // endif?>
								    <p>
								    	<?php 
								    	
								    		if (trim(text::parseContent($restmp[$i]['description'], 'text'))!='' && $restmp[$i]['type_id']=='articles') {
												//récupère l'article
												echo text::parseContent($restmp[$i]['description']);
											} elseif ($restmp[$i]['type_id']=='articles') {
												// affiche le résumé de la page 1
												$art = & $GLOBALS['_PX_render']['art'];

										        $sql = SQL::getOnlineResourceInCat($restmp[$i]['resource_id'], config::f('query_string'), 
										                                           config::f('website_id'));
										        $con =& pxDBConnect();
										        if (($art = $con->select($sql, 'Article')) !== false) {
										            if (!$art->isEmpty()) {
										                $art->load();
										            } else {
										            	
										                echo 'Error!';
										            }
										        }
										        
												?>
												<div id="tabs-<?php echo $restmp[$i]['resource_id']; ?>">
												
												<?php pxArticlePagesNav(); ?>
												<?php pxArticlePagesContent(); ?>						
												</div>
											<?php 
											
											} elseif ($restmp[$i]['type_id']=='rsslinks') {

												// charge les ressources 	
												$rsslinks =& $GLOBALS['_PX_render']['rsslinks'];
												// Parse query string to find the matching rss link
												//list($rss_id, $category_path) = Rsslinks::parseQueryString(config::f('query_string').$restmp->f('path'));
												
												// Load the matching rss links
												// If rss links does not exists, returns error code
												// Will be catched up by the 404 at the end
												$sql = SQL::getOnlineResourceInCat($restmp[$i]['resource_id'], config::f('query_string'),
														config::f('website_id'));
												$con =& pxDBConnect();
												if (($rsslinks = $con->select($sql, 'Rsslinks')) !== false) {
													if (!$rsslinks->isEmpty()  && $rsslinks->f('type_id')=='rsslinks') {
														$rsslinks->load();
													}
												}

												$description =  text::parseContent($restmp[$i]['description']);;
												if ($description != '') {
													echo $description;
													echo '<br/>';
												}
												$titlewebsite =  text::parseContent($rsslinks->details->f('rsslink_titlewebsite')); 
												if ($titlewebsite != '') {
													echo '<div class="titlewebsite">';
													echo $titlewebsite;
													echo '</div>';
												}
												echo '<div class="linkwebsite">';
												echo pxGetFeed($rsslinks->details->f('rsslink_linkwebsite'),'showtitle:true|shortdesc:300|showdate:j M Y');
												echo '</div>';
												
											} else {
								    			echo text::parseContent($restmp[$i]['description'], 'html');
								    		}
								    	?>					    
								    </p>					    		
									<?php pxResAssociatedLink(); ?>	
								    <?php if (pxResCommentAvailable()) { ?>
										<p class="comment-count">
									    	<?php echo __('Number of comments:') ?> <?php pxResCountComments() ?>
									    </p>
									 <?php } // end if ?>
							    </div><!-- end resource -->

								<?php if ($i< (count($restmp)-1) ) { //(!$restmp->EOF()) { ?> 
									<hr class="visible" style="float:left; width:100%;"/>
								<?php } ?>
						
							<?php } // end for // fin de la boucle sur les ressources ?>
						<?php } // end if?>
						
					<hr class="invisible"/>
				</div><!-- end content -->
	
			</div><!-- end mainleft -->

			<div id="menuright">
				<div id="infoPratique">
					<?php require('inc/breadCrumbs.php'); ?>				
					<?php require('inc/links.php'); ?>
				</div>
			</div><!-- end menuright -->

		</div><!-- end main -->

	</div><!-- end page -->
	<?php require(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php
    $cache->endCache();
endif;
?>
