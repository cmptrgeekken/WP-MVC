<?php
define('ASSETS_RELATIVE_PATH', '/assets/');
define('ASSETS_DIR', get_template_directory_uri() . ASSETS_RELATIVE_PATH);
define('WP_UPLOAD_DIR', WP_CONTENT_DIR . '/upload/');
define('ADDONS_DIR', TEMPLATEPATH . '/addons/');

require_once 'static/BlogConfiguration.php';
require_once 'static/Main.php';

Main::init();