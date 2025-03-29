<?php

require_once __DIR__ . "/../Model/User.model.php";



$userModel = new UserModel();

function verifyToken(): void
{
    global $userModel;
    $user_id = "";

    if (!isset($_SESSION["user_id"])) {
        header("Location: /login");
        exit();

    }
    $userId = $_SESSION["user_id"];


    $user = $userModel->findById($userId);

    if (!$user) {
        header("Location: /login");
        exit() ;
        return;
    }
}
