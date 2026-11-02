<?php
require_once "../core/Auth.php";
Auth::requireLogin();

$pageTitle = "Dashboard";
$pageSub   = "Overview of the Cricket Tournament Management System";

include("partials/layout_top.php");
?>

<div class="card-ui mb-3">
  <h4 class="mb-1">Welcome</h4>
  <p class="small-muted mb-0">
    Manage tournaments, matches and view live cricket scores.
  </p>
</div>

<div class="row g-3">

  <!-- Live Scoring -->
  <div class="col-md-4">
    <div class="card-ui h-100">
      <h6 class="mb-2">
        <i class="bi bi-broadcast me-1"></i>
        Live Scoring
      </h6>
      <p class="small-muted mb-3">
        Open a match to view the live scorecard and ball-by-ball updates.
      </p>
      <a class="btn btn-primary btn-sm icon-btn" href="match_list.php">
        <i class="bi bi-list-check"></i>
        View Matches
      </a>
    </div>
  </div>

  <!-- Admin only -->
  <?php if($_SESSION['role_id']==1){ ?>
  <div class="col-md-4">
    <div class="card-ui h-100">
      <h6 class="mb-2">
        <i class="bi bi-gear me-1"></i>
        Tournament Management
      </h6>
      <p class="small-muted mb-3">
        Create and manage tournaments, teams and players.
      </p>
      <a class="btn btn-secondary btn-sm icon-btn" href="tournaments.php">
        <i class="bi bi-pencil-square"></i>
        Manage Data
      </a>
    </div>
  </div>
  <?php } ?>

  <!-- Reports -->
  <div class="col-md-4">
    <div class="card-ui h-100">
      <h6 class="mb-2">
        <i class="bi bi-graph-up-arrow me-1"></i>
        Reports
      </h6>
      <p class="small-muted mb-3">
        View top scorers, wicket takers and match statistics.
      </p>
      <a class="btn btn-outline-primary btn-sm icon-btn" href="reports.php">
        <i class="bi bi-bar-chart"></i>
        Open Reports
      </a>
    </div>
  </div>

</div>

<?php
include("partials/layout_bottom.php");
