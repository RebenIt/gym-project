<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();
$pageTitle = __('Certificates & Awards', 'بڕوانامەکان و خەڵاتەکان');

$certificates = fetchAll("SELECT * FROM certificates WHERE is_active = 1 ORDER BY year_received DESC, sort_order");

include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?= __('Our Certificates & Awards', 'بڕوانامە و خەڵاتەکانمان') ?></h1>
        <p class="lead"><?= __('Recognition of excellence and commitment to quality', 'ناسینەوەی نایابی و پابەندبوون بە کوالیتی') ?></p>
    </div>
</div>

<div style="background: white; min-height: 100vh;">
<div class="container py-5">
    <?php if (empty($certificates)): ?>
        <div class="alert alert-info text-center">
            <h5><?= __('No certificates found', 'هیچ بڕوانامەیەک نەدۆزرایەوە') ?></h5>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($certificates as $cert): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm certificate-card" style="border: none; border-radius: 15px; overflow: hidden; transition: transform 0.3s; background: white;">
                        <div style="height: 250px; background: linear-gradient(135deg, #f97316, #dc2626); display: flex; align-items: center; justify-content: center; font-size: 80px; color: white;">
                            <?php
                            $typeIcons = [
                                'certificate' => '🏆',
                                'award' => '🥇',
                                'achievement' => '⭐',
                                'recognition' => '🎖️'
                            ];
                            echo $typeIcons[$cert['certificate_type']] ?? '🏆';
                            ?>
                        </div>
                        <div class="card-body p-4" style="background: white;">
                            <div class="mb-2">
                                <span class="badge" style="background: linear-gradient(135deg, #f97316, #dc2626);">
                                    <?= ucfirst($cert['certificate_type']) ?>
                                </span>
                                <?php if ($cert['year_received']): ?>
                                    <span class="badge bg-secondary ms-2"><?= $cert['year_received'] ?></span>
                                <?php endif; ?>
                            </div>

                            <h5 class="fw-bold mb-3" style="color: #1f2937;"><?= e(getLocalized($cert, 'title')) ?></h5>

                            <?php if ($cert['issuing_organization']): ?>
                                <p class="mb-3" style="color: #6b7280;">
                                    <small>
                                        <?= __('Issued by:', 'لەلایەن:') ?><br>
                                        <strong style="color: #1f2937;"><?= e(getLocalized($cert, 'issuing_organization')) ?></strong>
                                    </small>
                                </p>
                            <?php endif; ?>

                            <?php if ($cert['description']): ?>
                                <p class="card-text" style="color: #4b5563;"><?= e(getLocalized($cert, 'description')) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-5 pt-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="display-5 fw-bold mb-4" style="color: #1f2937;"><?= __('Commitment to Excellence', 'پابەندبوون بە نایابی') ?></h2>
                <p class="lead mb-4" style="color: #4b5563;"><?= __('Our certifications and awards reflect our dedication to providing the highest quality fitness services and maintaining international standards.', 'بڕوانامە و خەڵاتەکانمان پابەندبوونمان نیشان دەدەن بۆ پێشکەشکردنی باشترین خزمەتگوزاری تەندروستی.') ?></p>
                <a href="<?= SITE_URL ?>/about.php" class="btn btn-primary btn-lg">
                    <?= __('Learn More About Us', 'زیاتر بزانە دەربارەمان') ?>
                </a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center p-4" style="background: linear-gradient(135deg, #f97316, #dc2626); color: white; border-radius: 15px;">
                            <div style="font-size: 3rem;">🏆</div>
                            <h4 class="fw-bold" style="color: white;"><?= count($certificates) ?>+</h4>
                            <p class="mb-0" style="color: white;"><?= __('Achievements', 'دەستکەوتەکان') ?></p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-4" style="background: white; border: 2px solid #f97316; border-radius: 15px;">
                            <div style="font-size: 3rem; color: #f97316;">⭐</div>
                            <h4 class="fw-bold" style="color: #1f2937;"><?= e(getSetting('stat_experience')) ?>+</h4>
                            <p class="mb-0" style="color: #6b7280;"><?= __('Years', 'ساڵ') ?></p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="text-center p-4" style="background: white; border: 2px solid #f97316; border-radius: 15px;">
                            <div style="font-size: 3rem; color: #f97316;">✅</div>
                            <h4 class="fw-bold" style="color: #1f2937;"><?= __('Quality Assured', 'کوالیتی دڵنیاکراو') ?></h4>
                            <p class="mb-0" style="color: #6b7280;"><?= __('International Standards', 'ستانداردی نێودەوڵەتی') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>
.certificate-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}
</style>

<?php include 'includes/footer.php'; ?>
