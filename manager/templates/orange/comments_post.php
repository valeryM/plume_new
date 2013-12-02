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
          <?php include(dirname(__FILE__).'/inc/banner-content.php'); ?>
     </div> <!-- end banner -->

     <div id="content">

           <div class="resource">
           <div class="resource_head"></div>
           <h2 class="commentz"><?php echo __('Comment preview') ?></h2>
           <?php pxCtErrors(); ?>
           <p class="modified"><?php echo __('This is how your comment will look like.') ?></p>
           </div>
           <div class="resource_foot"></div>

           <div class="px-commente-liste">
           <div class="px-commente-head"></div>
           <h3 class="comment-auteur"><?php pxCtAuthor(); ?></h3>
           <?php /* pxCtEmail('%s', 'text');  display email */ ?>
           <h3 class="comment-site"><a href="<?php pxCtWeb(); ?>" title="<?php pxCtWeb(); ?>"><?php pxCtWeb(); ?></a></h3>
           <div class="px-commente-foot"></div>
           </div>

           <blockquote>
           <?php pxCtContent(); ?>
           </blockquote>
           
           <div class="resource">
           <div class="resource_head"></div>
           <h2 class="commentz"><?php echo __('Modify your comment') ?></h2>
           <p class="modified"><?php echo __('A valid email is required but not shared or displayed') ?></p>
           </div>
           <div class="resource_foot"></div>

           <form class="px-commente" action="<?php pxCtAction(); ?>" method="post">
           <div class="news-infos_head"></div>
           <ins><input name="redirect" value="<?php pxCtRedirect(); ?>" type="hidden" /></ins>
           <h3 class="comment-auteur">
           <label for='c_author'><?php echo __('Author:') ?></label>
           <input type="text" id='c_author' name="c_author" value="<?php pxCtAuthor(); ?>" />
           </h3>
           <h3 class="comment-email">
           <label for='c_email'><?php echo __('Email:') ?></label>
           <input type="text" id='c_email' name="c_email" value="<?php pxCtEmail('%s', 'text'); ?>" />
           </h3>
           <h3 class="comment-site">
           <acronym title="optional field"><label for='c_website'><?php echo __('Website:') ?></label></acronym>
           <input type="text" id='c_website' name="c_website" value="<?php pxCtWeb(); ?>" />
           </h3>
           <h3 class="comment-ecrire">
           <label for='c_content'><?php echo __('Comment:') ?></label>
           </h3>
           <p style="text-align:center"><textarea cols="58" rows="7" id='c_content' name="c_content"><?php pxCtContent('%s', 'textarea'); ?></textarea></p>
           
           <p style="text-align:center"><input style="width:50px;height:20px" type="image" src="<?php pxInfo('filesurl'); ?>theme/orange/img/bt-preview.png" name="c_preview" /></p>
           <p style="text-align:center"><input style="width:50px;height:20px" type="image" src="<?php pxInfo('filesurl'); ?>theme/orange/img/bt-submit.png" name="Submit" /></p>
           <div class="news-infos_foot"></div>
           </form>

           <hr class="invisible"/>
      </div>

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>