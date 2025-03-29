<?php

require_once __DIR__ . "/../utils/customException/index.php";
require_once __DIR__ . "/../Model/Project.model.php";
require_once __DIR__ . "/../Model/User.model.php";
require_once __DIR__ . "/../utils/response/index.php";
require_once __DIR__ . "/../utils/sendJsonResponse/index.php";

function verifyAdmin()
{
    $user_role = $_SESSION["role"];
    if ($user_role != "ADMIN") {
        sendJsonResponse(
            403,
            "Error: Only admins can perform this action",
            null,
        );
        header("Location: /login");
    }

}

function verifyStudent()
{
    $user_role = $_SESSION["role"];
    if ($user_role != "STUDENT") {
        sendJsonResponse(
            403,
            "Error: Only Student can perform this action",
            null,
        );
        exit();
    }
}

function verifyProfesseur()
{
    $user_role = $_SESSION["role"];
    if ($user_role != "PROFESSEUR") {
        echo sendJsonResponse(
            403,
            "Error: Only professeur can perform this action",
            null,
        );
        exit();
    }


}
