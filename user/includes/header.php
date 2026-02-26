<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();

$currentUser = getCurrentUser();
$lang = getCurrentLang();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="<?= $lang === 'ku' ? 'ckb' : 'en' ?>" dir="<?= $lang === 'ku' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= __('Dashboard', 'داشبۆرد') ?> - <?= SITE_NAME ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">

    <style>
        body {
            background: #f9fafb;
            font-family: 'Poppins', 'Noto Sans Arabic', sans-serif;
        }
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 0;
        }
        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
                z-index: 1000;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="topbar">
                <div>
                    <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">☰</button>
                    <h5 class="mb-0 d-inline-block ms-3"><?= isset($pageTitle) ? e($pageTitle) : '' ?></h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="?lang=<?= $lang === 'en' ? 'ku' : 'en' ?>" class="btn btn-sm btn-outline-secondary">
                        <?= $lang === 'en' ? 'کوردی' : 'English' ?>
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <?= e($currentUser['username']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><?= __('Profile', 'پڕۆفایل') ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/index.php"><?= __('Visit Site', 'سەردانی ماڵپەڕ') ?></a></li>
                            <li><a class="dropdown-item" href="logout.php"><?= __('Logout', 'چوونەدەرەوە') ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <?php
                $flash = getFlash();
                if ($flash):
                ?>
                    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
                        <?= e($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
