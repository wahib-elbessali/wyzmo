<?php

require_once __DIR__ . "/../Model/Collaborator.model.php";
require_once __DIR__ . "/../utils/customException/index.php";
require_once __DIR__ . "/../utils/sendJsonResponse/index.php";

// Models
require_once __DIR__ . "/../Model/User.model.php";
require_once __DIR__ . "/../Model/Project.model.php";

class CollaboratorController
{
    private $collaboratorModel;
    private $userModel;
    private $projectModel;
    public function __construct()
    {
        try {
            $this->collaboratorModel = new CollaboratorModel();
            $this->userModel = new UserModel();
            $this->projectModel = new ProjectModel();
        } catch (CustomException $error) {
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

    public function addCollaborator()
    {
        try {
            $collaboratorData = json_decode(file_get_contents("php://input"), true);

            if (!isset($collaboratorData["project_id"])) {
                throw new CustomException(
                    400,
                    "Error: Project id must be set",
                    null,
                    false
                );
            } elseif (!isset($collaboratorData["collaborator_list"])) {
                throw new CustomException(
                    400,
                    "Error: List Collaborators must be set",
                    null,
                    false
                );
            }

            // check if the user is the owner of project

            $findUser = $this->userModel->findById($_SESSION["user_id"]);
            if (!$findUser) {
                throw new CustomException(
                    403,
                    "Error: only the professeur of this project can perform this action",
                    null,
                    false
                );
            }

            foreach ($collaboratorData["collaborator_list"] as $user_id) {

                // check if the user is already collaborator in the project
                $findCollaborator = $this->collaboratorModel->getCollaborator(
                    $user_id,
                    $collaboratorData["project_id"]
                );

                if (!empty($findCollaborator)) {
                    throw new CustomException(
                        500,
                        "Error: the user with id  " . $user_id .
                        " already exist in as collaborator the project",
                        null,
                        false
                    );
                }

                $newColl = $this->collaboratorModel->createCollaborator(
                    $user_id,
                    $collaboratorData["project_id"]
                );
                // array_push($coll);
            }

            echo sendJsonResponse(
                200,
                "Success: the collaborators added successfully. ",
                null
            );

        } catch (CustomException $error) {
            echo sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

    public function removeCollaborator()
    {
        try {

        } catch (CustomException $error) {
            echo sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }

    }

    public function searchByEmail($email)
    {
        try {
            $collaboratorsList = $this->userModel->searchByEmail($email);
            sendJsonResponse(
                200,
                "Success: collaborators list",
                $collaboratorsList
            );
        } catch (CustomException $error) {
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

    public function getCollaboratorList($project_id)
    {
        try {
            $user_id = $_SESSION["user_id"];
            $findProject = $this->projectModel->findById($project_id);

            /*if ($findProject["owner_id"] !== $user_id) {
                throw new CustomException(
                    401,
                    "Error: only proffesseur of this project can perform this action",
                    null,
                    false
                );
            }*/

            $projectCollaborators = $this->collaboratorModel->collaboratorsList($project_id);

            sendJsonResponse(
                200,
                "Success: project list.",
                $projectCollaborators
            );

        } catch (CustomException $error) {
            sendJsonResponse(
                $error->statusCode,
                $error->message,
                $error->data
            );
        }
    }

    public function getMyCollaboratorId($project_id) {
        try {
            $user_id = $_SESSION["user_id"];
            $collaboratorId = $this->collaboratorModel->getCollaboratorId(
                $user_id, 
                $project_id
            );
            
            sendJsonResponse(
                200,
                "Collaborator ID retrieved",
                ["collaborator_id" => $collaboratorId]
            );
        } catch (CustomException $error) {
            sendJsonResponse($error->statusCode, $error->message, $error->data);
        }
    }
}
