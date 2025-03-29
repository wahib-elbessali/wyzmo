<?php
require_once __DIR__ . "/../../controllers/admin.controller.php";
require_once __DIR__ . "/../../middlewares/verifytoken.php";
require_once __DIR__ . "/../../middlewares/roles.middleware.php";

$adminController = new AdminController();

$router->add(
    "/api/admin/projects",
    "GET",
    [$adminController, "getAllProjectsWithTasks"],
    ["verifyToken", "verifyAdmin"]
);