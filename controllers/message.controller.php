<?php
require_once __DIR__ . "/../Model/Message.model.php";
require_once __DIR__ . "/../utils/response/index.php";

class MessageController {
    private $messageModel;

    public function __construct() {
        $this->messageModel = new MessageModel();
    }

    public function sendMessage($projectId) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $userId = $_SESSION['user_id'];
            
            if (empty($data['message'])) {
                throw new CustomException(400, "Message cannot be empty");
            }

            $message = $this->messageModel->createMessage(
                $projectId,
                $userId,
                $data['message']
            );

            sendJsonResponse(201, "Message sent", $message);
        } catch (CustomException $error) {
            sendJsonResponse($error->statusCode, $error->getMessage(), null);
        }
    }

    public function getProjectMessages($projectId) {
        try {
            $messages = $this->messageModel->getMessagesByProject($projectId);
            sendJsonResponse(200, "Messages retrieved", $messages);
        } catch (CustomException $error) {
            sendJsonResponse($error->statusCode, $error->getMessage(), null);
        }
    }
}