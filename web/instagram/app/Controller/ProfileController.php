<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\PostRepository;

class ProfileController
{
    public function main()
    {
        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $user_id = $_GET['id'];

        require(__DIR__ . '/../View/profile.php');
    }
}
