<?php if(!empty($oat_commerce_cart_form)) : ?>
    <!-- Cart form -->
    <?php if (!empty($messages)) : ?>
        <?php print $messages; ?>
    <?php endif; ?>
    <?= $oat_commerce_cart_form;?>

<?php else : ?>

    <div id="node-<?php echo $node->nid; ?>" class="<?php echo $classes; ?> clearfix"<?php echo $attributes; ?>>
      <?php if (!$page): ?>
        <h2<?php echo $title_attributes; ?>><?php echo $title; ?></h2>
      <?php endif; ?>
      <div class="content clearfix"<?php echo $content_attributes; ?>>
         <?php echo render($content); ?>
      </div>
    </div>
<?php endif; ?>