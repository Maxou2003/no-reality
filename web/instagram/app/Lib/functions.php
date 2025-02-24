<?php

use App\Lib\DatabaseConnection;
use App\Model\UserRepository;

function instance_exist($instanceId)
{
    $UserRepository = new UserRepository();
    $database = new DatabaseConnection();
    $UserRepository->connection = $database;

    return $UserRepository->checkInstanceId($instanceId);
}
