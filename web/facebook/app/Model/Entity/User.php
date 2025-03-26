<?php

namespace App\Model\Entity;

use JsonSerializable;

class User implements JsonSerializable
{
    public int $user_id;
    public string $user_firstname;
    public string $user_lastname;
    public string $user_pp_path;
    public string $user_description;
    public string $user_location;
    public string $user_work;
    public string $user_school;
    public string $user_banner_picture_path;

    public function jsonSerialize(): mixed
    {
        return [
            'user_id' => $this->user_id,
            'username' => $this->user_username,
            'profile_picture' => $this->user_pp_path,
            'firstname' => $this->user_firstname,
            'lastname' => $this->user_lastname,
            'description' => $this->user_description,
            'location' => $this->user_location,
            'work' => $this->user_work,
            'school' => $this->user_school,
            'banner_picture' => $this->user_banner_picture_path,
        ];
    }
}
