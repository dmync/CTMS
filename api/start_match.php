<?php
include("../config/db.php");

$conn->query("
INSERT INTO matches (tournament_id, batting_team_id, bowling_team_id, overs_limit, status)
VALUES (1,1,2,10,'In Progress')
");

echo json_encode(["status"=>"started"]);
