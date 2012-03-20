<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ken
 * Date: 2/4/12
 * Time: 7:10 PM
 * To change this template use File | Settings | File Templates.
 */

$pagesAdded = Main::addPagesToBlog();

if ($pagesAdded > 0) {
    wp_redirect($_SERVER['REQUEST_URI']);
    exit;
}

get_header();
?>
You have 404'd, sadly.
<?php
get_footer();
?>