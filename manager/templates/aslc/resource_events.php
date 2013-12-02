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
	<title><?php pxEventsTitle('%s'); ?></title>
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<meta name="description" content="<?php pxEventsTitle('%s'); ?>" />
	<meta name="DC.Date.modified"
		content="<?php pxEventsDatePublication('%Y-%m-%d'); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="DC.Author" content="<?php pxResAuthor(); ?>" />
	<meta name="DC.Title" content="<?php pxEventsTitle('%s'); ?>" />
</head>
<body class="events">
	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>

			<div id="mainfloat">
				<div id="content">
					<h2 class="restitle">
						<?php pxEventsTitle('%s'); ?>
					</h2>
					<div class="events-infos">
						<p class="modified">
							<?php echo __('On'); ?>
							&nbsp; <a href="<?php pxEventsPath(); ?>"><?php pxEventsDateCreation(__('%Y-%m-%d</a> at %H:%M')); ?>&nbsp;<?php echo __('by'); ?>
							</a>&nbsp; <a href="<?php pxEventsAuthorEmail('mailto:%s'); ?>"><?php pxEventsAuthor(); ?>
							</a>.
							<?php pxEventsCategories(__(' In %s'), ', ', __(' and ')); ?>
						</p>
						<?php if (pxResCommentAvailable()) { ?>
							<p class="comment-count">
								<?php echo __('Number of comments:') ?>
								<?php pxEventsCountComments() ?>
							</p>
						<?php } //end if ?>						
					</div>
					<!-- end news-infos -->

					<?php 
					$eventContent = pxEventsContent(true);
					echo $eventContent;
					?>
					<?php include dirname(__FILE__).'/comments_inline.php'; ?>

					<hr class="invisible" />
				</div>
				<!-- end content -->

			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<!-- col-content -->
				<div class="col-content">
					<?php pxSingleCatTitle('<h2 class="category">%s</h2>'); ?>
					<div class="pxSingleCatDescription">
						<?php pxSingleCatDescription(); ?>
					</div>
					<?php include(dirname(__FILE__).'/inc/welcome.php'); ?>
					<?php include(dirname(__FILE__).'/inc/calendar-events.php'); ?>
					<h2>
						<?php echo __('Links'); ?>
					</h2>
					<?php pxLink::linkList(); ?>
				</div>
				<!-- col-content -->			
			
			</div>
			<!-- end menuright -->

	</div>
	<!-- end main -->
	<?php 
	if (strpos($eventContent,'fullcalendar')>0)  {
		pxFullCalendar();
	}
	if (strpos($eventContent,'pdfviewer')>0) {
		pxPdfViewer();
	}
	?>
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
	</div>
	<!-- end page -->

</body>
</html>
<?php
    $cache->endCache();
endif;
?>