<?php
require_once __DIR__ . "/model.php";
require_once __DIR__ . "/../utils/customException/index.php";

class MessageModel extends Model {
    public function __construct() {
        try {
            parent::__construct();
            $sql = "CREATE TABLE IF NOT EXISTS messages (
                message_id CHAR(36) PRIMARY KEY DEFAULT(UUID()),
                project_id CHAR(36) NOT NULL,
                sender_id CHAR(36) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_read BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
                FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE
            )";
            $this->db->query($sql);
        } catch (PDOException $error) {
            throw new CustomException(500, "Error creating messages table", null, false);
        }
    }

    public function createMessage($projectId, $senderId, $message) {
        try {
            $sql = "INSERT INTO messages (project_id, sender_id, message)
                    VALUES (:project_id, :sender_id, :message)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':project_id' => $projectId,
                ':sender_id' => $senderId,
                ':message' => $message
            ]);
            return $this->getMessageById($this->db->lastInsertId());
        } catch (PDOException $error) {
            throw new CustomException(500, "Error creating message", null, false);
        }
    }

    public function getMessagesByProject($projectId) {
        try {
            $sql = "SELECT m.*, u.first_name, u.last_name 
                    FROM messages m
                    JOIN users u ON m.sender_id = u.user_id
                    WHERE project_id = :project_id
                    ORDER BY created_at";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':project_id' => $projectId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            throw new CustomException(500, "Error fetching messages", null, false);
        }
    }

    private function getMessageById($messageId) {
        $sql = "SELECT * FROM messages WHERE message_id = :message_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':message_id' => $messageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}