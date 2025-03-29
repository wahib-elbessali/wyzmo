<?php
require_once __DIR__ . "/../../controllers/representative.controller.php";
require_once __DIR__ . "/../../middlewares/verifytoken.php";
require_once __DIR__ . "/../../middlewares/roles.middleware.php";

$representativeController = new RepresentativeController();

$router->add(
    "/api/representative",
    "POST",
    [$representativeController, "toggleRepresentative"],
    ["verifyToken", "verifyProfesseur"]
);

$router->add(
    "/api/representative/:project_id",
    "GET",
    [$representativeController, "checkRepresentativeStatus"],
    ["verifyToken"]
);