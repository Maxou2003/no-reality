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

        $user_name = strval($_GET['username']);
        $user_id = $UserRepository->getUserIdByUsername($user_name);
        if ($user_id == -1) {;
            http_response_code(404);
            echo "Error 404: username '$user_name' not found.";
        } else {
            $user = $UserRepository->getUser($user_id);
            $posts = $UserRepository->getPosts($user_id);
            $followers_stats = $UserRepository->getUserFollowersStats($user_id);

            # require(__DIR__ . '/../View/profile.php');

            # Comment the lines below to use the profile.php
            $template = $this->twig->load('profile.twig');

            echo $template->render(['posts' => $posts, 'user' => $user, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH, 'followers_stats' => $followers_stats, 'nbPosts' => count($posts)]);
        }
    }
}
