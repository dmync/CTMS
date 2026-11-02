<?php
class Auth {
    public static function requireLogin() {
        session_start();
        if(!isset($_SESSION['user_id'])){
            header("Location: login.php");
            exit;
        }
    }

    public static function requireAdmin() {
        self::requireLogin();
        if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1){
            die("Access Denied");
        }
    }
}
