<?php

namespace App\Model;

use App\Lib\DatabaseConnection;
use App\Model\Entity\Post;
use App\Model\Entity\User;
use DateTime;

class UserRepository
{
    public DatabaseConnection $connection;

    public function getPosts($id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, user_username,u.user_id, instance_id,user_pp_path, nb_likes, nb_views, time_stamp, post_picture_path,post_description,post_location,nb_comments FROM posts p join users u on p.user_id=u.user_id  WHERE u.user_id = :id and instance_id=:instance_id ORDER BY time_stamp DESC'
        );
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $postArray = [];
        while (($row = $statement->fetch())) {
            $post = new Post();
            $post->user_username = $row['user_username'];
            $post->user_id = $row['user_id'];
            $post->instance_id = $row['instance_id'];
            $post->user_pp_path = $row['user_pp_path'];
            $post->nb_likes = $row['nb_likes'];
            $post->nb_views = $row['nb_views'];
            $post->time_stamp = new DateTime($row['time_stamp']);
            $post->post_picture_path = $row['post_picture_path'];
            $post->post_description = $row['post_description'];
            $post->post_location = $row['post_location'];
            $post->nb_comments = $row['nb_comments'];
            $post->post_id = $row['post_id'];

            $postArray[] = $post;
        }

        return $postArray;
    }

    public function getUserIdByUsername($user_name): int
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT user_id FROM users WHERE user_username = :user_name  and user_id in(
                        SELECT user_id FROM userlinkinstance WHERE instance_id=:instance_id)'
        );
        $statement->bindValue(':user_name', $user_name, \PDO::PARAM_STR);
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
            'SELECT user_id, user_username, user_pp_path, user_firstname, user_lastname, user_description FROM users WHERE user_id = :id  and user_id in(
                        SELECT user_id FROM userlinkinstance WHERE instance_id=:instance_id)'
        );

        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        $user = new User();
        $user->user_id = $row['user_id'];
        $user->user_username = $row['user_username'];
        $user->user_pp_path = $row['user_pp_path'];
        $user->user_firstname = $row['user_firstname'];
        $user->user_lastname = $row['user_lastname'];
        $user->user_description = $row['user_description'];

        return $user;
    }
    public function getUserNbPost($user_id)
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT count(*) as nbPost FROM posts where user_id=:user_id and instance_id=:instance_id'
        );
        $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();
        return $row['nbPost'];
    }

    public function getUserFollowersStats($user_id)
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT followers.nbFollowers, followings.nbFollowings 
            FROM (SELECT COUNT(follower_id) AS nbFollowers,instance_id 
                    FROM subscriptions 
                    WHERE followed_id = :user_id and instance_id=:instance_id
                ) AS followers 
            CROSS JOIN (SELECT COUNT(followed_id) AS nbFollowings 
                        FROM subscriptions 
                        WHERE follower_id = :user_id and instance_id=:instance_id
                ) AS followings;'
        );
        $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();
        return ['nbFollowers' => $row['nbFollowers'], 'nbFollowings' => $row['nbFollowings']];
    }


    public function fetchFollowers($user_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT instance_id,user_id,follower_id, user_firstname, user_lastname, user_description, user_username, user_pp_path
             FROM users u join subscriptions s on u.user_id=s.follower_id 
             where s.followed_id=:user_id and instance_id=:instance_id'
        );
        $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $followersArray = [];
        while (($row = $statement->fetch())) {
            $follower = new User();
            $follower->user_id = $row['user_id'];
            $follower->user_username = $row['user_username'];
            $follower->user_pp_path = $row['user_pp_path'];
            $follower->user_firstname = $row['user_firstname'];
            $follower->user_lastname = $row['user_lastname'];
            $follower->user_description = $row['user_description'];

            $followersArray[] = $follower;
        }

        return $followersArray;
    }

    public function fetchFollowings($user_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT instance_id,user_id,follower_id, user_firstname, user_lastname, user_description, user_username, user_pp_path 
            FROM users u 
            join subscriptions s on u.user_id=s.followed_id 
            where s.follower_id=:user_id and instance_id=:instance_id'
        );
        $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $followersArray = [];
        while (($row = $statement->fetch())) {
            $follower = new User();
            $follower->user_id = $row['user_id'];
            $follower->user_username = $row['user_username'];
            $follower->user_pp_path = $row['user_pp_path'];
            $follower->user_firstname = $row['user_firstname'];
            $follower->user_lastname = $row['user_lastname'];
            $follower->user_description = $row['user_description'];

            $followersArray[] = $follower;
        }

        return $followersArray;
    }
    public function fetchAllUsers(): array
    {
        // TODO: We may need to put a limit on the number of users fetched
        $statement = $this->connection->getConnection()->prepare(
            'SELECT user_id, user_firstname, user_lastname, user_description, user_username, user_pp_path FROM users WHERE user_id in (
            SELECT user_id 
        FROM userlinkinstance 
        WHERE instance_id =:instance_id) '
        );
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $users = [];
        while (($row = $statement->fetch())) {
            $user = new User();
            $user->user_id = $row['user_id'];
            $user->user_username = $row['user_username'];
            $user->user_pp_path = $row['user_pp_path'];
            $user->user_firstname = $row['user_firstname'];
            $user->user_lastname = $row['user_lastname'];
            $user->user_description = $row['user_description'];

            $users[] = $user;
        }

        return $users;
    }
    public function getSuggestions(): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT DISTINCT user_id, user_firstname, user_lastname, user_description, user_username, user_pp_path FROM users WHERE user_id in (
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
            $user->user_username = $row['user_username'];
            $user->user_pp_path = $row['user_pp_path'];
            $user->user_firstname = $row['user_firstname'];
            $user->user_lastname = $row['user_lastname'];
            $user->user_description = $row['user_description'];

            $users[] = $user;
        }

        return $users;
    }
}
