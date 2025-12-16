<?php
/**
 * Admin Dashboard
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireAdminLogin();

$admin = getCurrentAdmin();

// Get dashboard statistics
$stats = [
    'users' => fetchOne("SELECT COUNT(*) as count FROM users")['count'],
    'games' => fetchOne("SELECT COUNT(*) as count FROM games WHERE is_active = 1")['count'],
    'trainers' => fetchOne("SELECT COUNT(*) as count FROM trainers WHERE is_active = 1")['count'],
    'messages' => fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'],
    'tips' => fetchOne("SELECT COUNT(*) as count FROM tips WHERE is_published = 1")['count'],
    'new_users_today' => fetchOne("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()")['count'],
];

// Recent messages
$recentMessages = fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");

// Recent users
$recentUsers = fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-content">
            <div class="admin-header">
                <div>
                    <h1>Dashboard</h1>
                    <p style="color: var(--light-dim);">Welcome back, <?php echo e($admin['full_name']); ?></p>
                </div>
                <a href="../index.php" target="_blank" class="btn btn-secondary btn-sm">View Website</a>
            </div>
            
            <!-- Stats -->
            <div class="admin-stats">
                <div class="admin-stat-card">
                    <div class="icon">👥</div>
                    <div class="value"><?php echo $stats['users']; ?></div>
                    <div class="label">Total Users</div>
                </div>
                <div class="admin-stat-card">
                    <div class="icon">🎯</div>
                    <div class="value"><?php echo $stats['games']; ?></div>
                    <div class="label">Games/Exercises</div>
                </div>
                <div class="admin-stat-card">
                    <div class="icon">👨‍🏫</div>
                    <div class="value"><?php echo $stats['trainers']; ?></div>
                    <div class="label">Trainers</div>
                </div>
                <div class="admin-stat-card">
                    <div class="icon">📩</div>
                    <div class="value"><?php echo $stats['messages']; ?></div>
                    <div class="label">Unread Messages</div>
                </div>
                <div class="admin-stat-card">
                    <div class="icon">💡</div>
                    <div class="value"><?php echo $stats['tips']; ?></div>
                    <div class="label">Published Tips</div>
                </div>
                <div class="admin-stat-card">
                    <div class="icon">🆕</div>
                    <div class="value"><?php echo $stats['new_users_today']; ?></div>
                    <div class="label">New Users Today</div>
                </div>
            </div>
            
            <div class="admin-grid">
                <!-- Recent Messages -->
                <div class="card">
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3>Recent Messages</h3>
                            <a href="messages.php" class="btn btn-secondary btn-sm">View All</a>
                        </div>
                        
                        <?php if (empty($recentMessages)): ?>
                            <p style="color: var(--light-dim); text-align: center; padding: 30px;">No messages yet</p>
                        <?php else: ?>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentMessages as $msg): ?>
                                        <tr>
                                            <td><?php echo e($msg['name']); ?></td>
                                            <td><?php echo e($msg['phone']); ?></td>
                                            <td>
                                                <?php if ($msg['is_read']): ?>
                                                    <span class="badge badge-success">Read</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">New</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo timeAgo($msg['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Recent Users -->
                <div class="card">
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3>Recent Users</h3>
                            <a href="users.php" class="btn btn-secondary btn-sm">View All</a>
                        </div>
                        
                        <?php if (empty($recentUsers)): ?>
                            <p style="color: var(--light-dim); text-align: center; padding: 30px;">No users yet</p>
                        <?php else: ?>
                            <?php foreach ($recentUsers as $user): ?>
                                <div style="display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--dark-border);">
                                    <img src="<?php echo uploadUrl($user['avatar']); ?>" alt="" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    <div>
                                        <div style="font-weight: 500;"><?php echo e($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                        <div style="font-size: 0.85rem; color: var(--light-dim);"><?php echo e($user['email']); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card" style="margin-top: 30px;">
                <div class="card-body">
                    <h3 style="margin-bottom: 20px;">Quick Actions</h3>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="games.php?action=add" class="btn btn-primary">+ Add New Game</a>
                        <a href="tips.php?action=add" class="btn btn-primary">+ Add New Tip</a>
                        <a href="trainers.php?action=add" class="btn btn-secondary">+ Add Trainer</a>
                        <a href="settings.php" class="btn btn-secondary">Edit Settings</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
