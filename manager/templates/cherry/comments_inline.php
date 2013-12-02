<?php
/** If there are some comments for the ressource, display a title **/
if (!$ct->EOF()): ?>
<h3 class="px-comment-add"><?php echo __('Comments for this resource') ?></h3>
<?php endif; ?>

<?php
/** List the comments and display them **/
while (!$ct->EOF()) {
?>
<div class="px-comment-preview">
<p class="px-comment-info"><?php pxCtAuthor(); /* pxCtEmail('%s', 'text');  display email */ ?> &nbsp; <?php pxCtWeb('<a href="%s" class="px-comment-info-website">'); ?><?php pxCtWeb('%s</a>'); ?></p>
<p>
<?php pxCtContent(); ?>
</p>
</div>
<?php $ct->moveNext(); if (!$ct->EOF()) { ?>
<div class="px-comment">
<p class="px-comment-info"><?php pxCtAuthor(); /* pxCtEmail('%s', 'text');  display email */ ?> &nbsp; <?php pxCtWeb('<a href="%s" class="px-comment-info-website">'); ?><?php pxCtWeb('%s</a>'); ?></p>
<p>
<?php pxCtContent(); ?>
</p>
</div>
<?php
$ct->moveNext(); }
} ?>

<?php
/** If the comments are enabled, display the FORM **/
if (pxCtEnabled()): ?>
<h3 class="px-comment-add"><?php echo __('Add a comment') ?></h3>
<form class="px-comment" action="<?php pxCtAction(); ?>" method="post">
<fieldset>
<input name="redirect" value="<?php pxCtRedirect(); ?>" type="hidden" />
<p><label for='c_author'><?php echo __('Author:') ?></label> <input type="text" id='c_author' name="c_author" /></p>
<p><label for='c_email'><?php echo __('Email:') ?></label> <input type="text" id='c_email' name="c_email" /> <span class="px-comment-required-email"><?php echo __('required but not shared or displayed') ?></span></p>
<p><label for='c_website'><?php echo __('Website:') ?></label> <input type="text" id='c_website' name="c_website" /></p>
<p><label for='c_content'><?php echo __('Comment') ?></label> <textarea cols="80" rows="7" id='c_content' name="c_content"></textarea></p>
<p class="input-submit">
<input type="submit" name="c_preview" value="<?php echo __('Preview') ?>" />
<input type="submit" name="Submit" value="<?php echo __('Submit') ?>" />
</p>
</fieldset>
</form>

<?php
/** If the comments are disabled, display a message **/
else: ?>
<p class="comment-closed"><?php echo __('The comments are closed for this resource.') ?></p>
<?php endif; ?>