<div id="menuleft">
	<div class="col-content">
		<h2><?php echo __('Back to'); ?></h2>
		<ul><li><a href="<?php pxParentCatPath(); ?>"><?php pxParentCatTitle('%s'); ?></a></li></ul>

		<?php pxSubCategories(__('<h2>Categories</h2> %s')); ?>

		<h2><?php echo __('Links'); ?></h2>
		<?php pxLink::linkList(); ?>

	</div><!-- end col-content -->
</div><!-- end menuleft -->
	
<!-- Dans homepage -->
<div id="menuleft">
	<div class="col-content">
		<h2>
			<?php echo __('Categories'); ?>
		</h2>

		<?php //pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>

		<h2><?php echo __('Links'); ?></h2>
		<?php pxLink::linkList(); ?>

		<h2><?php echo __('In short'); ?></h2>
		<ul>
			<?php while (!$res->EOF()): ?>
				<li><a href="<?php pxResPath(); ?>"
					title="<?php pxResTitle(); ?>"><?php pxResTitle(); ?>
					</a>
				</li>
			<?php
			$res->moveNext();
			endwhile; 
			?>
		</ul>
	</div>
	<!-- col-content -->
</div>
<!-- end menuleft -->