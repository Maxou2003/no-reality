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
            'SELECT post_id, user_username,u.user_id, instance_id,user_pp_path, nb_likes, nb_views, time_stamp, post_picture_path,post_description,post_location,nb_comments FROM posts p join users u on p.user_id=u.user_id  WHERE u.user_id = :id'
        );
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
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

    public function getUser($id): User
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT user_id, user_username, user_pp_path, user_firstname, user_lastname, user_description FROM users WHERE user_id = :id'
        );

        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
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

    public function fetchFollowers($user_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT follower_id, user_firstname, user_lastname, user_description, user_username, user_pp_path FROM users u join subscriptions s on u.user_id=s.follower_id where s.followed_id=:user_id'
        );
        $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
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
}
