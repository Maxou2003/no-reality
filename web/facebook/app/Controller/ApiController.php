<?php

namespace App\Controller;

use App\Model\PostRepository;
use App\Model\UserRepository;
use App\Model\GroupRepository;
use App\Lib\DatabaseConnection;
use App\Model\Entity\CommentModal;
use App\Model\Entity\GroupCommentModal;


class ApiController
{
    private function checkSearch($searchContent)
    {
        $pattern = '/^[a-zA-Z0-9_\%]*$/';
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
            echo json_encode(['error' => 'Post number is required']);
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

    public function getDynamicUserPosts()
    {
        if (!isset($_GET['page'])) {
            echo json_encode(['error' => 'Page number is required']);
            return;
        }
        if (!isset($_GET['nbPosts'])) {
            echo json_encode(['error' => 'Post number is required']);
            return;
        }
        if (!isset($_GET['userId'])) {
            echo json_encode(['error' => 'User id is required']);
            return;
        }

        $page = intval($_GET['page']);
        $perPage = intval($_GET['nbPosts']);
        $user_id = intval($_GET['userId']);
        $offset = ($page - 1) * $perPage;

        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $posts = $UserRepository->getPostsByUserId($user_id, $perPage, $offset);
        header('Content-Type: application/json');
        echo json_encode($posts);
    }
    public function getUserIdBySlug()
    {
        if (!isset($_GET['userSlug'])) {
            echo json_encode(['error' => 'User slug is required']);
            return;
        }
        $user_slug = $_GET['userSlug'];

        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $user_id = $UserRepository->getUserIdBySlug($user_slug);

        header('Content-Type: application/json');
        echo json_encode(['user_id' => $user_id]);
    }
    public function getDynamicGroupPosts()
    {
        if (!isset($_GET['page'])) {
            echo json_encode(['error' => 'Page number is required']);
            return;
        }
        if (!isset($_GET['nbPosts'])) {
            echo json_encode(['error' => 'Post number is required']);
            return;
        }
        if (!isset($_GET['announcement'])) {
            echo json_encode(['error' => 'Announcement is required']);
            return;
        }
        if (!isset($_GET['groupSlug'])) {
            echo json_encode(['error' => 'Group slug is required']);
            return;
        }

        $page = intval($_GET['page']);
        $perPage = intval($_GET['nbPosts']);
        $group_slug = $_GET['groupSlug'];
        $announcement = boolval($_GET['announcement']);
        $offset = ($page - 1) * $perPage;

        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $posts = $GroupRepository->getPosts($group_slug, $announcement, $perPage, $offset);
        header('Content-Type: application/json');
        echo json_encode($posts);
    }
    public function getDynamicUserGroupPosts()
    {
        if (!isset($_GET['page'])) {
            echo json_encode(['error' => 'Page number is required']);
            return;
        }
        if (!isset($_GET['nbPosts'])) {
            echo json_encode(['error' => 'Post number is required']);
            return;
        }
        if (!isset($_GET['userSlug'])) {
            echo json_encode(['error' => 'User slug is required']);
            return;
        }
        if (!isset($_GET['groupSlug'])) {
            echo json_encode(['error' => 'Group slug is required']);
            return;
        }

        $page = intval($_GET['page']);
        $perPage = intval($_GET['nbPosts']);
        $group_slug = $_GET['groupSlug'];
        $user_slug = $_GET['userSlug'];
        $offset = ($page - 1) * $perPage;

        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $UserRepository = new UserRepository();
        $GroupRepository->connection = $database;
        $UserRepository->connection = $database;
        $group = $GroupRepository->getGroup($group_slug);
        $user_id = $UserRepository->getUserIdBySlug($user_slug);
        $user = $UserRepository->getUser($user_id);

        $posts = $GroupRepository->getUserGroupPosts($user_id, $group->group_id, $perPage, $offset);
        header('Content-Type: application/json');
        echo json_encode(['posts' => $posts, 'group' => $group, 'user' => $user]);
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

        $searchContent = urldecode($_GET['searchContent']);
        $searchContent = htmlspecialchars($searchContent);

        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $groups = $GroupRepository->searchGroups($searchContent, 5, 0);

        header('Content-Type: application/json');
        echo json_encode($groups);
    }

    public function searchUsers()
    {
        if (!isset($_GET['searchContent'])) {
            echo json_encode(['error' => 'Search term is required']);
            return;
        }

        $searchContent = urldecode($_GET['searchContent']);
        $searchContent = htmlspecialchars($searchContent);

        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $users = $UserRepository->searchUsers($searchContent, 5, 0);

        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function getLikes()
    {
        if (!isset($_GET['postId'])) {
            echo json_encode(['error' => 'Post id is required']);
            return;
        }
        $post_id = intval($_GET['postId']);

        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $users = $UserRepository->fetchLikes($post_id);

        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function getShares()
    {
        if (!isset($_GET['postId'])) {
            echo json_encode(['error' => 'Post id is required']);
            return;
        }
        $post_id = intval($_GET['postId']);

        $database = new DatabaseConnection();
        $UserRepository = new UserRepository();
        $UserRepository->connection = $database;

        $users = $UserRepository->fetchShares($post_id);

        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function getCommentModal()
    {
        if (!isset($_GET['postId'])) {
            echo json_encode(['error' => 'Post id is required']);
            return;
        }
        $post_id = intval($_GET['postId']);

        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $post = $PostRepository->getPostById($post_id);
        $comments = $PostRepository->getPostComments($post_id);
        $taggedUsers = $PostRepository->getTaggedUsers($post_id);

        $commentModal = new CommentModal();

        $commentModal->comments = $comments;
        $commentModal->post = $post;
        $commentModal->taggedUsers = $taggedUsers;

        header('Content-Type: application/json');
        echo json_encode($commentModal);
    }

    public function getResponses()
    {
        if (!isset($_GET['commentId'])) {
            echo json_encode(['error' => 'Post id is required']);
            return;
        }

        $commentId = intval($_GET['commentId']);

        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $PostRepository->connection = $database;

        $responses = $PostRepository->getResponses($commentId);
        header('Content-Type: application/json');
        echo json_encode($responses);
    }
    public function getGroupCommentModal()
    {
        if (!isset($_GET['postId'])) {
            echo json_encode(['error' => 'Post id is required']);
            return;
        }
        $post_id = intval($_GET['postId']);

        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $group_post = $GroupRepository->getPostById($post_id);
        $comments = $GroupRepository->getPostComments($post_id);
        $taggedUsers = $GroupRepository->getTaggedUsers($post_id);

        $commentModal = new GroupCommentModal();

        $commentModal->comments = $comments;
        $commentModal->group_post = $group_post;
        $commentModal->taggedUsers = $taggedUsers;

        header('Content-Type: application/json');
        echo json_encode($commentModal);
    }
    public function getGroupResponses()
    {
        if (!isset($_GET['commentId'])) {
            echo json_encode(['error' => 'Post id is required']);
            return;
        }

        $commentId = intval($_GET['commentId']);

        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $responses = $GroupRepository->getResponses($commentId);
        header('Content-Type: application/json');
        echo json_encode($responses);
    }
    public function getGroupLikes()
    {
        if (!isset($_GET['postId'])) {
            echo json_encode(['error' => 'Post id is required']);
            return;
        }
        $post_id = intval($_GET['postId']);

        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $users = $GroupRepository->fetchLikes($post_id);

        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function searchInGroupMembers()
    {
        if (!isset($_GET['groupId'])) {
            echo json_encode(['error' => 'Post id is required']);
            return;
        }
        if (!isset($_GET['searchContent'])) {
            echo json_encode(['error' => 'Search term is required']);
            return;
        }
        $searchContent = urldecode($_GET['searchContent']);
        $searchContent = htmlspecialchars($searchContent);
        $group_id = $_GET['groupId'];


        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $groupMembers = $GroupRepository->getGroupMembers($group_id);

        if (count($groupMembers) > 0) {
            $groupMembers = array_filter($groupMembers, function ($groupMember) use ($searchContent) {
                if (stripos($groupMember['users']->user_firstname, $searchContent) !== false || stripos($groupMember['users']->user_lastname, $searchContent) !== false) {
                    return $groupMember;
                }
            });
        }

        $groupMembers = array_values($groupMembers);
        header('Content-Type: application/json');
        echo json_encode($groupMembers);
    }
}
