<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title><?= htmlspecialchars($pageTitle ?? "CTMS") ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/app.css" rel="stylesheet">
</head>
<body>
<div class="app-shell">

  <aside class="sidebar">
    <div class="brand">
      <div class="logo"><i class="bi bi-trophy"></i></div>
      <div>
        <div>CTMS</div>
        <div class="muted">Cricket System</div>
      </div>
    </div>

    <a class="navlink" href="dashboard.php">
      <i class="bi bi-grid"></i> Dashboard
    </a>

    <a class="navlink" href="match_list.php">
      <i class="bi bi-list-check"></i> Matches
    </a>

    <?php if($_SESSION['role_id']==1){ ?>
      <a class="navlink" href="create_match.php">
        <i class="bi bi-plus-circle"></i> Create Match
      </a>
      <a class="navlink" href="tournaments.php">
        <i class="bi bi-calendar-event"></i> Tournaments
      </a>
      <a class="navlink" href="teams.php">
        <i class="bi bi-people"></i> Teams
      </a>
      <a class="navlink" href="players.php">
        <i class="bi bi-person-badge"></i> Players
      </a>
      <a class="navlink" href="users.php">
        <i class="bi bi-shield-lock"></i> Users
      </a>
    <?php } ?>

    <a class="navlink" href="reports.php">
      <i class="bi bi-graph-up"></i> Reports
    </a>

    <a class="navlink" href="../api/logout.php">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>

    <div class="mt-3 small-muted">
      Role:
      <?php
        $r = $_SESSION['role_id'] ?? 0;
        echo ($r==1?'Admin':($r==2?'Umpire':'Viewer'));
      ?>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <div>
        <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? "Page") ?></h1>
        <?php if(!empty($pageSub)){ ?>
          <div class="small-muted"><?= htmlspecialchars($pageSub) ?></div>
        <?php } ?>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm icon-btn" onclick="toggleTheme()">
          <i class="bi bi-moon-stars"></i> Theme
        </button>
      </div>
    </div>
