<?php


require_once __DIR__ . "/../../controllers/task.controller.php";
require_once __DIR__ . "/../../middlewares/verifytoken.php";
require_once __DIR__ . "/../../middlewares/roles.middleware.php";

$taskController = new TaskController();



// $router->add(
//     "/api/tasks/tasksList/:projectId",
//     "GET",
//     [$taskController, "getTaskList"],
//     ["verifyToken"]
// );

/*$router->add(
    "/api/tasks/:projectId",
    "POST",
    [$taskController, "createTask"],
    ["verifyToken", "verifyProfesseur"]
);*/

$router->add(
    "/api/tasks/update-status",
    "PUT",
    [$taskController, "updateTaskStatus"],
    ["verifyToken"]
);

// Get tasks for a project
$router->add(
    "/api/tasks/:project_id",
    "GET",
    [$taskController, "getTaskList"],
    ["verifyToken"]
);

// Create new task
$router->add(
    "/api/tasks/:project_id",
    "POST",
    [$taskController, "createTask"],
    ["verifyToken"]
);