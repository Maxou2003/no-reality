<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\PostRepository;
use App\Model\UserRepository;

class ProfileController
{
    private $twig;    

    public function __construct()
    {
        global $twig;
        $this->twig = $twig;
    }
    
    public function main()
    {
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $user_id = $_GET['id'];
        $user = $UserRepository->getUser($user_id);
        $posts = $UserRepository->getPosts($user_id);

        # require(__DIR__ . '/../View/profile.php');
        
        # Comment the lines below to use the profile.php
        $template = $this->twig->load('profile.twig');

        echo $template->render(['posts' => $posts, 'user' => $user, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH]); 
    }
}
