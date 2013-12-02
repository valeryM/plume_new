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
<meta name="DC.Title" content="<?php echo __('Comments'); ?> - <?php pxInfo('name'); ?>" />
</head>

<body>

<div id="page">

<!-- Beginning of title -->
<?php include(dirname(__FILE__).'/inc/banner.php'); ?>
<!-- End of title -->

<div id="main">
<!-- Beginning breadcrumb -->
<ol class="tree"><li><?php echo __('Comment'); ?></li></ol>
<!-- End Breadcrumb -->

<!-- Beginning Menu -->
<div id="menu">
 <?php include(dirname(__FILE__).'/inc/menu.php'); ?>
</div>
<!-- End of Menu -->

<!-- beginning content -->
<div id="content">

    <h2 id="comments"><?php echo __('Comment'); ?></h2>
    
    <?php
    while (!$res->cur->comments->EOF()) {
        echo $res->cur->comments->getContent();
        echo $res->cur->comments->countContent();
        $res->cur->comments->moveNext();
    }
    ?>
    
<hr class="invisible" />
</div>
<!-- end content -->

</div>
<!-- end main -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</div>
</body>
</html>
