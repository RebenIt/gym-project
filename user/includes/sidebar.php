<div class="sidebar" id="sidebar">
    <div class="p-4 text-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <h4 class="fw-bold mb-0" style="background: linear-gradient(135deg, #f97316, #dc2626); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?= SITE_NAME ?>
        </h4>
        <small class="text-muted"><?= __('Member Dashboard', 'داشبۆردی ئەندام') ?></small>
    </div>

    <div class="p-3">
        <div class="text-center mb-4 p-3" style="background: rgba(255,255,255,0.05); border-radius: 10px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #f97316, #dc2626); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 2rem; color: white;">
                <?= substr($currentUser['username'], 0, 1) ?>
            </div>
            <h6 class="mb-0"><?= e($currentUser['first_name']) ?> <?= e($currentUser['last_name']) ?></h6>
            <small class="text-muted"><?= e($currentUser['email']) ?></small>
        </div>

        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" style="color: <?= $currentPage === 'dashboard' ? '#f97316' : '#fff' ?>; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; <?= $currentPage === 'dashboard' ? 'background: rgba(249,115,22,0.1);' : '' ?>">
                📊 <?= __('Dashboard', 'داشبۆرد') ?>
            </a>
            <a href="my-lists.php" class="nav-link <?= $currentPage === 'my-lists' ? 'active' : '' ?>" style="color: <?= $currentPage === 'my-lists' ? '#f97316' : '#fff' ?>; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; <?= $currentPage === 'my-lists' ? 'background: rgba(249,115,22,0.1);' : '' ?>">
                📝 <?= __('My Lists', 'لیستەکانم') ?>
            </a>
            <a href="notes.php" class="nav-link <?= $currentPage === 'notes' ? 'active' : '' ?>" style="color: <?= $currentPage === 'notes' ? '#f97316' : '#fff' ?>; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; <?= $currentPage === 'notes' ? 'background: rgba(249,115,22,0.1);' : '' ?>">
                📅 <?= __('Daily Notes', 'تێبینیە ڕۆژانەکان') ?>
            </a>
            <a href="profile.php" class="nav-link <?= $currentPage === 'profile' ? 'active' : '' ?>" style="color: <?= $currentPage === 'profile' ? '#f97316' : '#fff' ?>; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; <?= $currentPage === 'profile' ? 'background: rgba(249,115,22,0.1);' : '' ?>">
                👤 <?= __('Profile', 'پڕۆفایل') ?>
            </a>

            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">

            <a href="<?= SITE_URL ?>/index.php" class="nav-link" style="color: #fff; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px;">
                🏠 <?= __('Back to Home', 'گەڕانەوە بۆ سەرەتا') ?>
            </a>
            <a href="logout.php" class="nav-link" style="color: #fff; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px;">
                🚪 <?= __('Logout', 'چوونەدەرەوە') ?>
            </a>
        </nav>
    </div>
</div>

<style>
.nav-link {
    transition: all 0.3s;
}
.nav-link:hover {
    background: rgba(249,115,22,0.1) !important;
    color: #f97316 !important;
}
</style>
