<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\GroupRepository;

class GroupController
{
    private $twig;

    public function __construct()
    {
        global $twig;
        $this->twig = $twig;
    }

    public function group()
    {
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $group_name = strval($_GET['groupname']);

        $group = $GroupRepository->getGroup($group_name);
        $posts = $GroupRepository->getPosts($group_name);

        $template = $this->twig->load('groupPage.twig');
        echo $template->render(['group' => $group, 'posts' => $posts, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH, 'nbPosts' => count($posts)]);
    }

    public function groupExplorer()
    {
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $suggestions = $GroupRepository->getGroupSuggestions(5, 0);
        $otherSuggestions = $GroupRepository->getGroupSuggestions(5, 5);

        $template = $this->twig->load('groupExplorer.twig');
        echo $template->render(['suggestions' => $suggestions, 'otherSuggestions' => $otherSuggestions, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH]);
    }
}
