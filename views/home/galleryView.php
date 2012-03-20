<?php
/**
 * @var $viewModel AlbumModel
 */
?>
<div id="album-thumbs">
    <?php foreach($viewModel->images as $image): ?>
    <?php include(get_template_directory() . '/views/shared/imageView.php'); ?>
    <?php endforeach; ?>
</div>

<div id="album-image">&nbsp;</div>