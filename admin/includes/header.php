<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireAdminLogin();

$currentAdmin = getCurrentAdmin();
$lang = getCurrentLang();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Get unread messages count for sidebar badge
$unreadMessages = fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= $lang === 'ku' ? 'ckb' : 'en' ?>" dir="<?= $lang === 'ku' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= __('Admin Panel', 'پانێڵی بەڕێوەبەر') ?> - <?= SITE_NAME ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Arabic:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Modern Admin Styles -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin-modern.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin-components.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin-animations.css">

    <!-- Feather Icons (CDN) -->
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        /* RTL Support */
        [dir="rtl"] .admin-sidebar {
            right: 0;
            left: auto;
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.12);
        }

        [dir="rtl"] .admin-content {
            margin-right: var(--sidebar-width);
            margin-left: 0;
        }

        [dir="rtl"] .sidebar-link.active::before {
            left: auto;
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        [dir="rtl"] .breadcrumb-nav svg {
            transform: scaleX(-1);
        }

        @media (max-width: 1024px) {
            [dir="rtl"] .admin-sidebar {
                transform: translateX(100%);
            }

            [dir="rtl"] .admin-sidebar.show {
                transform: translateX(0);
            }

            [dir="rtl"] .admin-content {
                margin-right: 0;
            }
        }

        /* Page-specific animations */
        .page-enter {
            animation: slideInUp 0.4s ease-out forwards;
        }

        /* Smooth page transitions */
        .admin-main > * {
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
        }

        .admin-main > *:nth-child(1) { animation-delay: 0.05s; }
        .admin-main > *:nth-child(2) { animation-delay: 0.1s; }
        .admin-main > *:nth-child(3) { animation-delay: 0.15s; }
        .admin-main > *:nth-child(4) { animation-delay: 0.2s; }
        .admin-main > *:nth-child(5) { animation-delay: 0.25s; }
    </style>

    <?php if (isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <div class="admin-content">
            <?php include 'topbar.php'; ?>

            <div class="admin-main page-enter">
                <?php
                // Display flash messages as toast notifications
                $flash = getFlash();
                if ($flash):
                ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast(<?= json_encode($flash['message']) ?>, <?= json_encode($flash['type']) ?>);
                        });
                    </script>
                <?php endif; ?>
