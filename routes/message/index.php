<?php
require_once __DIR__ . "/../../controllers/message.controller.php";
require_once __DIR__ . "/../../middlewares/verifytoken.php";

$messageController = new MessageController();

$router->add(
    "/api/messages/:project_id",
    "POST",
    [$messageController, "sendMessage"],
    ["verifyToken"]
);

$router->add(
    "/api/messages/:project_id",
    "GET",
    [$messageController, "getProjectMessages"],
    ["verifyToken"]
);