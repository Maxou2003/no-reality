<?php

namespace App\Model;

use DateTime;
use App\Model\Entity\GroupPost;
use App\Model\Entity\Group;
use App\Lib\DatabaseConnection;

class GroupRepository
{
    public DatabaseConnection $connection;

    public function getPosts($group_name): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, user_firstname, user_lastname, u.user_id, u.user_slug, instance_id, user_pp_path, nb_likes, time_stamp, post_picture_path, post_content, nb_comments FROM group_posts p join users u on p.user_id=u.user_id  WHERE p.group_id = (SELECT group_id FROM groups WHERE group_name = :group_name) and instance_id=:instance_id ORDER BY time_stamp DESC'
        );
        $statement->bindValue(':group_name', $group_name, \PDO::PARAM_STR);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $groupPostArray = [];
        while (($row = $statement->fetch())) {
            $groupPosts = new GroupPost();
            $groupPosts->post_id = $row['post_id'];
            $groupPosts->user_firstname = $row['user_firstname'];
            $groupPosts->user_lastname = $row['user_lastname'];
            $groupPosts->user_id = $row['user_id'];
            $groupPosts->user_slug = $row['user_slug'];
            $groupPosts->instance_id = $row['instance_id'];
            $groupPosts->user_pp_path = $row['user_pp_path'];
            $groupPosts->nb_likes = $row['nb_likes'];
            $groupPosts->time_stamp = new DateTime($row['time_stamp']);
            $groupPosts->post_picture_path = $row['post_picture_path'];
            $groupPosts->post_content = $row['post_content'];
            $groupPosts->nb_comments = $row['nb_comments'];

            $groupPostArray[] = $groupPosts;
        }

        return $groupPostArray;
    }

    public function getGroup($group_name): Group
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT group_id, group_name, time_stamp, group_banner_picture_path, group_description, nb_members FROM groups WHERE group_name = :group_name and instance_id=:instance_id'
        );
        $statement->bindValue(':group_name', $group_name, \PDO::PARAM_STR);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();
        if ($row == false) {;
            http_response_code(404);
            echo "Error 404: group name '$group_name' not found.";
        }
        else {
            $group = new Group();
            $group->group_id = $row['group_id'];
            $group->group_name = $row['group_name'];
            $group->time_stamp = new DateTime($row['time_stamp']);
            $group->group_banner_picture_path = $row['group_banner_picture_path'];
            $group->group_description = $row['group_description'];
            $group->nb_members = $row['nb_members'];
    
            return $group;
        }
    }
}
