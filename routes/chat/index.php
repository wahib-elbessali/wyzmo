
<?php
require_once __DIR__ . "/../../controllers/chat.controller.php";

require_once __DIR__."/../../middlewares/verifytoken.php";

$chatController = new ChatController();


$router->add("/chat/:chatId", "GET", [$chatController, "indexChat"], ["verifytoken"]);
