<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();
$pageTitle = __('Beginner Program', 'پڕۆگرامی سەرەتایی');

// Get beginner program (8 weeks)
$program = [];
for ($week = 1; $week <= 8; $week++) {
    $days = fetchAll("
        SELECT bg.*, g.name, g.name_ku, g.description, g.description_ku
        FROM beginner_games bg
        JOIN games g ON bg.game_id = g.id
        WHERE bg.week_number = ? AND bg.is_active = 1
        ORDER BY bg.day_of_week, bg.sort_order
    ", [$week]);

    $program[$week] = $days;
}

include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?= __('2-Month Beginner Program', 'پڕۆگرامی ٢ مانگ بۆ سەرەتاییەکان') ?></h1>
        <p class="lead"><?= __('Start your fitness journey with our comprehensive 8-week beginner program', 'گەشتی تەندروستی خۆت دەست پێبکە لەگەڵ پڕۆگرامی ٨ هەفتە بۆ سەرەتاییەکان') ?></p>
    </div>
</div>

<div style="background: white; min-height: 100vh;">
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; background: linear-gradient(135deg, #fff8f0, #ffffff);">
                <div class="card-body p-5">
                    <h3 class="fw-bold mb-4" style="color: #1f2937;">📋 <?= __('Program Overview', 'کورتەی پڕۆگرام') ?></h3>
                    <p class="mb-3" style="color: #4b5563;"><?= __('This 8-week program is designed specifically for beginners. Each week builds upon the previous one, gradually increasing intensity and complexity.', 'ئەم پڕۆگرامە ٨ هەفتەییە تایبەتە بۆ سەرەتاییەکان. هەر هەفتەیەک لەسەر هەفتەی پێشووتر دەبنیاتەوە.') ?></p>
                    <ul class="mb-0" style="color: #4b5563;">
                        <li><?= __('3 workout days per week', '٣ ڕۆژی ڕاهێنان لە هەفتەیەکدا') ?></li>
                        <li><?= __('Progressive difficulty increase', 'زیادبوونی سەختی بە دەرجە') ?></li>
                        <li><?= __('Full body workouts', 'ڕاهێنانی تەواوی جەستە') ?></li>
                        <li><?= __('Suitable for complete beginners', 'گونجاو بۆ سەرەتاییە تەواوەکان') ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php foreach ($program as $weekNum => $days): ?>
        <?php if (!empty($days)): ?>
            <div class="mb-5">
                <h2 class="fw-bold mb-4" style="color: #f97316;">
                    <?= __('Week', 'هەفتەی') ?> <?= $weekNum ?>
                </h2>

                <?php
                // Group by day
                $byDay = [];
                foreach ($days as $item) {
                    $byDay[$item['day_of_week']][] = $item;
                }

                $dayNames = [
                    'monday' => __('Monday', 'دووشەممە'),
                    'tuesday' => __('Tuesday', 'سێشەممە'),
                    'wednesday' => __('Wednesday', 'چوارشەممە'),
                    'thursday' => __('Thursday', 'پێنجشەممە'),
                    'friday' => __('Friday', 'هەینی'),
                    'saturday' => __('Saturday', 'شەممە'),
                    'sunday' => __('Sunday', 'یەکشەممە')
                ];
                ?>

                <div class="row g-4">
                    <?php foreach ($byDay as $day => $exercises): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm" style="border: none; border-radius: 15px; background: white;">
                                <div class="card-header" style="background: linear-gradient(135deg, #f97316, #dc2626); color: white; border-radius: 15px 15px 0 0;">
                                    <h5 class="mb-0 fw-bold"><?= $dayNames[$day] ?? ucfirst($day) ?></h5>
                                </div>
                                <div class="card-body p-4" style="background: white;">
                                    <?php foreach ($exercises as $exercise): ?>
                                        <div class="mb-3 pb-3" style="border-bottom: 1px solid #e5e7eb;">
                                            <h6 class="fw-bold mb-2" style="color: #1f2937;"><?= e(getLocalized($exercise, 'name')) ?></h6>
                                            <small class="d-block mb-1" style="color: #6b7280;">
                                                <?= $exercise['sets'] ?> sets × <?= e($exercise['reps']) ?> reps
                                            </small>
                                            <small class="d-block mb-1" style="color: #6b7280;">
                                                <?= __('Rest:', 'پشوو:') ?> <?= $exercise['rest_seconds'] ?>s
                                            </small>
                                            <?php if ($exercise['notes']): ?>
                                                <small class="d-block" style="color: #f97316;">
                                                    💡 <?= e(getLocalized($exercise, 'notes')) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (empty(array_filter($program))): ?>
        <div class="alert alert-info text-center">
            <h5><?= __('Beginner program is being prepared', 'پڕۆگرامی سەرەتایی ئامادە دەکرێت') ?></h5>
            <p class="mb-0"><?= __('Check back soon!', 'بەم زووانە گەڕێوە!') ?></p>
        </div>
    <?php endif; ?>

    <div class="text-center mt-5">
        <div class="card shadow-sm mx-auto" style="max-width: 600px; border: none; border-radius: 15px; background: linear-gradient(135deg, #f97316, #dc2626); color: white;">
            <div class="card-body p-5">
                <h4 class="fw-bold mb-3"><?= __('Ready to Start?', 'ئامادەیت دەست پێبکەیت؟') ?></h4>
                <p class="mb-4"><?= __('Join us today and get access to this program plus expert guidance!', 'ئەمڕۆ بەشداربە و دەستگەیشتن بە ئەم پڕۆگرامە بەدەست بهێنە!') ?></p>
                <a href="<?= SITE_URL ?>/register.php" class="btn btn-light btn-lg px-5">
                    <?= __('Register Now', 'ئێستا تۆمار بکە') ?>
                </a>
            </div>
        </div>
    </div>
</div>
</div>

<?php include 'includes/footer.php'; ?>
