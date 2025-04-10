<?php

namespace App\Model;

use DateTime;
use App\Model\Entity\GroupPost;
use App\Model\Entity\Group;
use App\Model\Entity\User;
use App\Lib\DatabaseConnection;

class GroupRepository
{
    public DatabaseConnection $connection;

    public function getPosts($group_slug): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, user_firstname, user_lastname, u.user_id, u.user_slug, instance_id, user_pp_path, nb_likes, time_stamp, post_picture_path, post_content, nb_comments 
            FROM group_posts p join users u on p.user_id=u.user_id  
            WHERE p.group_id = (
                SELECT group_id FROM groups 
                WHERE group_slug = :group_slug) 
            and instance_id=:instance_id ORDER BY time_stamp DESC'
        );
        $statement->bindValue(':group_slug', $group_slug, \PDO::PARAM_STR);
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

    public function getGroup($group_slug): Group
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT group_id, group_name,group_slug, time_stamp, group_banner_picture_path, group_description, nb_members FROM groups WHERE group_slug = :group_slug and instance_id=:instance_id'
        );
        $statement->bindValue(':group_slug', $group_slug, \PDO::PARAM_STR);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();
        if ($row == false) {;
            http_response_code(404);
            echo "Error 404: group name '$group_slug' not found.";
            exit;
        } else {
            $group = new Group();
            $group->group_id = $row['group_id'];
            $group->group_name = $row['group_name'];
            $group->group_slug = $row['group_slug'];
            $group->time_stamp = new DateTime($row['time_stamp']);
            $group->group_banner_picture_path = $row['group_banner_picture_path'];
            $group->group_description = $row['group_description'];
            $group->nb_members = $row['nb_members'];

            return $group;
        }
    }

    public function getUserGroups($userid): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT group_id, group_name,group_slug, time_stamp, group_banner_picture_path, group_description, nb_members FROM groups WHERE group_id IN (SELECT group_id FROM group_members WHERE user_id = :user_id) and instance_id=:instance_id'
        );
        $statement->bindValue(':user_id', $userid, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $groupArray = [];
        while (($row = $statement->fetch())) {
            $group = new Group();
            $group->group_id = $row['group_id'];
            $group->group_name = $row['group_name'];
            $group->group_slug = $row['group_slug'];
            $group->time_stamp = new DateTime($row['time_stamp']);
            $group->group_banner_picture_path = $row['group_banner_picture_path'];
            $group->group_description = $row['group_description'];
            $group->nb_members = $row['nb_members'];

            $groupArray[] = $group;
        }

        return $groupArray;
    }

    public function getGroupSuggestions($limit, $offset): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT group_id, group_name,group_slug, time_stamp, group_banner_picture_path, group_description, nb_members FROM groups WHERE instance_id=:instance_id ORDER BY time_stamp DESC LIMIT :limit OFFSET :offset'
        );
        $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $statement->execute();

        $groupArray = [];
        while (($row = $statement->fetch())) {
            $group = new Group();
            $group->group_id = $row['group_id'];
            $group->group_name = $row['group_name'];
            $group->group_slug = $row['group_slug'];
            $group->time_stamp = new DateTime($row['time_stamp']);
            $group->group_banner_picture_path = $row['group_banner_picture_path'];
            $group->group_description = $row['group_description'];
            $group->nb_members = $row['nb_members'];

            $groupArray[] = $group;
        }

        return $groupArray;
    }
    public function getGroupMembers($group_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT u.user_id, u.user_pp_path, u.user_firstname, u.user_lastname, u.user_description, u.user_slug, u.user_location, u.user_work, u.user_school, u.gender, u.website, u.year_of_birth, u.user_banner_picture_path, time_stamp FROM group_members g JOIN users u ON g.user_id=u.user_id  WHERE g.group_id = :group_id ORDER BY g.time_stamp DESC'
        );
        $statement->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        $statement->execute();

        $userArray = [];
        while (($row = $statement->fetch())) {
            $user = new User();
            $user->user_id = $row['user_id'];
            $user->user_pp_path = $row['user_pp_path'];
            $user->user_firstname = $row['user_firstname'];
            $user->user_lastname = $row['user_lastname'];
            $user->user_description = $row['user_description'];
            $user->user_slug = $row['user_slug'];
            $user->user_location = $row['user_location'];
            $user->user_work = $row['user_work'];
            $user->user_school = $row['user_school'];
            $user->user_gender = $row['gender'];
            $user->user_website = $row['website'];
            $user->user_yob = $row['year_of_birth'];
            $user->user_banner_picture_path = $row['user_banner_picture_path'];

            $userArray[] = ['users' => $user, 'timestamp' => $row['time_stamp']];
        }

        return $userArray;
    }

    public function searchGroups($search): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT group_id, group_name,group_slug, time_stamp, group_banner_picture_path, group_description, nb_members FROM groups WHERE instance_id=:instance_id and group_name LIKE :search'
        );
        $statement->bindValue(':search', '%' . $search . '%', \PDO::PARAM_STR);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $groupArray = [];
        while (($row = $statement->fetch())) {
            $group = new Group();
            $group->group_id = $row['group_id'];
            $group->group_name = $row['group_name'];
            $group->group_slug = $row['group_slug'];
            $group->time_stamp = new DateTime($row['time_stamp']);
            $group->group_banner_picture_path = $row['group_banner_picture_path'];
            $group->group_description = $row['group_description'];
            $group->nb_members = $row['nb_members'];

            $groupArray[] = $group;
        }

        return $groupArray;
    }
}
