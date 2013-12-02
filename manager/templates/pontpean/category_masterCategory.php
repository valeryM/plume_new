<?php
if ($cache->processPage(180)):
 
pxTemplateInit('order_res_manual|res_per_page:6|remove_numbers|resources_online:true');

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php pxInfo('encoding'); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.Description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.source" content="<?php pxSingleCatPath(); ?>" scheme="URI" />
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?> (<?php echo $GLOBALS['_PX_render']['cat']->f('category_path'); ?>)" />
	<?php 
	$keywords = pxSingleCatTitle('%s',true).' '.pxSingleCatPath('%s',false,true). ' ';
	while (!$res->EOF() ) {
		$keywords .= pxResWordIndex($res->f('resource_id'),'1,10');
		$res->moveNext();
	}
	$res->moveStart();
	?>
	<meta name="DC.Keywords" content="<?php echo $keywords; ?>" />
</head>

<body class="category_masterCategory category">
	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
			<div id="mainleft">
				<div class="category">
					<?php $catName = '';?>
					<?php while (!$res->EOF()) { ?>
						<?php if ($catName != $res->f('category_name')) { ?>
						<div id="content">
							<h2>
								<a href="<?php echo $res->f('category_path'); ?>">
									<?php echo  $res->f('category_name'); ?>
								</a>
							</h2>
							<?php $catName = $res->f('category_name'); ?>
						<?php } // end if ?>	
							<div class="resource">			    
							    <h3>
							    	<a href="<?php pxResPath(); ?>"><?php pxResTitle('%s'); ?></a>
							    </h3>
					    		<p class="modified"><?php echo __('on'); ?> <?php pxResDateModification(__('%Y-%m-%d at %H:%M')); ?></p>
					    		<p><?php pxResDescription('%s',300); ?></p>					    		
								<?php pxResAssociatedLink(); ?>													    		
					    		<?php if (pxResCommentAvailable()) { ?>
						    		<p class="comment-count">
						    			<?php echo __('Number of comments:') ?>
						    			&nbsp;
						    			<?php pxResCountComments() ?>
						    		</p>
					    		<?php } // end if ?>
				    		</div><!-- end resource -->
				    	<?php $res->moveNext(); ?>
						<?php if ( $res->EOF() || $catName != $res->f('category_name')) { ?>
						</div><!--  end content -->
						<?php } // end if?>										    								
					<?php  } //end while; ?>
				
					<div id="content" >
						<?php pxSingleCatNextPage(-1,__('<span class="resource"><a href="%s">Previous Page</a></span>')); ?>
						<?php pxSingleCatNextPage(1,__('<span class="resource"><a href="%s">Next Page</a></span>')); ?>				
					</div>
					<hr class="invisible" />
				</div><!-- end category -->
			</div><!-- end mainfloat -->

			<div id="menuright">
				<div id="infoPratique">
					<?php require('inc/breadCrumbs.php'); ?>				
					<?php require('inc/links.php'); ?>
				</div>
			</div><!-- end menuright -->

		</div><!-- end main -->

	</div><!-- end page -->
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php
$cache->endCache();
endif;
?>