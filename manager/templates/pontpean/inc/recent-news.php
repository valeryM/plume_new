		<h2><?php echo __('Recent news'); ?></h2>
		<div class="recent-news">
			<?php pxGetLastResources(3,'news'); ?>
			<?php while (!$last->EOF() ): ?>
				<h3><a href="<?php pxLastResPath(); ?>"><?php pxLastResTitle(); ?></a></h3>
				<?php pxLastResDescription(); ?>
				<?php pxLastResAssociatedLink(); ?>
				<div class="recent-news-date"><small><?php pxLastResDateModification(__('%Y&bull;%m&bull;%d')); ?></small></div>
				<?php
				$last->moveNext();
			endwhile; ?>
		</div><!-- end recent-news -->