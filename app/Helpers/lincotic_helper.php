<?php

// Body Id
if (! function_exists('body_id')) {
    function body_id(): string {
        $router = service('router');
        $controller = array_last(explode('\\', $router->controllerName()));
        return strtolower($controller . '-' . $router->methodName());
    }
}
