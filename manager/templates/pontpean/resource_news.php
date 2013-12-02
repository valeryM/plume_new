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
	<title><?php pxSingleCatTitle('%s'); ?> - <?php pxNewsTitle('%s'); ?></title>
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> : <?php pxNewsTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="DC.Date.modified" content="<?php pxNewsDatePublication('%Y-%m-%d'); ?>" />
	<meta name="DC.Author" content="<?php pxNewsAuthor(); ?>" />
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxNewsTitle('%s'); ?>" />
	<meta name="DC.Identifier" content="<?php pxNewsPath('fullurl'); ?>" />
	<?php 
	$keywords = pxSingleCatTitle('%s',true).' '.pxSingleCatPath('%s',false,true). ' ';
	$keywords.= pxResWordIndex($GLOBALS['_PX_render']['news']->f('resource_id'),'1,10');

	?>
	<meta name="DC.Keywords" content="<?php echo pxNewsKeywords('%s',true).' '.$keywords; ?>" />
</head>
<!-- template <?php echo (__FILE__)?> -->
<body class="resource_news news">

	<div id="page">
		<?php require dirname(__FILE__).'/inc/banner2.php'; ?>
		<?php require dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">

			<?php //pxSingleCatTree('<ol class="tree">%s</ol>'); ?>

			<div id="mainleft">
				<div id="content">

					<h1 class="restitle">
						<?php pxNewsTitle('%s'); ?>
					</h1>
					<div class="news-infos">
					<!-- 
						<p class="modified">
							<?php //echo __('On'); ?>
							&nbsp; <a href="<?php //pxNewsPath(); ?>"><?php //pxNewsDateCreation(__('%Y-%m-%d</a> at %H:%M')); ?>&nbsp;<?php //echo __('by'); ?>
							</a>&nbsp; <a href="<?php //pxNewsAuthorEmail('mailto:%s'); ?>"><?php //pxNewsAuthor(); ?>
							</a>.
							<?php //pxNewsCategories(__(' In %s'), ', ', __(' and ')); ?>
						</p>
						-->
						<?php if (pxResCommentAvailable()) { ?>
							<p class="comment-count">
								<?php echo __('Number of comments:') ?>
								<?php pxNewsCountComments() ?>
							</p>
						<?php } ?>
					</div>
					<!-- end news-infos -->

					<?php 
					$newsContent = pxNewsContent(true);
					echo $newsContent;
					?>
					<?php include dirname(__FILE__).'/comments_inline.php'; ?>

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

		</div><!-- end main -->
		<?php 
		if (strpos( $newsContent,'fullcalendar')>0)  {
			pxFullCalendar();
		}
		if (strpos($newsContent,'pdfviewer')>0) {
			pxPdfViewer();
		}
		?>
	</div>
	<!-- end page -->
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php
    $cache->endCache();
endif;
?>