<?php

namespace App\Model\Entity;

use DateTime;

use JsonSerializable;

class GroupPost implements JsonSerializable
{
    public int $post_id;
    public int $instance_id;
    public string $post_content;
    public int $user_id;
    public string $user_firstname;
    public string $user_lastname;
    public string $user_pp_path;
    public string $user_slug;
    public string $post_picture_path;
    public DateTime $time_stamp;
    public int $nb_comments;
    public int $nb_likes;


    public function jsonSerialize(): mixed
    {
        return [
            "post_id" => $this->post_id,
            "instance_id" => $this->instance_id,
            "post_description" => $this->post_content,
            "user_id" => $this->user_id,
            "firstname" => $this->user_firstname,
            "lastname" => $this->user_lastname,
            "user_pp_path" => $this->user_pp_path,
            "user_slug" => $this->user_slug,
            "post_picture_path" => $this->post_picture_path,
            "time_stamp" => $this->time_stamp->format('Y-m-d H:i'),
            "nb_comments" => $this->nb_comments,
            "nb_likes" => $this->nb_likes,
        ];
    }
}
