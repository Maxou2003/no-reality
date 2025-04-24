<?php

namespace App\Controller;

use App\Model\PostRepository;
use App\Model\UserRepository;
use App\Model\GroupRepository;
use App\Lib\DatabaseConnection;

class SearchController
{
    private $twig;

    public function __construct()
    {
        global $twig;
        $this->twig = $twig;
    }

    public function SearchResults()
    {
        $database = new DatabaseConnection();
        $PostRepository = new PostRepository();
        $UserRepository = new UserRepository();
        $GroupRepository = new GroupRepository();
        $PostRepository->connection = $database;
        $UserRepository->connection = $database;
        $GroupRepository->connection = $database;

        if (!isset($_GET['searchContent'])) {
            header('Location: ' . URL . 'home');
            exit;
        }
        $searchContent = urldecode($_GET['searchContent']);

        if (isset($_GET['filter'])) {
            $filter = $this->checkFilter($_GET['filter']);
        } else {
            $filter = 'all';
        }

        if ($filter == 'publications') {
            $posts = $PostRepository->searchPosts($searchContent, 5, 0);
            $users = null;
            $groups = null;
        } elseif ($filter == 'persons') {
            $posts = null;
            $users = $UserRepository->searchUsers($searchContent, 5, 0);
            $groups = null;
        } elseif ($filter == 'groups') {
            $posts = null;
            $users = null;
            $groups = $GroupRepository->searchGroups($searchContent, 5, 0);
        } else {
            $posts = $PostRepository->searchPosts($searchContent, 5, 0);
            $users = $UserRepository->searchUsers($searchContent, 5, 0);
            $groups = $GroupRepository->searchGroups($searchContent, 5, 0);
        }
        $template = $this->twig->load('searchResults.twig');
        echo $template->render([
            'filter' => $filter,
            'searchContent' => $searchContent,
            'posts' => $posts,
            'users' => $users,
            'groups' => $groups,
            'URL' => URL,
            'POST_IMG_PATH' => POST_IMG_PATH,
            'PROFILE_IMG_PATH' => PROFILE_IMG_PATH,
            'BANNER_IMG_PATH' => BANNER_IMG_PATH
        ]);
    }

    private function checkFilter($filter)
    {
        if ($filter == 'publications') {
            return 'publications';
        } elseif ($filter == 'persons') {
            return 'persons';
        } elseif ($filter == 'groups') {
            return 'groups';
        } else {
            return 'all';
        }
    }
}
