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
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<title><?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?></title>
	<?php require(dirname(__FILE__).'/inc/head-link.php'); ?>
	<?php require(dirname(__FILE__).'/inc/head-meta.php'); ?>	
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> : <?php pxArtTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.Date.modified" content="<?php pxArtDatePublication('%Y-%m-%d'); ?>" />
	<meta name="DC.Author" content="<?php pxArtAuthor(); ?>" />
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?>" />
	<meta name="DC.Identifier" content="<?php pxArtPath('fullurl'); ?>" />
	<?php 
	$keywords = pxSingleCatTitle('%s',true).' '.pxSingleCatPath('%s',false,true). ' ';
	$keywords.= pxResWordIndex($GLOBALS['_PX_render']['art']->f('resource_id'),'1,10');
	?>
	<meta name="DC.Keywords" content="<?php echo pxArtKeywords('%s',true).' '.$keywords; ?>" />
</head>

<body class="resource_article category">
	<div id="page">
		<?php require dirname(__FILE__).'/inc/banner2.php'; ?>
		<?php require dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">

			<div id="mainleft">
				<div id="content">
					<!--
					<h1 class="art-title">
						<?php //pxArtTitle(); ?>
					</h1>
					-->
					<div id="tabs-<?php echo $GLOBALS['_PX_render']['art']->f('resource_id') ?>">
						<?php pxArticlePagesNav(); ?>
						<?php /* if (pxArtPageIsFirst()): ?>
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
						<?php endif; */?>
						
						<?php 		
							pxArticlePagesContent();
						?>
						
					</div>
					<?php include dirname(__FILE__).'/comments_inline.php'; ?>

					<hr class="invisible" />
				</div>
				<!-- end content -->

			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<div id="infoPratique">
					<?php require('inc/breadCrumbs.php'); ?>				
					<?php require('inc/links.php'); ?>
				</div>
			</div><!-- end menuright -->

		</div>
		<!-- end main -->
		<?php 
		$artContent = pxArtPageContent(true);
		if (strpos($artContent,'fullcalendar')>0)  {
			pxFullCalendar();
		}
		if (strpos($artContent,'pdfviewer')>0) {
			pxPdfViewer();
		}
		?>
		
	</div><!-- end page -->
	<?php require(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php
    $cache->endCache();
endif;
?>