<?php
require_once '../includes/auth.php';
logoutAdmin();
redirect(SITE_URL . '/admin/login.php');
