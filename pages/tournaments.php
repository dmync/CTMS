<?php
require_once "../core/Auth.php";
Auth::requireAdmin();
include("../config/db.php");

$pageTitle = "Tournaments";
$pageSub   = "Create tournaments and manage seasons";
include("partials/layout_top.php");

if(isset($_POST['add'])){
  $name = $conn->real_escape_string($_POST['name']);
  $year = (int)$_POST['year'];
  $conn->query("INSERT INTO tournaments(name,year) VALUES('$name',$year)");
  header("Location: tournaments.php");
  exit;
}

if(isset($_GET['del'])){
  $id = (int)$_GET['del'];
  $conn->query("DELETE FROM tournaments WHERE id=$id");
  header("Location: tournaments.php");
  exit;
}

$t = $conn->query("SELECT * FROM tournaments ORDER BY year DESC, id DESC");
?>

<div class="row g-3">

  <!-- Add Tournament -->
  <div class="col-lg-5">
    <div class="card-ui">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Add Tournament</h5>
        <span class="badge badge-soft">Admin</span>
      </div>

      <form method="post" class="row g-2">
        <div class="col-12">
          <label class="form-label mb-1">Tournament Name</label>
          <input class="form-control" name="name" placeholder="e.g., Inter Faculty Cricket 2026" required>
        </div>

        <div class="col-12">
          <label class="form-label mb-1">Year</label>
          <input class="form-control" name="year" type="number" value="<?= date('Y') ?>" required>
        </div>

        <div class="col-12 mt-2">
          <button class="btn btn-primary w-100 icon-btn" name="add">
            <i class="bi bi-plus-circle"></i>
            Add Tournament
          </button>
        </div>
      </form>

      <div class="small-muted mt-3">
        Tip: Create a tournament first, then add teams and players under it.
      </div>
    </div>
  </div>

  <!-- Tournament List -->
  <div class="col-lg-7">
    <div class="card-ui">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Tournament List</h5>
        <span class="small-muted">Total: <?= (int)$t->num_rows ?></span>
      </div>

      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:90px;">ID</th>
              <th>Name</th>
              <th style="width:120px;">Year</th>
              <th style="width:120px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while($r=$t->fetch_assoc()){ ?>
              <tr>
                <td>#<?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= (int)$r['year'] ?></td>
                <td>
                  <a class="btn btn-outline-danger btn-sm"
                     href="?del=<?= $r['id'] ?>"
                     onclick="return confirm('Delete this tournament?')">
                    Delete
                  </a>
                </td>
              </tr>
            <?php } ?>

            <?php if($t->num_rows == 0){ ?>
              <tr>
                <td colspan="4" class="text-center small-muted">No tournaments added yet.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php include("partials/layout_bottom.php"); ?>
