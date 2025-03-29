<?php

require_once __DIR__ . "/../../controllers/pages.controller.php";
require_once __DIR__ . "/../../middlewares/verifytoken.php";
require_once __DIR__ . "/../../middlewares/roles.middleware.php";

$pageController = new PageController();

$router->add("/login", "GET", [$pageController, "loginPage"]);
$router->add("/register", "GET", [$pageController, "registerPage"]);
$router->add("/", "GET", [$pageController, "landingPage"]);
$router->add("/workspace", "GET", [$pageController, "workspacePage"], ["verifyToken"]);
$router->add("/profile", "GET", [$pageController, "profilePage"], ["verifyToken"]);
$router->add("/admin", "GET", [$pageController, "adminPage"], ["verifyToken", "verifyAdmin"]);