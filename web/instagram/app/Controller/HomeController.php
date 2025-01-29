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

        $posts = $PostRepository->getPost(3);

        require(__DIR__ . '/../View/home.php');
    }
}
