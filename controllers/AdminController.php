<?php
class AdminController {
    public function dashboard() {
//        if ($_SESSION['role'] !== 'admin') {
//            header('Location: /auth/login');
//            exit();
//        }

        require 'views/admin/dashboard.php';
    }
}
