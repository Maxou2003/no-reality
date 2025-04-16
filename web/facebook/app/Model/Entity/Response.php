<?php

namespace App\Model\Entity;

use JsonSerializable;
use DateTime;


class Response implements JsonSerializable
{
    public int $comment_id;
    public string $response_id;
    public int $user_id;
    public string $content;
    public DateTime $time_stamp;
    public string $user_firstname;
    public string $user_lastname;
    public string $user_pp_path;

    public function jsonSerialize(): mixed
    {
        return [
            'comment_id' => $this->comment_id,
            'user_id' => $this->user_id,
            'user_firstname' => $this->user_firstname,
            'user_lastname' => $this->user_lastname,
            'user_profile_picture' => $this->user_pp_path,
            'response_id' => $this->response_id,
            'content' => $this->content,
            'time_stamp' => $this->time_stamp->format('Y-m-d H:i'),
        ];
    }
}
