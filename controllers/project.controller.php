<?php

// require models
require_once __DIR__ . "/../Model/Project.model.php";
// require utils
require_once __DIR__ . "/../utils/response/index.php";
require_once __DIR__ . "/../utils/validator/index.php";
require_once __DIR__ . "/../utils/customException/index.php";
require_once __DIR__ . "/../utils/sendJsonResponse/index.php";

class ProjectController
{
    private $projectModel;

    public function __construct()
    {
        try {
            $this->projectModel = new ProjectModel();
        } catch (CustomException $error) {
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

    public function createProject()
    {
        try {
            $projectDateForm = json_decode(file_get_contents("php://input"), true);


            // validate project data form
            $projectDataValidationErrors = Validator::projectForm(
                $projectDateForm
            );

            if (!empty($projectDataValidationErrors)) {
                throw new CustomException(
                    400,
                    "Error: Validtion form failed",
                    $projectDataValidationErrors,
                    false
                );
            }

            $projectDateForm["owner_id"] = $_SESSION["user_id"];
            // create the project
            $project = $this->projectModel->createProject($projectDateForm);

            sendJsonResponse(
                201,
                "Success: project created successfully.",
                $project
            );


        } catch (CustomException $error) {
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }

    }


    public function getProjectList()
    {
        try {
            $user_id = $_SESSION["user_id"];
            $role = $_SESSION["role"];
            $projectsList = $this->projectModel->projectsList($user_id, $role);
            sendJsonResponse(200, "Projects list", $projectsList);

        } catch (CustomException $error) {
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }

    }

    public function deleteProject()
    {
        try {

        } catch (CustomException $error) {
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }


    }

    public function getProjectProgress($project_id)
    {
        try {
            if (!$project_id) {
                throw new CustomException(
                    400,
                    "Error: project ID is missing in the URL.",
                    null,
                    false
                );
            }
            
            $user_id = $_SESSION["user_id"];
            $project = $this->projectModel->findById($project_id);
            if (!$project) {
                throw new CustomException(
                    403,
                    "Error: you are not authorized to view this project.",
                    null,
                    false
                );
            }
            
            $progress = isset($project['progress']) ? $project['progress'] : null;
            
            sendJsonResponse(200, "Project progress retrieved successfully", ['progress' => $progress]);
        } catch (CustomException $error) {
            sendJsonResponse($error->statusCode, $error->message, $error->data);
        }
    }
    
}
