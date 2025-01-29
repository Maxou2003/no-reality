<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\PostRepository;
use App\Model\UserRepository;

class ProfileController
{
    public function main()
    {
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $user_id = $_GET['id'];
        $user = $UserRepository->getUser($user_id);
        $posts = $UserRepository->getPosts($user_id);
        require(__DIR__ . '/../View/profile.php');
    }
}
