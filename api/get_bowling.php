<?php
include("../config/db.php");

$match_id = $_GET['match_id'];

$q = $conn->query("
SELECT COUNT(*) overs, SUM(is_wicket) wickets
FROM deliveries
WHERE match_id=$match_id
");

echo json_encode($q->fetch_assoc());
