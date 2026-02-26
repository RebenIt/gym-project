<?php
/**
 * Create Workout List
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();
$lang = getCurrentLang();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request');
        redirect('dashboard.php');
    }

    $name = sanitize($_POST['name'] ?? '');
    $nameKu = sanitize($_POST['name_ku'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $descriptionKu = sanitize($_POST['description_ku'] ?? '');
    $isDefault = isset($_POST['is_default']) ? 1 : 0;

    if (empty($name)) {
        setFlash('error', 'List name is required');
        redirect('create-list.php');
    }

    // If setting as default, unset other defaults
    if ($isDefault) {
        query("UPDATE user_game_lists SET is_default = 0 WHERE user_id = ?", [$user['id']]);
    }

    query("INSERT INTO user_game_lists (user_id, name, name_ku, description, description_ku, is_default, created_at)
           VALUES (?, ?, ?, ?, ?, ?, NOW())", [$user['id'], $name, $nameKu, $description, $descriptionKu, $isDefault]);

    $listId = lastInsertId();
    setFlash('success', 'Workout list created successfully!');
    redirect('edit-list.php?id=' . $listId);
}

$pageTitle = $lang === 'ku' ? 'دروستکردنی لیستی نوێ' : 'Create New List';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang === 'ku' ? 'ckb' : 'en'; ?>" dir="<?php echo $lang === 'ku' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo getSetting('site_name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar scrolled">
        <a href="../index.php" class="logo"><?php echo getSetting('site_name', $lang); ?></a>
        <ul class="nav-links">
            <li><a href="../index.php"><?php echo $lang === 'ku' ? 'ماڵەوە' : 'Home'; ?></a></li>
            <li><a href="../games.php"><?php echo $lang === 'ku' ? 'یاریەکان' : 'Games'; ?></a></li>
            <li><a href="dashboard.php" class="active"><?php echo $lang === 'ku' ? 'داشبۆرد' : 'Dashboard'; ?></a></li>
        </ul>
        <div class="nav-actions">
            <div class="lang-switcher">
                <a href="?lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
                <a href="?lang=ku" class="lang-btn <?php echo $lang === 'ku' ? 'active' : ''; ?>">کوردی</a>
            </div>
            <a href="logout.php" class="btn btn-outline btn-sm"><?php echo $lang === 'ku' ? 'دەرچوون' : 'Logout'; ?></a>
        </div>
        <div class="menu-toggle">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <main style="padding: 100px 0 60px;">
        <div class="container" style="max-width: 800px;">
            <div style="margin-bottom: 40px;">
                <h1><?php echo $pageTitle; ?></h1>
                <p style="color: var(--light-dim); margin-top: 10px;">
                    <?php echo $lang === 'ku' ? 'لیستێکی نوێ بۆ ڕاهێنانەکانت دروست بکە' : 'Create a new workout list to organize your exercises'; ?>
                </p>
            </div>

            <?php displayFlash(); ?>

            <div class="card">
                <div class="card-body" style="padding: 40px;">
                    <form method="POST">
                        <?php echo csrfField(); ?>

                        <div class="form-group">
                            <label class="form-label"><?php echo $lang === 'ku' ? 'ناوی لیست (ئینگلیزی)' : 'List Name (English)'; ?> *</label>
                            <input type="text" name="name" class="form-control" required placeholder="<?php echo $lang === 'ku' ? 'وەک: ڕاهێنانی بەیانی' : 'e.g., Morning Workout'; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo $lang === 'ku' ? 'ناوی لیست (کوردی)' : 'List Name (Kurdish)'; ?></label>
                            <input type="text" name="name_ku" class="form-control" dir="rtl" placeholder="<?php echo $lang === 'ku' ? 'ڕاهێنانی بەیانی' : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo $lang === 'ku' ? 'وەسف (ئینگلیزی)' : 'Description (English)'; ?></label>
                            <textarea name="description" class="form-control" rows="3" placeholder="<?php echo $lang === 'ku' ? 'وەسفێکی کورت بنووسە...' : 'Write a short description...'; ?>"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo $lang === 'ku' ? 'وەسف (کوردی)' : 'Description (Kurdish)'; ?></label>
                            <textarea name="description_ku" class="form-control" rows="3" dir="rtl"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" name="is_default">
                                <span><?php echo $lang === 'ku' ? 'وەک لیستی سەرەکی دایبنێ' : 'Set as default list'; ?></span>
                            </label>
                        </div>

                        <div style="display: flex; gap: 15px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary"><?php echo $lang === 'ku' ? 'دروستکردنی لیست' : 'Create List'; ?></button>
                            <a href="dashboard.php" class="btn btn-secondary"><?php echo $lang === 'ku' ? 'پاشگەزبوونەوە' : 'Cancel'; ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    </script>
</body>
</html>
