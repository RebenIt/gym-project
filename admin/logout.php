<?php
require_once "../includes/auth.php";

// Logout admin
logoutAdmin();

// Redirect to login page
redirect(SITE_URL . "/admin/login.php");
?>
