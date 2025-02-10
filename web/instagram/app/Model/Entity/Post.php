<?php

namespace App\Model\Entity;

use DateTime;

use JsonSerializable;

class Post implements JsonSerializable
{
    public string $user_username;
    public int $user_id;
    public int $instance_id;
    public string $user_pp_path;
    public int $nb_likes;
    public int $nb_views;
    public int $nb_comments;
    public DateTime $time_stamp;
    public string $post_picture_path;
    public string $post_description;
    public string $post_location;
    public int $post_id;

    public function jsonSerialize(): mixed
    {
        return [
            "post_id" => $this->post_id,
            "username" => $this->user_username,
            "user_id" => $this->user_id,
            "instance_id" => $this->instance_id,
            "user_pp_path" => $this->user_pp_path,
            "nb_likes" => $this->nb_likes,
            "nb_views" => $this->nb_views,
            "nb_comments" => $this->nb_comments,
            "time_stamp" => $this->time_stamp,
            "post_picture_path" => $this->post_picture_path,
            "post_description" => $this->post_description,
            "post_location" => $this->post_location,
        ];
    }
}
