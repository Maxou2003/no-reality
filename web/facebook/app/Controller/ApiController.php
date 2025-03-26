<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\Entity\ModalPost;
use App\Model\PostRepository;
use App\Model\UserRepository;


class ApiController
{
    private function checkSearch($searchContent)
    {
        $pattern = '/^[a-zA-Z0-9_]*$/';
        return preg_match($pattern, $searchContent);
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
}
