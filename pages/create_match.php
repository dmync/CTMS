<?php
require_once "../core/Auth.php";
Auth::requireAdmin();
include("../config/db.php");

$pageTitle = "Create Match";
$pageSub   = "Schedule a match using registered tournaments and teams";
include("partials/layout_top.php");

// Selected tournament (for filtering teams)
$selected_tid = (int)($_GET['tournament_id'] ?? 0);

// Fetch tournaments
$tournaments = $conn->query("SELECT * FROM tournaments ORDER BY year DESC, id DESC");

// If none selected, pick latest tournament as default (if exists)
if ($selected_tid <= 0) {
  $tmp = $conn->query("SELECT id FROM tournaments ORDER BY year DESC, id DESC LIMIT 1")->fetch_assoc();
  if ($tmp) $selected_tid = (int)$tmp['id'];
}

// Fetch teams only for selected tournament
$teams = $conn->query("
  SELECT id, name
  FROM teams
  WHERE tournament_id = $selected_tid
  ORDER BY name ASC
");

if(isset($_POST['create'])){
    $tournament_id = (int)$_POST['tournament_id'];
    $team1 = (int)$_POST['team1'];
    $team2 = (int)$_POST['team2'];
    $overs = (int)$_POST['overs'];

    if($team1 == $team2){
        $error = "Both teams cannot be the same.";
    } elseif($overs <= 0) {
        $error = "Overs limit must be greater than 0.";
    } else {
        $conn->query("
            INSERT INTO matches (tournament_id, team1_id, team2_id, overs_limit, status)
            VALUES ($tournament_id, $team1, $team2, $overs, 'Not Started')
        ");
        header("Location: match_list.php");
        exit;
    }
}
?>

<div class="row g-3">

  <div class="col-lg-7">
    <div class="card-ui">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <h5 class="mb-0">Match Details</h5>
          <div class="small-muted">Select tournament, teams and overs limit</div>
        </div>
        <span class="badge badge-soft">Admin</span>
      </div>

      <?php if(isset($error)){ ?>
        <div class="alert alert-danger mt-3 mb-0"><?= htmlspecialchars($error) ?></div>
      <?php } ?>

      <!-- Tournament selector (GET) -->
      <form method="get" class="row g-2 mt-3">
        <div class="col-md-8">
          <label class="form-label mb-1">Tournament</label>
          <select name="tournament_id" class="form-control" onchange="this.form.submit()">
            <?php
              // We need to re-run tournaments because it was already iterated when default selected.
              $tournaments2 = $conn->query("SELECT * FROM tournaments ORDER BY year DESC, id DESC");
              while($t=$tournaments2->fetch_assoc()){
                $sel = ((int)$t['id'] === $selected_tid) ? "selected" : "";
            ?>
              <option value="<?= (int)$t['id'] ?>" <?= $sel ?>>
                <?= htmlspecialchars($t['name']) ?> (<?= (int)$t['year'] ?>)
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <a class="btn btn-outline-secondary w-100" href="create_match.php">Reset</a>
        </div>
      </form>

      <!-- Match form (POST) -->
      <form method="post" class="row g-3 mt-1">
        <input type="hidden" name="tournament_id" value="<?= $selected_tid ?>">

        <div class="col-md-6">
          <label class="form-label">Team 1</label>
          <select name="team1" class="form-control" required>
            <?php
              $teams->data_seek(0);
              while($r=$teams->fetch_assoc()){
            ?>
              <option value="<?= (int)$r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Team 2</label>
          <select name="team2" class="form-control" required>
            <?php
              $teams->data_seek(0);
              while($r=$teams->fetch_assoc()){
            ?>
              <option value="<?= (int)$r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Overs Limit</label>
          <input type="number" name="overs" class="form-control" value="5" min="1" required>
        </div>

        <div class="col-md-6 d-flex align-items-end">
          <button class="btn btn-primary w-100 icon-btn" name="create">
            <i class="bi bi-plus-circle"></i>
            Create Match
          </button>
        </div>

        <div class="col-12 small-muted">
          Note: Teams shown are only from the selected tournament.
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card-ui">
      <h6 class="mb-2"><i class="bi bi-check2-square me-1"></i> Quick Checklist</h6>
      <ul class="mb-0 small-muted">
        <li>Create a tournament</li>
        <li>Add at least 2 teams under that tournament</li>
        <li>Add players for each team</li>
        <li>Create match and open Live Match</li>
      </ul>
    </div>
  </div>

</div>

<?php include("partials/layout_bottom.php"); ?>
