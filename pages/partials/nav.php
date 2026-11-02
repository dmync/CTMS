<?php
// expects session active
?>
<div class="card p-3 mb-3">
  <h5 class="mb-3">CTMS</h5>

  <div class="d-grid gap-2">
    <a class="btn btn-outline-dark btn-sm" href="dashboard.php">Dashboard</a>
    <a class="btn btn-outline-primary btn-sm" href="match_list.php">Matches</a>

    <?php if($_SESSION['role_id']==1){ ?>
      <a class="btn btn-outline-success btn-sm" href="create_match.php">Create Match</a>
      <a class="btn btn-outline-secondary btn-sm" href="tournaments.php">Tournaments</a>
      <a class="btn btn-outline-secondary btn-sm" href="teams.php">Teams</a>
      <a class="btn btn-outline-secondary btn-sm" href="players.php">Players</a>
      <a class="btn btn-outline-warning btn-sm" href="users.php">Users</a>

    <?php } ?>

    <a class="btn btn-outline-info btn-sm" href="reports.php">Reports</a>
    <a class="btn btn-outline-danger btn-sm" href="../api/logout.php">Logout</a>
  </div>
</div>
