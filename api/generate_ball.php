<?php
require_once __DIR__ . "/bootstrap.php";
require_once realpath(__DIR__ . "/../core/MatchEngine.php");

header("Content-Type: application/json; charset=utf-8");

$match_id = isset($_POST['match_id']) ? (int)$_POST['match_id'] : 0;

if ($match_id <= 0) {
    echo json_encode(["status"=>"error", "message"=>"match_id required"]);
    exit;
}

/**
 * Helper: get current live snapshot for UI
 */
function getLiveSnapshot($conn, $match_id){
    $inn = $conn->query("
        SELECT innings_no, total_runs, wickets, balls, status, target
        FROM innings
        WHERE match_id=$match_id
        ORDER BY innings_no DESC
        LIMIT 1
    ")->fetch_assoc();

    if(!$inn){
        return [
            "runs"=>0,
            "wickets"=>0,
            "overs"=>"0.0",
            "balls"=>0,
            "innings_no"=>null,
            "innings_status"=>null,
            "target"=>null
        ];
    }

    $balls = (int)$inn["balls"];
    $overs = floor($balls/6) . "." . ($balls%6);

    return [
        "runs" => (int)$inn["total_runs"],
        "wickets" => (int)$inn["wickets"],
        "overs" => $overs,
        "balls" => $balls,
        "innings_no" => (int)$inn["innings_no"],
        "innings_status" => $inn["status"],
        "target" => $inn["target"] !== null ? (int)$inn["target"] : null
    ];
}

/**
 * Helper: last 6 deliveries (for UI list)
 */
function getLastBalls($conn, $match_id){
    $arr = [];
    $q = $conn->query("
        SELECT innings_no, over_no, ball_no, runs_bat, runs_extra, extra_type, is_wicket, wicket_type, commentary
        FROM deliveries
        WHERE match_id=$match_id
        ORDER BY id DESC
        LIMIT 6
    ");
    while($r = $q->fetch_assoc()){
        $arr[] = $r;
    }
    return array_reverse($arr);
}

// 1) Generate next event
$result = MatchEngine::generateNext($conn, $match_id);

// 2) Always attach snapshot + last balls
$snapshot = getLiveSnapshot($conn, $match_id);
$lastBalls = getLastBalls($conn, $match_id);

// 3) Normalize output
$out = [
    "status" => $result["status"] ?? "ok",
    "event"  => $result["event"] ?? null,
    "reason" => $result["reason"] ?? null,
    "engine" => $result,             // full raw engine response (optional)
    "snapshot" => $snapshot,         // ✅ what UI needs
    "last_balls" => $lastBalls        // ✅ useful for UI
];

echo json_encode($out);
