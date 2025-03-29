<?php

// MODELS
require_once __DIR__ . "/../Model/Project.model.php";
require_once __DIR__ . "/../Model/Task.model.php";
require_once __DIR__ . "/../Model/Collaborator.model.php";
require_once __DIR__ . "/../Model/Representative.model.php";


require_once __DIR__ . "/../utils/customException/index.php";
require_once __DIR__ . "/../utils/response/index.php";
require_once __DIR__ . "/../utils/validator/index.php";

class TaskController
{
    private $taskModel;
    private $projectModel;
    private $collaboratorModel;
    private $representativeModel;
    public function __construct()
    {
        try {
            $this->projectModel = new ProjectModel();
            $this->taskModel = new TaskModel();
            $this->collaboratorModel = new CollaboratorModel();
            $this->representativeModel = new RepresentativeModel();
        } catch (CustomException $error) {
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

   /* public function getTaskList($projectId)
    {
        try {

        } catch (CustomException $error) {
            echo  sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }*/

    public function createTask($project_id)
    {
        try {
            $taskFormData = json_decode(file_get_contents("php://input"), true);
            $taskFormDataErrors = Validator::taskForm($taskFormData);

            if (!empty($taskFormDataErrors)) {
                throw new CustomException(
                    400,
                    "Task form validation failed.",
                    $taskFormDataErrors,
                    false
                );
            }
            //

            $user_id = $_SESSION["user_id"];

            // check if the assigner is collaborators in this project
            $findCollaborator = $this->collaboratorModel->findById($taskFormData["assigned_to"]);

            if (!$findCollaborator) {
                throw new CustomException(
                    403,
                    "Error: this collaborator not assigned into project.",
                    null,
                    false
                );
            }

            $taskFormData["project_id"] = $project_id;
            $newTask = $this->taskModel->createTaks($taskFormData);

            echo sendJsonResponse(
                201,
                "Success: Task created successfully.",
                $newTask
            );

        } catch (CustomException $error) {
            echo  sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

    public function updateTaskStatus()
{
    try {
        $taskData = json_decode(file_get_contents("php://input"), true);
        $user_id = $_SESSION["user_id"];

        if (!isset($taskData["task_id"]) || !isset($taskData["status"])) {
            throw new CustomException(400, "Missing required fields");
        }

        $collaborator_id = $this->taskModel->getCollaboratorIdByUserAndTask(
            $user_id,
            $taskData["task_id"]
        );

        if (!$collaborator_id) {
            throw new CustomException(403, "You are not assigned to this task");
        }

        // Update the task status
        $this->taskModel->updateTaskStatus($taskData["task_id"], $taskData["status"]);

        // Retrieve the project id from the task
        $project_id = $this->taskModel->getTaskProjectId($taskData["task_id"]);
        if ($project_id) {
            // Update the project progress
            $progress = $this->projectModel->updateProjectProgress($project_id);
        }

        sendJsonResponse(200, "Task status updated successfully and project progress updated.", null);

    } catch (CustomException $error) {
        sendJsonResponse($error->statusCode, $error->message, $error->data);
    }
}

public function getTaskList($project_id) {
    try {
        $user_id = $_SESSION["user_id"];
        $role = $_SESSION["role"];
        
        $isRepresentative = false;
        if ($role === 'STUDENT') {
            $isRepresentative = $this->representativeModel->isUserRepresentative(
                $project_id, 
                $user_id
            );
        }

        $tasks = $this->taskModel->getTasksByProject(
            $project_id,
            ($role === 'STUDENT' && !$isRepresentative) ? $user_id : null,
            $role
        );

        sendJsonResponse(200, "Tasks retrieved successfully", $tasks);

    } catch (CustomException $error) {
        sendJsonResponse($error->statusCode, $error->message, $error->data);
    }
}


}
