<div class="admin-sidebar" id="adminSidebar">
    <!-- Logo Section -->
    <div class="sidebar-logo">
        <div class="logo-container">
            <div class="logo-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                </svg>
            </div>
            <div class="logo-text">
                <h4 class="logo-title"><?= SITE_NAME ?></h4>
                <small class="logo-subtitle"><?= __('Admin Panel', 'پانێڵی بەڕێوەبەر') ?></small>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="sidebar-nav">
        <!-- Main Section -->
        <div class="nav-section">
            <div class="nav-section-title"><?= __('Main', 'سەرەکی') ?></div>
            <a href="index.php" class="sidebar-link <?= $currentPage === 'index' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </span>
                <span class="link-text"><?= __('Dashboard', 'داشبۆرد') ?></span>
            </a>
        </div>

        <!-- Content Management Section -->
        <div class="nav-section">
            <div class="nav-section-title"><?= __('Content', 'ناوەڕۆک') ?></div>

            <a href="games.php" class="sidebar-link <?= $currentPage === 'games' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                </span>
                <span class="link-text"><?= __('Exercises', 'ڕاهێنانەکان') ?></span>
            </a>

            <a href="trainers.php" class="sidebar-link <?= $currentPage === 'trainers' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span class="link-text"><?= __('Trainers', 'ڕاهێنەرەکان') ?></span>
            </a>

            <a href="services.php" class="sidebar-link <?= $currentPage === 'services' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 6v6l4 2"></path>
                    </svg>
                </span>
                <span class="link-text"><?= __('Services', 'خزمەتگوزاریەکان') ?></span>
            </a>

            <a href="plans.php" class="sidebar-link <?= $currentPage === 'plans' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                </span>
                <span class="link-text"><?= __('Plans', 'پلانەکان') ?></span>
            </a>

            <a href="tips.php" class="sidebar-link <?= $currentPage === 'tips' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </span>
                <span class="link-text"><?= __('Tips & News', 'ئامۆژگاری و هەواڵ') ?></span>
            </a>

            <a href="certificates.php" class="sidebar-link <?= $currentPage === 'certificates' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="7"></circle>
                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                    </svg>
                </span>
                <span class="link-text"><?= __('Certificates', 'بڕوانامەکان') ?></span>
            </a>
        </div>

        <!-- User Management Section -->
        <div class="nav-section">
            <div class="nav-section-title"><?= __('Users', 'بەکارهێنەران') ?></div>

            <a href="users.php" class="sidebar-link <?= $currentPage === 'users' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span class="link-text"><?= __('All Users', 'هەموو بەکارهێنەران') ?></span>
            </a>

            <a href="messages.php" class="sidebar-link <?= $currentPage === 'messages' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </span>
                <span class="link-text"><?= __('Messages', 'پەیامەکان') ?></span>
                <?php if ($unreadMessages > 0): ?>
                    <span class="badge badge-danger badge-pulse" style="margin-left: auto;"><?= $unreadMessages ?></span>
                <?php endif; ?>
            </a>

            <a href="beginners.php" class="sidebar-link <?= $currentPage === 'beginners' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </span>
                <span class="link-text"><?= __('Beginner Program', 'پڕۆگرامی سەرەتایی') ?></span>
            </a>
        </div>

        <!-- Settings Section -->
        <div class="nav-section">
            <div class="nav-section-title"><?= __('Configuration', 'ڕێکخستن') ?></div>

            <a href="settings.php" class="sidebar-link <?= $currentPage === 'settings' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 1v6m0 6v6M17 7l-5 5m0 0l-5-5"></path>
                    </svg>
                </span>
                <span class="link-text"><?= __('Settings', 'ڕێکخستنەکان') ?></span>
            </a>

            <a href="pages.php" class="sidebar-link <?= $currentPage === 'pages' ? 'active' : '' ?>">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                    </svg>
                </span>
                <span class="link-text"><?= __('Pages', 'لاپەڕەکان') ?></span>
            </a>
        </div>

        <!-- Footer Actions -->
        <div class="nav-section sidebar-footer-actions">
            <a href="<?= SITE_URL ?>/index.php" target="_blank" class="sidebar-link sidebar-link-secondary">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                </span>
                <span class="link-text"><?= __('View Website', 'بینینی ماڵپەڕ') ?></span>
            </a>

            <a href="logout.php" class="sidebar-link sidebar-link-danger">
                <span class="link-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </span>
                <span class="link-text"><?= __('Logout', 'چوونەدەرەوە') ?></span>
            </a>
        </div>
    </div>
</div>
