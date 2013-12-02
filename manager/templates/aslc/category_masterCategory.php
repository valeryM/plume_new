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
	<title><?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?></title>
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<meta name="description"
		content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="DC.Title"
		content="<?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?>" />
</head>

<body class="category">
	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
			<div id="mainfloat">
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