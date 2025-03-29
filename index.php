<?php


require_once __DIR__ . "/routes/index.php";
session_start(); // Start the session

$requestUrl = $_SERVER["REQUEST_URI"]; //  path(/login, /register ...)
$requestMethod = $_SERVER["REQUEST_METHOD"]; //  method(GET, POST, DELETE, PUT)


$router = new Router();

require_once "./routes/auth/index.php";
require_once "./routes/project/index.php";
require_once "./routes/collaborator/index.php";
require_once "./routes/chat/index.php";
require_once "./routes/pages/index.php";
require_once "./routes/task/index.php";
require_once "./routes/admin/index.php";
require_once "./routes/document/index.php";
require_once "./routes/message/index.php";
require_once "./routes/representative/index.php";


$router->dispatch($requestUrl, $requestMethod);
