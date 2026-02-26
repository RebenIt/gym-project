<?php
/**
 * Edit Workout List
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();
$lang = getCurrentLang();
$listId = intval($_GET['id'] ?? 0);

// Get the list
$list = fetchOne("SELECT * FROM user_game_lists WHERE id = ? AND user_id = ?", [$listId, $user['id']]);

if (!$list) {
    setFlash('error', 'List not found');
    redirect('dashboard.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request');
        redirect('edit-list.php?id=' . $listId);
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $name = sanitize($_POST['name'] ?? '');
        $nameKu = sanitize($_POST['name_ku'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $descriptionKu = sanitize($_POST['description_ku'] ?? '');
        $isDefault = isset($_POST['is_default']) ? 1 : 0;

        if (empty($name)) {
            setFlash('error', 'List name is required');
        } else {
            // If setting as default, unset other defaults
            if ($isDefault) {
                query("UPDATE user_game_lists SET is_default = 0 WHERE user_id = ?", [$user['id']]);
            }

            query("UPDATE user_game_lists SET name = ?, name_ku = ?, description = ?, description_ku = ?, is_default = ?, updated_at = NOW()
                   WHERE id = ?", [$name, $nameKu, $description, $descriptionKu, $isDefault, $listId]);

            setFlash('success', 'List updated successfully!');
            $list = fetchOne("SELECT * FROM user_game_lists WHERE id = ?", [$listId]);
        }
    } elseif ($action === 'delete') {
        query("DELETE FROM user_list_games WHERE list_id = ?", [$listId]);
        query("DELETE FROM user_game_lists WHERE id = ?", [$listId]);
        setFlash('success', 'List deleted successfully!');
        redirect('dashboard.php');
    }
}

$pageTitle = $lang === 'ku' ? 'دەستکاریکردنی لیست' : 'Edit List';
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
                <a href="?lang=en&id=<?php echo $listId; ?>" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
                <a href="?lang=ku&id=<?php echo $listId; ?>" class="lang-btn <?php echo $lang === 'ku' ? 'active' : ''; ?>">کوردی</a>
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
                <h1><?php echo $pageTitle; ?>: <?php echo e(getLocalized($list, 'name')); ?></h1>
            </div>

            <?php displayFlash(); ?>

            <div class="card">
                <div class="card-body" style="padding: 40px;">
                    <form method="POST">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="update">

                        <div class="form-group">
                            <label class="form-label"><?php echo $lang === 'ku' ? 'ناوی لیست (ئینگلیزی)' : 'List Name (English)'; ?> *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo e($list['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo $lang === 'ku' ? 'ناوی لیست (کوردی)' : 'List Name (Kurdish)'; ?></label>
                            <input type="text" name="name_ku" class="form-control" dir="rtl" value="<?php echo e($list['name_ku']); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo $lang === 'ku' ? 'وەسف (ئینگلیزی)' : 'Description (English)'; ?></label>
                            <textarea name="description" class="form-control" rows="3"><?php echo e($list['description']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?php echo $lang === 'ku' ? 'وەسف (کوردی)' : 'Description (Kurdish)'; ?></label>
                            <textarea name="description_ku" class="form-control" rows="3" dir="rtl"><?php echo e($list['description_ku']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-check">
                                <input type="checkbox" name="is_default" <?php echo $list['is_default'] ? 'checked' : ''; ?>>
                                <span><?php echo $lang === 'ku' ? 'وەک لیستی سەرەکی' : 'Set as default list'; ?></span>
                            </label>
                        </div>

                        <div style="display: flex; gap: 15px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary"><?php echo $lang === 'ku' ? 'نوێکردنەوە' : 'Update List'; ?></button>
                            <a href="view-list.php?id=<?php echo $listId; ?>" class="btn btn-secondary"><?php echo $lang === 'ku' ? 'بینین' : 'View List'; ?></a>
                            <a href="dashboard.php" class="btn btn-outline"><?php echo $lang === 'ku' ? 'گەڕانەوە' : 'Back'; ?></a>
                        </div>
                    </form>

                    <hr style="margin: 40px 0; border-color: var(--dark-border);">

                    <form method="POST" onsubmit="return confirm('<?php echo $lang === 'ku' ? 'دڵنیایت لە سڕینەوەی ئەم لیستە؟' : 'Are you sure you want to delete this list?'; ?>')">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="delete">
                        <h4 style="margin-bottom: 15px; color: var(--danger);"><?php echo $lang === 'ku' ? 'سڕینەوەی لیست' : 'Delete List'; ?></h4>
                        <p style="color: var(--light-dim); margin-bottom: 20px;">
                            <?php echo $lang === 'ku' ? 'ئەم لیستە و هەموو ڕاهێنانەکانی دەسڕێتەوە.' : 'This will permanently delete this list and all its exercises.'; ?>
                        </p>
                        <button type="submit" class="btn btn-danger"><?php echo $lang === 'ku' ? 'سڕینەوەی لیست' : 'Delete List'; ?></button>
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
