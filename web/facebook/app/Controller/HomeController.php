<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\PostRepository;
use App\Model\UserRepository;

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
        $UserRepository = new UserRepository();
        $PostRepository->connection = $database;
        $UserRepository->connection = $database;

        $suggestions = $UserRepository->getSuggestions();
        $template = $this->twig->load('home.twig');

        echo $template->render([
            'suggestions' => $suggestions,
            'URL' => URL,
            'POST_IMG_PATH' => POST_IMG_PATH,
            'PROFILE_IMG_PATH' => PROFILE_IMG_PATH,
            'BANNER_IMG_PATH' => BANNER_IMG_PATH
        ]);
    }
}
