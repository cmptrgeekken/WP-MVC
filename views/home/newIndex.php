<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ken
 * Date: 3/4/12
 * Time: 8:55 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="slideshow">
    <?php foreach($viewModel as $image): ?>
    <?php include(get_template_directory() . '/views/shared/imageView.php'); ?>
    <?php endforeach; ?>
</div>