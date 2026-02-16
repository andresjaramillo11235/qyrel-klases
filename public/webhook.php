<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Verificación del webhook
    $verify_token = "a4b9c3d7e5f2g8h1i0j6k4l3m2n8o7p5q9r6s3t1u0v7w5x9y2z4a6b3c8";
    $hub_verify_token = $_GET['hub_verify_token'];
    if ($hub_verify_token === $verify_token) {
        echo $_GET['hub_challenge'];
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents("php://input");
    file_put_contents("webhook_log.txt", $data . PHP_EOL, FILE_APPEND);
}

http_response_code(200);
?>
