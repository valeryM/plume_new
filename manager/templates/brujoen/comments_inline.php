<?php
/*** Display the comments of a resource. ***/
?>
<?php if (pxCtEnabled()): ?>
      <?php if($ct->EOF()): ?>
      <div id="comment-titre">
           <div id="comment-titre-left"></div>
           <div id="comment-titre-body">
           <h3><?php echo __('There is no comment') ?></h3>
           </div>
           <div id="comment-titre-right"></div>
      </div>
      <?php else : ?>
      <div id="comment-titre">
           <div id="comment-titre-left"></div>
           <div id="comment-titre-body">
           <h3><?php echo __('Comment(s):') ?></h3>
           </div>
           <div id="comment-titre-right"></div>
      </div>
      <?php endif; ?>
      <div id="comment-list-cont">
           <?php
           while (!$ct->EOF()):
           ?>
           <div class="comment-list">
           <div class="px-comment-info_hd"></div>
           <p class="px-comment-info"><strong><?php pxCtAuthor(); ?></strong> <?php /* pxCtEmail('%s', 'text');  display email */ ?> - <a href="<?php pxCtWeb(); ?>" class="px-comment-info-website"><?php pxCtWeb(); ?></a></p>
           <div class="px-comment-info_mid"></div>
           <p class="px-comment-info_txt">
           <?php pxCtContent(); ?>
           </p>
           <div class="px-comment-info_foot"></div>
           </div>
           <?php
           $ct->moveNext();
           endwhile;
           ?>
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
<?php else: ?>
      <div id="comment-titre">
      <div id="comment-titre-left"></div>
      <div id="comment-titre-body">
      <h3><?php echo __('The comments are closed.') ?></h3>
      </div>
      <div id="comment-titre-right"></div>
      </div>
<?php endif; ?>