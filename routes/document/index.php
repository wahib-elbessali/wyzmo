<?php

require_once __DIR__ . "/../../controllers/docs.controller.php";
require_once __DIR__ . "/../../middlewares/verifytoken.php";
require_once __DIR__ . "/../../middlewares/roles.middleware.php";

$documentController = new DocumentController();

$router->add(
    "/api/docs/:path",
    "GET",
    [$documentController, "getDocuments"],
    ["verifyToken"]
);

$router->add(
    "/api/docs/:project_id",
    "POST",
    [$documentController, "addDocument"],
    ["verifyToken", "verifyProfesseur"]
);