<?php

// Enable error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

define('URL', "/no-reality/web/instagram/public/");
define('POST_IMG_PATH', "/no-reality/web/instagram/public/img/post_img/");
define('PROFILE_IMG_PATH', "/no-reality/web/instagram/public/img/profile_picture/");

// Load configurations and autoloader
require_once '../app/Lib/autoloader.php';
require_once '../app/Lib/functions.php';

// Parse the URL to determine the controller and action
$url = isset($_GET['p']) ? rtrim($_GET['p'], '/') : 'home/home';
$urlParts = explode('/', $url);

// Check if the instanceId is valid
$instanceId = isset($_GET['instanceId']) ? $_GET['instanceId'] : null;

if (!is_null($instanceId) && !instance_exist($instanceId)) {
    session_unset();
    session_destroy();
    http_response_code(404);
    echo "Error 404: Instance '$instanceId' not found";
} else if (!is_null($instanceId) && instance_exist($instanceId)) {
    $_SESSION['instanceId'] = $instanceId;
} else if (is_null($instanceId) && !isset($_SESSION['instanceId'])) {
    session_unset();
    session_destroy();
    http_response_code(404);
    echo "Error 404: Instance required !";
}
if (isset($_SESSION['instanceId'])) {
    $controllerName = ucfirst($urlParts[0]) . 'Controller'; // Example: "TestController"
    $actionName = isset($urlParts[1]) ? $urlParts[1] : 'home'; // Default action: "index"


    // Full path to the controller file
    $controllerPath = '../app/Controller/' . $controllerName . '.php';

    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        $controllerClass = '\\App\\Controller\\' . $controllerName;

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();

            if (method_exists($controller, $actionName)) {
                $controller->$actionName(); // Call the method
            } else {
                http_response_code(404);
                echo "Error 404: Method '$actionName' not found in $controllerClass.";
            }
        } else {
            http_response_code(404);
            echo "Error 404: Class '$controllerClass' not found.";
        }
    } else {
        http_response_code(404);
        echo "Error 404: Controller '$controllerName' not found.";
    }
}
