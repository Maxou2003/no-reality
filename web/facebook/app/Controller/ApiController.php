<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\PostRepository;
use App\Model\UserRepository;
use App\Model\GroupRepository;


class ApiController
{
    private function checkSearch($searchContent)
    {
        $pattern = '/^[a-zA-Z0-9_]*$/';
        return preg_match($pattern, $searchContent);
    }
    private function checkFilter($filter)
    {
        if ($filter == 'school' || $filter == 'all' || $filter == 'location') {
            return true;
        } else {
            return false;
        }
    }


    public function getMorePosts()
    {
        if (!isset($_GET['page'])) {
            echo json_encode(['error' => 'Page number is required']);
            return;
        }
        if (!isset($_GET['nbPosts'])) {
            echo json_encode(['error' => 'Page number is required']);
            return;
        }

        $page = intval($_GET['page']);
        $perPage = intval($_GET['nbPosts']); // Number of posts to be loaded for each page
        $offset = ($page - 1) * $perPage;

        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $posts = $PostRepository->getPosts($perPage, $offset);
        header('Content-Type: application/json');
        echo json_encode($posts);
    }

    public function getFriends()
    {
        if (!isset($_GET['userId'])) {
            echo json_encode(['error' => 'User ID is required']);
            return;
        }
        $userId = intval($_GET['userId']);

        if (isset($_GET['filter']) && $this->checkFilter($_GET['filter'])) {
            $filter = $_GET['filter'];
        } else {
            $filter = 'all';
        }
        if (isset($_GET['search']) && $this->checkSearch($_GET['search'])) {
            $search = $_GET['search'];
        } else {
            $search = '';
        }

        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $user = $UserRepository->getUser($userId);
        $friends = $UserRepository->getFriends($userId);


        if ($filter == 'school') {
            $friends = array_filter($friends, function ($friend) use ($user) {
                if (strtolower($friend->user_school) == strtolower($user->user_school)) {
                    return $friend;
                }
            });
        } elseif ($filter == 'location') {
            $friends = array_filter($friends, function ($friend) use ($user) {
                if (strtolower($friend->user_location) == strtolower($user->user_location)) {
                    return $friend;
                }
            });
        }

        if ($search != '') {
            $friends = array_filter($friends, function ($friend) use ($search) {
                if (stripos($friend->user_firstname, $search) !== false || stripos($friend->user_lastname, $search) !== false) {
                    return $friend;
                }
            });
        }
        $friends = array_values($friends);
        header('Content-Type: application/json');
        echo json_encode($friends);
    }

    public function getPhotos()
    {
        if (!isset($_GET['userId'])) {
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        $userId = intval($_GET['userId']);

        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        if (isset($_GET['filter']) && $_GET['filter'] == 'others') {
            $photos = $PostRepository->getTaggedPhotos($userId);
        } else {
            $photos = $PostRepository->getPhotos($userId);
        }

        header('Content-Type: application/json');
        echo json_encode($photos);
    }

    public function searchGroups()
    {
        if (!isset($_GET['searchContent'])) {
            echo json_encode(['error' => 'Search term is required']);
            return;
        }
        if (!$this->checkSearch($_GET['searchContent'])) {
            echo json_encode(['error' => 'Invalid search term']);
            return;
        }

        $search = $_GET['searchContent'];
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $groups = $GroupRepository->searchGroups($search);

        header('Content-Type: application/json');
        echo json_encode($groups);
    }

    public function searchUsers()
    {
        if (!isset($_GET['searchContent'])) {
            echo json_encode(['error' => 'Search term is required']);
            return;
        }
        if (!$this->checkSearch($_GET['searchContent'])) {
            echo json_encode(['error' => 'Invalid search term']);
            return;
        }

        $search = $_GET['searchContent'];
        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $users = $UserRepository->searchUsers($search);

        header('Content-Type: application/json');
        echo json_encode($users);
    }
}
