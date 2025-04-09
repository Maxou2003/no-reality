<?php

namespace App\Model\Entity;

use DateTime;

use JsonSerializable;

class Group implements JsonSerializable
{
    public int $group_id;
    public string $group_name;
    public DateTime $time_stamp;
    public string $group_banner_picture_path;
    public string $group_description;
    public string $nb_members;


    public function jsonSerialize(): mixed
    {
        return [
            "group_id" => $this->group_id,
            "group_name" => $this->group_name,
            "time_stamp" => $this->time_stamp->format('Y-m-d H:i'),
            "group_banner_picture_path" => $this->group_banner_picture_path,
            "group_description" => $this->group_description,
            "nb_members" => $this->nb_members,
        ];
    }
}
