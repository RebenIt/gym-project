<?php
/**
 * View Workout List
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();
$lang = getCurrentLang();
$listId = intval($_GET['id'] ?? 0);

// Get the list
$list = fetchOne("SELECT * FROM user_lists WHERE id = ? AND user_id = ?", [$listId, $user['id']]);

if (!$list) {
    setFlash('error', 'List not found');
    redirect('dashboard.php');
}

// Get games in this list
$listGames = fetchAll("
    SELECT lg.*, g.name, g.name_ku, g.image, g.difficulty, g.muscle_group, g.muscle_group_ku, g.duration_minutes
    FROM user_list_games lg
    JOIN games g ON lg.game_id = g.id
    WHERE lg.list_id = ?
    ORDER BY lg.sort_order, lg.added_at
", [$listId]);

$pageTitle = getLocalized($list, 'name');
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
    <style>
        .game-list-item {
            display: flex;
            gap: 20px;
            background: var(--dark-light);
            padding: 20px;
            border-radius: var(--radius-lg);
            margin-bottom: 15px;
            border: 1px solid var(--dark-border);
            transition: transform 0.2s;
        }
        .game-list-item:hover {
            transform: translateX(5px);
            border-color: var(--primary);
        }
        html[dir="rtl"] .game-list-item:hover {
            transform: translateX(-5px);
        }
        .game-image {
            width: 120px;
            height: 90px;
            border-radius: var(--radius-md);
            object-fit: cover;
            flex-shrink: 0;
        }
        .game-info {
            flex: 1;
        }
        .game-meta {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .game-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.875rem;
            color: var(--light-dim);
        }
    </style>
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
        <div class="container">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <div>
                    <h1><?php echo e(getLocalized($list, 'name')); ?></h1>
                    <?php if ($list['description']): ?>
                        <p style="color: var(--light-dim); margin-top: 10px;"><?php echo e(getLocalized($list, 'description')); ?></p>
                    <?php endif; ?>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="edit-list.php?id=<?php echo $listId; ?>" class="btn btn-secondary"><?php echo $lang === 'ku' ? 'دەستکاری' : 'Edit List'; ?></a>
                    <a href="dashboard.php" class="btn btn-outline"><?php echo $lang === 'ku' ? 'گەڕانەوە' : 'Back'; ?></a>
                </div>
            </div>

            <?php displayFlash(); ?>

            <?php if (empty($listGames)): ?>
                <div class="card" style="padding: 60px; text-align: center;">
                    <div style="font-size: 4rem; margin-bottom: 20px;">🏋️</div>
                    <h3 style="margin-bottom: 15px;"><?php echo $lang === 'ku' ? 'لیستەکە بەتاڵە' : 'List is Empty'; ?></h3>
                    <p style="color: var(--light-dim); margin-bottom: 25px;">
                        <?php echo $lang === 'ku' ? 'هێشتا ڕاهێنانت زیادنەکردووە بۆ ئەم لیستە' : 'You haven\'t added any exercises to this list yet'; ?>
                    </p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <a href="../games.php" class="btn btn-primary"><?php echo $lang === 'ku' ? 'گەڕان بۆ ڕاهێنانەکان' : 'Browse Exercises'; ?></a>
                        <a href="edit-list.php?id=<?php echo $listId; ?>" class="btn btn-secondary"><?php echo $lang === 'ku' ? 'دەستکاری لیست' : 'Edit List'; ?></a>
                    </div>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 20px;">
                    <p style="color: var(--light-dim);">
                        <?php echo count($listGames); ?> <?php echo $lang === 'ku' ? 'ڕاهێنان' : 'exercises'; ?>
                        <?php
                        $totalMinutes = array_sum(array_column($listGames, 'duration_minutes'));
                        if ($totalMinutes > 0) {
                            echo ' • ' . $totalMinutes . ' ' . ($lang === 'ku' ? 'خولەک' : 'minutes total');
                        }
                        ?>
                    </p>
                </div>

                <?php foreach ($listGames as $game): ?>
                    <div class="game-list-item">
                        <img src="<?php echo uploadUrl($game['image']); ?>" alt="<?php echo e(getLocalized($game, 'name')); ?>" class="game-image">
                        <div class="game-info">
                            <h4 style="margin-bottom: 5px;">
                                <a href="../game-detail.php?id=<?php echo $game['game_id']; ?>" style="color: var(--light); text-decoration: none;">
                                    <?php echo e(getLocalized($game, 'name')); ?>
                                </a>
                            </h4>
                            <div class="game-meta">
                                <span class="game-meta-item">
                                    <span>🎯</span>
                                    <span><?php echo e(getLocalized($game, 'muscle_group')); ?></span>
                                </span>
                                <?php if ($game['duration_minutes']): ?>
                                    <span class="game-meta-item">
                                        <span>⏱️</span>
                                        <span><?php echo $game['duration_minutes']; ?> <?php echo $lang === 'ku' ? 'خولەک' : 'min'; ?></span>
                                    </span>
                                <?php endif; ?>
                                <span class="game-meta-item">
                                    <span class="badge badge-<?php echo $game['difficulty'] === 'beginner' ? 'success' : ($game['difficulty'] === 'intermediate' ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst($game['difficulty']); ?>
                                    </span>
                                </span>
                                <?php if ($game['notes']): ?>
                                    <span class="game-meta-item">
                                        <span>📝</span>
                                        <span><?php echo e($game['notes']); ?></span>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    </script>
</body>
</html>
