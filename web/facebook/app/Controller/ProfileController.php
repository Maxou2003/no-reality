<?php

namespace App\Controller;

use App\Model\UserRepository;
use App\Model\GroupRepository;
use App\Lib\DatabaseConnection;

class ProfileController
{
    private $twig;

    public function __construct()
    {
        global $twig;
        $this->twig = $twig;
    }

    public function publications()
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
            $friends = $UserRepository->getFriends($user_id);
            $template = $this->twig->load('profilePublications.twig');

            echo $template->render([
                'posts' => $posts,
                'user' => $user,
                'friends' => $friends,
                'nb_friends' => count($friends),
                'URL' => URL,
                'POST_IMG_PATH' => POST_IMG_PATH,
                'PROFILE_IMG_PATH' => PROFILE_IMG_PATH,
                'BANNER_IMG_PATH' => BANNER_IMG_PATH
            ]);
        }
    }

    public function about()
    {
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;
        $UserRepository->connection = $database;

        $slug = strval($_GET['username']);
        $user_id = $UserRepository->getUserIdBySlug($slug);
        if ($user_id == -1) {;
            http_response_code(404);
            echo "Error 404: username '$slug' not found.";
        } else {
            $user = $UserRepository->getUser($user_id);
            $posts = $UserRepository->getPosts($user_id);
            $friends = $UserRepository->getFriends($user_id);
            $groups = $GroupRepository->getUserGroups($user_id);
            $template = $this->twig->load('profileAbout.twig');

            echo $template->render([
                'groups' => $groups,
                'posts' => $posts,
                'user' => $user,
                'friends' => $friends,
                'nb_friends' => count($friends),
                'URL' => URL,
                'POST_IMG_PATH' => POST_IMG_PATH,
                'PROFILE_IMG_PATH' => PROFILE_IMG_PATH,
                'BANNER_IMG_PATH' => BANNER_IMG_PATH,
                'nbPosts' => count($posts)
            ]);
        }
    }
    public function friends()
    {
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;
        $UserRepository->connection = $database;

        $slug = strval($_GET['username']);
        $user_id = $UserRepository->getUserIdBySlug($slug);
        if ($user_id == -1) {;
            http_response_code(404);
            echo "Error 404: username '$slug' not found.";
        } else {
            $user = $UserRepository->getUser($user_id);
            $posts = $UserRepository->getPosts($user_id);
            $friends = $UserRepository->getFriends($user_id);
            $groups = $GroupRepository->getUserGroups($user_id);
            $template = $this->twig->load('profileFriends.twig');

            echo $template->render([
                'groups' => $groups,
                'posts' => $posts,
                'user' => $user,
                'friends' => $friends,
                'nb_friends' => count($friends),
                'URL' => URL,
                'POST_IMG_PATH' => POST_IMG_PATH,
                'PROFILE_IMG_PATH' => PROFILE_IMG_PATH,
                'BANNER_IMG_PATH' => BANNER_IMG_PATH,
                'nbPosts' => count($posts)
            ]);
        }
    }
    public function photos()
    {
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;
        $UserRepository->connection = $database;

        $slug = strval($_GET['username']);
        $user_id = $UserRepository->getUserIdBySlug($slug);
        if ($user_id == -1) {;
            http_response_code(404);
            echo "Error 404: username '$slug' not found.";
        } else {
            $user = $UserRepository->getUser($user_id);
            $posts = $UserRepository->getPosts($user_id);
            $friends = $UserRepository->getFriends($user_id);
            $groups = $GroupRepository->getUserGroups($user_id);
            $template = $this->twig->load('profilePhotos.twig');

            echo $template->render([
                'groups' => $groups,
                'posts' => $posts,
                'user' => $user,
                'friends' => $friends,
                'nb_friends' => count($friends),
                'URL' => URL,
                'POST_IMG_PATH' => POST_IMG_PATH,
                'PROFILE_IMG_PATH' => PROFILE_IMG_PATH,
                'BANNER_IMG_PATH' => BANNER_IMG_PATH,
                'nbPosts' => count($posts)
            ]);
        }
    }
}
