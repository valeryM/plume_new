<div id="leave-comment"></div>

<?php if(!($ct->EOF())): ?>
<div class="resource">
<div class="resource_head"></div>

<h2 class="commentz"><?php echo __('Comment(s) for this article') ?></h2>
<p class="modified"><?php echo __('Feel free to express yourself.') ?></p>
</div>
<div class="resource_foot"></div>
<?php endif; ?>

<?php
/**
 * Display the comments of a resource. 
 */
while (!$ct->EOF()):
?>

<div class="px-commente-liste">
<div class="px-commente-head"></div>
<h3 class="comment-auteur"><?php pxCtAuthor(); ?></h3><?php /* pxCtEmail('%s', 'text');  display email */ ?>
<h3 class="comment-site"><a href="<?php pxCtWeb(); ?>" title="<?php pxCtWeb(); ?>"><?php pxCtWeb(); ?></a></h3>
<div class="px-commente-foot"></div>
</div>

<blockquote>
<p><?php pxCtContent('%s', 'text'); ?></p>
</blockquote>

<?php
$ct->moveNext();
endwhile;    
?>

<?php if (pxCtEnabled()): ?>
	<div class="resource">
		<div class="resource_head"></div>
		<h2 class="commentz"><?php echo __('Write your comment') ?></h2>
		<p class="modified"><?php echo __('A valid email is required but not shared or displayed') ?></p>
	</div>
	<div class="resource_foot"></div>

	<form class="px-commente" action="<?php pxCtAction(); ?>" method="post">
		<div class="news-infos_head"></div>
		<ins><input name="redirect" value="<?php pxCtRedirect(); ?>" type="hidden" /></ins>
		<h3 class="comment-auteur">
		<label for='c_author'><?php echo __('Author:') ?></label>
		<input type="text" id='c_author' name="c_author" />
		</h3>
		<h3 class="comment-email">
		<label for='c_email'><?php echo __('Email:') ?></label>
		<input type="text" id='c_email' name="c_email"/>
		</h3>
		<h3 class="comment-site">
		<acronym title="optional field"><label for='c_website'><?php echo __('Website:') ?></label></acronym>
		<input type="text" id='c_website' name="c_website"/>
		</h3>
		<h3 class="comment-ecrire">
		<label for='c_content'><?php echo __('Comment:') ?></label>
		</h3>
		<p style="text-align:center"><textarea cols="54" rows="7" id='c_content' name="c_content"></textarea></p>
		<p style="text-align:center"><input style="width:50px;height:20px" type="image" src="<?php pxInfo('filesurl'); ?>theme/orange/img/bt-preview.png" name="c_preview" /></p>
		<div class="news-infos_foot"></div>
	</form>
<?php else: ?>
	<div class="resource">
		<div class="resource_head"></div>
		<h2 class="commentz"><?php echo __('Comments are closed for this article') ?></h2>
		<p class="modified"><?php echo __('Sorry for the inconvenience') ?></p>
	</div>
	<div class="resource_foot"></div>
<?php endif; ?>