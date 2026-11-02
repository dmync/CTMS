<?php
session_start();
$_SESSION['user_id'] = 1; // force login for test

$_POST['match_id'] = 1;
include("api/generate_ball.php");
