<?php
require_once "../core/Auth.php";
Auth::requireLogin();
include("../config/db.php");

$match_id = (int)($_GET['match_id'] ?? 0);
if($match_id <= 0) die("match_id missing");

$m = $conn->query("SELECT * FROM matches WHERE id=$match_id")->fetch_assoc();
$inn1 = $conn->query("SELECT * FROM innings WHERE match_id=$match_id AND innings_no=1")->fetch_assoc();
$inn2 = $conn->query("SELECT * FROM innings WHERE match_id=$match_id AND innings_no=2")->fetch_assoc();

function teamName($conn,$id){
  if(!$id) return "-";
  $r = $conn->query("SELECT name FROM teams WHERE id=$id")->fetch_assoc();
  return $r ? $r['name'] : "-";
}

$team1 = teamName($conn, $inn1['batting_team_id'] ?? null);
$team2 = teamName($conn, $inn2['batting_team_id'] ?? null);

$winnerText = "Not decided";
if($inn1 && $inn2){
  $r1 = (int)$inn1['total_runs'];
  $r2 = (int)$inn2['total_runs'];
  $w2 = (int)$inn2['wickets'];
  $target = (int)$inn2['target'];

  if($m['status'] == 'Completed'){
    if($r2 >= $target){
      // chasing team won
      $winnerText = $team2 . " won by " . (10 - $w2) . " wickets";
    } else {
      $winnerText = $team1 . " won by " . ($r1 - $r2) . " runs";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Match Summary</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container-fluid mt-3">
  <div class="row">
    <div class="col-md-3"><?php include("partials/nav.php"); ?></div>
    <div class="col-md-9">
      <div class="card p-3">
        <h4>Match Summary (Match #<?= $match_id ?>)</h4>
        <p class="mb-1"><b>Status:</b> <?= $m['status'] ?></p>
        <p class="mb-3"><b>Result:</b> <?= $winnerText ?></p>

        <div class="row">
          <div class="col-md-6">
            <div class="card p-3">
              <h6>Innings 1</h6>
              <?php if($inn1){ ?>
                <p class="mb-0"><?= $team1 ?>: <?= $inn1['total_runs'] ?>/<?= $inn1['wickets'] ?> (<?= floor($inn1['balls']/6) ?>.<?= $inn1['balls']%6 ?>)</p>
              <?php } else { echo "Not started"; } ?>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card p-3">
              <h6>Innings 2</h6>
              <?php if($inn2){ ?>
                <p class="mb-0"><?= $team2 ?>: <?= $inn2['total_runs'] ?>/<?= $inn2['wickets'] ?> (<?= floor($inn2['balls']/6) ?>.<?= $inn2['balls']%6 ?>)</p>
                <p class="text-muted mb-0">Target: <?= $inn2['target'] ?></p>
              <?php } else { echo "Not started"; } ?>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
</body>
</html>
