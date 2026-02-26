<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    redirect(SITE_URL . '/trainers.php');
}

$trainer = fetchOne("SELECT * FROM trainers WHERE id = ? AND is_active = 1", [$id]);
if (!$trainer) {
    redirect(SITE_URL . '/trainers.php');
}

$pageTitle = getLocalized($trainer, 'first_name') . ' ' . getLocalized($trainer, 'last_name');
include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-white">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" class="text-white"><?= __('Home', 'سەرەتا') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/trainers.php" class="text-white"><?= __('Trainers', 'ڕاهێنەرەکان') ?></a></li>
                <li class="breadcrumb-item active text-white"><?= e($pageTitle) ?></li>
            </ol>
        </nav>
        <h1 class="display-4 fw-bold"><?= e($pageTitle) ?></h1>
        <p class="lead"><?= e(getLocalized($trainer, 'specialization')) ?></p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; background: white;">
                <div style="height: 400px; background: linear-gradient(135deg, #f97316, #dc2626); display: flex; align-items: center; justify-content: center; font-size: 150px; color: white;">
                    👨‍🏫
                </div>
                <div class="card-body p-4" style="background: white;">
                    <div class="mb-3" style="color: #4b5563;">
                        <strong style="color: #1f2937;"><?= __('Specialization', 'پسپۆڕی') ?>:</strong><br>
                        <?= e(getLocalized($trainer, 'specialization')) ?>
                    </div>

                    <div class="mb-3" style="color: #4b5563;">
                        <strong style="color: #1f2937;"><?= __('Experience', 'ئەزموون') ?>:</strong><br>
                        <?= $trainer['experience_years'] ?> <?= __('years', 'ساڵ') ?>
                    </div>

                    <?php if ($trainer['phone']): ?>
                        <div class="mb-3" style="color: #4b5563;">
                            <strong style="color: #1f2937;">📞 <?= __('Phone', 'تەلەفۆن') ?>:</strong><br>
                            <?= e($trainer['phone']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($trainer['email']): ?>
                        <div class="mb-3" style="color: #4b5563;">
                            <strong style="color: #1f2937;">✉️ <?= __('Email', 'ئیمەیڵ') ?>:</strong><br>
                            <?= e($trainer['email']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Social Links -->
                    <?php if ($trainer['social_instagram'] || $trainer['social_facebook'] || $trainer['social_youtube']): ?>
                        <div class="mt-4" style="color: #4b5563;">
                            <strong style="color: #1f2937;"><?= __('Follow', 'فۆڵۆو') ?>:</strong><br>
                            <div class="d-flex gap-2 mt-2">
                                <?php if ($trainer['social_instagram']): ?>
                                    <a href="<?= e($trainer['social_instagram']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Instagram</a>
                                <?php endif; ?>
                                <?php if ($trainer['social_facebook']): ?>
                                    <a href="<?= e($trainer['social_facebook']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Facebook</a>
                                <?php endif; ?>
                                <?php if ($trainer['social_youtube']): ?>
                                    <a href="<?= e($trainer['social_youtube']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">YouTube</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Bio -->
            <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; background: white;">
                <div class="card-body p-4" style="background: white;">
                    <h3 class="fw-bold mb-3" style="color: #1f2937;"><?= __('About', 'دەربارە') ?></h3>
                    <p class="lead" style="color: #4b5563;"><?= nl2br(e(getLocalized($trainer, 'bio'))) ?></p>
                </div>
            </div>

            <!-- Certifications -->
            <?php if ($trainer['certifications']): ?>
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; background: white;">
                    <div class="card-body p-4" style="background: white;">
                        <h3 class="fw-bold mb-3" style="color: #1f2937;">🏆 <?= __('Certifications & Qualifications', 'بڕوانامەکان و بەرهەمەکان') ?></h3>
                        <p style="color: #4b5563;"><?= nl2br(e(getLocalized($trainer, 'certifications'))) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contact Card -->
            <div class="card shadow-sm" style="border: none; border-radius: 15px; background: linear-gradient(135deg, #f97316, #dc2626); color: white;">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3"><?= __('Want to Train with Me?', 'دەتەوێت لەگەڵم ڕابهێنیت؟') ?></h4>
                    <p class="mb-4"><?= __('Get in touch to discuss your fitness goals and how I can help you achieve them.', 'پەیوەندیم پێوەبکە بۆ گفتوگۆ لەسەر ئامانجەکانت و چۆن یارمەتیت دەدەم.') ?></p>
                    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-light btn-lg">
                        <?= __('Contact Me', 'پەیوەندیم پێوەبکە') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
