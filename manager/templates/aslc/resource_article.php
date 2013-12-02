<?php 
// ajout commentaire

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
	<meta name="DC.Date.modified" content="<?php pxArtDatePublication('%Y-%m-%d'); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="DC.Author" content="<?php pxResAuthor(); ?>" />
	<meta name="DC.Title"
		content="<?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?>" />
</head>
<body class="category">
	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">

			<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>

			<div id="mainfloat">
				<div id="content">

					<h1 class="art-title">
						<?php pxArtTitle(); ?>
					</h1>
					<?php if (pxArtPageIsFirst()): ?>
						<div id="art-description">
							<p class="modified">
								<?php echo __('on'); ?>
								<a href="<?php pxArtPath(); ?>">
									<?php pxArtDateModification(__('%Y-%m-%d at %H:%M'), '%s'); ?>
								</a>
								&nbsp;
								<?php echo __('by'); ?>
								&nbsp;
								<a href="<?php pxArtAuthorEmail('mailto:%s'); ?>">
									<?php pxArtAuthor(); ?>
								</a>&nbsp;<?php pxArtCategories(__(' In %s'), ', ', __(' and ')); ?>
							</p>
							<?php if (pxResCommentAvailable()) { ?>
								<p class="comment-count">
									<?php echo __('Number of comments:') ?>
									<?php pxArtCountComments() ?>
								</p>
							<?php } //end if ?>
							<?php pxArtDescription(); ?>
						</div>
						<!-- end content -->

					<?php else: ?>
						<div id="art-description">
							<p class="modified">
								<?php echo __('on'); ?>
								<a href="<?php pxArtPath(); ?>">
									<?php pxArtDateModification(__('%Y-%m-%d at %H:%M'), '%s'); ?>
								</a>
								&nbsp;
								<?php echo __('by'); ?>
								<a href="<?php pxArtAuthorEmail('mailto:%s'); ?>">
									<?php pxArtAuthor(); ?>
								</a>						
							</p>
						</div>
						<!-- end art-description -->
					<?php endif; ?>

					<h2 class="art-page-title">
						<?php pxArtPageTitle(); ?>
					</h2>
					<?php 
						$artContent = pxArtPageContent(true); 
						echo $artContent;
					?>
					<?php pxArtListPages(__('<div id="art-pages-list"><h3>Pages of the article</h3> %s</div>')); ?>

					<?php include dirname(__FILE__).'/comments_inline.php'; ?>

					<hr class="invisible" />
				</div>
				<!-- end content -->


			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<div class="col-content">
					<?php pxArtListPages(__('<h2>Articles pages</h2> %s')); ?>
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
				</div><!-- col-content -->				
			</div>
			<!-- end menuright -->

		</div>
		<!-- end main -->
		<?php 
		if (strpos($artContent,'fullcalendar')>0)  {
			pxFullCalendar();
		}
		if (strpos($artContent,'pdfviewer')>0) {
			pxPdfViewer();
		}
		?>
		<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
		</div><!-- end page -->

</body>
</html>
		<?php
    $cache->endCache();
endif;
?>