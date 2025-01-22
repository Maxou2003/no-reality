<?php

namespace App\Lib;

class DatabaseConnection
{
    public ?\PDO $database = null;

    public function getConnection()
    {
        if ($this->database === null) {
            $host = 'localhost';
            $user = 'root';
            $pass = '';
            $port = '3306';
            $db = 'nr_instagram';
            $this->database = new \PDO('mysql:host=' . $host . ';port=' . $port . ';dbname=' . $db . ";charset=utf8", $user, $pass);
        }
        return $this->database;
    }
}
