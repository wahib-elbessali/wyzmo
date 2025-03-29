<?php

require_once __DIR__ . "/../../controllers/project.controller.php";
require_once  __DIR__ . "/../../middlewares/verifytoken.php";
require_once __DIR__  . "/../../middlewares/roles.middleware.php";


$projectController = new ProjectController();


$router->add(
    "/api/project",
    "POST",
    [$projectController, "createProject"],
    ["verifytoken", "verifyProfesseur"]
);


$router->add(
    "/api/projects/list",
    "GET",
    [$projectController, "getProjectList"],
    ["verifytoken"]
);

$router->add(
    "/api/progress/:project_id",
    "GET",
    [$projectController, "getProjectProgress"],
    ["verifytoken"]
);


