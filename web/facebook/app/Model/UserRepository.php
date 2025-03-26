<?php

namespace App\Model;

use App\Lib\DatabaseConnection;
use App\Model\Entity\User;

class UserRepository
{
    public DatabaseConnection $connection;
    public function getSuggestions(): array
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT DISTINCT user_id, user_firstname, user_lastname, user_description, user_pp_path, user_location, user_work, user_school, user_banner_picture_path FROM users WHERE user_id in (
            SELECT user_id 
        FROM userlinkinstance 
        WHERE instance_id =:instance_id

) LIMIT 0,6;'
        );
        $statement->bindValue(':instance_id', $_SESSION['instanceId'], \PDO::PARAM_INT);
        $statement->execute();
        $users = [];
        while (($row = $statement->fetch())) {
            $user = new User();
            $user->user_id = $row['user_id'];
            $user->user_pp_path = $row['user_pp_path'];
            $user->user_firstname = $row['user_firstname'];
            $user->user_lastname = $row['user_lastname'];
            $user->user_description = $row['user_description'];
            $user->user_location = $row['user_location'];
            $user->user_work = $row['user_work'];
            $user->user_school = $row['user_school'];
            $user->user_banner_picture_path = $row['user_banner_picture_path'];

            $users[] = $user;
        }

        return $users;
    }
}
