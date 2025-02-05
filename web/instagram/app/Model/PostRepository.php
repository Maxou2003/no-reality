<?php

namespace App\Model;

use App\Lib\DatabaseConnection;
use App\Model\Entity\Post;
use App\Model\Entity\Comment;
use DateTime;

class PostRepository
{
    public DatabaseConnection $connection;

    public function getPost($nb): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT user_username,u.user_id, instance_id,user_pp_path, nb_likes, nb_views, time_stamp, post_picture_path,post_description,post_location,nb_comments, post_id FROM posts p join users u on p.user_id=u.user_id ORDER BY time_stamp DESC limit :offset '
        );
        $statement->bindValue(':offset', $nb, \PDO::PARAM_INT);
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

    public function fetchComments($post_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT c.comment_id, c.user_id, c.post_id, c.comment_text, c.time_stamp, u.user_username, u.user_pp_path FROM comments c join users u on c.user_id=u.user_id WHERE c.post_id = :post_id ORDER BY c.time_stamp DESC'
        );
        $statement->bindValue(':post_id', $post_id, \PDO::PARAM_INT);
        $statement->execute();

        $commentsArray = [];
        while (($row = $statement->fetch())) {
            $comment = new Comment();
            $comment->comment_id = $row['comment_id'];
            $comment->user_id = $row['user_id'];
            $comment->post_id = $row['post_id'];
            $comment->comment_text = $row['comment_text'];
            $comment->time_stamp = new DateTime($row['time_stamp']);
            $comment->user_username = $row['user_username'];
            $comment->user_pp_path = $row['user_pp_path'];

            $commentsArray[] = $comment;
        }

        return $commentsArray;
    }
}
