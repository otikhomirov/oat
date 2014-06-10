<?php if(!empty($form_type)) : ?>
    <!-- Cart form -->
    <?php if (!empty($messages)) : ?>
        <?php print $messages; ?>
    <?php endif; ?>
    <?php
        switch($form_type) {
            case 1:
                echo $oat_commerce_cart_form;
                break;
            case 2:
                echo $oat_commerce_address_form;
                break;
            case 3:
                echo $oat_commerce_select_address_form;
                break;
        }
    ?>

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