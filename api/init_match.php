<?php
require_once __DIR__ . "/bootstrap.php";
require_once realpath(__DIR__ . "/../core/MatchEngine.php");

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;

if ($match_id <= 0) {
    echo json_encode(["error" => "match_id required"]);
    exit;
}

$result = MatchEngine::ensureInnings($conn, $match_id);
echo json_encode($result);
