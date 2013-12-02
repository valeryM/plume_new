<?php 
if ($cache->processPage(180)):
 
pxTemplateInit('order_res_manual|res_per_page:10|remove_numbers');
 ?>
 <!DOCTYPE html>
<html>
<head>
	<meta charset="<?php pxInfo('encoding'); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.Description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.source" content="<?php pxSingleCatPath(); ?>" scheme="URI" />
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

<body class="category_liens">
	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner2.php'; ?>	
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
		
			<div id="mainleft">
				<div id="content">
					<h2 class="art-title">Liste des rubriques et documents disponibles</h2>
					<div id="sitemap">
					<?php 	
						pxSitemapShowPrimaryCategory($GLOBALS['_PX_render']['cat']->f('category_id'));
					?>
					</div>
					<hr class="invisible"/>
				</div><!-- end content -->
	
			</div><!-- end mainleft -->
					
			<div id="menuright">
				<div id="infoPratique">
					<?php require('inc/breadCrumbs.php'); ?>				
					<?php require('inc/links.php'); ?>
				</div>
			</div><!-- end menuright -->
		
		</div> <!--  end main -->

	</div><!-- end page -->
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php
    $cache->endCache();
endif;
?>