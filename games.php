<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();
$pageTitle = __('Exercises', 'ڕاهێنانەکان');

// Filters
$difficulty = $_GET['difficulty'] ?? '';
$muscle_group = $_GET['muscle'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where = ["is_active = 1"];
$params = [];

if ($difficulty) {
    $where[] = "difficulty = ?";
    $params[] = $difficulty;
}

if ($muscle_group) {
    $where[] = "(muscle_group LIKE ? OR muscle_group_ku LIKE ?)";
    $params[] = "%$muscle_group%";
    $params[] = "%$muscle_group%";
}

if ($search) {
    $where[] = "(name LIKE ? OR name_ku LIKE ? OR description LIKE ? OR description_ku LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSql = implode(' AND ', $where);
$games = fetchAll("SELECT * FROM games WHERE $whereSql ORDER BY sort_order, name", $params);

// Get unique muscle groups for filter
$muscleGroups = fetchAll("SELECT DISTINCT muscle_group FROM games WHERE is_active = 1 AND muscle_group IS NOT NULL ORDER BY muscle_group");

include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?= __('Browse Exercises', 'گەڕان لە ڕاهێنانەکان') ?></h1>
        <p class="lead"><?= __('Discover the perfect exercises for your fitness goals', 'ڕاهێنانی گونجاو بدۆزەوە بۆ ئامانجەکانت') ?></p>
    </div>
</div>

<div class="container py-5">
    <!-- Filters -->
    <div class="card shadow-sm mb-4" style="background: white;">
        <div class="card-body" style="background: white;">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" style="color: #1f2937;"><?= __('Search', 'گەڕان') ?></label>
                    <input type="text" name="search" class="form-control" placeholder="<?= __('Search exercises...', 'گەڕان لە ڕاهێنانەکان...') ?>" value="<?= e($search) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="color: #1f2937;"><?= __('Difficulty', 'ئاستی سەختی') ?></label>
                    <select name="difficulty" class="form-select">
                        <option value=""><?= __('All Levels', 'هەموو ئاستەکان') ?></option>
                        <option value="beginner" <?= $difficulty === 'beginner' ? 'selected' : '' ?>><?= __('Beginner', 'سەرەتایی') ?></option>
                        <option value="intermediate" <?= $difficulty === 'intermediate' ? 'selected' : '' ?>><?= __('Intermediate', 'مامناوەند') ?></option>
                        <option value="advanced" <?= $difficulty === 'advanced' ? 'selected' : '' ?>><?= __('Advanced', 'پێشکەوتوو') ?></option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="color: #1f2937;"><?= __('Muscle Group', 'گرووپی ماسولکە') ?></label>
                    <select name="muscle" class="form-select">
                        <option value=""><?= __('All Muscles', 'هەموو ماسولکەکان') ?></option>
                        <?php foreach ($muscleGroups as $mg): ?>
                            <option value="<?= e($mg['muscle_group']) ?>" <?= $muscle_group === $mg['muscle_group'] ? 'selected' : '' ?>>
                                <?= e($mg['muscle_group']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><?= __('Filter', 'پاڵاوتن') ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <?php if (empty($games)): ?>
        <div class="alert alert-info text-center">
            <h5><?= __('No exercises found', 'هیچ ڕاهێنانێک نەدۆزرایەوە') ?></h5>
            <p class="mb-0"><?= __('Try adjusting your filters', 'فیلتەرەکانت بگۆڕە') ?></p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($games as $game): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm game-card" style="border: none; border-radius: 15px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; background: white;">
                        <div style="height: 200px; background: linear-gradient(135deg, #f97316, #dc2626); display: flex; align-items: center; justify-content: center; font-size: 60px; color: white;">
                            🏋️
                        </div>
                        <div class="card-body" style="background: white;">
                            <div class="mb-2">
                                <span class="badge" style="background: linear-gradient(135deg, #f97316, #dc2626);">
                                    <?php
                                    $diffLabels = ['beginner' => __('Beginner', 'سەرەتایی'), 'intermediate' => __('Intermediate', 'مامناوەند'), 'advanced' => __('Advanced', 'پێشکەوتوو')];
                                    echo $diffLabels[$game['difficulty']] ?? $game['difficulty'];
                                    ?>
                                </span>
                                <?php if ($game['is_beginner_friendly']): ?>
                                    <span class="badge bg-success"><?= __('Beginner Friendly', 'گونجاو بۆ سەرەتایی') ?></span>
                                <?php endif; ?>
                            </div>
                            <h5 class="card-title fw-bold" style="color: #1f2937;"><?= e(getLocalized($game, 'name')) ?></h5>
                            <p class="card-text" style="color: #6b7280;"><?= truncate(e(getLocalized($game, 'short_description')), 100) ?></p>

                            <div class="mb-3">
                                <small style="color: #6b7280;">
                                    <strong style="color: #1f2937;"><?= __('Target:', 'ئامانج:') ?></strong> <?= e(getLocalized($game, 'muscle_group')) ?>
                                </small>
                            </div>

                            <?php if ($game['duration_minutes']): ?>
                                <div class="mb-2">
                                    <small>⏱️ <?= $game['duration_minutes'] ?> <?= __('min', 'خولەک') ?></small>
                                    <?php if ($game['calories_burn']): ?>
                                        | <small>🔥 ~<?= $game['calories_burn'] ?> <?= __('cal', 'کالۆری') ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <a href="<?= SITE_URL ?>/game-detail.php?id=<?= $game['id'] ?>" class="btn btn-primary w-100 mt-2">
                                <?= __('View Details', 'وردەکاری ببینە') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.game-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}
</style>

<?php include 'includes/footer.php'; ?>
