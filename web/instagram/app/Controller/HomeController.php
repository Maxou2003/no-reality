<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\PostRepository;

class HomeController
{
    public function home()
    {
        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $posts = $PostRepository->getPost(1);

        require(__DIR__ . '/../View/home.php');
        // var_dump(__DIR__ . '/../View/home.php');

        // require(URL . '../View/home.php');
    }
}
