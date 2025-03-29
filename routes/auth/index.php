<?php

require_once  __DIR__ . "/../../controllers/auth.php";
require_once __DIR__."/../../middlewares/verifytoken.php";
require_once __DIR__ . "/../../middlewares/roles.middleware.php";

$authController = new AuthController();


// login
$router->add("/api/auth/login", "POST", [$authController, "login"]);

// register
$router->add("/api/auth/register", "POST", [$authController, "register"]);

// logout
$router->add("/api/auth/logout", "GET", [$authController, "logout"], ["verifyToken"]);
