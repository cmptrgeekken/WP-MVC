<?php
    /**
     * @var $image ImageModel
     */
?>
<div
    class="album-thumb"
    style="background: url('<?php esc_attr_e($image->thumbUrl); ?>') no-repeat top left;"
    data-smallurl="<?php esc_attr_e($image->smallUrl); ?>"
    data-mediumurl="<?php esc_attr_e($image->mediumUrl); ?>"
    data-largeurl="<?php esc_attr_e($image->largeUrl); ?>"
    data-xlurl="<?php esc_attr_e($image->xLargeUrl); ?>"
    data-xxlurl="<?php esc_attr_e($image->xxLargeUrl); ?>"
    data-xxxlurl="<?php esc_attr_e($image->xxxLargeUrl); ?>"
    data-width="<?php esc_attr_e($image->width); ?>"
    data-height="<?php esc_attr_e($image->height); ?>">
        <span class="caption"><?php esc_html_e($image->caption); ?></span>
</div>