<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ken
 * Date: 2/4/12
 * Time: 6:17 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="MSSmartTagsPreventParsing" content="true" />
    <meta http-equiv="imagetoolbar" content="no" />
    <title>Emily's Portfolio</title>
    <?php if(is_page('index')): ?>
    <style>
        <?php do_action('header_styles'); ?>
    </style>
    <?php endif; ?>
    <?php wp_head(); ?>
</head>
<body>
    <div id="main-wrapper">
        <section id="header-wrapper">
            <header id="site-header">
                <nav>
                    <table id="site-nav">
                        <th id="site-logo">
                            <a href="<?php echo site_url('/'); ?>">
                                Emily&nbsp;E.&nbsp;Hagens<br />Photography
                            </a>
                        </th>
                        <th class="site-link"><a href="<?php echo site_url('gallery/'); ?>">Galleries</a></th>
                        <th class="site-link" style="visibility: hidden"><a href="<?php echo site_url('about/'); ?>">About&nbsp;Me</a></th>
                        <th class="site-link" style="visibility: hidden"><a href="<?php echo site_url('cv/'); ?>">Academic&nbsp;CV</a></th>
                    </table>
                </nav>
                <div class="clear"></div>
            </header>
        </section>

        <section id="page-content">