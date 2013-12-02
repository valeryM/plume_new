<?php pxTemplateInit(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title>Comments - <?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxInfo('description'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Title" content="Comments - <?php pxInfo('name'); ?>" />
</head>

<body>

<div id="page">

<!-- Beginning Banner -->
<?php include(dirname(__FILE__).'/inc/banner.php'); ?>
<!-- End of Banner -->

<div id="main">
<!-- Beginning breadcrumb -->
<ol class="tree"><li><?php echo __('Add a comment'); ?></li></ol>
<!-- End Breadcrumb -->

<!-- Beginning Menu -->
<div id="menu">
  <?php include(dirname(__FILE__).'/inc/menu.php'); ?>
</div>
<!-- End of Menu -->

<!-- Beginning content -->
<div id="content">
        <h2 id="comments"><?php echo __('Add a comment'); ?></h2>
        <div class="resource">
	   <h3 class="comment-preview"><?php echo __('Comment preview'); ?></h3> 
	     <?php pxCtContent('<div class="description">%s</div>'); ?>
	</div>

        <div class="resource">
         <?php pxCtErrors(__('<h3 class="error">Error !</h3>%s<h3 class="comment-add">Modify your comment</h3>')); ?>
        
       <form class="px-comment" action="<?php pxCtAction(); ?>" method="post">
         <fieldset>
          <?php pxCtSpamControl(); ?>
	   <input name="redirect" value="<?php pxCtRedirect(); ?>" type="hidden" />
	   <p><label for='c_author'><?php echo __('Author:'); ?></label> <input type="text" id='c_author' name="c_author" value="<?php pxCtAuthor(); ?>"/></p>
	   <p><label for='c_email'><?php echo __('Email:'); ?></label> <input type="text" id='c_email' name="c_email" value="<?php pxCtEmail('%s', 'text'); ?>" />&nbsp;*</p>
	   <p><label for='c_website'><?php echo __('Website:'); ?></label> <input type="text" id='c_website' name="c_website" value="<?php pxCtWeb(); ?>" /></p>
	   <p><label for='c_content'><?php echo __('Comment'); ?></label><br /><textarea id='c_content' name="c_content" cols="80" rows="7"><?php pxCtContent('%s', 'textarea'); ?></textarea></p>
           <p class="px-comment-required-email">*&nbsp;<?php echo __('required but not shared or displayed'); ?></p>
	   <p class="px-comment-validation"><?php echo __('Your post will be online after validation by the webmaster.'); ?></p>
	   
	   <p class="input-submit">
		<input type="submit" name="c_preview" value="<?php echo __('Preview'); ?>" />
		<input type="submit" name="Submit" value="<?php echo __('Submit'); ?>" />
	   </p>
        </fieldset>
    </form>
</div>
<hr class="invisible" />
</div>
<!-- end content -->

</div>
<!-- end main -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</div>
</body>
</html>