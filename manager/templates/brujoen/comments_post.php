<?php pxTemplateInit(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php pxTemplateInit(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<?php pxHeadLinks(); ?>
<meta name="description" content="<?php pxInfo('description'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body class="category">

<div id="page">
     <div id="frame_header"></div>
     <div id="banner">
          <div id="banner_body">
               <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
               <?php include(dirname(__FILE__).'/inc/banner-content.php'); ?>
          </div>
          <div id="banner_right"></div>
     </div>
     
     <div class="contenair">
          <div id="secontenair">
          
          <div id="col-content">
               <div id="menuleft_foot"></div>
          </div> <!-- end menu -->
          
          <div id="desc">
               <div id="desc_head"></div>
               <div id="descontenair">
               <h2 class="category"><?php echo __('Comment preview') ?></h2>
               <p><?php pxCtErrors(); ?></p>
               </div>
          </div>
          <div id="desc_foot"></div>

          <div id="main_frame">
               <div id="content">

               <div class="resource">

               <h2 class="restitle"><?php echo __('Please enter your comment') ?></h2>
               <div class="footer_titre"></div>
               
               <div class="commentaire">
               
               <div id="comment-titre">
                    <div id="comment-titre-left"></div>
                    <div id="comment-titre-body">
                    <h3><?php echo __('Your comment will look like:') ?></h3>
                    </div>
                    <div id="comment-titre-right"></div>
               </div>

               <div id="comment-list-cont">
               <div class="comment-list">
                    <div class="px-comment-info_hd"></div>
                    <p class="px-comment-info"><strong><?php pxCtAuthor(); ?></strong> - <?php /* pxCtEmail('%s', 'text');  display email */ ?><a href="<?php pxCtWeb(); ?>" class="px-comment-info-website"><?php pxCtWeb(); ?></a></p>
                    <div class="px-comment-info_mid"></div>
                    <p class="px-comment-info_txt">
                    <?php pxCtContent(); ?>
                    </p>
                    <div class="px-comment-info_foot"></div>
               </div>
               </div>

               <div class="comment-poster">
                    <div class="comment_head"></div>
                    <form class="comment" action="<?php pxCtAction(); ?>" method="post">
                    <div id="px-comment-add"><h3><?php echo __('Add a comment') ?></h3></div>
                    <input name="redirect" value="<?php pxCtRedirect(); ?>" type="hidden" />
                    <div class="auteur_comment"><h3><label for='c_author'><?php echo __('Author:') ?></label></h3>
                    <input type="text" id='c_author' name="c_author" /></div>
                    <div class="mail_comment"><h3><label for='c_email'><?php echo __('Email: (required but not shared or displayed)') ?></label></h3>
                    <input type="text" id='c_email' name="c_email"/></div>
                    <div class="web_comment"><h3><acronym title="<?php echo __('Optional field') ?>"><label for='c_website'><?php echo __('Website:') ?></label></acronym></h3>
                    <input type="text" id='c_website' name="c_website"/></div>
                    <div class="text_comment"><h3><label for='c_content'><?php echo __('Comment:') ?></label></h3>
                    <textarea id='c_content' name="c_content"></textarea></div>
                    <input type="image" src="<?php pxInfo('filesurl'); ?>theme/brujoen/img/bt-preview.png" name="c_preview" />
                    <input type="image" src="<?php pxInfo('filesurl'); ?>theme/brujoen/img/bt-submit.png" name="Submit" />
                    </form>
                    <div class="comment_foot"></div>
               </div>
               
               </div>
               <hr class="invisible"/>
               </div>
               </div>
          </div> <!-- end main -->
          </div>
     </div>
     <div class="contenair">
     <div id="menu_mask"></div>
     </div>

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>