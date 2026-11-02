<?php
include("../config/db.php");

$match_id = $_GET['match_id'];

$q = $conn->query("
SELECT 
SUM(runs) AS total_runs,
SUM(is_wicket) AS wickets,
COUNT(*) AS balls
FROM deliveries
WHERE match_id=$match_id
");

$d = $q->fetch_assoc();

$overs = floor($d['balls']/6) . "." . ($d['balls'] % 6);

echo json_encode([
    "runs" => $d['total_runs'],
    "wickets" => $d['wickets'],
    "overs" => $overs
]);
