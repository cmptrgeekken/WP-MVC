<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ken
 * Date: 2/4/12
 * Time: 7:23 PM
 * To change this template use File | Settings | File Templates.
 *
 * @var $viewModel array
 */
?>
<div class="galleries-wrapper">
    <ul class="gallery-categories">
        <li class="gallery-category-all"><a href="#">All</a></li>
    <?php foreach($viewModel as $category => $subcategories):
        $catId = sanitize_title($category);
        ksort($subcategories); ?>
        <li class="gallery-category">
            <a id="category-<?php esc_attr_e($catId); ?>" href="#<?php esc_attr_e($catId); ?>"><?
                esc_html_e($category);
            ?></a>
            <ul class="gallery-subcategories">
                <li class="gallery-subcategory-all"><a href="#<?php esc_attr_e($catId); ?>">All</a></li>
                <?php foreach($subcategories as $subcategory => $albums):
                    if ($subcategory == '_Global'){$subcategory = 'Misc';}
                    $subCatId = $catId . '_' . sanitize_title($subcategory); ?>
                <li class="gallery-subcategory">
                    <a id="subcategory-<?php esc_attr_e($subCatId); ?>" <?
                       ?>href="#<?php esc_attr_e($subCatId); ?>"><?
                       esc_html_e($subcategory);
                    ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach; ?>
    </ul>

    <div class="clear"></div>

    <div class="gallery-thumbs">
        <?php foreach($viewModel as $category => $subcategories): ?>
            <?php foreach($subcategories as $subcategory => $albums):
                if ($subcategory == '_Global'){$subcategory = 'Misc';} ?>
                <?php foreach($albums as $album): ?>
        <div class="gallery-thumb category-<?php esc_attr_e(sanitize_title($category)); ?> subcategory-<?php esc_attr_e(sanitize_title($subcategory)); ?>" <?
            ?>style="background: url('<?php echo $album->images[0]->mediumUrl; ?>') no-repeat top left;">
            <a href="<?php echo site_url('gallery-view'); ?>?albumId=<?php echo $album->id; ?>&albumKey=<?php echo $album->key; ?>">
                <span class="gallery-title"><?php esc_html_e($album->title); ?></span>
            </a>
        </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <?php /*ksort($subcategories); ?>
        <div class="gallery-category-wrapper">
            <div class="gallery-category"><?php echo $category; ?></div>
            <div class="gallery-subcategory-wrapper">
                <?php foreach($subcategories as $subcategory => $albums): ?>
                    <?php if ($subcategory != '_Global'): ?>
                    <div class="gallery-subcategory"><?php echo $subcategory; ?></div>
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>
            <div class="clear"></div>
        </div>
    <?php endforeach;*/ ?>
</div>