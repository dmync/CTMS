<?php
require_once "../core/Auth.php";

if(isset($_SESSION['user_id'])){
  header("Location: dashboard.php");
  exit;
}

$error = $_GET['error'] ?? "";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login | CTMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/app.css" rel="stylesheet">
</head>
<body>

<div class="d-flex align-items-center justify-content-center" style="min-height:100vh; background: var(--bg);">

  <div class="card-ui" style="width:380px;">
    <div class="text-center mb-3">
      <div class="logo mx-auto mb-2" style="background: var(--primary); color:white;">
        <i class="bi bi-trophy"></i>
      </div>
      <h4 class="mb-1">CTMS</h4>
      <div class="small-muted">Cricket Tournament Management System</div>
    </div>

    <?php if($error){ ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php } ?>

    <form method="post" action="../api/login.php">
      <div class="mb-2">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button class="btn btn-primary w-100">Login</button>
    </form>

    <div class="text-center small-muted mt-3">
      © <?= date('Y') ?> CTMS
    </div>
  </div>

</div>

</body>
</html>
