<?php
header("Content-Type: application/json; charset=UTF-8");

ini_set('display_errors', '1');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "login required"]);
    exit;
}

$ROOT = realpath(__DIR__ . "/..");

require_once $ROOT . "/config/db.php";
