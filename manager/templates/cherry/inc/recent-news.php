<h2><?php echo __('Recent news') ?></h2>
<ul>
<?php pxGetLastResources(10,'news'); ?>
<?php while (!$last->EOF() ): ?>
<li><a href="<?php pxLastResPath(); ?>" title="<?php pxLastResTitle(); ?>"><?php pxLastResTitle(); ?></a></li>
<?php
$last->moveNext();
endwhile; ?>
</ul>