<?php

require_once __DIR__ . "/model.php";
require_once __DIR__ . "/../utils/customException/index.php";

class TaskModel extends Model
{
    public function __construct()
    {
        try {
            parent::__construct();
            $sql = "CREATE TABLE IF NOT EXISTS tasks (
                    task_id CHAR(36) PRIMARY KEY DEFAULT(UUID()),  
                    name VARCHAR(255) NOT NULL,                 
                    description TEXT NOT NULL,                           
                    project_id CHAR(36) NOT NULL,                  
                    assigned_to CHAR(36),                         
                    status ENUM('todo', 'in_progress', 'done') DEFAULT 'todo',
                    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',   
                    start_date DATE NOT NULL,
                    end_date DATE NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
                    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
                    FOREIGN KEY (assigned_to) REFERENCES collaborators(collaborator_id) ON DELETE SET NULL
                    );";

            $this->db->query($sql);
        } catch (PDOException $error) {

            throw new CustomException(
                500,
                "Error: creating table collaborators.",
                null,
                false
            );
        }
    }


    public function createTaks($taskData)
    {
        try {
            $sql = "INSERT INTO tasks(name, description, project_id,
                    assigned_to, status, priority, start_date, end_date)
                    VALUES(:name, :description, :project_id, :assigned_to,
                    :status, :priority, :start_date, :end_date)";
            $stm = $this->db->prepare($sql);

            $stm->execute([
                  ":name"  => $taskData["name"],
                  ":description"  => $taskData["description"],
                  ":project_id"  => $taskData["project_id"],
                  ":assigned_to"  => $taskData["assigned_to"],
                  ":status" => $taskData["status"],
                  ":priority" => $taskData["priority"],
                  ":start_date"  => $taskData["start_date"],
                  ":end_date"  => $taskData["end_date"],
            ]);

            $sql = "SELECT * FROM tasks WHERE project_id = :project_id AND
                    assigned_to = :assigned_to";

            $stm = $this->db->prepare($sql);
            $stm->execute(
                [
                  ":project_id" => $taskData["project_id"],
                  ":assigned_to" => $taskData["assigned_to"]
                ]
            );

            return $stm->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $error) {
            echo $error;
            throw new CustomException(500, "Error: in creating task.", null, false);
        }

    }


    public function getTaskList($user_id, $project_id, $role)
    {
        try {
            $sql = "";
            if ($role == "STUDENT") {
                $sql = "SELECT * FROM tasks WHERE assigned_to = :user_id AND project_id = :project_id";
            } elseif ($role == "PROFESSEUR") {
                $sql .= "SELECT t.*, u.user_id, u.email FROM users u, tasks t WHERE t.assigned_to = u.user_id AND t.project_id = :project_id ";
            }

        } catch (PDOException $error) {
            throw new CustomException(
                500,
                "Error: get task list.",
                null,
                false
            );
        }

    }

    public function findById($taskId)
    {
        try {
            $sql = "SELECT * FROM tasks WHERE task_id = :task_id ";
            $stm = $this->db->prepare($sql);
            $stm->execute([
            ":task_id" => $taskId
            ]);
            return $stm->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            throw new CustomException(
                500,
                "Error: select task by id",
                null,
                false
            );
        }
    }
    public function updateTaskStatus($task_id, $newTaskStatusValue)
{
    try {
        $sql = "UPDATE tasks SET status = :status WHERE task_id = :task_id";
        $stm = $this->db->prepare($sql);
        $stm->execute([
            ":status" => $newTaskStatusValue,
            ":task_id" => $task_id
        ]);
        return true;
    } catch (PDOException $error) {
        throw new CustomException(
            500,
            "Error updating task status: " . $error->getMessage(),
            null,
            false
        );
    }
}

public function getTasksByProject($project_id, $user_id = null, $role = null) {
    try {
        $sql = "SELECT t.*, u.first_name, u.last_name 
                FROM tasks t
                LEFT JOIN collaborators c ON t.assigned_to = c.collaborator_id
                LEFT JOIN users u ON c.user_id = u.user_id
                WHERE t.project_id = :project_id";

        if ($role === 'STUDENT' && !is_null($user_id)) {
            $sql .= " AND c.user_id = :user_id";
        }

        $stm = $this->db->prepare($sql);
        $params = [':project_id' => $project_id];
        
        if ($role === 'STUDENT' && !is_null($user_id)) {
            $params[':user_id'] = $user_id;
        }

        $stm->execute($params);
        return $stm->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $error) {
        throw new CustomException(
            500, 
            "Error fetching tasks: " . $error->getMessage(),
            null,
            false
        );
    }
}

public function getCollaboratorIdByUserAndTask($user_id, $task_id)
{
    try {
        $sql = "SELECT c.collaborator_id 
                FROM collaborators c
                JOIN tasks t ON t.assigned_to = c.collaborator_id
                WHERE t.task_id = :task_id AND c.user_id = :user_id";
        
        $stm = $this->db->prepare($sql);
        $stm->execute([
            ':task_id' => $task_id,
            ':user_id' => $user_id
        ]);
        
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['collaborator_id'] : null;

    } catch (PDOException $error) {
        throw new CustomException(
            500,
            "Error verifying task assignment: " . $error->getMessage(),
            null,
            false
        );
    }
}

public function getTaskProjectId($task_id)
{
    try {
        $sql = "SELECT project_id FROM tasks WHERE task_id = :task_id";
        $stm = $this->db->prepare($sql);
        $stm->execute([':task_id' => $task_id]);
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['project_id'] : null;
    } catch (PDOException $error) {
        throw new CustomException(500, "Error retrieving task's project id: " . $error->getMessage(), null, false);
    }
}
}
