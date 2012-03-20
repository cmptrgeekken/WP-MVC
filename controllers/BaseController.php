<?php

abstract class BaseController
{
    /**
     * Physical path to views, based on controller and action
     *
     * @var array
     * TODO: Determine how to persist path cache across page views
     */
    protected static $_pathCache = array();

    /**
     * Returns true if the page is being accessed via a form submittal
     *
     * @return bool
     */
    protected function _isPost(){
        return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
    }


    /**
     * Returns string with the contents of the rendered view
     *
     * @param object $model      The model to pass to the view
     * @param string $controller Name of the controller that contains the
     *                           action that renders this view
     * @param string $action     Name of the action that renders this view
     * @return string Rendered contents of the view
     *
     * @todo Determine how inefficient walking up the stack is
     */
    protected function _view($model = null, $action = null, $controller = null)
    {
        global $viewModel;

        // Save the current view model, as we must restore it later on
        $originalViewModel = $viewModel;

        // Set the global view model to be used within the rendered view
        $viewModel = $model;

        // Extract the controller and action name from
        // the stack trace, if not explicitly provided
        if ($action == null || $controller == null) {
            $trace = debug_backtrace(false);

            /*
             * Walk the stack until the first non-BaseController class
             * is encountered.
             */
            while (count($trace) > 0) {
                $tmpCaller = array_shift($trace);
                $className = $tmpCaller['class'];
                if ($className != 'BaseController') {
                    $caller = $tmpCaller;
                    break;
                }
            }

            if ($action == null) {
                $action = $caller['function'];
            }

            if ($controller == null) {
                $controller = $caller['class'];
            }
        }

        // The 'View' directory should have the same name as the controller
        // class, minus the 'Controller' qualifier
        $viewDirectory = strtolower(
            substr(
                $controller, 0, strrpos($controller, 'Controller')
            )
        );

        // The actual view should have the same name as the action method,
        // minus the 'Action' qualifier
        if (strrpos($action, 'Action') !== false) {
            $viewName = substr($action, 0, strrpos($action, 'Action'));
        } else {
            $viewName = $action;
        }



        // Views folder is at the root of the theme
        $viewPath = dirname(dirname(__FILE__)) . "/views/%s/%s.php";

        if (!isset($pathCache[$action][$controller])) {
            // First check for a non-pluralized directory name
            if (!file_exists(
                sprintf($viewPath, $viewDirectory, $viewName)
            )
            ) {
                // Then account for pluralized forms of the view directory
                if (file_exists(
                    sprintf($viewPath, $viewDirectory . 's', $viewName)
                )
                ) {
                    $viewDirectory .= 's';
                } else {
                    if (file_exists(
                        sprintf($viewPath, $viewDirectory . 'es', $viewName)
                    )
                    ) {
                        $viewDirectory .= 'es';
                    }
                }
            }

            // Save the path in the page cache.
            $pathCache[$action][$controller] = sprintf(
                $viewPath, $viewDirectory, $viewName
            );
        }

        // Include the view, buffering the results
        if (file_exists($pathCache[$action][$controller])) {
            ob_start();
            include($pathCache[$action][$controller]);
            $viewContent = ob_get_contents();
            ob_end_clean();
        } else {
            // TODO: 404 here
            $viewContent = "";
        }

        $viewModel = $originalViewModel;
        return $viewContent;
    }

    /**
     * Outputs the rendered view and immediately exists.
     *
     * @param object $model      The model to pass to the view
     * @param string $controller Name of the controller that contains the
     *                           action that renders this view
     * @param string $action     Name of the action that renders this view
     * @return void
     */
    protected function _raw($model = null, $controller = null, $action = null)
    {
        echo $this->_view($model, $controller, $action);
        exit;
    }

    /**
     * Outputs the specified array to the browser as a JSON-encoded object
     *
     * @param object $obj PHP array to encode
     * @return void
     */
    protected function _json($obj)
    {
        header('Content-type: text/json');
        echo json_encode($obj);
        exit;
    }
}
