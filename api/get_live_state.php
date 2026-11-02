<?php
require_once __DIR__ . "/bootstrap.php";
header("Content-Type: application/json; charset=utf-8");

$match_id = (int)($_GET['match_id'] ?? 0);
if ($match_id <= 0) {
  echo json_encode(["status" => "error", "message" => "match_id required"]);
  exit;
}

$match = $conn->query("SELECT id, overs_limit, status FROM matches WHERE id=$match_id")->fetch_assoc();
if (!$match) {
  echo json_encode(["status" => "error", "message" => "Match not found"]);
  exit;
}

// Current innings: prefer In Progress, else latest
$inn = $conn->query("
  SELECT *
  FROM innings
  WHERE match_id=$match_id
  ORDER BY (status='In Progress') DESC, innings_no DESC
  LIMIT 1
")->fetch_assoc();

if (!$inn) {
  echo json_encode(["status" => "error", "message" => "No innings found. Init match first.", "match" => $match]);
  exit;
}

$innings_no  = (int)$inn['innings_no'];
$runs        = (int)$inn['total_runs'];
$wkts        = (int)$inn['wickets'];
$legal_balls = (int)$inn['balls'];
$overs_str   = floor($legal_balls / 6) . "." . ($legal_balls % 6);

$score = [
  "runs" => $runs,
  "wickets" => $wkts,
  "overs" => $overs_str,
  "legal_balls" => $legal_balls,
];

// Batting table (this innings)
$batQ = $conn->query("
  SELECT p.name AS player_name,
         b.runs, b.balls, b.fours, b.sixes,
         b.is_out, b.out_desc
  FROM batting_score b
  JOIN players p ON p.id = b.player_id
  WHERE b.match_id = $match_id
    AND b.innings_no = $innings_no
  ORDER BY b.id ASC
");

$batting = [];
while ($r = $batQ->fetch_assoc()) {
  $batting[] = [
    "player" => $r['player_name'],
    "status" => $r['is_out'] ? $r['out_desc'] : 'not out',
    "runs"   => (int)$r['runs'],
    "balls"  => (int)$r['balls'],
    "fours"  => (int)$r['fours'],
    "sixes"  => (int)$r['sixes']
  ];
}


// Bowling table (this innings)
$bowlQ = $conn->query("
  SELECT p.name AS bowler_name,
         f.balls, f.runs, f.wickets
  FROM bowling_figures f
  JOIN players p ON p.id = f.player_id
  WHERE f.match_id = $match_id
    AND f.innings_no = $innings_no
");

$bowling = [];
while ($r = $bowlQ->fetch_assoc()) {
  $overs = floor($r['balls'] / 6) . "." . ($r['balls'] % 6);
  $bowling[] = [
    "bowler" => $r['bowler_name'],
    "overs"  => $overs,
    "runs"   => (int)$r['runs'],
    "wickets"=> (int)$r['wickets']
  ];
}


// Last 6 deliveries in this innings
$last = [];
$lastQ = $conn->query("
  SELECT id, commentary, runs_bat, runs_extra, extra_type, wicket_type
  FROM deliveries
  WHERE match_id=$match_id AND innings_no=$innings_no
  ORDER BY id DESC
  LIMIT 6
");
if ($lastQ) {
  while ($r = $lastQ->fetch_assoc()) {
    $txt = $r['commentary'];
    if (!$txt) {
      $txt = "Runs: " . ((int)$r['runs_bat'] + (int)$r['runs_extra']);
    }
    $last[] = ["text" => $txt, "id" => (int)$r['id']];
  }
}

echo json_encode([
  "status" => "ok",
  "match" => [
    "id" => (int)$match['id'],
    "overs_limit" => (int)$match['overs_limit'],
    "status" => $match['status'],
  ],
  "innings" => [
    "innings_no" => $innings_no,
    "total_runs" => $runs,
    "wickets" => $wkts,
    "balls" => $legal_balls,
    "overs" => $overs_str,
    "status" => $inn['status'],
    "target" => $inn['target'],
  ],
  "score" => $score,
  "batting" => $batting,
  "bowling" => $bowling,
  // keep both keys (some JS versions used lastBalls)
  "last_balls" => array_map(function($x){ return $x['text']; }, $last),
  "lastBalls" => array_map(function($x){ return $x['text']; }, $last),
]);
