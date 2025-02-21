<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\Entity\ModalPost;
use App\Model\PostRepository;
use App\Model\UserRepository;


class ApiController
{
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

    public function getComments()
    {
        if (!isset($_GET['post_id'])) {
            echo json_encode(['error' => 'Post ID is required']);
            return;
        }

        $post_id = intval($_GET['post_id']);
        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $comments = $PostRepository->fetchComments($post_id);
        header('Content-Type: application/json');
        echo json_encode($comments);
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
        $filteredFollowers = array_values($filteredFollowers);
        header('Content-Type: application/json');
        echo json_encode($filteredFollowers);
    }

    public function searchInUsers()
    {

        $searchContent = strval($_GET['searchContent']);

        if (!$this->checkSearch($searchContent) && $searchContent !== '') {
            echo json_encode(['error' => 'Invalid search content']);
            return;
        }
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $users = $UserRepository->fetchAllUsers();

        if ($searchContent === '') {
            header('Content-Type: application/json');
            echo json_encode($users);
            return;
        } else {
            $filteredUsers = array_filter($users, function ($user) use ($searchContent) {
                return (strpos(strtolower($user->user_username), strtolower($searchContent)) !== false) ||
                    (strpos(strtolower($user->user_firstname), strtolower($searchContent)) !== false) ||
                    (strpos(strtolower($user->user_lastname), strtolower($searchContent)) !== false);
            });
            header('Content-Type: application/json');
            $filteredUsers = array_values($filteredUsers);
            echo json_encode($filteredUsers);
        }
    }

    private function checkSearch($searchContent)
    {
        $pattern = '/^[a-zA-Z0-9_]+$/';
        return preg_match($pattern, $searchContent);
    }

    public function getModalPost()
    {
        if (!isset($_GET["postId"])) {
            echo json_encode(['error' => 'Post ID is required']);
            return;
        }
        $post_id = intval($_GET["postId"]);

        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $post = $PostRepository->getPostByPostId($post_id);
        $comments = $PostRepository->fetchComments($post_id);
        $taggedUsers = $PostRepository->getTaggedUsers($post_id);

        $modalPost = new ModalPost();
        $modalPost->comments = $comments;
        $modalPost->post = $post;
        $modalPost->taggedUsers = $taggedUsers;

        header('Content-Type: application/json');
        echo json_encode($modalPost);
    }
    public function getResponses()
    {
        if (!isset($_GET["commentId"])) {
            echo json_encode(['error' => 'Comment ID is required']);
            return;
        }
        $comment_id = $_GET["commentId"];

        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $responses = $PostRepository->fetchResponses($comment_id);
        header('Content-Type: application/json');
        echo json_encode($responses);
    }
    public function getIdentifications()
    {
        if (!isset($_GET["userId"])) {
            echo json_encode(['error' => 'User ID is required']);
            return;
        }
        if (!isset($_GET["choice"])) {
            echo json_encode(['error' => 'Choice is required']);
            return;
        }
        $userId = intval($_GET["userId"]);
        $choice = strval($_GET["choice"]);

        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $identifications = $PostRepository->getPostIdentifications($userId, $choice);

        header('Content-Type: application/json');
        echo json_encode($identifications);
    }

    public function getMorePosts()
    {
        if (!isset($_GET['page'])) {
            echo json_encode(['error' => 'Page number is required']);
            return;
        }

        $page = intval($_GET['page']);
        $perPage = 2; // Number of posts to be loaded for each page
        $offset = ($page - 1) * $perPage;

        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $posts = $PostRepository->getPost($perPage, $offset);
        $postsArray = [];
        foreach ($posts as $post) {
            $postsArray[] = [
                'post_id' => $post->post_id,
                'user_username' => $post->user_username,
                'user_id' => $post->user_id,
                'instance_id' => $post->instance_id,
                'user_pp_path' => $post->user_pp_path,
                'nb_likes' => $post->nb_likes,
                'nb_views' => $post->nb_views,
                'time_stamp' => $post->time_stamp->format('Y-m-d H:i:s'), // Format correct pour le timestamp
                'post_picture_path' => $post->post_picture_path,
                'post_description' => $post->post_description,
                'post_location' => $post->post_location,
                'nb_comments' => $post->nb_comments,
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($postsArray);
    }
}
