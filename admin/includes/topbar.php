<?php
// Get unread messages count
$unreadMessages = fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'] ?? 0;
?>

<div class="admin-topbar">
    <div class="topbar-left">
        <!-- Mobile Menu Toggle -->
        <button class="btn-icon d-lg-none" id="sidebarToggle" style="background: transparent; border: none; color: var(--gray-600); cursor: pointer;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <!-- Breadcrumb Navigation -->
        <div class="breadcrumb-nav">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span style="color: var(--gray-300); margin: 0 8px;">/</span>
            <span class="breadcrumb-item active"><?= isset($pageTitle) ? e($pageTitle) : __('Dashboard', 'داشبۆرد') ?></span>
        </div>
    </div>

    <div class="topbar-right">
        <!-- Global Search -->
        <div class="topbar-search d-none d-md-block">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input type="text" placeholder="<?= __('Search...', 'گەڕان...') ?>" id="globalSearch">
        </div>

        <!-- Dark Mode Toggle -->
        <button class="topbar-btn" id="darkModeToggle" title="<?= __('Toggle Dark Mode', 'گۆڕینی دۆخی تاریک') ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>

        <!-- Notifications -->
        <div class="dropdown">
            <button class="topbar-btn topbar-notifications" data-bs-toggle="dropdown">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <?php if ($unreadMessages > 0): ?>
                    <span class="notification-badge"><?= $unreadMessages > 9 ? '9+' : $unreadMessages ?></span>
                <?php endif; ?>
            </button>

            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                <li style="padding: 16px 20px; border-bottom: 1px solid var(--gray-200);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h6 style="margin: 0; font-weight: 700; font-size: 16px;"><?= __('Notifications', 'ئاگادارکردنەوەکان') ?></h6>
                        <?php if ($unreadMessages > 0): ?>
                            <span class="badge badge-primary"><?= $unreadMessages ?> <?= __('new', 'نوێ') ?></span>
                        <?php endif; ?>
                    </div>
                </li>

                <?php
                $recentMessages = fetchAll("SELECT * FROM contact_messages WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5");
                if (!empty($recentMessages)):
                    foreach ($recentMessages as $msg):
                ?>
                    <li>
                        <a class="dropdown-item" href="messages.php?id=<?= $msg['id'] ?>" style="padding: 12px 20px; white-space: normal;">
                            <div style="display: flex; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: var(--border-radius-full); background: var(--gradient-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">
                                    <?= strtoupper(substr($msg['name'], 0, 1)) ?>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; font-size: 14px; color: var(--dark-900); margin-bottom: 2px;">
                                        <?= e(substr($msg['name'], 0, 25)) ?><?= strlen($msg['name']) > 25 ? '...' : '' ?>
                                    </div>
                                    <div style="font-size: 13px; color: var(--gray-600); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= e(substr($msg['message'], 0, 40)) ?>...
                                    </div>
                                    <div style="font-size: 11px; color: var(--gray-400); margin-top: 4px;">
                                        <?= timeAgo($msg['created_at']) ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php
                    endforeach;
                else:
                ?>
                    <li style="padding: 40px 20px; text-align: center;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--gray-300); margin-bottom: 12px;">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <div style="color: var(--gray-500); font-size: 14px;"><?= __('No new notifications', 'هیچ ئاگادارکردنەوەیەکی نوێ نییە') ?></div>
                    </li>
                <?php endif; ?>

                <?php if (!empty($recentMessages)): ?>
                    <li style="border-top: 1px solid var(--gray-200); padding: 12px 20px;">
                        <a href="messages.php" style="text-decoration: none; color: var(--primary); font-weight: 600; font-size: 13px; display: block; text-align: center;">
                            <?= __('View all messages', 'بینینی هەموو پەیامەکان') ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Language Toggle -->
        <a href="?lang=<?= $lang === 'en' ? 'ku' : 'en' ?>" class="topbar-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="2" y1="12" x2="22" y2="12"></line>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </svg>
            <span><?= $lang === 'en' ? 'کوردی' : 'English' ?></span>
        </a>

        <!-- Profile Dropdown -->
        <div class="dropdown">
            <button class="topbar-profile" data-bs-toggle="dropdown">
                <div class="profile-avatar">
                    <?= strtoupper(substr(getLocalized($currentAdmin, 'full_name'), 0, 1)) ?>
                </div>
                <span class="d-none d-md-inline"><?= e(getLocalized($currentAdmin, 'full_name')) ?></span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>

            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 220px;">
                <li style="padding: 16px 20px; border-bottom: 1px solid var(--gray-200);">
                    <div style="font-weight: 700; font-size: 15px; color: var(--dark-900); margin-bottom: 2px;">
                        <?= e(getLocalized($currentAdmin, 'full_name')) ?>
                    </div>
                    <div style="font-size: 13px; color: var(--gray-500);">
                        <?= __('Administrator', 'بەڕێوەبەر') ?>
                    </div>
                </li>

                <li>
                    <a class="dropdown-item" href="<?= SITE_URL ?>/index.php" target="_blank">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                        <?= __('Visit Website', 'سەردانی ماڵپەڕ') ?>
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="settings.php">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M12 1v6m0 6v6"></path>
                            <path d="M17 7l-5 5m0 0l-5-5"></path>
                        </svg>
                        <?= __('Settings', 'ڕێکخستنەکان') ?>
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item" href="logout.php" style="color: var(--error);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <?= __('Logout', 'چوونەدەرەوە') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
