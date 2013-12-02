<h2 class="nouvelles"><?php echo __('Recent news') ?></h2>

<div class="recent-news">
<?php pxGetLastResources(5,'news'); ?>
<?php while (!$last->EOF() ): ?>
      <div class="recent-news-boite">
      <h3><a href="<?php pxLastResPath(); ?>" title="<?php echo __('Read the whole story:') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResTitle(); ?></a></h3>
      <span class="recent-news-date"><?php pxLastResDateModification(__('%Y&bull;%m&bull;%d')); ?></span>
      <?php pxLastResDescription2('<p>%s<br />', 60); ?>
      <span><a href="<?php pxLastResPath(); ?>" title="<?php echo __('Read the whole story:') ?> <?php pxLastResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a></span></p>
      <span class="associated-link"><?php echo __('Associated Link:') ?>&nbsp;<?php pxLastResAssociatedLink(); ?></span>
      </div>
<?php
$last->moveNext();
endwhile; ?>
</div><!-- end recent-news -->