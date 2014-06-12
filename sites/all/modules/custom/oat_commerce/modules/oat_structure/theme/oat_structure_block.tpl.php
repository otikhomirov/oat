<?php
/**
 * Created by PhpStorm.
 * User: oleg.tikhomirov
 * Date: 6/11/14
 * Time: 4:34 PM
 */
if(!empty($structure)) :?>
    <ul class="structure-sections">
    <?php foreach($structure as $section) : ?>
        <li class="section">
            <a href="/<?=drupal_lookup_path('alias', 'taxonomy/term/'.$section['tid'])?>"><?=$section['name']?></a>
            <?php if(!empty($section['categories'])) : ?>
                <ul class="categories">
                <?php foreach($section['categories'] as $category) : ?>
                    <li class="category">
                        <a href="/<?=drupal_lookup_path('alias', 'taxonomy/term/'.$category['tid'])?>"><?=$category['name']?></a>
                        <?php if(!empty($category['groups'])) : ?>
                            <ul class="groups">
                            <?php foreach($category['groups'] as $group) : ?>
                                <li class="category">
                                    <a href="/<?=drupal_lookup_path('alias', 'taxonomy/term/'.$group['tid'])?>"><?=$group['name']?></a>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>