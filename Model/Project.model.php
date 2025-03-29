<?php

require_once __DIR__ . "/model.php";
require_once __DIR__ . "/../utils/customException/index.php";

class ProjectModel extends Model
{
    public function __construct()
    {
        try {
            parent::__construct();
            $sql = "CREATE TABLE IF NOT EXISTS projects (
              project_id CHAR(36) PRIMARY KEY DEFAULT(UUID()),
              name VARCHAR(100) NOT NULL,
              description TEXT NOT NULL,
              date_debut DATE NOT NULL,
              date_fin DATE NOT NULL,
              owner_id VARCHAR(36) NOT NULL,
              progress INT DEFAULT 0,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE CASCADE
              );";

            $this->db->query($sql);
        } catch (PDOException $error) {
            echo json_encode($error);
            throw new CustomException(500, "Error: creating table projects.", null, false);
        }
    }


    public function createProject($projectData)
    {
        try {
            $sql = "INSERT INTO projects(name, description, owner_id, date_debut,
                    date_fin) VALUES(:name, :description, :owner_id,
                    :date_debut, :date_fin)";
            $stm = $this->db->prepare($sql);
            $success = $stm->execute([
                                      ":name" => $projectData["name"],
                                      ":description" => $projectData["description"],
                                      ":owner_id" => $projectData["owner_id"],
                                      ":date_debut" => $projectData["date_debut"],
                                      ":date_fin" => $projectData["date_fin"]
                                      ]);
            if (!$success) {
                throw new CustomException(
                    400,
                    "Error: creating project with the name " . $projectData["name"],
                    null,
                    false
                );
            }

            return $stm->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            if ($error instanceof CustomException) {
                throw $error;
            } else {
                throw new CustomException(
                    500,
                    "Error: try later",
                    null,
                    false
                );
            }
        }
    }



    public function projectsList($user_id, $role)
    {
        try {

            $sql = "";
            if ($role == "PROFESSEUR") {
                $sql .= "SELECT * FROM projects WHERE owner_id = :user_id";

            } elseif ($role === "STUDENT") {
                $sql .= "SELECT p.* FROM projects p JOIN collaborators c ON
                          p.project_id = c.project_id WHERE c.user_id = :user_id";
            }

            $stm = $this->db->prepare($sql);
            $stm->execute([":user_id" => $user_id]);
            return $stm->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $error) {
            throw new CustomException(404, "Error: fetching user projects", null, false);
        }
    }


    public function deleteProject($project_id, $owner_id)
    {
        try {
            $sql = "DELETE FROM projects WHERE project_id = :project_id AND owner_id = :user_id";
            $stm = $this->db->prepare($sql);
            $stm->execute([
            ':project_id' => $project_id,
            ":owner_id" => $owner_id
            ]);
        } catch (PDOException) {
        }
    }

    public function getProject($project_id, $user_id)
    {
        try {
            $sql = "SELECT * FROM projects WHERE project_id = :project_id AND
                    owner_id = :owner_id";
            $stm = $this->db->prepare($sql);

            $stm->execute([
            ":project_id" => $project_id,
            ":owner_id" => $user_id
            ]);

            return $stm->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $error) {
            throw new CustomException(
                500,
                "Error: selecting project.",
                null,
                false
            );
        }
    }
    public function findById($project_id)
    {
        try {
            $sql  = "SELECT * FROM projects WHERE project_id = :project_id ";
            $stm = $this->db->prepare($sql);

            $stm->execute([":project_id" => $project_id]);
            return $stm->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            echo $error;
            throw new CustomException(
                500,
                "Error: finding prject by project Id",
                null,
                false
            );
        }
    }

    public function updateProjectProgress($project_id)
    {
        try {
            // Get total tasks in the project
            $sqlTotal = "SELECT COUNT(*) as total FROM tasks WHERE project_id = :project_id";
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute([':project_id' => $project_id]);
            $totalResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
            $total = $totalResult ? $totalResult['total'] : 0;

            // Get tasks that are completed (status = 'done')
            $sqlDone = "SELECT COUNT(*) as done FROM tasks WHERE project_id = :project_id AND status = 'done'";
            $stmtDone = $this->db->prepare($sqlDone);
            $stmtDone->execute([':project_id' => $project_id]);
            $doneResult = $stmtDone->fetch(PDO::FETCH_ASSOC);
            $done = $doneResult ? $doneResult['done'] : 0;

            // Calculate progress percentage
            $progress = $total > 0 ? round(($done / $total) * 100, 2) : 0;

            // Update the projects table
            $sqlUpdate = "UPDATE projects SET progress = :progress WHERE project_id = :project_id";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':progress' => $progress,
                ':project_id' => $project_id
            ]);

            return $progress;
        } catch (PDOException $error) {
            throw new CustomException(500, "Error updating project progress: " . $error->getMessage(), null, false);
        }
    }

    public function getAllProjects() {
        try {
            $sql = "SELECT * FROM projects";
            $stm = $this->db->query($sql);
            return $stm->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            throw new CustomException(500, "Error fetching all projects", null, false);
        }
    }
}
