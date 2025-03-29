<?php

require_once __DIR__ . "/../../controllers/collaborator.conroller.php";
require_once __DIR__ . "/../../middlewares/verifytoken.php";
require_once __DIR__ . "/../../middlewares/roles.middleware.php";


$collaboratorController = new CollaboratorController();

$router->add(
    "/api/collaborator",
    "POST",
    [$collaboratorController, "addCollaborator"],
    ["verifyToken", "verifyProfesseur"]
);

$router->add(
    "/api/collaborator/search/:email",
    "GET",
    [$collaboratorController, "searchByEmail"],
    ["verifyToken","verifyProfesseur"]
);


$router->add(
    "/api/collaborator/list/:project_id",
    "GET",
    [$collaboratorController, "getCollaboratorList"],
    ["verifyToken"]
);

$router->add(
    "/api/collaborator/my-id/:project_id",
    "GET",
    [$collaboratorController, "getMyCollaboratorId"],
    ["verifyToken"]
);