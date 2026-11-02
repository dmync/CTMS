<?php
require_once "../core/Auth.php";
Auth::requireLogin();
include("../config/db.php");

$pageTitle = "Reports";
$pageSub   = "Top performers and match analytics";
include("partials/layout_top.php");

// Filters
$tournament_id = (int)($_GET['tournament_id'] ?? 0);
$match_id = (int)($_GET['match_id'] ?? 0);

// Tournaments
$tournaments = $conn->query("SELECT * FROM tournaments ORDER BY year DESC, id DESC");

// Matches (optional)
$matchWhere = "";
if ($tournament_id > 0) {
    $matchWhere = " WHERE tournament_id = $tournament_id ";
}
$matches = $conn->query("
  SELECT id, overs_limit, status, created_at
  FROM matches
  $matchWhere
  ORDER BY id DESC
");

// Stats WHERE
$whereStats = "";
if ($match_id > 0) {
    $whereStats = " WHERE match_id = $match_id ";
} elseif ($tournament_id > 0) {
    $whereStats = " WHERE match_id IN (SELECT id FROM matches WHERE tournament_id = $tournament_id) ";
}

// Top scorers
$topRuns = $conn->query("
  SELECT p.name, SUM(b.runs) AS total_runs, SUM(b.balls) AS total_balls,
         SUM(b.fours) AS fours, SUM(b.sixes) AS sixes
  FROM batting_score b
  JOIN players p ON p.id=b.player_id
  $whereStats
  GROUP BY b.player_id
  ORDER BY total_runs DESC
  LIMIT 10
");

// Top wicket takers
$topWkts = $conn->query("
  SELECT p.name, SUM(f.wickets) AS total_wickets,
         SUM(f.runs) AS runs_conceded, SUM(f.balls) AS balls
  FROM bowling_figures f
  JOIN players p ON p.id=f.player_id
  $whereStats
  GROUP BY f.player_id
  ORDER BY total_wickets DESC, runs_conceded ASC
  LIMIT 10
");

// Match results
$resultsWhere = "";
if ($tournament_id > 0) $resultsWhere = " WHERE tournament_id = $tournament_id ";
$results = $conn->query("
  SELECT id, status, overs_limit, created_at
  FROM matches
  $resultsWhere
  ORDER BY id DESC
  LIMIT 20
");
?>

<div class="card-ui mb-3">
  <form class="row g-2" method="get">
    <div class="col-md-5">
      <label class="form-label mb-1">Tournament</label>
      <select class="form-control" name="tournament_id" onchange="this.form.submit()">
        <option value="0">All Tournaments</option>
        <?php while($t=$tournaments->fetch_assoc()){ ?>
          <option value="<?= $t['id'] ?>" <?= ($tournament_id==$t['id'])?'selected':'' ?>>
            <?= htmlspecialchars($t['name']) ?> (<?= $t['year'] ?>)
          </option>
        <?php } ?>
      </select>
    </div>

    <div class="col-md-5">
      <label class="form-label mb-1">Match (optional)</label>
      <select class="form-control" name="match_id" onchange="this.form.submit()">
        <option value="0">All Matches</option>
        <?php while($m=$matches->fetch_assoc()){ ?>
          <option value="<?= $m['id'] ?>" <?= ($match_id==$m['id'])?'selected':'' ?>>
            Match #<?= $m['id'] ?> - <?= $m['status'] ?> - <?= $m['overs_limit'] ?> overs
          </option>
        <?php } ?>
      </select>
    </div>

    <div class="col-md-2 d-flex align-items-end">
      <a class="btn btn-outline-secondary w-100" href="reports.php">Reset</a>
    </div>
  </form>
</div>

<ul class="nav nav-tabs">
  <li class="nav-item">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1">
      Top Performers
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2">
      Match Results
    </button>
  </li>
</ul>

<div class="tab-content">

  <!-- TOP PERFORMERS -->
  <div class="tab-pane fade show active" id="tab1">
    <div class="row g-3 mt-2">

      <div class="col-md-6">
        <div class="card-ui">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Top Scorers</h6>
            <span class="badge badge-soft">Batting</span>
          </div>

          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
              <thead>
                <tr>
                  <th>Rank</th>
                  <th>Player</th>
                  <th>Runs</th>
                  <th>Balls</th>
                  <th>4s</th>
                  <th>6s</th>
                </tr>
              </thead>
              <tbody>
                <?php $rank=1; while($r=$topRuns->fetch_assoc()){ ?>
                  <tr>
                    <td><?= $rank++ ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><b><?= (int)$r['total_runs'] ?></b></td>
                    <td><?= (int)$r['total_balls'] ?></td>
                    <td><?= (int)$r['fours'] ?></td>
                    <td><?= (int)$r['sixes'] ?></td>
                  </tr>
                <?php } ?>
                <?php if($rank==1){ ?>
                  <tr><td colspan="6" class="text-center small-muted">No data available</td></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card-ui">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Top Wicket Takers</h6>
            <span class="badge badge-soft">Bowling</span>
          </div>

          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
              <thead>
                <tr>
                  <th>Rank</th>
                  <th>Player</th>
                  <th>Wickets</th>
                  <th>Runs</th>
                  <th>Overs</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $rank=1;
                while($r=$topWkts->fetch_assoc()){
                  $overs = floor($r['balls']/6).".".($r['balls']%6);
                ?>
                  <tr>
                    <td><?= $rank++ ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><b><?= (int)$r['total_wickets'] ?></b></td>
                    <td><?= (int)$r['runs_conceded'] ?></td>
                    <td><?= $overs ?></td>
                  </tr>
                <?php } ?>
                <?php if($rank==1){ ?>
                  <tr><td colspan="5" class="text-center small-muted">No data available</td></tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- MATCH RESULTS -->
  <div class="tab-pane fade" id="tab2">
    <div class="card-ui mt-3">
      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
          <thead>
            <tr>
              <th>Match</th>
              <th>Status</th>
              <th>Overs</th>
              <th>Created</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while($m=$results->fetch_assoc()){ ?>
              <tr>
                <td>#<?= $m['id'] ?></td>
                <td>
                  <?php
                    $badge="secondary";
                    if($m['status']=="In Progress") $badge="warning";
                    if($m['status']=="Completed") $badge="success";
                  ?>
                  <span class="badge bg-<?= $badge ?>">
                    <?= htmlspecialchars($m['status']) ?>
                  </span>
                </td>
                <td><?= (int)$m['overs_limit'] ?></td>
                <td><?= htmlspecialchars($m['created_at']) ?></td>
                <td class="d-flex gap-2">
                  <a class="btn btn-outline-primary btn-sm" href="live_match.php?match_id=<?= $m['id'] ?>">Open</a>
                  <a class="btn btn-outline-secondary btn-sm" href="match_summary.php?match_id=<?= $m['id'] ?>">Summary</a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php include("partials/layout_bottom.php"); ?>
