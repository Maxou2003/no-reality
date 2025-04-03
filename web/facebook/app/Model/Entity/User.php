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
    public string $user_yob; // year of birth
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
            'slug' => $this->user_slug,
            'location' => $this->user_location,
            'work' => $this->user_work,
            'school' => $this->user_school,
            'gender' => $this->user_gender,
            'website' => $this->user_website,
            'yob' => $this->user_yob,
            'banner_picture' => $this->user_banner_picture_path,
        ];
    }
}
