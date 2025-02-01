<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\PostRepository;

class HomeController
{
    private $twig;    

    public function __construct()
    {
        global $twig;
        $this->twig = $twig;
    }

    public function home()
    {
        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $posts = $PostRepository->getPost(3);

        # require(__DIR__ . '/../View/home.php');
        
        # Comment the lines below to use the home.php
        $template = $this->twig->load('home.twig');

        echo $template->render(['posts' => $posts, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH]); 
    
    }
}
