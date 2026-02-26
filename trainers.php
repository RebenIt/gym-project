<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();
$pageTitle = __('Our Trainers', 'ڕاهێنەرەکانمان');

$trainers = fetchAll("SELECT * FROM trainers WHERE is_active = 1 ORDER BY sort_order, first_name");

include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?= __('Meet Our Expert Trainers', 'ناسینی ڕاهێنەرە شارەزاکانمان') ?></h1>
        <p class="lead"><?= __('Professional trainers dedicated to helping you achieve your fitness goals', 'ڕاهێنەری پیشەیی کە یارمەتیت دەدەن بۆ گەیشتن بە ئامانجەکانت') ?></p>
    </div>
</div>

<div class="container py-5" style="background: white;">
    <?php if (empty($trainers)): ?>
        <div class="alert alert-info text-center">
            <h5><?= __('No trainers found', 'هیچ ڕاهێنەرێک نەدۆزرایەوە') ?></h5>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($trainers as $trainer): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm trainer-card" style="border: none; border-radius: 15px; overflow: hidden; transition: transform 0.3s; background: white;">
                        <div style="height: 350px; background: linear-gradient(135deg, #f97316, #dc2626); display: flex; align-items: center; justify-content: center; font-size: 120px; color: white;">
                            👨‍🏫
                        </div>
                        <div class="card-body p-4" style="background: white;">
                            <h4 class="fw-bold mb-2" style="color: #1f2937;">
                                <?= e(getLocalized($trainer, 'first_name')) ?> <?= e(getLocalized($trainer, 'last_name')) ?>
                            </h4>
                            <p class="mb-3" style="color: #f97316; font-weight: 600;">
                                <?= e(getLocalized($trainer, 'specialization')) ?>
                            </p>

                            <div class="mb-3">
                                <small style="color: #6b7280;">
                                    <?= __('Experience:', 'ئەزموون:') ?> <?= $trainer['experience_years'] ?> <?= __('years', 'ساڵ') ?>
                                </small>
                            </div>

                            <p class="mb-4" style="color: #4b5563;"><?= truncate(e(getLocalized($trainer, 'bio')), 120) ?></p>

                            <a href="<?= SITE_URL ?>/trainer-detail.php?id=<?= $trainer['id'] ?>" class="btn btn-primary w-100">
                                <?= __('View Full Profile', 'پڕۆفایلی تەواو ببینە') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.trainer-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}
</style>

<?php include 'includes/footer.php'; ?>
