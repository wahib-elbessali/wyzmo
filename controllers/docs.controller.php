<?php

require_once __DIR__ . "/../utils/customException/index.php";
require_once __DIR__ . "/../utils/sendJsonResponse/index.php";

class DocumentController{

    public function __construct(){

    }

    public function addDocument($path) {
        list($project_id, $file_name) = explode(":", $path);
        
        $folderPath = __DIR__ . "/../documents/" . $project_id;
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        
        if (isset($_FILES['file'])) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $destinationPath = $folderPath . '/' . $file_name;
            
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                sendJsonResponse(200, "File uploaded successfully to " . $destinationPath, null);
            } else {
                sendJsonResponse(500, "Failed to upload file", null);
            }
        } else {
            sendJsonResponse(404, "No file uploaded.", null);
        }
    }

    public function getDocuments($project_id) {
        $folderPath = __DIR__ . "/../documents/" . $project_id;
    
        if (!file_exists($folderPath)) {
            return;
        }
    
        $files = scandir($folderPath);
        $filePaths = [];
    
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePaths[] = '/documents/' . $project_id . '/' . $file;
            }
        }
    
        if (empty($filePaths)) {
            return;
        }
    
        sendJsonResponse(200, "Files", $filePaths);
    }
}

