<?php

namespace App\Model\Entity;

use JsonSerializable;
use DateTime;

class Comment implements JsonSerializable
{
    public int $comment_id;
    public string $user_id;
    public int $post_id;
    public string $comment_text;
    public DateTime $time_stamp;
    public string $user_firstname;
    public string $user_lastname;
    public string $user_pp_path;
    public int $nb_responses;

    public function jsonSerialize(): mixed
    {
        return [
            'comment_id' => $this->comment_id,
            'user_id' => $this->user_id,
            'user_firstname' => $this->user_firstname,
            'user_lastname' => $this->user_lastname,
            'user_profile_picture' => $this->user_pp_path,
            'post_id' => $this->post_id,
            'comment_text' => $this->comment_text,
            'time_stamp' => $this->time_stamp->format('Y-m-d H:i'),
            'nb_responses' => $this->nb_responses,
        ];
    }
}
