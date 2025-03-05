<?php

namespace App\Model;

use App\Lib\DatabaseConnection;


class InstanceRepository
{
    public DatabaseConnection $connection;

    public function checkInstanceId($instanceId): bool
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT instance_id FROM instance WHERE instance_id = :instance_id'
        );
        $statement->bindValue(':instance_id', $instanceId, \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch();
        if ($row === false) {
            return false;
        } else {
            return true;
        }
    }

    public function getInstanceIdByName($name)
    {
        $statement = $this->connection->getConnection()->prepare(
            'SELECT instance_id FROM instance WHERE instance_name = :instance_name'
        );
        $statement->bindValue(':instance_name', $name, \PDO::PARAM_STR_CHAR);
        $statement->execute();

        return $statement->fetch()['instance_id'];
    }
}
