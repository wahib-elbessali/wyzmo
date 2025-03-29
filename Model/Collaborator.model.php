<?php

require_once __DIR__ . "/model.php";
require_once __DIR__ . "/../utils/customException/index.php";

class CollaboratorModel extends Model
{
    public function __construct()
    {
        try {
            parent::__construct();
            $sql = "CREATE TABLE IF NOT EXISTS collaborators (
                    collaborator_id CHAR(36) PRIMARY KEY DEFAULT(UUID()),
                    project_id CHAR(36) NOT NULL,
                    user_id CHAR(36) NOT NULL, 
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    CONSTRAINT collaborators_ibfk_1 FOREIGN KEY (user_id)
                    REFERENCES users(user_id), CONSTRAINT collaborators_ibfk_2 FOREIGN KEY
                    (project_id) REFERENCES projects(project_id)
                    );";
            $this->db->query($sql);

        } catch (PDOException $error) {
            echo $error;
            throw new CustomException(
                500,
                "Error: creating table collaborators.",
                null,
                false
            );
        }
    }

    public function createCollaborator($user_id, $project_id)
    {
        try {
            $sql = "INSERT INTO collaborators (project_id, user_id) VALUES
                    (:project_id, :user_id)";

            $stm = $this->db->prepare($sql);

            $stm->execute([
              ":project_id" => $project_id,
              ":user_id" => $user_id
            ]);

            return $stm->fetch(PDO::FETCH_ASSOC) ;
        } catch (PDOException $error) {
            echo $error;
            throw new CustomException(
                500,
                "Error: Creating collaborator with id " .$user_id,
                null,
                false
            );
        }
    }

    public function findById($collaborator_id)
    {
        try {
            $sql = "SELECT * FROM collaborators WHERE collaborator_id = :collaborator_id";
            $stm = $this->db->prepare($sql);
            $stm->execute([":collaborator_id" => $collaborator_id]);
            return $stm->fetch(PDO::FETCH_ASSOC);
        } catch (CustomException $error) {
            throw new CustomException(
                500,
                "Error: finding a collaborator by id",
                null,
                false
            );
        }
    }

    public function collaboratorsList($project_id) {
        try {
            $sql = "SELECT c.*, u.first_name, u.last_name, u.email, 
                    (SELECT COUNT(*) FROM representatives 
                     WHERE representatives.user_id = c.user_id 
                     AND representatives.project_id = c.project_id) as is_representative
                    FROM collaborators c
                    JOIN users u ON u.user_id = c.user_id
                    WHERE c.project_id = :project_id";
            $stm = $this->db->prepare($sql);
            $stm->execute([":project_id" => $project_id]);
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            throw new CustomException(
                500,
                "Error fetching collaborators with representative status",
                null,
                false
            );
        }
    }

    public function getCollaborator($user_id, $project_id)
    {
        try {
            $sql = "SELECT * FROM collaborators WHERE project_id = :project_id AND user_id = :user_id";
            $stm = $this->db->prepare($sql);
            $stm->execute([
                ":project_id" => $project_id,
                ":user_id" => $user_id
            ]);
            return $stm->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            throw new CustomException(
                500, 
                "Error finding collaborator",
                null, 
                false 
            );
        }

    }

    public function getCollaboratorId($user_id, $project_id) {
        try {
            $sql = "SELECT collaborator_id FROM collaborators 
                    WHERE user_id = :user_id AND project_id = :project_id";
            $stm = $this->db->prepare($sql);
            $stm->execute([
                ":user_id" => $user_id,
                ":project_id" => $project_id
            ]);
            $result = $stm->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['collaborator_id'] : null;
        } catch (PDOException $error) {
            throw new CustomException(
                500,
                "Error getting collaborator ID: " . $error->getMessage(),
                null,
                false
            );
        }
    }
    

}