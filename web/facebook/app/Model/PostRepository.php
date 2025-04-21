<?php

namespace App\Model;

use DateTime;
use App\Model\Entity\Post;
use App\Model\Entity\User;
use App\Model\Entity\Comment;
use App\Lib\DatabaseConnection;
use App\Model\Entity\Response;

class PostRepository
{
    public DatabaseConnection $connection;

    public function getPosts($limit, $offset): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, instance_id, user_firstname, user_lastname, post_content, u.user_id, u.user_slug, user_pp_path, 
            post_picture_path, time_stamp, nb_comments, nb_likes, nb_shares 
            FROM posts p join users u on p.user_id=u.user_id Where instance_id=:instance_id ORDER BY time_stamp DESC LIMIT :limit OFFSET :offset'
        );
        $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
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
            $post->time_stamp = new DateTime($row['time_stamp']);
            $post->post_picture_path = $row['post_picture_path'];
            $post->post_content = $row['post_content'];
            $post->nb_comments = $row['nb_comments'];
            $post->nb_shares = $row['nb_shares'];

            $postArray[] = $post;
        }
        return $postArray;
    }
    public function getPhotos($userId): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_picture_path FROM posts WHERE user_id=:user_id AND instance_id=:instance_id'
        );
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $photos = [];
        while (($row = $statement->fetch())) {
            $photos[] = $row['post_picture_path'];
        }
        return $photos;
    }

    public function getTaggedPhotos($userId): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_picture_path FROM posts WHERE instance_id=:instance_id AND post_id IN (
            SELECT post_id FROM identifications WHERE user_id=:user_id)'
        );
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();

        $photos = [];
        while (($row = $statement->fetch())) {
            $photos[] = $row['post_picture_path'];
        }
        return $photos;
    }

    public function searchPosts($search, $limit, $offset)
    {

        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, instance_id, user_firstname, user_lastname, post_content, u.user_id, u.user_slug, user_pp_path, post_picture_path, time_stamp, nb_comments, nb_likes, nb_shares 
             FROM posts p join users u on p.user_id=u.user_id
             WHERE u.user_id in (
                SELECT ul.user_id 
                FROM userlinkinstance ul
                WHERE ul.instance_id=:instance_id
                ) and (
               u.user_firstname LIKE :search OR u.user_lastname LIKE :search or p.post_content LIKE :search
                ) LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindValue(':search', '%' . $search . '%', \PDO::PARAM_STR);
        $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
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
            $post->time_stamp = new DateTime($row['time_stamp']);
            $post->post_picture_path = $row['post_picture_path'];
            $post->post_content = $row['post_content'];
            $post->nb_comments = $row['nb_comments'];
            $post->nb_shares = $row['nb_shares'];

            $postArray[] = $post;
        }
        return $postArray;
    }

    public function getPostById($post_id): Post
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT post_id, instance_id, user_firstname, user_lastname, post_content, u.user_id, u.user_slug, user_pp_path, post_picture_path, time_stamp, nb_comments, nb_likes, nb_shares 
             FROM posts p join users u on p.user_id=u.user_id
             WHERE instance_id =:instance_id and post_id = :post_id'
        );
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->bindParam(':post_id', $post_id, \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();

        $post = new Post();
        $post->post_id = $row['post_id'];
        $post->user_firstname = $row['user_firstname'];
        $post->user_lastname = $row['user_lastname'];
        $post->user_id = $row['user_id'];
        $post->user_slug = $row['user_slug'];
        $post->instance_id = $row['instance_id'];
        $post->user_pp_path = $row['user_pp_path'];
        $post->nb_likes = $row['nb_likes'];
        $post->time_stamp = new DateTime($row['time_stamp']);
        $post->post_picture_path = $row['post_picture_path'];
        $post->post_content = $row['post_content'];
        $post->nb_comments = $row['nb_comments'];
        $post->nb_shares = $row['nb_shares'];

        return $post;
    }

    public function getPostComments($post_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT comment_id, u.user_id, post_id, comment_text, time_stamp, u.user_slug, u.user_firstname, u.user_lastname, u.user_pp_path, nb_responses 
             FROM comments c join users u on c.user_id=u.user_id
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
            $comment->user_id = $row['user_id'];
            $comment->post_id = $row['post_id'];
            $comment->comment_text = $row['comment_text'];
            $comment->nb_responses = $row['nb_responses'];
            $comment->user_pp_path = $row['user_pp_path'];
            $comment->user_slug = $row['user_slug'];
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
                SELECT user_id FROM identifications 
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
            'SELECT comment_id,response_id,u.user_id, u.user_slug, response_content,time_stamp,u.user_firstname,u.user_lastname,u.user_pp_path FROM responses r
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
            $response->user_pp_path = $row['user_pp_path'];
            $response->user_slug = $row['user_slug'];

            $responseArray[] = $response;
        }
        return $responseArray;
    }
}
