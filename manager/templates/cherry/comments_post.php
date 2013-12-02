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

<body class="category">

<div id="page">
     <div id="banner">
     <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
     </div><!-- end banner -->

     <div id="mainfloat">
     <div id="menuleft">
          <div class="col-content-green">
		      <div class="menuleft-green-top"></div>
          <h2><?php echo __('Comment preview') ?></h2>
		      <div class="menuleft-green-bottom"></div>
		      </div>
     </div><!-- end menuleft -->

     <div id="content">
          <div class="px-comment-preview">
          <p class="px-comment-info"><?php pxCtAuthor(); /* pxCtEmail('%s', 'text');  display email */ ?> &nbsp; <?php pxCtWeb('<a href="%s" class="px-comment-info-website">'); ?><?php pxCtWeb('%s</a>'); ?></p>
	        <p><?php pxCtContent(); ?></p>
	        </div>
         <?php pxCtErrors(__('<h3 class="px-comment-add">There are some errors with your comment !</h3><div class="px-comment-error">%s</div>')); ?>
         <h3 class="px-comment-add"><?php echo __('Validate your comment') ?></h3>
         <form class="px-comment" action="<?php pxCtAction(); ?>" method="post">
         <fieldset>
	       <input name="redirect" value="<?php pxCtRedirect(); ?>" type="hidden" />
	       <p><label for='c_author'><?php echo __('Author:') ?></label> <input type="text" id='c_author' name="c_author" value="<?php pxCtAuthor(); ?>"/></p>
	       <p><label for='c_email'><?php echo __('Email:') ?></label> <input type="text" id='c_email' name="c_email" value="<?php pxCtEmail('%s', 'text'); ?>" /> <span class="px-comment-required-email"><?php echo __('required but not shared or displayed') ?></span></p>
	       <p><label for='c_website'><?php echo __('Website:') ?></label> <input type="text" id='c_website' name="c_website" value="<?php pxCtWeb(); ?>" /></p>
	       <p><label for='c_content'><?php echo __('Comment') ?></label><br /><textarea id='c_content' name="c_content" cols="80" rows="7"><?php pxCtContent('%s', 'textarea'); ?></textarea></p>
	       <p class="px-comment-validation"><?php echo __('Your post will be online after validation by the webmaster.') ?></p>
	       <p class="input-submit">
		     <input type="submit" name="c_preview" value="<?php echo __('Preview') ?>" />
		     <input type="submit" name="Submit" value="<?php echo __('Submit') ?>" />
         </p>
         </fieldset>
         </form>

         <hr class="invisible" />
     </div><!-- end content -->
     </div><!-- end mainfloat -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>