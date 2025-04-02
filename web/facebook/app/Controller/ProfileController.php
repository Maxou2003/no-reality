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

        $slug = strval($_GET['username']);
        $user_id = $UserRepository->getUserIdBySlug($slug);
        if ($user_id == -1) {;
            http_response_code(404);
            echo "Error 404: username '$slug' not found.";
        } else {
            $user = $UserRepository->getUser($user_id);
            $posts = $UserRepository->getPosts($user_id);

            $template = $this->twig->load('profile.twig');

            echo $template->render(['posts' => $posts, 'user' => $user, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH, /*'followers_stats' => $followers_stats,*/ 'nbPosts' => count($posts)]);
        }
    }
}
