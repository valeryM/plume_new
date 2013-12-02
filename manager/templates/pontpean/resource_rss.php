<?php 
if ($cache->processPage(180)):
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
	<title><?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?></title>
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> : <?php pxArtTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="DC.Date.modified" content="<?php pxArtDatePublication('%Y-%m-%d'); ?>" />
	<meta name="DC.Author" content="<?php pxResAuthor(); ?>" />
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?>" />
	<meta name="DC.Identifier" content="<?php pxArtPath('fullurl'); ?>" />
	<?php 
	$keywords = pxSingleCatTitle('%s',true).' '.pxSingleCatPath('%s',false,true). ' ';
	$keywords.= pxResWordIndex($GLOBALS['_PX_render']['rss']->f('resource_id'),'1,10');
	?>
	<meta name="DC.Keywords" content="<?php echo pxArtKeywords('%s',true).' '.$keywords; ?>" />
</head>
<body class="resource_rss">

	<div id="page">

		<!-- Beginning Banner -->
		<?php require dirname(__FILE__).'/inc/banner2.php'; ?>
		<?php require dirname(__FILE__).'/inc/menu_top.php'; ?>
		<!-- End of Banner -->

		<div id="main">
			<!-- Beginning breadcrumb -->
			<?php //pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
			<!-- End Breadcrumb -->

			<div id="mainleft">
				<!-- Beginning content -->
				<div id="content">
					<?php
					$rssContent = pxGetFeed('%s');
					echo $rssContent;
					?>
					<hr class="invisible" />
				</div>
				<!-- end content -->
			</div><!-- end mainfloat -->
			
			<div id="menuright">
				<div id="infoPratique">
					<?php require('inc/breadCrumbs.php'); ?>				
					<?php require('inc/links.php'); ?>
				</div>
			</div><!-- end menuright -->
			
		</div>
		<!-- end main -->
		<?php 
		if (strpos($rssContent,'fullcalendar')>0)  {
			pxFullCalendar();
		}		
		if (strpos($rssContent,'pdfviewer')>0) {
			pxPdfViewer();
		}
		?>
	</div>
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php
$cache->endCache();
endif;
?>