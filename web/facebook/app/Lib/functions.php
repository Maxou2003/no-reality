<?php

use App\Lib\DatabaseConnection;
use App\Model\InstanceRepository;

function instance_exist($instanceId)
{
    $InstanceRepository = new InstanceRepository();
    $database = new DatabaseConnection();
    $InstanceRepository->connection = $database;

    return $InstanceRepository->checkInstanceId($instanceId);
}

function get_instanceId($instance_name)
{
    $InstanceRepository = new InstanceRepository();
    $database = new DatabaseConnection();
    $InstanceRepository->connection = $database;

    $instanceId = $InstanceRepository->getInstanceIdByName($instance_name);

    return $instanceId;
}
