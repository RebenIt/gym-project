<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Destroy session
session_destroy();
$_SESSION = array();

// Redirect to home
redirect(SITE_URL . '/index.php');
?>
