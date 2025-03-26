<?php

namespace App\Model;

use DateTime;
use App\Model\Entity\Post;
use App\Lib\DatabaseConnection;

class PostRepository
{
    public DatabaseConnection $connection;

    public function getPosts($limit, $offset): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, instance_id, post_description, u.user_id, user_pp_path, post_picture_path, time_stamp, nb_comments, nb_likes, nb_shares FROM posts p join users u on p.user_id=u.user_id Where instance_id=:instance_id ORDER BY time_stamp DESC LIMIT :limit OFFSET :offset'
        );
        $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $statement->execute();

        $postArray = [];
        while (($row = $statement->fetch())) {
            $post = new Post();
            $post->post_id = $row['post_id'];
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

            $postArray[] = $post;
        }
        return $postArray;
    }
}
