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
    public string $user_slug;
    public string $user_location;
    public string $user_work;
    public string $user_school;
    public int $user_gender;
    public string $user_website;
    public string $user_yob;
    public string $user_banner_picture_path;

    public function jsonSerialize(): mixed
    {
        return [
            'user_id' => $this->user_id,
            'user_pp_path' => $this->user_pp_path,
            'user_firstname' => $this->user_firstname,
            'user_lastname' => $this->user_lastname,
            'user_description' => $this->user_description,
            'user_slug' => $this->user_slug,
            'user_location' => $this->user_location,
            'user_work' => $this->user_work,
            'user_school' => $this->user_school,
            'user_gender' => $this->user_gender,
            'user_website' => $this->user_website,
            'user_yob' => $this->user_yob,
            'user_banner_picture_path' => $this->user_banner_picture_path,
        ];
    }
}
