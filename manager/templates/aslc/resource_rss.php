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
<meta name="description"
	content="<?php pxSingleCatTitle('%s'); ?> : <?php pxArtTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
<meta name="DC.Date.modified"
	content="<?php pxArtDatePublication('%Y-%m-%d'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Author" content="<?php pxResAuthor(); ?>" />
<meta name="DC.Title"
	content="<?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?>" />
</head>

<body>

	<div id="page">

		<!-- Beginning Banner -->
		<?php include(dirname(__FILE__).'/inc/banner.php'); ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<!-- End of Banner -->

		<div id="main">
			<!-- Beginning breadcrumb -->
			<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
			<!-- End Breadcrumb -->

			<div id="mainfloat">
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
				<div class="col-content">
					<?php include(dirname(__FILE__).'/inc/welcome.php'); ?>
					<?php include(dirname(__FILE__).'/inc/calendar-events.php'); ?>
					<h2>
						<?php echo __('Links'); ?>
					</h2>
					<?php pxLink::linkList(); ?>
				</div><!-- col-content -->
				
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
		<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
	</div>
</body>
</html>
<?php
$cache->endCache();
endif;
?>