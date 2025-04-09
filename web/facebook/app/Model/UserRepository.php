<?php

namespace App\Model;

use DateTime;
use App\Lib\DatabaseConnection;
use App\Model\Entity\User;
use App\Model\Entity\Post;

class UserRepository
{
    public DatabaseConnection $connection;
    public function getSuggestions(): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT DISTINCT user_id, user_firstname, user_lastname, user_description, user_slug, user_pp_path, user_location, user_work, user_school,user_yob,user_gender,user_website, user_banner_picture_path FROM users WHERE user_id in (
            SELECT user_id 
        FROM userlinkinstance 
        WHERE instance_id =:instance_id

) LIMIT 0,6;'
        );
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();
        $users = [];
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

            $users[] = $user;
        }

        return $users;
    }
    public function getPosts($id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, user_firstname, user_lastname, u.user_id, u.user_slug, instance_id, user_pp_path, nb_likes, nb_shares, time_stamp, post_picture_path, post_content, nb_comments FROM posts p join users u on p.user_id=u.user_id  WHERE u.user_id = :id and instance_id=:instance_id ORDER BY time_stamp DESC'
        );
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $postArray = [];
        while (($row = $statement->fetch())) {
            $post = new Post();
            $post->post_id = $row['post_id'];
            $post->user_firstname = $row['user_firstname'];
            $post->user_lastname = $row['user_lastname'];
            $post->user_id = $row['user_id'];
            $post->user_slug = $row['user_slug'];
            $post->instance_id = $row['instance_id'];
            $post->user_pp_path = $row['user_pp_path'];
            $post->nb_likes = $row['nb_likes'];
            $post->nb_shares = $row['nb_shares'];
            $post->time_stamp = new DateTime($row['time_stamp']);
            $post->post_picture_path = $row['post_picture_path'];
            $post->post_content = $row['post_content'];
            $post->nb_comments = $row['nb_comments'];

            $postArray[] = $post;
        }

        return $postArray;
    }
    public function getUserIdBySlug($slug): int
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT user_id FROM users WHERE user_slug = :slug  and user_id in(
                        SELECT user_id FROM userlinkinstance WHERE instance_id=:instance_id)'
        );
        $statement->bindValue(':slug', $slug, \PDO::PARAM_STR);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();
        if ($row === false) {
            return -1;
        } else {
            return $row['user_id'];
        }
    }
    public function getUser($id): User
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT user_id, user_firstname, user_lastname, user_description, user_slug, user_pp_path, user_location, user_work, user_school,user_yob,user_gender,user_website, user_banner_picture_path FROM users WHERE user_id = :id  and user_id in(
                        SELECT user_id FROM userlinkinstance WHERE instance_id=:instance_id)'
        );

        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

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


        return $user;
    }

    public function getFriends($userid): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT * FROM users WHere user_id in (SELECT user_id_1 FROM friends where user_id_2 =:id and instance_id=:instanceId UNION SELECT user_id_2 FROM friends where user_id_1 =:id and instance_id=:instanceId);'
        );
        $statement->bindValue(':id', $userid, \PDO::PARAM_INT);
        $statement->bindValue(':instanceId', $_SESSION['instanceId'], \PDO::PARAM_INT);
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
