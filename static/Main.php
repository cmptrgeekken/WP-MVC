<?php

class Main
{
    /**
     * Stores an instance of this class, to ensure it is only
     * initialized once.
     *
     * @var Main
     */
    private static $_instance;

    /**
     * Initialize the site
     *
     * @return void
     * @static
     */
    public static function init()
    {
        // Only initialize if we haven't already
        if (isset(self::$_instance)) {
            return;
        }

        $instance = new Main();

        // Register tha autoload method
        spl_autoload_register(array(&$instance, '__autoload'));

        add_action('init', array(&$instance, 'installAddons'), 2);


        add_action(
            'wp_print_scripts',
            array(&$instance, 'enqueueScripts')
        );
        add_action('wp_print_styles', array(&$instance, 'enqueueStyles'));
        add_action(
            'wp_head', array(&$instance, 'printConditionalStylesheets'), 99
        );

        add_action(
            'admin_init', array(&$instance, 'registerAjaxControllerMethods')
        );

        add_filter(
            'get_page_content',
            array(&$instance, 'loadPageContent'),
            100
        );

        // TODO: Remove some time?
        add_action(
            'admin_init',
            array(&$instance, 'addPagesToBlog')
        );

        self::$_instance = $instance;
    }

    /**
     * The constructor is private so that only the static init() method
     * can construct a new instance.
     */
    private function __construct()
    {

    }

    /**
     * Load all classes within the addons/ subdirectory
     *
     * @return void
     */
    public function installAddons()
    {
        if (file_exists(ADDONS_DIR)) {
            if ($handle = opendir(ADDONS_DIR)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file != '.' && $file != '..' && !is_dir(
                        ADDONS_DIR . $file
                    )
                    ) {
                        require_once ADDONS_DIR . $file;
                        $className = basename($file, '.php');
                        new $className();
                    }
                }
                closedir($handle);
            }
        }
    }

    /**
     * Queue global scripts
     *
     * @return void
     */
    public function enqueueScripts()
    {
        if (!is_admin()) {
            // Register / Enqueue scripts here

            wp_enqueue_script(
                'jquery-easing',
                ASSETS_DIR . 'scripts/jquery.easing.min.js',
                array('jquery')
            );

            wp_enqueue_script(
                'global',
                ASSETS_DIR . 'scripts/global.js',
                array('jquery')
            );


            wp_enqueue_script(
                'jquery-isotope',
                ASSETS_DIR . 'scripts/lib/jquery.isotope.min.js',
                array('jquery')
            );

            if (is_page('gallery')) {
                wp_enqueue_script(
                    'gallery',
                    ASSETS_DIR . 'scripts/gallery.js',
                    array('jquery')
                );

                wp_enqueue_script(
                    'jquery-bbq',
                    ASSETS_DIR . 'scripts/lib/jquery.bba-bbq.min.js',
                    array('jquery')
                );
            } else if (is_page('gallery-view')) {
                wp_enqueue_script(
                    'jquery-imagefit',
                    ASSETS_DIR . 'scripts/lib/jquery.imagefit-0.2.js',
                    array('jquery')
                );

                wp_enqueue_script(
                    'jquery-lionbars',
                    ASSETS_DIR . 'scripts/lib/jquery.lionbars.0.3.min.js',
                    array('jquery')
                );

                wp_enqueue_script(
                    'gallery-view',
                    ASSETS_DIR . 'scripts/galleryView.js',
                    array('jquery-imagefit')
                );
            }
        }
    }

    /**
     * Queue global styles
     *
     * @return void
     */
    public function enqueueStyles()
    {
        if (!is_admin()) {
            wp_enqueue_style(
                'main',
                get_template_directory_uri() . '/style.css'
            );

            wp_enqueue_style(
                'google-fonts',
                'http://fonts.googleapis.com/css?family=Reenie+Beanie|Fondamento'
            );

            wp_enqueue_style(
                'isotope',
                ASSETS_DIR . 'styles/isotope.css'
            );

            if (is_page('gallery-view')) {
                wp_enqueue_style(
                    'jquery-lionbars',
                    ASSETS_DIR . 'styles/lionbars.css'
                );
            }
        }
    }


    /**
     * Queue IE conditional stylesheets
     *
     * @return void
     */
    public function printConditionalStylesheets()
    {
        global $wp_styles;

        // Register / Print IE styles here
        /*
         * Example:
         * wp_register_style("style-ie7", ASSETS_DIR . "css/ie7.css");
         * $wp_styles->add_data("style-ie7", 'conditional', "IE 7");
         *
         * wp_print_styles("style-ie7");
         */
    }


    /**
     * Register the AJAX controller to handle AJAX requests
     * through admin-ajax.php
     * NOTE: Methods marked 'private' will only be accesible to logged-in users
     *
     * @return void
     */
    public function registerAjaxControllerMethods()
    {
        if (defined(DOING_AJAX) && DOING_AJAX === true) {
            $ajaxController         = new ReflectionClass("AjaxController");
            $ajaxControllerInstance = $ajaxController->newInstance();


            foreach (
                $ajaxController->getMethods()
                as $actionMethod
            ) {
                /** @var $actionMethod ReflectionMethod  */
                $methodName = $actionMethod->getName();

                if (stristr($methodName, 'action') !== false) {
                    $action = substr(
                        $methodName, 0, strlen($methodName) - strlen('action')
                    );

                    $ajaxCallback =
                        function() use ($actionMethod, $ajaxControllerInstance)
                        {
                            $actionMethod->invokeArgs(
                                $ajaxControllerInstance,
                                Main::getActionArguments($actionMethod)
                            );
                        };

                    if (!$actionMethod->isPrivate()) {
                        add_action(
                            'wp_ajax_nopriv_' . $action,
                            $ajaxCallback,
                            10,
                            0
                        );
                    }

                    add_action('wp_ajax_' . $action, $ajaxCallback, 10, 0);
                }
            }
        }
    }


    /**
     * Load the current page from the Home controller
     *
     * @param string $content Content of the page
     * @return mixed
     */
    public function loadPageContent($content)
    {
        $post       = get_queried_object();
        if (isset($post)) {
            $actionName = $post->post_name;
        }


        if (empty($actionName)) {
            return $content;
        } // TODO: 404 here.

        $actionName = preg_replace_callback(
            '/-([a-z])/',
            function($match) {
                return strtoupper($match[1]);
            },
            $actionName);

        try {
            $actionMethod = @new ReflectionMethod(
                'HomeController', "{$actionName}Action"
            );
        } catch (Exception $e) {
        }

        /*
         * Fall back to dumping the WordPress page content onto the page
         */
        if (empty($actionMethod)) {
            return $content . the_content();
        }

        return $content
            . $actionMethod->invokeArgs(
                new HomeController(),
                Main::getActionArguments($actionMethod)
            );
    }


    /**
     * Determines the arguments for the specified method based on
     * query-string parameters, and constructs an array to be passed into
     * ReflectionMethod::invokeArgs() to invoke the method with
     * appropriate arguments.
     *
     * @param ReflectionMethod $actionMethod Method from which to get arguments
     * @return array
     */
    public static function getActionArguments($actionMethod)
    {
        // Map to lowercased entries to ensure case-insensitive mapping
        $request = array();
        foreach($_REQUEST as $name => $value) {
            $request[strtolower(str_replace('-', '', $name))] = $value;
        }

        $params = array();
        foreach ($actionMethod->getParameters() as $param) {
            $name = strtolower($param->getName());

            if (isset($request[$name])) {
                // TODO: Any problems with trimming the parameter here?
                $value = stripslashes_deep($request[$name]);
                if (is_string($value)) {
                    $value = trim($value);
                }
                array_push($params, $value);
            } else {
                if ($param->isDefaultValueAvailable()) {
                    array_push($params, $param->getDefaultValue());
                } else {
                    if ($param->isOptional()) {
                        array_push($params, null);
                    } else {
                        // TODO: Handle case when required argument is not provided.
                    }
                }
            }
        }
        return $params;
    }


    /**
     * Extracts the action names from the HomeController to determines which
     * pages need to be present in WordPress
     *
     * @return array
     */
    public static function getBlogPages()
    {
        $homeController = new ReflectionClass('HomeController');
        $actionMethods = array_filter(
            $homeController->getMethods(ReflectionMethod::IS_PUBLIC),
            function($method) {
                return substr_compare(
                    $method->getName(),
                    'Action',
                    -strlen('Action'),
                    strlen('Action')
                ) === 0;
            }
        );

        $pages = array();
        foreach($actionMethods as $method) {
            $methodName = preg_replace('/Action$/','', $method->getName());
            $pageTitle = ucfirst(
                preg_replace('/([a-z])([A-Z])/', '$1 $2', $methodName)
            );

            $pages[sanitize_title($pageTitle)] = $pageTitle;
        }

        return $pages;
    }

    /**
     * Add required pages to a blog.
     *
     * This pulls the pages from the main site (blog ID = 1) and duplicates
     * those pages on the specified blog.
     *
     * @return int Number of page added
     */
    public static function addPagesToBlog()
    {
        global $wpdb;

        $numPagesAdded = 0;
        $blogId = get_current_blog_id();

        if (!isset($blogId)) {
            return false;
        }

        $pagesToAdd = self::getBlogPages();

        $sitePages = $wpdb->get_col(
            "SELECT post_name FROM $wpdb->posts WHERE post_type = 'page'"
        );

        foreach($sitePages as $page) {
            if (isset($pagesToAdd[$page])) {
                $wpdb->update(
                    $wpdb->posts,
                    array('post_title' => $pagesToAdd[$page]),
                    array($pagesToAdd[$page])
                );

                unset($pagesToAdd[$page]);
            }
        }

        foreach($pagesToAdd as $title) {
            $newPageId = wp_insert_post(
                array(
                    'post_title'   => $title,
                    'post_type'    => 'page',
                    'post_content' => '',
                    'post_status'  => 'publish',
                    'post_author'  => 1
                )
            );

            if (!is_wp_error($newPageId)) {
                $numPagesAdded++;
            }
        }

        return $numPagesAdded;
    }

    /**
     * Method used to include classes within the ReplayIt theme
     *
     * @param string $className Name of the class to attempt to load
     * @return void
     */
    public function __autoload($className)
    {
        $path = null;
        if (preg_match("/ViewModel$/", $className)) {
            $path = "viewModels/$className.php";
        } else {
            if (preg_match("/Model$/", $className)) {
                $path = "models/$className.php";
            } else {
                if (preg_match("/Controller$/", $className)) {
                    $path = "controllers/$className.php";
                } else {
                    if (preg_match("/Service$/", $className)) {
                        $path = "services/$className.php";
                    }
                }
            }
        }

        if (isset($path)) {
            require_once TEMPLATEPATH . "/$path";
        }
    }
};
