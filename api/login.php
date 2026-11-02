<?php
session_start();
include("../config/db.php");

$username = $_POST['username'];
$password = $_POST['password'];

$q = $conn->query("
    SELECT * FROM users 
    WHERE username='$username' AND password='$password'
");

if ($q->num_rows == 1) {
    $user = $q->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role_id'] = $user['role_id'];

    header("Location: ../pages/dashboard.php");
    exit;
} else {
    header("Location: ../pages/login.php?error=1");
    exit;
}
