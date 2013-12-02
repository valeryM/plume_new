<?php
/**
 * Display the comments of a resource. 
 */
?>

<?php
$i = 1;
while (!$ct->EOF()):
?>

   <div class="infos">
   <p id="comment<?php echo $i ?>">[<a href="<?php echo $respath; ?>#comment<?php echo $i ?>"><?php echo $i ?></a>]&nbsp;<span class="author"><?php pxCtAuthor(); ?></span>&nbsp;<?php pxCtWeb('<span class="link"><a href="%s">'); ?><?php pxCtWeb('%s</a></span>'); ?></p>
   </div>
   <div class="comment-content">
       <?php pxCtContent(); ?>
   </div>

<?php
$i = $i+1;
$ct->moveNext();
endwhile;    
?>
<?php if (pxCtEnabled()): ?>

<h3 class="comment-add"><?php echo __('Add a comment'); ?></h3>

<form class="px-comment" action="<?php pxCtAction(); ?>" method="post">
<fieldset>
	<input name="redirect" value="<?php pxCtRedirect(); ?>" type="hidden" />
	<p><label for='c_author'><?php echo __('Author:'); ?></label> <input type="text" id='c_author' name="c_author" /></p>
	<p><label for='c_email'><?php echo __('Email:'); ?></label> <input type="text" id='c_email' name="c_email" />&nbsp;*</p>
	<p><label for='c_website'><?php echo __('Website:'); ?></label> <input type="text" id='c_website' name="c_website" /></p>
	<p><label for='c_content'><?php echo __('Comment'); ?></label> <textarea cols="80" rows="7" id='c_content' name="c_content"></textarea></p>
	<p class="px-comment-required-email">*&nbsp;<?php echo __('required but not shared or displayed'); ?></p>
	<p class="input-submit">
		<input type="submit" name="c_preview" value="<?php echo __('Preview'); ?>" />
	</p>
</fieldset>
</form>
<?php else: ?>

<p><?php __('The comments are closed for this resource.'); ?></p>

<?php endif; ?>