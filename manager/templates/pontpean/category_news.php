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
			<?php //pxSingleCatTree('<ol class="tree">%s</ol>'); ?>

			<div id="mainleft">
				<div id="content">
					<?php 

						$cat_news = FrontEnd::getCategory(PX_CONFIG_CAT_NEWS)->f('category_id');
						$cat_actus = FrontEnd::getCategory(PX_CONFIG_CAT_ACTUS)->f('category_id');
						
						$sql = pxGetResourcesInCats(array($cat_actus,$cat_news));
						$sql .= ' AND status='.PX_RESOURCE_STATUS_VALIDE;
						$sql .= ' ORDER BY startdate ASC';
						
						//echo 'sql: '.$sql;
						$res = $con->select($sql, 'Resource');
						// Load resources into the child categories (by path)
						//$result = pxGetResourceFromLongPath($GLOBALS['_PX_render']['cat']->f('category_path'),true,'',true);
						//$GLOBALS['_PX_render']['res'] = $result;
 						
						while (!$res->EOF()): ?>
						    <div class="resource">
							    <h2 class="resource_title">
							    	<?php pxResTitle('%s'); ?>
							    	<!-- <a href="<?php pxResPath(); ?>"><?php pxResTitle('%s'); ?></a>-->
							    </h2>
							    <!-- 
							    <p class="modified">
							    	<?php //echo __('on'); ?>
							    	&nbsp;
							    	<?php //pxResDateModification(__('%Y-%m-%d at %H:%M')); ?>
							    </p>
							    -->
							    <p>
							    	<?php 
							    	//echo text::parseContent($GLOBALS['_PX_render']['res']->cur->f('page_content'), 'html');
							    	
							    		if (trim(text::parseContent($GLOBALS['_PX_render']['res']->f('description'), 'text'))==''
							    				&& $GLOBALS['_PX_render']['res']->f('type_id')=='articles') {
											//récupère l'article
											//echo var_dump($GLOBALS['_PX_render']['res']->cur);
											// affiche le résumé de la page 1
											echo text::parseContent($GLOBALS['_PX_render']['res']->cur->f('page_content'), 'html');
										} else {
							    			//pxResDescription('%s',300);
							    			echo text::parseContent($GLOBALS['_PX_render']['res']->f('description'), 'html');
							    		}
							    	?>					    
							    </p>					    		
								<?php if(strlen($res->f('news_titlewebsite'))>0) pxResAssociatedLink(); ?>	
							    <?php if (pxResCommentAvailable()) { ?>
									<p class="comment-count">
								    	<?php echo __('Number of comments:') ?> <?php pxResCountComments() ?>
								    </p>
								 <?php } // end if ?>
						    </div><!-- end resource -->
							<?php 
							$res->moveNext();
							?>
							<?php if (!$res->EOF()) { ?> 
								<hr class="visible" style="float:left; width:100%;"/>
							<?php } ?>
					
						<?php endwhile; // fin de la boucle sur les ressources ?>

					<?php //pxSingleCatNextPage(-1,__('<p><a href="%s">Previous Page</a></p>')); ?>
					<?php //pxSingleCatNextPage(1,__('<p><a href="%s">Next Page</a></p>')); ?>

					<hr class="invisible"/>
				</div><!-- end content -->
	
			</div><!-- end mainleft -->

			<div id="menuright">
				<div id="infoPratique">
					<?php //require('inc/breadCrumbs.php'); ?>				
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
