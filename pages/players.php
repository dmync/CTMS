<?php
require_once "../core/Auth.php";
Auth::requireAdmin();
include("../config/db.php");

$pageTitle = "Players";
$pageSub   = "Add players and manage squad list";
include("partials/layout_top.php");

$teams = $conn->query("SELECT id,name FROM teams ORDER BY name ASC");

if(isset($_POST['add'])){
  $team_id = (int)$_POST['team_id'];
  $name = $conn->real_escape_string($_POST['name']);
  $cap = isset($_POST['is_captain']) ? 1 : 0;
  $wk  = isset($_POST['is_wicketkeeper']) ? 1 : 0;

  $conn->query("INSERT INTO players(team_id,name,is_captain,is_wicketkeeper)
                VALUES($team_id,'$name',$cap,$wk)");
  header("Location: players.php");
  exit;
}

if(isset($_GET['del'])){
  $id = (int)$_GET['del'];
  $conn->query("DELETE FROM players WHERE id=$id");
  header("Location: players.php");
  exit;
}

$q = $conn->query("
  SELECT players.id, players.name, players.is_captain, players.is_wicketkeeper, teams.name AS team
  FROM players
  JOIN teams ON players.team_id = teams.id
  ORDER BY players.id DESC
");
?>

<div class="row g-3">

  <!-- Add Player -->
  <div class="col-lg-5">
    <div class="card-ui">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Add Player</h5>
        <span class="badge badge-soft">Admin</span>
      </div>

      <form method="post" class="row g-2 mt-1">
        <div class="col-12">
          <label class="form-label mb-1">Team</label>
          <select class="form-control" name="team_id" required>
            <?php while($t=$teams->fetch_assoc()){ ?>
              <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label mb-1">Player Name</label>
          <input class="form-control" name="name" placeholder="e.g., Kusal Mendis" required>
        </div>

        <div class="col-12 d-flex gap-4 mt-1">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_captain" id="cap">
            <label class="form-check-label" for="cap">Captain</label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_wicketkeeper" id="wk">
            <label class="form-check-label" for="wk">Wicketkeeper</label>
          </div>
        </div>

        <div class="col-12 mt-2">
          <button class="btn btn-primary w-100 icon-btn" name="add">
            <i class="bi bi-person-plus"></i>
            Add Player
          </button>
        </div>
      </form>

      <div class="small-muted mt-3">
        Tip: Mark only one Captain and one Wicketkeeper per team for better realism.
      </div>
    </div>
  </div>

  <!-- Player List -->
  <div class="col-lg-7">
    <div class="card-ui">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Player List</h5>
        <span class="small-muted">Total: <?= (int)$q->num_rows ?></span>
      </div>

      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Team</th>
              <th>Player</th>
              <th style="width:70px;">C</th>
              <th style="width:70px;">WK</th>
              <th style="width:120px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while($r=$q->fetch_assoc()){ ?>
              <tr>
                <td>#<?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['team']) ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= $r['is_captain'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                <td><?= $r['is_wicketkeeper'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                <td>
                  <a class="btn btn-outline-danger btn-sm"
                     href="?del=<?= $r['id'] ?>"
                     onclick="return confirm('Delete this player?')">
                     Delete
                  </a>
                </td>
              </tr>
            <?php } ?>

            <?php if($q->num_rows == 0){ ?>
              <tr>
                <td colspan="6" class="text-center small-muted">No players added yet.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php include("partials/layout_bottom.php"); ?>
