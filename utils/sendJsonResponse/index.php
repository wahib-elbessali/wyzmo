<?php

require_once __DIR__. "/../response/index.php";
function sendJsonResponse($statusCode, $message, $data)
{
    http_response_code($statusCode);
    header("Content-Type: application/json");
    echo json_encode(
        new Response(
            $statusCode,
            $message,
            $data
        )
    );

}
