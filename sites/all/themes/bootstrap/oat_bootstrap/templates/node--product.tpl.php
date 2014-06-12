<?php
    $is_in_stock = !empty($node->field_count['und'][0]['value']);
?>

<!-- Title -->
<div class="product-title">
    <?=$node->title?>
</div>

<!-- Price -->
<?php if(!empty($node->field_price['und'][0]['value'])) : ?>
    <div class="product-price">
        Цена: <?=$node->field_price['und'][0]['value']?>
    </div>
<?php endif; ?>

<!-- In stock -->
<?php if($is_in_stock) : ?>
    <div class="product-in-stock">
        В НАЛИЧИИ!
    </div>
<?php endif; ?>

<!-- Image -->
<?php if(!empty($node->field_product_image['und'][0]['uri'])) : ?>
    <div class="product-image">
        <img src="<?=file_create_url($node->field_product_image['und'][0]['uri'])?>" alt="Product image">
    </div>
<?php endif; ?>

<!-- Description -->
<?php if(!empty($node->body['und'][0]['safe_value'])) : ?>
    <div class="product-desc">
        <?=$node->body['und'][0]['safe_value']?>
    </div>
<?php endif; ?>

<!-- Image gallery -->
<?php if(!empty($node->field_images)) : ?>
    <div class="product-images">
        <?php foreach($node->field_images['und'] as $image) : ?>
            <?php
                $image_preview_url = image_style_url('medium', $image['uri']);
                $image_url = file_create_url($image['uri']);
            ?>
            <img src="<?= $image_preview_url ?>" data-url="<?= $image_url ?>" alt="Image preview" class="product-image-preview">
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Add to cart -->
<?php if($is_in_stock) : ?>
    <div class="add-to-cart">
        <div class="product-quantity">
            <input type="text" value="1" class="product-quantity" name="product-quantity">
        </div>
        <div class="product-add-to-cart">
            <a href="" data-nid="<?=$node->nid?>">Добавить в корзину</a>
        </div>
    </div>
<?php endif; ?>

