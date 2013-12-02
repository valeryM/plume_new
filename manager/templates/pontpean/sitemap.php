<?php
if ($cache->processPage(3600)):

define('TREEVIEW_LIB_PATH', config::f('xmedia_root').'/theme/'.pxInfo('theme',true).'/js/dbtreeview/lib/dbtreeview');
define('TREEVIEW_JS_PATH', pxInfo('filesurl',true).'theme/'.pxInfo('theme',true).'/js/dbtreeview/lib/dbtreeview');

define('TREEVIEW_IMG_PATH',TREEVIEW_JS_PATH.'/media/');

require_once(TREEVIEW_LIB_PATH . '/dbtreeview.php');
require_once(TREEVIEW_LIB_PATH . '/../../handler.class.php');

try {
	DBTreeView::processRequest(new MyHandler());
} catch(Exception $e) {
	echo("Error:". $e->getMessage());
}

pxTemplateInit('remove_numbers');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	
	<!-- <meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" /> -->
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<title>Site map of <?php pxInfo('name'); ?></title>
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<?php pxHeadLinks(); ?>
	<meta name="DC.title" content="Site map of <?php pxInfo('name'); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<?php 
	printf("<script src=\"%s/treeview.js\" type=\"text/javascript\"></script>\n",
			TREEVIEW_JS_PATH);

	printf('<link href="%s/treeview.css" rel="stylesheet" type="text/css" media="screen"/>'."\n",
			TREEVIEW_JS_PATH);
	?>
</head>

<body class="category">

	<div id="page">
		<?php require dirname(__FILE__).'/inc/banner2.php'; ?>
		<?php //require dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">

			<div id="mainleft">
				<div id="content">
					<div id="sitemap">
						<h1>
							<?php echo __('Sitemap'); ?>
						</h1>					
						<?php //pxShowSitemap('all', 25); ?>
						
						<h3>Voici l'arborescence du site.</h3>
						<p>Cliquez sur '+' pour ouvir la branche, sur '-' pour la refermer.</p>
						<p>Cliquer sur le titre pour ouvrir la rubrique ou le document.</p>
						<?php
						$rootAttributes = array("noeud"=>"/");
						$treeID = "treev1";
						$tv = DBTreeView::createTreeView(
								$rootAttributes,
								TREEVIEW_JS_PATH, 
								$treeID);
						$tv->printTreeViewScript();
						?>
						
					</div>
					
					<hr class="invisible" />
				</div>
				<!-- end content -->

			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<div id="infoPratique">
					<?php //require('inc/breadCrumbs.php'); ?>				
					<?php require('inc/links.php'); ?>
				</div>
			</div><!-- end menuright -->

		</div>
		<!-- end main -->

	</div>
	<!-- end page -->
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php 
	$cache->endCache();
endif;
?>