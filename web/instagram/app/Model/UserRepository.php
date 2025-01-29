<?php

namespace App\Model;

use App\Lib\DatabaseConnection;
use App\Model\Entity\User;

class UserRepository
{
    public DatabaseConnection $connection;

    public function fetchFollowers($user_id): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT follower_id, user_firstname, user_lastname, user_description, user_username, user_pp_path FROM users u join subscription s on u.user_id=f.followed_id where f.user_id=:user_id'
        );
        $statement->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
        $statement->execute();

        $followersArray = [];
        while (($row = $statement->fetch())) {
            $follower = new User();
            $follower->user_id = $row['user_id'];
            $follower->user_username = $row['user_username'];
            $follower->user_pp_path = $row['user_pp_path'];
            $follower->user_firstname = $row['user_firstname'];
            $follower->user_lastname = $row['user_lastname'];
            $follower->user_description = $row['user_description'];

            $followersArray[] = $follower;
        }

        return $followersArray;
    }
}
