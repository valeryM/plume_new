<?php
if ($cache->processPage(180)):
   
pxTemplateInit('order_res_manual|res_per_page:10|remove_numbers');
$thisCatPath = $GLOBALS['_PX_render']['cat']->f('category_path');
$thisCatName = pxSingleCatTitle('%s', true);
//pxGetLastResources();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php pxInfo('encoding'); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<title><?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?></title>
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?>" />
</head>

<body class="category">

	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>

			<div id="mainfloat">
				<div id="content">
					<?php if (file_exists(config::f('xmedia_root').$thisCatPath.'/slideshow')) {?>
					<div class="gallery" data-gallery-desc="" data-gallery-height="250" data-gallery-title="" 
							data-gallery-uri="/xmedia<?php echo $thisCatPath;?>slideshow/" 
							data-gallery-url="/xmedia<?php echo $thisCatPath;?>slideshow" data-gallery-width="90%" 
							data-gallery-options="[{next:'slides-next-invisible', prev:'slides-prev-invisible', pagination: false,generatePagination:false}]"
							>
					</div>
					<?php } else {
							// Afficher autre chose ?
						}
					?>
					<div class="news">
	                    <div class="news-title">L'actualité de la section <?php pxSingleCatTitle('%s'); ?></div>
						<div class="news-content">		
									
								<?php pxGetLastResources(5,'news,events',$thisCatPath.'%',true); ?>
								<?php while (!$last->EOF() ): ?>
									<h3><a href="<?php pxLastResPath(); ?>"><?php pxLastResTitle(); ?></a></h3>
									<?php pxLastResDescription('%s',300); ?>
									<?php pxLastResAssociatedLink(); ?>
									<div class="recent-news-date"><small><?php pxLastResDateModification(__('%Y&bull;%m&bull;%d')); ?></small></div>
									<?php
									$last->moveNext();
								endwhile; ?>							
						</div>
					</div>
					<div><hr class="invisible"/></div>
					<div class="news">
						<div class="info-title">Les autres infos de la section <?php pxSingleCatTitle('%s'); ?></div>
						<div class="news-content">
								<?php pxGetLastResources(5,'articles',$thisCatPath.'%',true); ?>
								<?php while (!$last->EOF() ): ?>
									<?php $catRes = pxLastResCategory('%s',true); ?>
									<h3><?php 
										if ($catRes != $thisCatName) {
											echo "[".$catRes."] - ";
										}
										?>
										<a href="<?php pxLastResPath(); ?>"><?php pxLastResTitle(); ?></a>
									</h3>
									<?php pxLastResDescription('%s',300); ?>
									<?php pxLastResAssociatedLink(); ?>
									<div class="recent-news-date"><small><?php pxLastResDateModification(__('%Y&bull;%m&bull;%d')); ?></small></div>
									<?php
									$last->moveNext();
								endwhile; ?>							
						</div>
					</div>					

					<hr class="invisible"/>
				</div><!-- end content -->
	
			</div><!-- end mainfloat -->

			<div id="menuright">
				<div class="col-content">
					<?php pxSingleCatTitle('<h2 class="category">%s</h2>'); ?>
					<div class="pxSingleCatDescription">
						<?php pxSingleCatDescription(); ?>
					</div>
					<?php //include(dirname(__FILE__).'/inc/welcome.php'); ?>
					<?php include(dirname(__FILE__).'/inc/calendar-events.php'); ?>
					<h2>
						<?php echo __('Links'); ?>
					</h2>
					<?php pxLink::linkList(); ?>
				</div><!-- col-content -->
			</div><!-- end menuright -->

		</div><!-- end main -->

		<?php include(dirname(__FILE__).'/inc/footer.php'); ?>

	</div><!-- end page -->

</body>
</html>
<?php
    $cache->endCache();
endif;
?>
