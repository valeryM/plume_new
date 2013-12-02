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
	<title><?php pxSingleCatTitle('%s'); ?> - <?php pxRsslinksTitle('%s'); ?></title>
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> : <?php pxRsslinksTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="DC.Date.modified" content="<?php pxRsslinksDatePublication('%Y-%m-%d'); ?>" />
	<meta name="DC.Author" content="<?php pxRsslinksAuthor(); ?>" />
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxRsslinksTitle('%s'); ?>" />
	<meta name="DC.Identifier" content="<?php pxRsslinksPath('fullurl'); ?>" />
	<?php 
	$keywords = pxSingleCatTitle('%s',true).' '.pxSingleCatPath('%s',false,true). ' ';
	$keywords.= pxResWordIndex($GLOBALS['_PX_render']['rsslinks']->f('resource_id'),'1,10');
	?>
	<meta name="DC.Keywords" content="<?php echo pxRsslinksKeywords('%s',true).' '.$keywords; ?>" />
</head>
<!-- template <?php echo (__FILE__)?> -->
<body class="resource_rsslinks rsslinks">
	<div id="page">
		<?php require dirname(__FILE__).'/inc/banner2.php'; ?>
		<?php require dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">

			<?php //pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
			<div id="mainleft">
				<!-- beginning content -->
				<div id="content">

					<h2 class="rsslinks">
						<?php pxRsslinksTitle('%s'); ?>
					</h2>
					<div class="resource">
						<div class="infos">
							<p>
								<span class="datetime"><?php pxRsslinksDateCreation(__('%Y-%m-%d at %H:%M')); ?>
								</span>
								<?php echo __('by'); ?>
								<span class="author"><a
									href="<?php pxRsslinksAuthorEmail('mailto:%s'); ?>"><?php pxRsslinksAuthor(); ?>
								</a> </span>
							</p>
							<p>
								<span class="cat"><?php pxRsslinksCategories(__(' In %s'), ', ', __(' and ')); ?>
								</span>
							</p>
							<p>
								<span class="link"><a href="<?php pxRsslinksPath('fullurl'); ?>"><?php pxRsslinksPath('fullurl'); ?>
								</a> </span>
							</p>
							<p>
								<span class="comments"><?php echo __('Number of comments:'); ?>
								</span> <a href="#comments"
									title="<?php echo __('Comments'); ?>"><?php pxRsslinksCountComments(); ?>
								</a>
							</p>
						</div>
					</div>

					<div id="rsslinks-content">
						<?php 
						$rssContent = pxRsslinksContent(true);
						echo $rssContent;
						?>
						<?php //pxRsslinksAssociatedLink('<p class="associated-link"><a href="%1$s">%2$s</a></p>'); ?>
					</div>
					<?php //absolute path for comments permalinks
		    $respath = pxRsslinksPath('fullurl', true); ?>
					<h2 id="comments">
						<?php echo __('Comments'); ?>
					</h2>
					<div class="resource">
						<?php include dirname(__FILE__).'/comments_inline.php'; ?>
					</div>
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
		if (strpos($rssContent,'fullcalendar')>0)  {
			pxFullCalendar();
		}
		if (strpos($rssContent,'pdfviewer')>0) {
			pxPdfViewer();
		}
		?>
	</div>  <!-- end page -->
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
	
</body>
</html>
<?php
    $cache->endCache();
endif;
?>