	<?php $last = FrontEnd::getOnlineResourcesInCat('/Presentation/', '%Bienvenue%',1,'news'); ?>
	<?php if (!$last->EOF()) { ?>
		<h2><?php pxLastResTitle(); ?></h2>
		<div class="recent-news">
				<?php pxLastResDescription(); ?>
		</div><!-- end recent-news -->
	<?php } ?>
