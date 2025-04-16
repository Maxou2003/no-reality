<?php

namespace App\Controller;

use App\Lib\DatabaseConnection;
use App\Model\GroupRepository;
use App\Model\UserRepository;

class GroupController
{
    private $twig;

    public function __construct()
    {
        global $twig;
        $this->twig = $twig;
    }

    public function discussions()
    {
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $group_slug = strval($_GET['groupslug']);

        $group = $GroupRepository->getGroup($group_slug);
        $members = $GroupRepository->getGroupMembers($group->group_id);
        $posts = $GroupRepository->getPosts($group_slug, false);
        $posts_pictures = $GroupRepository->getAllPostsPictures($group_slug);

        $template = $this->twig->load('groupPage.twig');
        echo $template->render(['group' => $group, 'posts' => $posts, 'posts_pictures' => $posts_pictures, 'members' => $members, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH, 'nbPosts' => count($posts)]);
    }

    public function groupExplorer()
    {
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $suggestions = $GroupRepository->getGroupSuggestions(4, 0);
        $otherSuggestions = $GroupRepository->getGroupSuggestions(4, 4);

        $template = $this->twig->load('groupExplorer.twig');
        echo $template->render(['suggestions' => $suggestions, 'otherSuggestions' => $otherSuggestions, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH]);
    }
    public function announcements()
    {
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $group_slug = strval($_GET['groupslug']);

        $group = $GroupRepository->getGroup($group_slug);
        $members = $GroupRepository->getGroupMembers($group->group_id);
        $posts = $GroupRepository->getPosts($group_slug, true);
        $posts_pictures = $GroupRepository->getAllPostsPictures($group_slug);

        $template = $this->twig->load('groupAnnouncement.twig');
        echo $template->render(['group' => $group, 'posts' => $posts, 'posts_pictures' => $posts_pictures, 'members' => $members, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH, 'nbPosts' => count($posts)]);
    }
    public function members()
    {
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $group_slug = strval($_GET['groupslug']);

        $group = $GroupRepository->getGroup($group_slug);
        $members = $GroupRepository->getGroupMembers($group->group_id);

        $template = $this->twig->load('groupMembers.twig');
        echo $template->render(['group' => $group, 'members' => $members, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH]);
    }
    public function groupUser()
    {
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $UserRepository = new UserRepository();
        $GroupRepository->connection = $database;
        $UserRepository->connection = $database;

        $group_slug = strval($_GET['groupslug']);
        $user_slug = strval($_GET['userslug']);

        $user_id = $UserRepository->getUserIdBySlug($user_slug);
        $group = $GroupRepository->getGroup($group_slug);
        $member_since = $GroupRepository->checkUserInGroup($user_id, $group->group_id);

        if ($user_id == -1) {;
            http_response_code(404);
            echo "Error 404: username '$slug' not found.";
        } 
        if ($member_since == false) {
            http_response_code(404);
            echo "Error 404: username '$user_slug' not in '$group_slug' group.";
        }
        else {  
            $user = $UserRepository->getUser($user_id);
            $members = $GroupRepository->getGroupMembers($group->group_id);   
            $posts = $GroupRepository->getUserGroupPosts($user_id, $group->group_id);
    
            $template = $this->twig->load('groupMemberPage.twig');
            echo $template->render(['group' => $group, 
                                    'posts' => $posts,
                                    'user' => $user, 
                                    'member_since' => $member_since,
                                    'members' => $members, 
                                    'URL' => URL, 
                                    'POST_IMG_PATH' => POST_IMG_PATH, 
                                    'PROFILE_IMG_PATH' => PROFILE_IMG_PATH
                                ]);
        }
    }
    public function media()
    {
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $group_slug = strval($_GET['groupslug']);

        $group = $GroupRepository->getGroup($group_slug);
        $members = $GroupRepository->getGroupMembers($group->group_id);   
        $posts_pictures = $GroupRepository->getAllPostsPictures($group_slug);

        $template = $this->twig->load('groupMedia.twig');
        echo $template->render(['group' => $group, 'posts_pictures' => $posts_pictures, 'members' => $members, 'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH]);
    }
    public function about()
    {
        $database = new DatabaseConnection();
        $GroupRepository = new GroupRepository();
        $GroupRepository->connection = $database;

        $group_slug = strval($_GET['groupslug']);

        $group = $GroupRepository->getGroup($group_slug);
        $members = $GroupRepository->getGroupMembers($group->group_id);   
        $posts_pictures = $GroupRepository->getAllPostsPictures($group_slug);
        $activity = $GroupRepository->getGroupActivity($group->group_id);

        $template = $this->twig->load('groupAbout.twig');
        echo $template->render(['group' => $group, 'posts_pictures' => $posts_pictures, 'members' => $members, 'activity' => $activity,
                                'URL' => URL, 'POST_IMG_PATH' => POST_IMG_PATH, 'PROFILE_IMG_PATH' => PROFILE_IMG_PATH]);
    }
}
