<?php 
if ($cache->processPage(180)):

pxTemplateInit('remove_numbers');
//pxGetLastResources();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php pxInfo('encoding'); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<title><?php pxInfo('name'); ?></title>
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<meta name="description"
		content="<?php pxInfo('name'); ?> - <?php pxMetasDescription(); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body class="category">

	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			<div id="mainfloat">
				<div id="content">
					<div class="gallery" data-gallery-desc="" data-gallery-height="250" data-gallery-title="" 
							data-gallery-uri="/xmedia/slideshow/Concert_jeunes_2010.jpg" 
							data-gallery-url="/xmedia/slideshow" data-gallery-width="90%" 
							data-gallery-options="[{next:'slides-next-invisible', prev:'slides-prev-invisible', pagination: false,generatePagination:false}]"
							>
					</div>
                    <div><hr class="invisible"/></div>					
					<div class="news">
	                    <div class="news-title">L'actualité de l'ASLC</div>
						<div class="news-content">					
								<?php pxGetLastResources(3,'news','/Actualites%/',true); ?>
								<?php while (!$last->EOF() ): ?>
									<h3><a href="<?php pxLastResPath(); ?>"><?php pxLastResTitle(); ?></a></h3>
									<?php pxLastResDescription(); ?>
									<?php pxLastResAssociatedLink(); ?>
									<div class="recent-news-date"><small><?php pxLastResDateModification(__('%Y&bull;%m&bull;%d')); ?></small></div>
									<?php
									$last->moveNext();
								endwhile; ?>							
						</div>
					</div>
					<div><hr class="invisible"/></div>
					<div class="news">
						<div class="info-title">Les dernières infos des sections</div>
						<div class="news-content">
								<?php pxGetLastResources(5,'news','/Section%/',true); ?>
								<?php while (!$last->EOF() ): ?>
									<h3>[<?php pxLastResCategories(); ?>] - <a href="<?php pxLastResPath(); ?>"><?php pxLastResTitle(); ?></a></h3>
									<?php pxLastResDescription(); ?>
									<?php pxLastResAssociatedLink(); ?>
									<div class="recent-news-date"><small><?php pxLastResDateModification(__('%Y&bull;%m&bull;%d')); ?></small></div>
									<?php
									$last->moveNext();
								endwhile; ?>							
						</div>
					</div>
				</div>
				<!-- end content -->

			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<div class="col-content">
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
		<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
	</div>
	<!-- end page -->

</body>
</html>
<?php
    $cache->endCache();
endif;
?>
