<?php
require_once "../core/Auth.php";
Auth::requireAdmin();
include("../config/db.php");

$pageTitle = "Users";
$pageSub   = "Manage user roles (Admin / Umpire / Viewer)";
include("partials/layout_top.php");

$myId = (int)($_SESSION['user_id'] ?? 0);

if(isset($_POST['update'])){
  $uid  = (int)$_POST['user_id'];
  $role = (int)$_POST['role_id'];

  // Safety: prevent removing your own admin role
  if($uid === $myId && $role != 1){
    $msg = "You cannot remove your own Admin role.";
  } else {
    $conn->query("UPDATE users SET role_id=$role WHERE id=$uid");
    $msg = "Role updated successfully.";
  }
}

$users = $conn->query("
  SELECT users.id, users.username, users.role_id, roles.name AS role
  FROM users
  JOIN roles ON users.role_id = roles.id
  ORDER BY users.id DESC
");

$roles = $conn->query("SELECT * FROM roles ORDER BY id ASC");
?>
<div class="card-ui">

  <div class="d-flex justify-content-between align-items-center mb-2">
    <div>
      <h5 class="mb-0">User Management</h5>
      <div class="small-muted">Change roles to control access (Admin can do everything).</div>
    </div>
    <span class="badge badge-soft">Admin</span>
  </div>

  <?php if(isset($msg)){ ?>
    <div class="alert alert-info mt-3 mb-0"><?= htmlspecialchars($msg) ?></div>
  <?php } ?>

  <div class="table-responsive mt-3">
    <table class="table table-sm table-bordered align-middle mb-0">
      <thead>
        <tr>
          <th style="width:90px;">ID</th>
          <th>Username</th>
          <th style="width:160px;">Current Role</th>
          <th style="width:320px;">Change Role</th>
        </tr>
      </thead>
      <tbody>
        <?php while($u=$users->fetch_assoc()){ ?>
          <tr>
            <td>#<?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td>
              <?php
                $r = (int)$u['role_id'];
                $badge = ($r==1) ? "success" : (($r==2) ? "warning" : "secondary");
              ?>
              <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($u['role']) ?></span>
              <?php if((int)$u['id'] === $myId){ ?>
                <span class="badge bg-primary">You</span>
              <?php } ?>
            </td>
            <td>
              <form method="post" class="d-flex gap-2">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">

                <select name="role_id" class="form-control form-control-sm">
                  <?php
                  $roles->data_seek(0);
                  while($r=$roles->fetch_assoc()){
                    $selected = ((int)$u['role_id'] === (int)$r['id']) ? "selected" : "";
                  ?>
                    <option value="<?= (int)$r['id'] ?>" <?= $selected ?>>
                      <?= htmlspecialchars($r['name']) ?>
                    </option>
                  <?php } ?>
                </select>

                <button class="btn btn-primary btn-sm icon-btn" name="update">
                  <i class="bi bi-check2-circle"></i>
                  Update
                </button>
              </form>
            </td>
          </tr>
        <?php } ?>

        <?php if($users->num_rows == 0){ ?>
          <tr>
            <td colspan="4" class="text-center small-muted">No users found.</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <div class="small-muted mt-3">
    <b>Role Guide:</b> Admin (full access), Umpire (scoring), Viewer (read-only).
  </div>

</div>

<?php include("partials/layout_bottom.php"); ?>
