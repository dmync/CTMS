<?php
include("../config/db.php");
include("../core/MatchLogic.php");

$match_id = $_POST['match_id'];

$res = $conn->query("SELECT COUNT(*) AS balls FROM deliveries WHERE match_id=$match_id");
$row = $res->fetch_assoc();

$ball_no = $row['balls'] + 1;
$over_no = floor($row['balls'] / 6);

$event = MatchLogic::generateDelivery();

$runs = 0;
$wicket = 0;

if ($event == "single") $runs = 1;
if ($event == "double") $runs = 2;
if ($event == "four")   $runs = 4;
if ($event == "six")    $runs = 6;
if ($event == "wicket") $wicket = 1;

$conn->query("
INSERT INTO deliveries(match_id,over_no,ball_no,runs,is_wicket)
VALUES($match_id,$over_no,$ball_no,$runs,$wicket)
");

echo json_encode([
    "event" => $event,
    "runs" => $runs,
    "wicket" => $wicket
]);
