<?php
require_once "../includes/auth.php";

// Logout user
logoutUser();

// Redirect to homepage
redirect(SITE_URL . "/index.php");
?>
