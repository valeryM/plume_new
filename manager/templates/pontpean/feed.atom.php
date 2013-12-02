<?php
 
if ($cache->processPage(3600)):
    pxTemplateInit('remove_numbers|context:external|resources_online:true');

$last = pxGetLastResources(30, '', '/%' /*$category_id*/,true, true);

echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xml:base='<?php pxInfo('fullurl'); ?>rssinfo.php'
      xml:lang='<?php pxInfo('lang'); ?>'>

  <title type="text"><?php pxInfo('name'); ?></title>
  <id><?php pxInfo('fullurl'); ?></id>
  <link href="<?php pxInfo('fullurl'); ?>" />
  <link rel="self" href="" />
  <updated><?php pxGetLastModification(__('%Y-%m-%dT%H:%M:%SZ')); ?></updated>  
  <link rel="alternate" type="text/html" href="<?php pxInfo('fullurl'); ?>" />
  <generator uri="http://plume-cms.net/">Plume CMS</generator>
  
<?php while (!$last->EOF()): ?>
	  <entry>
	  	<category term="<?php pxLastResTypeName(); ?>" label="<?php pxLastResTypeName(); ?>" />
	    <title type="text"><?php pxLastResTitle('%s'); ?></title>
	    <link rel="alternate" type="text/html" href="<?php pxLastResPath(); ?>" />
	    <id><?php pxLastResPath(); ?></id>
	    <updated><?php pxLastResDateModification(__('%Y-%m-%dT%H:%M:%SZ')); ?></updated>
	    <author><name><?php pxLastResAuthor(); ?></name></author>
	    <content type="html">
	    <?php // 	<div xmlns="http://www.w3.org/1999/html">  ?>
	    <?php pxLastResDescriptionHtml(); ?>
		<?php	// </div>	?>
	    </content>
	  </entry>

	<?php $last->moveNext(); 
	endwhile; ?>
</feed>
<?php
    $cache->endCache();
endif;
?>