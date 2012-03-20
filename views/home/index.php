<?php
    /**
     * @var $viewModel ImageModel
     */

    if (!is_null($viewModel)) {
        $bgImage = $viewModel[rand(0,sizeof($viewModel)-1)]->xxLargeUrl;
    } else {
        $bgImage = ASSETS_DIR . 'images/sample-image.jpg';
    }

    $indexHeaderStyles = function () use ($bgImage)
    {

?>
    body {
        background: url(<?php esc_attr_e($bgImage); ?>) no-repeat center top;
    }
<?php
    };

    add_action('header_styles', $indexHeaderStyles);