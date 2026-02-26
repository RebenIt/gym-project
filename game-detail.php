<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    redirect(SITE_URL . '/games.php');
}

$game = fetchOne("SELECT * FROM games WHERE id = ? AND is_active = 1", [$id]);
if (!$game) {
    redirect(SITE_URL . '/games.php');
}

// Update view count
query("UPDATE games SET view_count = view_count + 1 WHERE id = ?", [$id]);

$pageTitle = getLocalized($game, 'name');
include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-white">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" class="text-white"><?= __('Home', 'سەرەتا') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/games.php" class="text-white"><?= __('Exercises', 'ڕاهێنانەکان') ?></a></li>
                <li class="breadcrumb-item active text-white"><?= e(getLocalized($game, 'name')) ?></li>
            </ol>
        </nav>
        <h1 class="display-4 fw-bold"><?= e(getLocalized($game, 'name')) ?></h1>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Video -->
            <?php if ($game['youtube_url']): ?>
                <div class="mb-4">
                    <div class="ratio ratio-16x9" style="border-radius: 15px; overflow: hidden;">
                        <iframe src="<?= e(getYouTubeEmbed($game['youtube_url'])) ?>" allowfullscreen></iframe>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Description -->
            <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; background: white;">
                <div class="card-body p-4" style="background: white;">
                    <h3 class="fw-bold mb-3" style="color: #1f2937;"><?= __('Description', 'وەسف') ?></h3>
                    <p class="lead" style="color: #4b5563;"><?= nl2br(e(getLocalized($game, 'description'))) ?></p>
                </div>
            </div>

            <!-- Instructions -->
            <?php if ($game['instructions']): ?>
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; background: white;">
                    <div class="card-body p-4" style="background: white;">
                        <h3 class="fw-bold mb-3" style="color: #1f2937;"><?= __('Instructions', 'ڕێنماییەکان') ?></h3>
                        <div style="color: #4b5563;"><?= nl2br(e(getLocalized($game, 'instructions'))) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tips -->
            <?php if ($game['tips']): ?>
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; background: white;">
                    <div class="card-body p-4" style="background: white;">
                        <h3 class="fw-bold mb-3" style="color: #1f2937;">💡 <?= __('Pro Tips', 'ئامۆژگاری') ?></h3>
                        <div style="color: #4b5563;"><?= nl2br(e(getLocalized($game, 'tips'))) ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Quick Info -->
            <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; background: linear-gradient(135deg, #f97316, #dc2626); color: white;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><?= __('Quick Info', 'زانیاری خێرا') ?></h5>

                    <div class="mb-3">
                        <strong><?= __('Difficulty', 'ئاستی سەختی') ?>:</strong><br>
                        <?php
                        $diffLabels = ['beginner' => __('Beginner', 'سەرەتایی'), 'intermediate' => __('Intermediate', 'مامناوەند'), 'advanced' => __('Advanced', 'پێشکەوتوو')];
                        echo $diffLabels[$game['difficulty']] ?? $game['difficulty'];
                        ?>
                    </div>

                    <div class="mb-3">
                        <strong><?= __('Target Muscles', 'ماسولکەی ئامانج') ?>:</strong><br>
                        <?= e(getLocalized($game, 'muscle_group')) ?>
                    </div>

                    <?php if ($game['equipment_needed']): ?>
                        <div class="mb-3">
                            <strong><?= __('Equipment', 'ئامێر') ?>:</strong><br>
                            <?= e(getLocalized($game, 'equipment_needed')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($game['duration_minutes']): ?>
                        <div class="mb-3">
                            <strong>⏱️ <?= __('Duration', 'ماوە') ?>:</strong><br>
                            <?= $game['duration_minutes'] ?> <?= __('minutes', 'خولەک') ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($game['calories_burn']): ?>
                        <div class="mb-3">
                            <strong>🔥 <?= __('Calories', 'کالۆری') ?>:</strong><br>
                            ~<?= $game['calories_burn'] ?> cal
                        </div>
                    <?php endif; ?>

                    <div class="mb-0">
                        <strong>👁️ <?= __('Views', 'سەردان') ?>:</strong><br>
                        <?= number_format($game['view_count']) ?>
                    </div>
                </div>
            </div>

            <!-- Add to List (if logged in) -->
            <?php if (isLoggedIn()): ?>
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; background: white;">
                    <div class="card-body p-4" style="background: white;">
                        <h5 class="fw-bold mb-3" style="color: #1f2937;"><?= __('Add to Your List', 'زیادکردن بۆ لیستەکەت') ?></h5>
                        <a href="<?= SITE_URL ?>/user/my-lists.php?add=<?= $game['id'] ?>" class="btn btn-primary w-100">
                            <?= __('Add to My List', 'زیادکردن بۆ لیستم') ?>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; background: white;">
                    <div class="card-body p-4 text-center" style="background: white;">
                        <p class="mb-3" style="color: #1f2937;"><?= __('Login to add this to your personal workout list', 'بچۆ ژوورەوە بۆ زیادکردنی بۆ لیستی کارەکانت') ?></p>
                        <a href="<?= SITE_URL ?>/login.php" class="btn btn-primary w-100">
                            <?= __('Login', 'چوونەژوورەوە') ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Share -->
            <div class="card shadow-sm" style="border: none; border-radius: 15px; background: white;">
                <div class="card-body p-4" style="background: white;">
                    <h5 class="fw-bold mb-3" style="color: #1f2937;"><?= __('Share', 'هاوبەشکردن') ?></h5>
                    <div class="d-grid gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . '/game-detail.php?id=' . $game['id']) ?>" target="_blank" class="btn btn-outline-primary">
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(SITE_URL . '/game-detail.php?id=' . $game['id']) ?>&text=<?= urlencode(getLocalized($game, 'name')) ?>" target="_blank" class="btn btn-outline-primary">
                            Twitter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
