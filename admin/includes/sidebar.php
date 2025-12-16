<?php
/**
 * Admin Sidebar
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$unreadMessages = fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'];
?>
<aside class="admin-sidebar">
    <div class="sidebar-logo">
        <a href="index.php" class="logo"><?php echo getSetting('site_name'); ?></a>
        <p>Admin Panel</p>
    </div>
    
    <ul class="sidebar-menu">
        <li><a href="index.php" class="<?php echo $currentPage === 'index' ? 'active' : ''; ?>"><span class="icon">📊</span> Dashboard</a></li>
        <li><a href="users.php" class="<?php echo $currentPage === 'users' ? 'active' : ''; ?>"><span class="icon">👥</span> Users</a></li>
        <li><a href="games.php" class="<?php echo $currentPage === 'games' ? 'active' : ''; ?>"><span class="icon">🎯</span> Games/Exercises</a></li>
        <li><a href="trainers.php" class="<?php echo $currentPage === 'trainers' ? 'active' : ''; ?>"><span class="icon">👨‍🏫</span> Trainers</a></li>
        <li><a href="services.php" class="<?php echo $currentPage === 'services' ? 'active' : ''; ?>"><span class="icon">💪</span> Services</a></li>
        <li><a href="plans.php" class="<?php echo $currentPage === 'plans' ? 'active' : ''; ?>"><span class="icon">💳</span> Plans</a></li>
        <li><a href="tips.php" class="<?php echo $currentPage === 'tips' ? 'active' : ''; ?>"><span class="icon">💡</span> Tips & News</a></li>
        <li><a href="beginner.php" class="<?php echo $currentPage === 'beginner' ? 'active' : ''; ?>"><span class="icon">🌟</span> Beginner Program</a></li>
        <li><a href="certificates.php" class="<?php echo $currentPage === 'certificates' ? 'active' : ''; ?>"><span class="icon">🏆</span> Certificates</a></li>
        <li><a href="reviews.php" class="<?php echo $currentPage === 'reviews' ? 'active' : ''; ?>"><span class="icon">⭐</span> Reviews</a></li>
        <li>
            <a href="messages.php" class="<?php echo $currentPage === 'messages' ? 'active' : ''; ?>">
                <span class="icon">📩</span> Messages
                <?php if($unreadMessages > 0): ?><span class="badge badge-danger"><?php echo $unreadMessages; ?></span><?php endif; ?>
            </a>
        </li>
        <li><a href="settings.php" class="<?php echo $currentPage === 'settings' ? 'active' : ''; ?>"><span class="icon">⚙️</span> Settings</a></li>
    </ul>
    
    <div class="sidebar-footer">
        <a href="logout.php"><span>🚪</span> Logout</a>
    </div>
</aside>
