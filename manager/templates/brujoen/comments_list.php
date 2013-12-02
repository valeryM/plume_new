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
               <?php pxSubCategories(__('<h2 class="subcategories">Categorie(s)</h2> %s')); ?>

               <h2 class="categories"><?php echo __('Main categorie(s)') ?></h2>
               <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>
               
               <h2 class="search"><?php echo __('Search') ?></h2>
                    <form id="search" action="<?php pxInfo('url'); ?>search.php" method="get">
                    <input type="text" style="width:180px;margin:0px 0px 5px 0px" name="q" value="" id="q" />
                    <input style="float:right;margin:-25px 0px 0px 0px" type="image" src="<?php pxInfo('filesurl'); ?>theme/brujoen/img/bt-search.png" name="s" id="search-s" />
                    </form>

               <h2 class="links"><?php echo __('Links') ?></h2>
                    <?php pxLink::linkList(); ?>

               <h2 class="extra"><?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?></h2>

               <div id="menuleft_foot"></div>
          </div> <!-- end menu -->
          
          <div id="desc">
               <div id="desc_head"></div>
               <div id="descontenair">
               <h2 class="category"><?php echo __('Welcome') ?></h2>
               <p class="description"><?php pxInfo('description'); ?></p>
               </div>
          </div>
          
          <div id="desc_foot"></div>
          <div id="main_frame">
               <div id="content">
               <div class="resource">
               <h2 class="restitle"><?php echo __('Comment(s):') ?></h2>
               <div class="footer_titre"></div>
               <?php
               while (!$res->cur->comments->EOF()) {
               echo $res->cur->comments->getContent();
               $res->cur->comments->moveNext();
               }?>
               <hr class="invisible"/>
               </div>
          </div> <!-- end main -->
          </div>
          
     </div>
     
     <div class="contenair">
          <div id="menu_mask"></div>
     </div>

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>