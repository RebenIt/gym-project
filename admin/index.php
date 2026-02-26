<?php
/**
 * Admin Dashboard - Modern Design
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireAdminLogin();

$admin = getCurrentAdmin();
$lang = getCurrentLang();

// Get dashboard statistics
$stats = [
    'users' => fetchOne("SELECT COUNT(*) as count FROM users")['count'] ?? 0,
    'games' => fetchOne("SELECT COUNT(*) as count FROM games WHERE is_active = 1")['count'] ?? 0,
    'trainers' => fetchOne("SELECT COUNT(*) as count FROM trainers WHERE is_active = 1")['count'] ?? 0,
    'messages' => fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'] ?? 0,
    'tips' => fetchOne("SELECT COUNT(*) as count FROM tips WHERE is_published = 1")['count'] ?? 0,
    'new_users_today' => fetchOne("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()")['count'] ?? 0,
    'total_messages' => fetchOne("SELECT COUNT(*) as count FROM contact_messages")['count'] ?? 0,
    'total_services' => fetchOne("SELECT COUNT(*) as count FROM services WHERE is_active = 1")['count'] ?? 0,
];

// Calculate growth percentages (mock data for now - can be real calculations)
$stats['users_growth'] = 12.5;
$stats['games_growth'] = 8.3;
$stats['trainers_growth'] = 5.0;
$stats['messages_growth'] = -3.2;

// Recent messages
$recentMessages = fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");

// Recent users
$recentUsers = fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

$pageTitle = __('Dashboard', 'داشبۆرد');
include 'includes/header.php';
?>

<!-- Welcome Hero Section -->
<div class="welcome-section">
    <h1><?= __('Welcome back', 'بەخێربێیتەوە') ?>, <?= e(getLocalized($admin, 'full_name')) ?>!</h1>
    <p><?= __("Here's what's happening with your gym today.", 'ئەمە ئەوەیە کە ئەمڕۆ لە زانکۆکەتدا ڕوودەدات.') ?></p>
</div>

<!-- Statistics Grid -->
<div class="stats-grid stagger-slide-up">
    <!-- Total Users -->
    <div class="stat-card">
        <div class="stat-card-icon primary">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
        <div class="stat-card-value"><?= number_format($stats['users']) ?></div>
        <div class="stat-card-label"><?= __('Total Users', 'کۆی بەکارهێنەران') ?></div>
        <span class="stat-card-trend up">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <?= $stats['users_growth'] ?>%
        </span>
    </div>

    <!-- Active Exercises -->
    <div class="stat-card">
        <div class="stat-card-icon success">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
        </div>
        <div class="stat-card-value"><?= number_format($stats['games']) ?></div>
        <div class="stat-card-label"><?= __('Active Exercises', 'ڕاهێنانی چالاک') ?></div>
        <span class="stat-card-trend up">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <?= $stats['games_growth'] ?>%
        </span>
    </div>

    <!-- Total Trainers -->
    <div class="stat-card">
        <div class="stat-card-icon info">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
        <div class="stat-card-value"><?= number_format($stats['trainers']) ?></div>
        <div class="stat-card-label"><?= __('Professional Trainers', 'ڕاهێنەری پیشەیی') ?></div>
        <span class="stat-card-trend up">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <?= $stats['trainers_growth'] ?>%
        </span>
    </div>

    <!-- Unread Messages -->
    <div class="stat-card">
        <div class="stat-card-icon warning">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
        </div>
        <div class="stat-card-value"><?= number_format($stats['messages']) ?></div>
        <div class="stat-card-label"><?= __('Unread Messages', 'پەیامی نەخوێندراوە') ?></div>
        <?php if ($stats['messages'] > 0): ?>
            <a href="messages.php" class="btn btn-sm btn-primary" style="margin-top: 8px;">
                <?= __('View Messages', 'بینینی پەیامەکان') ?>
            </a>
        <?php endif; ?>
    </div>

    <!-- Published Tips -->
    <div class="stat-card">
        <div class="stat-card-icon teal">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
        </div>
        <div class="stat-card-value"><?= number_format($stats['tips']) ?></div>
        <div class="stat-card-label"><?= __('Published Tips', 'ئامۆژگاری بڵاوکراوە') ?></div>
    </div>

    <!-- New Users Today -->
    <div class="stat-card">
        <div class="stat-card-icon purple">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
        </div>
        <div class="stat-card-value"><?= number_format($stats['new_users_today']) ?></div>
        <div class="stat-card-label"><?= __('New Users Today', 'بەکارهێنەری نوێ ئەمڕۆ') ?></div>
        <?php if ($stats['new_users_today'] > 0): ?>
            <span class="stat-card-trend up">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                </svg>
                <?= __('Today', 'ئەمڕۆ') ?>
            </span>
        <?php endif; ?>
    </div>
</div>

<!-- Two Column Grid for Content -->
<div class="two-column-grid" style="margin-top: 32px;">
    <!-- Recent Messages -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title"><?= __('Recent Messages', 'پەیامە نوێیەکان') ?></h3>
            <a href="messages.php" class="btn btn-sm btn-secondary"><?= __('View All', 'بینینی هەموو') ?></a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($recentMessages)): ?>
                <div class="empty-state" style="padding: 40px 24px;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: var(--gradient-primary); border-radius: var(--border-radius-2xl); display: flex; align-items: center; justify-content: center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <p style="color: var(--gray-500); font-size: 14px;"><?= __('No messages yet', 'هێشتا پەیامێک نییە') ?></p>
                </div>
            <?php else: ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($recentMessages as $index => $msg): ?>
                        <a href="messages.php?id=<?= $msg['id'] ?>" style="display: flex; gap: 16px; padding: 16px 24px; text-decoration: none; border-bottom: 1px solid var(--gray-200); transition: all var(--transition-fast); <?= $index === count($recentMessages) - 1 ? 'border-bottom: none;' : '' ?>; cursor: pointer;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
                            <div style="width: 48px; height: 48px; border-radius: var(--border-radius-full); background: var(--gradient-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0;">
                                <?= strtoupper(substr($msg['name'], 0, 1)) ?>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 4px;">
                                    <div style="font-weight: 600; font-size: 15px; color: var(--dark-900);">
                                        <?= e($msg['name']) ?>
                                    </div>
                                    <?php if (!$msg['is_read']): ?>
                                        <span class="badge badge-warning" style="flex-shrink: 0;"><?= __('New', 'نوێ') ?></span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 14px; color: var(--gray-600); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 4px;">
                                    <?= e(substr($msg['message'], 0, 60)) ?><?= strlen($msg['message']) > 60 ? '...' : '' ?>
                                </div>
                                <div style="font-size: 12px; color: var(--gray-400);">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <?= timeAgo($msg['created_at']) ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title"><?= __('Recent Users', 'بەکارهێنەرانی نوێ') ?></h3>
            <a href="users.php" class="btn btn-sm btn-secondary"><?= __('View All', 'بینینی هەموو') ?></a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($recentUsers)): ?>
                <div class="empty-state" style="padding: 40px 24px;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: var(--gradient-success); border-radius: var(--border-radius-2xl); display: flex; align-items: center; justify-content: center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <p style="color: var(--gray-500); font-size: 14px;"><?= __('No users yet', 'هێشتا بەکارهێنەرێک نییە') ?></p>
                </div>
            <?php else: ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($recentUsers as $index => $user): ?>
                        <div style="display: flex; gap: 16px; padding: 16px 24px; border-bottom: 1px solid var(--gray-200); <?= $index === count($recentUsers) - 1 ? 'border-bottom: none;' : '' ?>">
                            <?php
                            $avatarUrl = !empty($user['avatar']) ? uploadUrl($user['avatar']) : '';
                            if ($avatarUrl && file_exists($_SERVER['DOCUMENT_ROOT'] . parse_url($avatarUrl, PHP_URL_PATH))):
                            ?>
                                <img src="<?= $avatarUrl ?>" alt="" style="width: 48px; height: 48px; border-radius: var(--border-radius-full); object-fit: cover; flex-shrink: 0;">
                            <?php else: ?>
                                <div style="width: 48px; height: 48px; border-radius: var(--border-radius-full); background: var(--gradient-success); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; flex-shrink: 0;">
                                    <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; font-size: 15px; color: var(--dark-900); margin-bottom: 2px;">
                                    <?= e($user['first_name'] . ' ' . $user['last_name']) ?>
                                </div>
                                <div style="font-size: 14px; color: var(--gray-600); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 4px;">
                                    <?= e($user['email']) ?>
                                </div>
                                <div style="font-size: 12px; color: var(--gray-400);">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <?= __('Joined', 'بەشداربوو') ?> <?= timeAgo($user['created_at']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions Card -->
<div class="card" style="margin-top: 32px;">
    <div class="card-header">
        <h3 class="card-title"><?= __('Quick Actions', 'کردارە خێراکان') ?></h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <a href="games.php?action=add" class="btn btn-primary btn-lg" style="height: auto; padding: 20px; flex-direction: column; gap: 12px;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                </svg>
                <?= __('Add Exercise', 'زیادکردنی ڕاهێنان') ?>
            </a>

            <a href="trainers.php?action=add" class="btn btn-success btn-lg" style="height: auto; padding: 20px; flex-direction: column; gap: 12px;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <?= __('Add Trainer', 'زیادکردنی ڕاهێنەر') ?>
            </a>

            <a href="tips.php?action=add" class="btn btn-info btn-lg" style="height: auto; padding: 20px; flex-direction: column; gap: 12px;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <?= __('Add Tip', 'زیادکردنی ئامۆژگاری') ?>
            </a>

            <a href="settings.php" class="btn btn-secondary btn-lg" style="height: auto; padding: 20px; flex-direction: column; gap: 12px;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M12 1v6m0 6v6"></path>
                </svg>
                <?= __('Settings', 'ڕێکخستنەکان') ?>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
