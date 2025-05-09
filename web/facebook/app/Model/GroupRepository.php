<?php

namespace App\Model;

use DateTime;
use App\Model\Entity\GroupPost;
use App\Model\Entity\Group;
use App\Model\Entity\User;
use App\Model\Entity\Comment;
use App\Model\Entity\Response;
use App\Lib\DatabaseConnection;

class GroupRepository
{
    public DatabaseConnection $connection;

    public function getPosts($group_slug, $announcement, $limit, $offset): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, user_firstname, user_lastname, u.user_id, u.user_slug, instance_id, user_pp_path, nb_likes, time_stamp, post_picture_path, post_content, nb_comments 
            FROM group_posts p join users u on p.user_id=u.user_id  
            WHERE announcement = :announcement AND p.group_id = (
                SELECT group_id FROM groups 
                WHERE group_slug = :group_slug) 
            and instance_id=:instance_id ORDER BY time_stamp DESC LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':group_slug', $group_slug, \PDO::PARAM_STR);
        $statement->bindValue(':announcement', $announcement, \PDO::PARAM_BOOL);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
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
            'SELECT group_id, group_name,group_slug, time_stamp, group_banner_picture_path, group_description, nb_members, instance_id, group_location FROM groups WHERE group_slug = :group_slug and instance_id=:instance_id'
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
            $group->instance_id = $row['instance_id'];
            $group->group_location = $row['group_location'];

            return $group;
        }
    }

    public function getUserGroups($userid): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT group_id, group_name,group_slug, time_stamp, group_banner_picture_path, group_description, nb_members, instance_id, group_location FROM groups WHERE group_id IN (SELECT group_id FROM group_members WHERE user_id = :user_id) and instance_id=:instance_id'
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
            $group->instance_id = $row['instance_id'];
            $group->group_location = $row['group_location'];

            $groupArray[] = $group;
        }

        return $groupArray;
    }

    public function getGroupSuggestions($limit, $offset): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT group_id, group_name,group_slug, time_stamp, group_banner_picture_path, group_description, nb_members, instance_id, group_location FROM groups WHERE instance_id=:instance_id ORDER BY time_stamp DESC LIMIT :limit OFFSET :offset'
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
            $group->instance_id = $row['instance_id'];
            $group->group_location = $row['group_location'];

            $groupArray[] = $group;
        }

        return $groupArray;
    }
    public function getGroupMembers($group_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT u.user_id, u.user_pp_path, u.user_firstname, u.user_lastname, u.user_description, u.user_slug, u.user_location, u.user_work, u.user_school, u.user_gender, u.user_website, u.user_yob, u.user_banner_picture_path, time_stamp FROM group_members g JOIN users u ON g.user_id=u.user_id  WHERE g.group_id = :group_id ORDER BY g.time_stamp DESC'
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
            $user->user_gender = $row['user_gender'];
            $user->user_website = $row['user_website'];
            $user->user_yob = $row['user_yob'];
            $user->user_banner_picture_path = $row['user_banner_picture_path'];

            $userArray[] = ['users' => $user, 'timestamp' => $row['time_stamp']];
        }

        return $userArray;
    }

    public function searchGroups($search, $limit, $offset): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT group_id, group_name, group_slug, time_stamp, group_banner_picture_path, group_description, nb_members, instance_id, group_location FROM groups WHERE instance_id=:instance_id and group_name LIKE :search LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':search', '%' . $search . '%', \PDO::PARAM_STR);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);
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
            $group->instance_id = $row['instance_id'];
            $group->group_location = $row['group_location'];

            $groupArray[] = $group;
        }

        return $groupArray;
    }

    public function getUserGroupPage($user_slug): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT u.user_id, u.user_pp_path, u.user_firstname, u.user_lastname, u.user_description, u.user_slug, u.user_location, u.user_work, u.user_school, u.user_gender, u.user_website, u.user_yob, u.user_banner_picture_path, time_stamp FROM group_members g JOIN users u ON g.user_id=u.user_id  WHERE g.group_id = :group_id ORDER BY g.time_stamp DESC'
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
            $user->user_gender = $row['user_gender'];
            $user->user_website = $row['user_website'];
            $user->user_yob = $row['user_yob'];
            $user->user_banner_picture_path = $row['user_banner_picture_path'];

            $userArray[] = ['users' => $user, 'timestamp' => $row['time_stamp']];
        }

        return $userArray;
    }
    public function getUserGroupPosts($user_id, $group_id, $limit, $offset): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, user_firstname, user_lastname, u.user_id, u.user_slug, instance_id, user_pp_path, nb_likes, nb_comments, time_stamp, post_picture_path, post_content, nb_comments 
            FROM group_posts p join users u on p.user_id=u.user_id  
            WHERE u.user_id=:user_id and instance_id=:instance_id and group_id=:group_id 
            ORDER BY time_stamp DESC LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $statement->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
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
    public function checkUserInGroup($user_id, $group_id): string | bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT IF(:user_id in(SELECT user_id FROM group_members WHERE group_id=:group_id), (SELECT time_stamp FROM group_members WHERE group_id=:group_id and user_id=:user_id), false)'
        );

        $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $statement->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        return $row[0];
    }
    public function getAllPostsPictures($group_slug): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, post_picture_path FROM group_posts 
            WHERE group_id = (SELECT group_id FROM groups WHERE group_slug = :group_slug) AND instance_id=:instance_id ORDER BY time_stamp DESC'
        );
        $statement->bindValue(':group_slug', $group_slug, \PDO::PARAM_STR);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $picturesArray = [];
        while (($row = $statement->fetch())) {
            $picture = [ 'post_id' => $row['post_id'], 'post_picture_path' => $row['post_picture_path'] ];

            $picturesArray[] = $picture;
        }

        return $picturesArray;
    }
    public function getGroupActivity($group_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT 
            (SELECT COUNT(*) 
            FROM group_posts 
            WHERE DATE(time_stamp) = CURRENT_DATE AND group_id = :group_id AND instance_id = :instance_id) AS posts_today,
            (SELECT COUNT(*) 
            FROM group_posts 
            WHERE time_stamp >= DATE_FORMAT(DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 MONTH), "%Y-%m-01")
            AND time_stamp < CURRENT_TIMESTAMP AND group_id = :group_id AND instance_id = :instance_id) AS posts_last_month,

            (SELECT COUNT(*) 
            FROM group_members 
            WHERE time_stamp >= DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 7 DAY) AND group_id = :group_id) AS new_members'
        );
        $statement->bindValue(':group_id', $group_id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        return ['posts_today' => $row['posts_today'], 'posts_last_month' => $row['posts_last_month'], 'new_members' => $row['new_members']];
    }

    public function getPostById($post_id): GroupPost
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, user_firstname, user_lastname, u.user_id, u.user_slug, instance_id, user_pp_path, nb_likes, time_stamp, post_picture_path, post_content, nb_comments 
             FROM group_posts p join users u on p.user_id=u.user_id
             WHERE instance_id =:instance_id and post_id = :post_id'
        );
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':post_id', $post_id, \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        $group_post = new GroupPost();
        $group_post->post_id = $row['post_id'];
        $group_post->user_firstname = $row['user_firstname'];
        $group_post->user_lastname = $row['user_lastname'];
        $group_post->user_id = $row['user_id'];
        $group_post->user_slug = $row['user_slug'];
        $group_post->instance_id = $row['instance_id'];
        $group_post->user_pp_path = $row['user_pp_path'];
        $group_post->nb_likes = $row['nb_likes'];
        $group_post->time_stamp = new DateTime($row['time_stamp']);
        $group_post->post_picture_path = $row['post_picture_path'];
        $group_post->post_content = $row['post_content'];
        $group_post->nb_comments = $row['nb_comments'];

        return $group_post;
    }

    public function getPostComments($post_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT comment_id, u.user_id, post_id, comment_text, time_stamp, u.user_firstname, u.user_lastname, u.user_slug, u.user_pp_path, nb_responses 
             FROM group_comments c join users u on c.user_id=u.user_id
             WHERE post_id = :post_id'
        );

        $statement->bindParam(':post_id', $post_id, \PDO::PARAM_INT);
        $statement->execute();

        $commentArray = [];
        while (($row = $statement->fetch())) {
            $comment = new Comment();
            $comment->comment_id = $row['comment_id'];
            $comment->user_firstname = $row['user_firstname'];
            $comment->user_lastname = $row['user_lastname'];
            $comment->user_slug = $row['user_slug'];
            $comment->user_id = $row['user_id'];
            $comment->post_id = $row['post_id'];
            $comment->comment_text = $row['comment_text'];
            $comment->nb_responses = $row['nb_responses'];
            $comment->user_pp_path = $row['user_pp_path'];
            $comment->time_stamp = new DateTime($row['time_stamp']);

            $commentArray[] = $comment;
        }
        return $commentArray;
    }

    public function getTaggedUsers($post_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT * FROM users
                WHERE user_id in(
                SELECT user_id FROM group_identifications 
                WHERE post_id = :post_id and instance_id =:instance_id)'
        );

        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':post_id', $post_id, \PDO::PARAM_INT);
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
            $user->user_yob = $row['user_yob'];
            $user->user_gender = $row['user_gender'];
            $user->user_website = $row['user_website'];
            $user->user_banner_picture_path = $row['user_banner_picture_path'];

            $userArray[] = $user;
        }
        return $userArray;
    }

    public function getResponses($commentId): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT comment_id,response_id,u.user_id, u.user_slug, response_content,time_stamp,u.user_firstname,u.user_lastname,u.user_pp_path FROM group_responses r
               join users u on r.user_id= u.user_id 
                WHERE comment_id = :commentId and r.user_id in (
                    SELECT user_id FROM userLinkInstance where instance_id =:instance_id)'
        );

        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':commentId', $commentId, \PDO::PARAM_INT);
        $statement->execute();

        $responseArray = [];
        while (($row = $statement->fetch())) {
            $response = new Response();
            $response->comment_id = $row['comment_id'];
            $response->response_id = $row['response_id'];
            $response->user_id = $row['user_id'];
            $response->content = $row['response_content'];
            $response->time_stamp = new DateTime($row['time_stamp']);
            $response->user_firstname = $row['user_firstname'];
            $response->user_lastname = $row['user_lastname'];
            $response->user_slug = $row['user_slug'];
            $response->user_pp_path = $row['user_pp_path'];

            $responseArray[] = $response;
        }
        return $responseArray;
    }

    public function fetchLikes($post_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT * FROM users WHERE user_id in (SELECT user_id FROM userlinkinstance WHERE instance_id=:instance_id) 
            and user_id in (SELECT user_id FROM group_likes where post_id = :post_id)'
        );
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':post_id', $post_id, \PDO::PARAM_INT);
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
            $user->user_yob = $row['user_yob'];
            $user->user_gender = $row['user_gender'];
            $user->user_website = $row['user_website'];
            $user->user_banner_picture_path = $row['user_banner_picture_path'];

            $userArray[] = $user;
        }
        return $userArray;
    }
}
