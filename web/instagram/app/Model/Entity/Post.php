<?php

namespace App\Model\Entity;

use DateTime;

class Post
{
    public string $user_username;
    public int $instance_id;
    public int $nb_likes;
    public int $nb_views;
    public int $nb_comments;
    public DateTime $time_stamp;
    public string $post_picture;
    public string $post_description;
    public string $post_location;
}
