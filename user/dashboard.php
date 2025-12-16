<?php
/**
 * User Dashboard
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();
$lang = getCurrentLang();

// Get user stats
$totalLists = fetchOne("SELECT COUNT(*) as count FROM user_game_lists WHERE user_id = ?", [$user['id']])['count'];
$totalNotes = fetchOne("SELECT COUNT(*) as count FROM user_notes WHERE user_id = ?", [$user['id']])['count'];
$todayNote = fetchOne("SELECT * FROM user_notes WHERE user_id = ? AND note_date = CURDATE()", [$user['id']]);

// Get user's game lists
$gameLists = fetchAll("SELECT * FROM user_game_lists WHERE user_id = ? ORDER BY is_default DESC, created_at DESC", [$user['id']]);

// Get recent notes
$recentNotes = fetchAll("SELECT * FROM user_notes WHERE user_id = ? ORDER BY note_date DESC LIMIT 5", [$user['id']]);

$pageTitle = $lang === 'ku' ? 'داشبۆرد' : 'Dashboard';
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
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        .quick-note-form { background: var(--dark-light); padding: 25px; border-radius: var(--radius-lg); border: 1px solid var(--dark-border); }
        .list-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--dark); border-radius: var(--radius-md); margin-bottom: 10px; }
        .list-item:hover { background: var(--dark-lighter); }
        .note-item { padding: 15px; background: var(--dark); border-radius: var(--radius-md); margin-bottom: 10px; border-left: 3px solid var(--primary); }
        html[dir="rtl"] .note-item { border-left: none; border-right: 3px solid var(--primary); }
        .note-date { font-size: 0.85rem; color: var(--primary); margin-bottom: 5px; }
        .note-content { color: var(--light-dim); font-size: 0.95rem; }
        .mood-badge { padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 600; }
        .mood-great { background: #22c55e20; color: #22c55e; }
        .mood-good { background: #3b82f620; color: #3b82f6; }
        .mood-okay { background: #eab30820; color: #eab308; }
        .mood-tired { background: #f9731620; color: #f97316; }
        .mood-bad { background: #ef444420; color: #ef4444; }
        @media (max-width: 1024px) { .dashboard-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar scrolled">
        <a href="../index.php" class="logo"><?php echo getSetting('site_name', $lang); ?></a>
        <ul class="nav-links">
            <li><a href="../index.php"><?php echo $lang === 'ku' ? 'ماڵەوە' : 'Home'; ?></a></li>
            <li><a href="../pages/games.php"><?php echo $lang === 'ku' ? 'یاریەکان' : 'Games'; ?></a></li>
            <li><a href="../pages/tips.php"><?php echo $lang === 'ku' ? 'ئامۆژگاریەکان' : 'Tips'; ?></a></li>
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

    <main class="dashboard">
        <div class="container">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <img src="<?php echo uploadUrl($user['avatar']); ?>" alt="Avatar" class="dashboard-avatar">
                <div class="dashboard-welcome">
                    <h2><?php echo $lang === 'ku' ? 'بەخێربێیت، ' : 'Welcome, '; ?><?php echo e(getLocalized($user, 'first_name')); ?>!</h2>
                    <p><?php echo $lang === 'ku' ? 'ئەمڕۆ چی کردووە؟ ڕاهێنانەکانت بەردەوام بکە!' : 'What have you done today? Keep up with your training!'; ?></p>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-value"><?php echo $totalLists; ?></div>
                    <div class="stat-label"><?php echo $lang === 'ku' ? 'لیستی ڕاهێنان' : 'Workout Lists'; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📝</div>
                    <div class="stat-value"><?php echo $totalNotes; ?></div>
                    <div class="stat-label"><?php echo $lang === 'ku' ? 'تێبینیەکان' : 'Notes'; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-value"><?php echo $todayNote ? '✓' : '✗'; ?></div>
                    <div class="stat-label"><?php echo $lang === 'ku' ? 'تێبینی ئەمڕۆ' : "Today's Note"; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚡</div>
                    <div class="stat-value"><?php echo timeAgo($user['last_login']); ?></div>
                    <div class="stat-label"><?php echo $lang === 'ku' ? 'دوایین چالاکی' : 'Last Active'; ?></div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <!-- Main Content -->
                <div>
                    <!-- Quick Note -->
                    <div class="quick-note-form">
                        <h3 style="margin-bottom: 20px;"><?php echo $lang === 'ku' ? 'تێبینی ئەمڕۆ' : "Today's Note"; ?></h3>
                        <form method="POST" action="save-note.php">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="note_date" value="<?php echo date('Y-m-d'); ?>">
                            
                            <div class="form-group">
                                <label class="form-label"><?php echo $lang === 'ku' ? 'کەیفی ئەمڕۆ چۆنە؟' : 'How do you feel today?'; ?></label>
                                <select name="mood" class="form-control">
                                    <option value="great" <?php echo ($todayNote['mood'] ?? '') === 'great' ? 'selected' : ''; ?>>😄 <?php echo $lang === 'ku' ? 'نایاب' : 'Great'; ?></option>
                                    <option value="good" <?php echo ($todayNote['mood'] ?? 'good') === 'good' ? 'selected' : ''; ?>>🙂 <?php echo $lang === 'ku' ? 'باش' : 'Good'; ?></option>
                                    <option value="okay" <?php echo ($todayNote['mood'] ?? '') === 'okay' ? 'selected' : ''; ?>>😐 <?php echo $lang === 'ku' ? 'باشە' : 'Okay'; ?></option>
                                    <option value="tired" <?php echo ($todayNote['mood'] ?? '') === 'tired' ? 'selected' : ''; ?>>😴 <?php echo $lang === 'ku' ? 'ماندوو' : 'Tired'; ?></option>
                                    <option value="bad" <?php echo ($todayNote['mood'] ?? '') === 'bad' ? 'selected' : ''; ?>>😞 <?php echo $lang === 'ku' ? 'خراپ' : 'Bad'; ?></option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label"><?php echo $lang === 'ku' ? 'تێبینی' : 'Note'; ?></label>
                                <textarea name="content" class="form-control" rows="4" placeholder="<?php echo $lang === 'ku' ? 'تێبینیەکانت بنووسە...' : 'Write your notes...'; ?>"><?php echo e($todayNote['content'] ?? ''); ?></textarea>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="form-group">
                                    <label class="form-label"><?php echo $lang === 'ku' ? 'کێش (کگ)' : 'Weight (kg)'; ?></label>
                                    <input type="number" step="0.1" name="weight_kg" class="form-control" value="<?php echo e($todayNote['weight_kg'] ?? ''); ?>" placeholder="70.5">
                                </div>
                                <div class="form-group">
                                    <label class="form-label"><?php echo $lang === 'ku' ? 'ڕاهێنانت کرد؟' : 'Did you workout?'; ?></label>
                                    <select name="workout_done" class="form-control">
                                        <option value="0" <?php echo ($todayNote['workout_done'] ?? 0) == 0 ? 'selected' : ''; ?>><?php echo $lang === 'ku' ? 'نەخێر' : 'No'; ?></option>
                                        <option value="1" <?php echo ($todayNote['workout_done'] ?? 0) == 1 ? 'selected' : ''; ?>><?php echo $lang === 'ku' ? 'بەڵێ' : 'Yes'; ?></option>
                                    </select>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary"><?php echo $lang === 'ku' ? 'پاشەکەوت بکە' : 'Save Note'; ?></button>
                        </form>
                    </div>
                    
                    <!-- My Game Lists -->
                    <div style="margin-top: 30px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3><?php echo $lang === 'ku' ? 'لیستەکانی ڕاهێنانم' : 'My Workout Lists'; ?></h3>
                            <a href="create-list.php" class="btn btn-primary btn-sm"><?php echo $lang === 'ku' ? '+ لیستی نوێ' : '+ New List'; ?></a>
                        </div>
                        
                        <?php if (empty($gameLists)): ?>
                            <div class="card" style="padding: 40px; text-align: center;">
                                <p style="color: var(--light-dim); margin-bottom: 20px;"><?php echo $lang === 'ku' ? 'هێشتا لیستت نییە' : 'No lists yet'; ?></p>
                                <a href="create-list.php" class="btn btn-primary"><?php echo $lang === 'ku' ? 'یەکەم لیستت دروست بکە' : 'Create your first list'; ?></a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($gameLists as $list): ?>
                                <div class="list-item">
                                    <div>
                                        <strong><?php echo e(getLocalized($list, 'name')); ?></strong>
                                        <?php if ($list['is_default']): ?>
                                            <span class="mood-badge mood-good" style="margin-left: 10px;"><?php echo $lang === 'ku' ? 'سەرەکی' : 'Default'; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <a href="view-list.php?id=<?php echo $list['id']; ?>" class="btn btn-secondary btn-sm"><?php echo $lang === 'ku' ? 'بینین' : 'View'; ?></a>
                                        <a href="edit-list.php?id=<?php echo $list['id']; ?>" class="btn btn-outline btn-sm"><?php echo $lang === 'ku' ? 'دەستکاری' : 'Edit'; ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div>
                    <!-- Recent Notes -->
                    <div class="card">
                        <div class="card-body">
                            <h4 style="margin-bottom: 20px;"><?php echo $lang === 'ku' ? 'تێبینیە تازەکان' : 'Recent Notes'; ?></h4>
                            
                            <?php if (empty($recentNotes)): ?>
                                <p style="color: var(--light-dim); text-align: center; padding: 20px 0;"><?php echo $lang === 'ku' ? 'هێشتا تێبینیت نییە' : 'No notes yet'; ?></p>
                            <?php else: ?>
                                <?php foreach ($recentNotes as $note): ?>
                                    <div class="note-item">
                                        <div class="note-date">
                                            <?php echo formatDate($note['note_date'], 'M d, Y'); ?>
                                            <span class="mood-badge mood-<?php echo $note['mood']; ?>" style="margin-left: 10px;">
                                                <?php echo ucfirst($note['mood']); ?>
                                            </span>
                                        </div>
                                        <div class="note-content"><?php echo e(truncate($note['content'], 100)); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <a href="notes.php" style="display: block; text-align: center; margin-top: 15px; font-size: 0.9rem;"><?php echo $lang === 'ku' ? 'هەموو تێبینیەکان ببینە' : 'View all notes'; ?> →</a>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="card" style="margin-top: 20px;">
                        <div class="card-body">
                            <h4 style="margin-bottom: 20px;"><?php echo $lang === 'ku' ? 'لینکە خێراکان' : 'Quick Links'; ?></h4>
                            <a href="../pages/games.php" class="list-item" style="text-decoration: none; color: var(--light);">
                                <span>🎯 <?php echo $lang === 'ku' ? 'هەموو یاریەکان' : 'All Games'; ?></span>
                                <span>→</span>
                            </a>
                            <a href="../pages/beginner.php" class="list-item" style="text-decoration: none; color: var(--light);">
                                <span>🌟 <?php echo $lang === 'ku' ? 'پڕۆگرامی سەرەتایی' : 'Beginner Program'; ?></span>
                                <span>→</span>
                            </a>
                            <a href="../pages/trainers.php" class="list-item" style="text-decoration: none; color: var(--light);">
                                <span>👨‍🏫 <?php echo $lang === 'ku' ? 'ڕاهێنەرەکان' : 'Trainers'; ?></span>
                                <span>→</span>
                            </a>
                            <a href="profile.php" class="list-item" style="text-decoration: none; color: var(--light);">
                                <span>⚙️ <?php echo $lang === 'ku' ? 'پڕۆفایل' : 'My Profile'; ?></span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Mobile menu toggle
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    </script>
</body>
</html>
