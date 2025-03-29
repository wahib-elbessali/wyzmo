<?php

require_once __DIR__ . "/model.php";
require_once __DIR__ . "/../utils/customException/index.php";

class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $sql = "CREATE TABLE IF NOT EXISTS users (
              user_id VARCHAR(36) PRIMARY KEY DEFAULT(UUID()),
              first_name VARCHAR(50) NOT NULL,
              last_name VARCHAR(50) NOT NULL,
              password VARCHAR(255) NOT NULL,
              email VARCHAR(255) UNIQUE NOT NULL,
              role ENUM('ADMIN' ,'STUDENT','PROFESSEUR') NOT NULL,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
              );";
        $this->db->query($sql);
    }

    public function findById(string $id)
    {
        try {

            $sql = "SELECT * FROM users WHERE user_id = :user_id";
            $stm = $this->db->prepare($sql);
            $stm->execute(["user_id" => $id]);
            return $stm->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new CustomException(
                500,
                "Error: error in finding a user with this id: " . $id,
                null,
                false
            );
        }

    }

    public function createUser(array $userData)
    {
        try {
            $sql = "INSERT INTO users (first_name, last_name, email, password, role)
                VALUES (:first_name, :last_name, :email, :password, :role)";
            $stm = $this->db->prepare($sql);

            $success = $stm->execute([
                          ":first_name" => $userData["first_name"],
                          ":last_name" =>  $userData["last_name"],
                          ":email" =>  $userData["email"],
                          ":password" => $userData["password"],
                          ":role" => $userData["role"],
                          ]);

            if (!$success) {
                throw new CustomException(
                    500,
                    "Error: cannot create email: " . $userData["email"],
                    null,
                    false
                );
            }

        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                throw new CustomException(
                    409,
                    "Error: account with this email already exist.",
                    null,
                    false
                );
            } else {
                throw new CustomException(
                    500,
                    "Error: creating user with email " . $userData['email'],
                    null,
                    false
                );
            }
        }
    }

    public function findByEmail(string $email)
    {
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stm = $this->db->prepare($sql);
            $stm->execute(["email" => $email]);
            return $stm->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $error) {
            throw new CustomException(500, "Please try again", null, false);
        }
    }

    public function searchByEmail($email)
    {
        try {
            $sql = "SElECT user_id, first_name, last_name, email FROM users
                    WHERE role = 'STUDENT' AND email LIKE :email LIMIT 6";
            $stm = $this->db->prepare($sql);
            $stm->execute([":email" => "%".$email."%"]);
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        } catch (CustomException $error) {
            throw new CustomException(
                500,
                "Error: Search for collaborators",
                null,
                false
            );
        }
    }
}
