<?php
require_once "../core/Auth.php";
Auth::requireLogin();
include("../config/db.php");

$pageTitle = "Matches";
$pageSub   = "Open live matches and view summaries";
include("partials/layout_top.php");

$q = $conn->query("SELECT * FROM matches ORDER BY id DESC");
?>

<div class="card-ui">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 class="mb-0">Match List</h5>
      <div class="small-muted">All scheduled and ongoing matches</div>
    </div>

    <?php if($_SESSION['role_id']==1){ ?>
      <a href="create_match.php" class="btn btn-primary btn-sm icon-btn">
        <i class="bi bi-plus-circle"></i>
        Create Match
      </a>
    <?php } ?>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-sm align-middle">
      <thead>
        <tr>
          <th style="width:80px;">ID</th>
          <th>Status</th>
          <th>Overs</th>
          <th style="width:220px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($m=$q->fetch_assoc()){ ?>
          <tr>
            <td>#<?= $m['id'] ?></td>
            <td>
              <?php
                $st = $m['status'];
                $badge = "secondary";
                if($st=="In Progress") $badge="warning";
                if($st=="Completed") $badge="success";
              ?>
              <span class="badge bg-<?= $badge ?>">
                <?= htmlspecialchars($st) ?>
              </span>
            </td>
            <td><?= (int)$m['overs_limit'] ?></td>
            <td class="d-flex gap-2">
              <a class="btn btn-primary btn-sm icon-btn"
                 href="live_match.php?match_id=<?= $m['id'] ?>">
                <i class="bi bi-broadcast"></i>
                Open
              </a>

              <a class="btn btn-outline-secondary btn-sm icon-btn"
                 href="match_summary.php?match_id=<?= $m['id'] ?>">
                <i class="bi bi-file-text"></i>
                Summary
              </a>
            </td>
          </tr>
        <?php } ?>

        <?php if($q->num_rows == 0){ ?>
          <tr>
            <td colspan="4" class="text-center small-muted">
              No matches found.
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<?php
include("partials/layout_bottom.php");
