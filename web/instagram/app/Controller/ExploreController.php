<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\PostRepository;

class ExploreController
{
    private $twig;

    public function __construct()
    {
        global $twig;
        $this->twig = $twig;
    }

    public function explore()
    {
        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $posts = $PostRepository->getPost(10);

        $template = $this->twig->load('explore.twig');

        echo $template->render(['posts' => $posts, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH]);
    }
}
