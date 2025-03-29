<?php

require_once __DIR__ . "/../utils/customException/index.php";

class Model
{
    protected $db;
    public function __construct()
    {
        try {
            $host = "localhost";
            $username = "root";
            $password = "";
            $dbname = "project";
            $this->db = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $username,
                $password
            );
            $this->db->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            $this->db->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );
        } catch (PDOException $e) {
            throw new CustomException(
                500,
                "Error: connection to db failed",
                null,
                false
            );
        }
    }

}
