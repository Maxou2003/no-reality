<?php
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $filename = str_replace('App\\', '', $class);
        $filename = str_replace('\\', '/', $filename);
        $dir = str_replace('Lib', '', __DIR__);
        $filename = $dir . $filename . '.php';

        if (file_exists($filename)) {
            require_once($filename);
        }
    }
});
