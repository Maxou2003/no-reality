<?php

namespace App\Model\Entity;

use DateTime;

use JsonSerializable;

class Post implements JsonSerializable
{
    public int $post_id;
    public int $instance_id;
    public string $post_description;
    public int $user_id;
    public string $user_username;
    public string $user_pp_path;
    public string $post_picture_path;
    public DateTime $time_stamp;
    public int $nb_comments;
    public int $nb_likes;
    public int $nb_shares;


    public function jsonSerialize(): mixed
    {
        return [
            "post_id" => $this->post_id,
            "instance_id" => $this->instance_id,
            "post_description" => $this->post_description,
            "user_id" => $this->user_id,
            "username" => $this->user_username,
            "user_pp_path" => $this->user_pp_path,
            "post_picture_path" => $this->post_picture_path,
            "time_stamp" => $this->time_stamp->format('Y-m-d H:i'),
            "nb_comments" => $this->nb_comments,
            "nb_likes" => $this->nb_likes,
            "nb_shares" => $this->nb_shares,
        ];
    }
}
