<?php
require_once __DIR__ . "/../Model/Representative.model.php";
require_once __DIR__ . "/../Model/Project.model.php";
require_once __DIR__ . "/../utils/response/index.php";

class RepresentativeController {
    private $representativeModel;
    private $projectModel;

    public function __construct() {
        $this->representativeModel = new RepresentativeModel();
        $this->projectModel = new ProjectModel();
    }

    public function toggleRepresentative() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $project_id = $data['project_id'];
            $user_id = $data['user_id'];

            $project = $this->projectModel->findById($project_id);
            if ($project['owner_id'] !== $_SESSION['user_id']) {
                throw new CustomException(403, "Only project owner can manage representatives");
            }

            // Check if user is already a representative
            $existing = $this->representativeModel->isUserRepresentative($project_id, $user_id);
            
            if ($existing) {
                $this->representativeModel->deleteRepresentative($project_id, $user_id);
                $message = "Representative removed successfully";
            } else {
                $this->representativeModel->createRepresentative($project_id, $user_id);
                $message = "Representative added successfully";
            }

            sendJsonResponse(200, $message, ['is_representative' => !$existing]);
        } catch (CustomException $error) {
            sendJsonResponse($error->statusCode, $error->message, $error->data);
        }
    }

    public function checkRepresentativeStatus($project_id) {
        try {
            $user_id = $_SESSION['user_id'];
            
            $isRepresentative = $this->representativeModel->isUserRepresentative(
                $project_id, 
                $user_id
            );

            sendJsonResponse(
                200, 
                "Representative status checked", 
                ['is_representative' => (bool)$isRepresentative]
            );
            
        } catch (CustomException $error) {
            sendJsonResponse($error->statusCode, $error->message, $error->data);
        }
    }
}