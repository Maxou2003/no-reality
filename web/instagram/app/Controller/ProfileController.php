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
        $followers_stats = $UserRepository->getUserFollowersStats($user_id);

        # require(__DIR__ . '/../View/profile.php');

        # Comment the lines below to use the profile.php
        $template = $this->twig->load('profile.twig');

        echo $template->render(['posts' => $posts, 'user' => $user, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH, 'followers_stats' => $followers_stats, 'nbPosts' => count($posts)]);
    }

    public function getFollowers()
    {
        if (!isset($_GET['user_id'])) {
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        $user_id = intval($_GET['user_id']);
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $followers = $UserRepository->fetchFollowers($user_id);
        header('Content-Type: application/json');
        echo json_encode($followers);
    }

    public function getFollowings()
    {
        if (!isset($_GET['user_id'])) {
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        $user_id = intval($_GET['user_id']);
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $followings = $UserRepository->fetchFollowings($user_id);
        header('Content-Type: application/json');
        echo json_encode($followings);
    }

    public function searchInFollowers()
    {
        if (!isset($_GET['user_id'])) {
            echo json_encode(['error' => 'User ID is required']);
            return;
        }
        $user_id = intval($_GET['user_id']);
        $searchContent = strval($_GET['searchContent']);
        $follow = boolval($_GET['follow']);

        if (!$this->checkSearch($searchContent)) {
            echo json_encode(['error' => 'Invalid search content']);
            return;
        }
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        if ($follow) {
            $followers = $UserRepository->fetchFollowers($user_id);
        } else {
            $followers = $UserRepository->fetchFollowings($user_id);
        }

        $filteredFollowers = array_filter($followers, function ($follower) use ($searchContent) {
            return (strpos(strtolower($follower->user_username), strtolower($searchContent)) !== false) ||
                (strpos(strtolower($follower->user_firstname), strtolower($searchContent)) !== false) ||
                (strpos(strtolower($follower->user_lastname), strtolower($searchContent)) !== false);
        });
        header('Content-Type: application/json');
        echo json_encode($filteredFollowers);
    }

    private function checkSearch($searchContent)
    {
        $pattern = '/^[a-zA-Z0-9_]+$/';
        return preg_match($pattern, $searchContent);
    }
}
