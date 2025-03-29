<?php
require_once __DIR__ . "/../Model/Project.model.php";
require_once __DIR__ . "/../Model/Task.model.php";
require_once __DIR__ . "/../utils/response/index.php";

class AdminController {
    private $projectModel;
    private $taskModel;

    public function __construct() {
        $this->projectModel = new ProjectModel();
        $this->taskModel = new TaskModel();
    }
    
    public function getAllProjectsWithTasks() {
        try {
            $projects = $this->projectModel->getAllProjects();
            
            foreach ($projects as &$project) {
                $tasks = $this->taskModel->getTasksByProject(
                    $project['project_id'], 
                    null,
                    'ADMIN'
                );
                $project['tasks'] = $tasks;
            }

            sendJsonResponse(200, "All projects with tasks", $projects);
        } catch (CustomException $error) {
            sendJsonResponse($error->statusCode, $error->message, $error->data);
        }
    }
}