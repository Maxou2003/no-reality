<?php

namespace App\Controller;

class TestController
{
    public function index()
    {
        require(__DIR__ . '/../View/home.php');
    }
    public function testmethod()
    {
        echo "This is test method";
    }
}
