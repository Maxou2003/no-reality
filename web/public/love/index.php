<?php

// Enable error reporting for development
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$platform = "facebook";

// Load configurations and autoloader
require_once '../../' . $platform . '/app/Lib/autoloader.php';
require_once '../../' . $platform . '/app/Lib/functions.php';

// Parse the URL to determine the controller and action
$url = isset($_GET['p']) ? rtrim($_GET['p'], '/') : 'home/home';
$urlParts = explode('/', $url);
$uri = explode('/', $_SERVER['REQUEST_URI'])[4];

define('URL', "/no-reality/web/public/" . $uri . "/");
define('POST_IMG_PATH', "/no-reality/web/public/" . $uri . "/img/post_img/");
define('PROFILE_IMG_PATH', "/no-reality/web/profile_pictures/");


$_SESSION['instanceId'] = get_instanceId($uri);


$controllerName = ucfirst($urlParts[0]) . 'Controller';
$actionName = isset($urlParts[1]) ? $urlParts[1] : 'home';


// Full path to the controller file
$controllerPath = '../../' . $platform . '/app/Controller/' . $controllerName . '.php';

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
