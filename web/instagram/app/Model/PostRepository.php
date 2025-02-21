<?php

namespace App\Model;

use DateTime;
use App\Model\Entity\Post;
use App\Model\Entity\Comment;
use App\Model\Entity\Response;
use App\Lib\DatabaseConnection;

class PostRepository
{
    public DatabaseConnection $connection;

    public function getPost($limit, $offset = 0): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT user_username,u.user_id, instance_id,user_pp_path, nb_likes, nb_views, time_stamp, post_picture_path,post_description,post_location,nb_comments, post_id FROM posts p join users u on p.user_id=u.user_id ORDER BY time_stamp DESC LIMIT :limit OFFSET :offset'
        );
        $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);
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

    public function getPostByPostId($post_id): Post
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT user_username,u.user_id, instance_id,user_pp_path, nb_likes, nb_views, time_stamp, post_picture_path,post_description,post_location,nb_comments, post_id FROM posts p join users u on p.user_id=u.user_id  where p.post_id =:post_id '
        );
        $statement->bindValue(':post_id', $post_id, \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

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

        return $post;
    }

    public function fetchComments($post_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT c.comment_id,c.nb_responses , c.user_id, c.post_id, c.comment_text, c.time_stamp, u.user_username, u.user_pp_path FROM comments c join users u on c.user_id=u.user_id WHERE c.post_id = :post_id ORDER BY c.time_stamp DESC'
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
            $comment->nb_responses = $row['nb_responses'];

            $commentsArray[] = $comment;
        }

        return $commentsArray;
    }
    public function fetchResponses($comment_id)
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT r.response_id,r.comment_id,r.user_id,r.content,r.time_stamp, u.user_username, u.user_pp_path FROM response r JOIN users u ON u.user_id=r.user_id WHERE comment_id = :comment_id ORDER BY time_stamp ASC'
        );
        $statement->bindValue(':comment_id', $comment_id, \PDO::PARAM_INT);
        $statement->execute();

        $responseArray = [];
        while (($row = $statement->fetch())) {
            $response = new Response();
            $response->comment_id = $row['comment_id'];
            $response->user_id = $row['user_id'];
            $response->response_id = $row['response_id'];
            $response->content = $row['content'];
            $response->time_stamp = new DateTime($row['time_stamp']);
            $response->user_username = $row['user_username'];
            $response->user_pp_path = $row['user_pp_path'];

            $responseArray[] = $response;
        }

        return $responseArray;
    }
    public function getPostIdentifications($user_id, $choice): array
    {
        $query = '';
        if ($choice == 'identification') {

            $query = 'SELECT p.post_id, p.post_picture_path from identification i join posts p on i.post_id = p.post_id where i.user_id = :user_id ORDER BY time_stamp DESC';
        } elseif ($choice == 'post') {
            $query = 'SELECT post_id, post_picture_path from posts where user_id = :user_id ORDER BY time_stamp DESC';
        } else {
            return ['error' => 'Choice undifined'];
        }
        $statement = $this->connection->getConnection()->prepare($query);
        $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $statement->execute();
        $postIdentificationArray = [];
        while (($row = $statement->fetch())) {
            $tab['post_id'] =  $row['post_id'];
            $tab['post_picture_path'] =  $row['post_picture_path'];
            $postIdentificationArray[] = $tab;
        }
        return $postIdentificationArray;
    }
    public function getTaggedUsers($post_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT user_username FROM identification i Join users u on i.user_id=u.user_id Where i.post_id=:post_id '
        );
        $statement->bindValue(':post_id', $post_id, \PDO::PARAM_INT);
        $statement->execute();
        $taggedUsers = [];
        while (($row = $statement->fetch())) {
            $taggedUsers[] = $row['user_username'];
        }
        return $taggedUsers;
    }
}
