<?php

namespace App\Model\Entity;

use DateTime;

use JsonSerializable;

class Group implements JsonSerializable
{
    public int $group_id;
    public string $group_name;
    public string $group_slug;
    public DateTime $time_stamp;
    public string $group_banner_picture_path;
    public string $group_description;
    public string $nb_members;
    public string $instance_id;
    public string $group_location;


    public function jsonSerialize(): mixed
    {
        return [
            "group_id" => $this->group_id,
            "group_name" => $this->group_name,
            "group_slug" => $this->group_slug,
            "time_stamp" => $this->time_stamp->format('Y-m-d H:i'),
            "group_banner_picture_path" => $this->group_banner_picture_path,
            "group_description" => $this->group_description,
            "nb_members" => $this->nb_members,
            "instance_id" => $this->instance_id,
            "group_location" => $this->group_location,
        ];
    }
}
