<?php
require_once __DIR__ . "/model.php";
require_once __DIR__ . "/../utils/customException/index.php";

class RepresentativeModel extends Model {
    public function __construct() {
        try {
            parent::__construct();
            $sql = "CREATE TABLE IF NOT EXISTS representatives (
                    project_id CHAR(36) NOT NULL,
                    user_id CHAR(36) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (project_id, user_id),
                    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
                );";
            $this->db->query($sql);
        } catch (PDOException $error) {
            throw new CustomException(500, "Error creating representatives table");
        }
    }

    public function createRepresentative($project_id, $user_id) {
        try {
            $sql = "INSERT INTO representatives (project_id, user_id) VALUES (:project_id, :user_id)";
            $stm = $this->db->prepare($sql);
            $stm->execute([
                ":project_id" => $project_id,
                ":user_id" => $user_id
            ]);
            return true;
        } catch (PDOException $error) {
            throw new CustomException(500, "Error creating representative");
        }
    }

    public function deleteRepresentative($project_id, $user_id) {
        try {
            $sql = "DELETE FROM representatives WHERE project_id = :project_id AND user_id = :user_id";
            $stm = $this->db->prepare($sql);
            $stm->execute([
                ":project_id" => $project_id,
                ":user_id" => $user_id
            ]);
            return true;
        } catch (PDOException $error) {
            throw new CustomException(500, "Error removing representative");
        }
    }

    public function isUserRepresentative($project_id, $user_id) {
        try {
            $sql = "SELECT * FROM representatives 
                    WHERE project_id = :project_id 
                    AND user_id = :user_id";
                    
            $stm = $this->db->prepare($sql);
            $stm->execute([
                ":project_id" => $project_id,
                ":user_id" => $user_id
            ]);
            
            return $stm->fetch(PDO::FETCH_ASSOC) !== false;

            
        } catch (PDOException $error) {
            throw new CustomException(
                500,
                "Error checking representative status: " . $error->getMessage(),
                null,
                false
            );
        }
    }
}