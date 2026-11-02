<?php
require_once "../core/Auth.php";
Auth::requireAdmin();
include("../config/db.php");

$pageTitle = "Teams";
$pageSub   = "Create teams and organize them under tournaments";
include("partials/layout_top.php");

$ts = $conn->query("SELECT * FROM tournaments ORDER BY year DESC, id DESC");

if(isset($_POST['add'])){
  $tid  = (int)$_POST['tournament_id'];
  $name = $conn->real_escape_string($_POST['name']);
  $conn->query("INSERT INTO teams(tournament_id,name) VALUES($tid,'$name')");
  header("Location: teams.php");
  exit;
}

if(isset($_GET['del'])){
  $id = (int)$_GET['del'];
  $conn->query("DELETE FROM teams WHERE id=$id");
  header("Location: teams.php");
  exit;
}

$q = $conn->query("
  SELECT teams.id, teams.name, tournaments.name AS tname, tournaments.year AS tyear
  FROM teams
  JOIN tournaments ON teams.tournament_id = tournaments.id
  ORDER BY teams.id DESC
");
?>

<div class="row g-3">

  <!-- Add Team -->
  <div class="col-lg-5">
    <div class="card-ui">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Add Team</h5>
        <span class="badge badge-soft">Admin</span>
      </div>

      <form method="post" class="row g-2">
        <div class="col-12">
          <label class="form-label mb-1">Tournament</label>
          <select class="form-control" name="tournament_id" required>
            <?php while($t=$ts->fetch_assoc()){ ?>
              <option value="<?= $t['id'] ?>">
                <?= htmlspecialchars($t['name']) ?> (<?= (int)$t['year'] ?>)
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label mb-1">Team Name</label>
          <input class="form-control" name="name" placeholder="e.g., Colombo Kings" required>
        </div>

        <div class="col-12 mt-2">
          <button class="btn btn-primary w-100 icon-btn" name="add">
            <i class="bi bi-plus-circle"></i>
            Add Team
          </button>
        </div>
      </form>

      <div class="small-muted mt-3">
        Tip: Add at least 2 teams in a tournament before creating matches.
      </div>
    </div>
  </div>

  <!-- Team List -->
  <div class="col-lg-7">
    <div class="card-ui">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Team List</h5>
        <span class="small-muted">Total: <?= (int)$q->num_rows ?></span>
      </div>

      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Tournament</th>
              <th>Team</th>
              <th style="width:120px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while($r=$q->fetch_assoc()){ ?>
              <tr>
                <td>#<?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['tname']) ?> (<?= (int)$r['tyear'] ?>)</td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td>
                  <a class="btn btn-outline-danger btn-sm"
                     href="?del=<?= $r['id'] ?>"
                     onclick="return confirm('Delete this team?')">
                    Delete
                  </a>
                </td>
              </tr>
            <?php } ?>

            <?php if($q->num_rows == 0){ ?>
              <tr>
                <td colspan="4" class="text-center small-muted">No teams added yet.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php include("partials/layout_bottom.php"); ?>
