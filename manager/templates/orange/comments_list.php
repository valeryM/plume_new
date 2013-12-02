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

     <div id="col-content">
     <div id="col-content-head"></div>
          <div class="menuright">
          <div class="menuright_head"></div>
             <div class="col-content">
             <?php pxSubCategories(__('<h2 class="subcategories">Categorie(s)</h2> %s')); ?>
             <h2 class="categories"><?php echo __('Main Categorie(s)') ?></h2>
             <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>
             </div>
          <div class="menuright_foot"></div>
          </div>

          <div class="menuright">
          <div class="menuright_head"></div>
               <div class="col-content">
               <?php include(dirname(__FILE__).'/inc/search-form.php'); ?>
               </div>
          <div class="menuright_foot"></div>
          </div>

          <div class="menuright">
          <div class="menuright_head"></div>
               <div class="col-content">
               <h2 class="links"><?php echo __('Links') ?></h2>
               <?php pxLink::linkList(); ?>
               </div>
          <div class="menuright_foot"></div>
          </div>

          <?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?>
     <div id="col-content-foot"></div>
     </div> <!-- end menu -->

     <div id="content">
           <ol class="tree"><li><?php echo __('Comments') ?></li></ol>
           <?php
           while (!$res->cur->comments->EOF()) {
           echo $res->cur->comments->getContent();
           $res->cur->comments->moveNext();
           }
           ?>
	   <hr class="invisible"/>
    </div>

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>