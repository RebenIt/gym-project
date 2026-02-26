<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();
$pageTitle = __('About Us', 'دەربارەمان');

include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?= __('About Us', 'دەربارەی ئێمە') ?></h1>
        <p class="lead"><?= __('Learn more about our gym and our commitment to your fitness journey', 'زیاتر بزانە دەربارەی جیمەکەمان و پابەندبوونمان بە گەشتی تەندروستیت') ?></p>
    </div>
</div>

<div class="container py-5">
    <!-- Our Story -->
    <div class="row align-items-center mb-5 pb-5">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div style="font-size: 150px; text-align: center;">💪</div>
        </div>
        <div class="col-lg-6">
            <h2 class="display-5 fw-bold mb-4"><?= __('Our Story', 'چیرۆکی ئێمە') ?></h2>
            <p class="lead mb-4">
                <?= __('Founded with a passion for fitness and a commitment to excellence, FitZone has become the premier gymnastics and fitness center in the region.',
                'لەگەڵ حەزێکی زۆر بۆ تەندروستی و پابەندبوون بە نایابی، فیتزۆن بووە بە ناوەندی سەرەکی جیمناستیک و تەندروستی لە هەرێمەکەدا.') ?>
            </p>
            <p class="mb-4">
                <?= __('We believe that fitness is not just about physical transformation, but about building confidence, discipline, and a healthy lifestyle that lasts a lifetime.',
                'ئێمە باوەڕمان وایە تەندروستی تەنها گۆڕانی جەستەیی نییە، بەڵکو دروستکردنی متمانە، بەرگری و شێوازی ژیانێکی تەندروستە کە بۆ هەمیشە دەمێنێتەوە.') ?>
            </p>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100" style="border: none; border-radius: 15px; background: linear-gradient(135deg, #fff8f0, #ffffff);">
                <div class="card-body p-5">
                    <div style="font-size: 3rem; margin-bottom: 20px;">🎯</div>
                    <h3 class="fw-bold mb-3"><?= __('Our Mission', 'ئامانجمان') ?></h3>
                    <p class="mb-0">
                        <?= __('To empower individuals to achieve their fitness goals through world-class facilities, expert trainers, and a supportive community. We strive to make fitness accessible, enjoyable, and effective for everyone.',
                        'بەهێزکردنی تاکەکان بۆ گەیشتن بە ئامانجە تەندروستیەکانیان لەڕێگەی ئامێرە نێودەوڵەتیەکان، ڕاهێنەری شارەزا و کۆمەڵگەیەکی پشتگیرەوە.') ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100" style="border: none; border-radius: 15px; background: linear-gradient(135deg, #f0f9ff, #ffffff);">
                <div class="card-body p-5">
                    <div style="font-size: 3rem; margin-bottom: 20px;">👁️</div>
                    <h3 class="fw-bold mb-3"><?= __('Our Vision', 'دیدمان') ?></h3>
                    <p class="mb-0">
                        <?= __('To become the leading fitness center in Kurdistan, known for transforming lives and creating a culture of health and wellness. We envision a community where everyone has the opportunity to reach their full potential.',
                        'بوون بە ناوەندی سەرەکی تەندروستی لە کوردستان، کە ناسراو بێت بە گۆڕینی ژیان و دروستکردنی کلتوری تەندروستی.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Why Choose Us -->
    <div class="mb-5">
        <h2 class="display-5 fw-bold text-center mb-5"><?= __('Why Choose Us?', 'بۆچی ئێمە هەڵبژێریت؟') ?></h2>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div style="font-size: 4rem; margin-bottom: 20px;">🏋️</div>
                    <h5 class="fw-bold mb-3"><?= __('Modern Equipment', 'ئامێری نوێ') ?></h5>
                    <p class="text-muted"><?= __('State-of-the-art fitness equipment for all your workout needs', 'ئامێری تەندروستی نوێ بۆ هەموو پێداویستیەکانت') ?></p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div style="font-size: 4rem; margin-bottom: 20px;">👨‍🏫</div>
                    <h5 class="fw-bold mb-3"><?= __('Expert Trainers', 'ڕاهێنەری شارەزا') ?></h5>
                    <p class="text-muted"><?= __('Certified professionals dedicated to your success', 'پیشەیی بڕوانامەدار کە تەرخانکراون بۆ سەرکەوتنەکەت') ?></p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div style="font-size: 4rem; margin-bottom: 20px;">🌟</div>
                    <h5 class="fw-bold mb-3"><?= __('Flexible Plans', 'پلانی نەرم') ?></h5>
                    <p class="text-muted"><?= __('Membership options that fit your lifestyle and budget', 'هەڵبژاردەی ئەندامێتی کە گونجاوە بۆ ژیانت') ?></p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="text-center p-4">
                    <div style="font-size: 4rem; margin-bottom: 20px;">👥</div>
                    <h5 class="fw-bold mb-3"><?= __('Great Community', 'کۆمەڵگەی نایاب') ?></h5>
                    <p class="text-muted"><?= __('A supportive environment where everyone belongs', 'ژینگەیەکی پشتگیرانە کە هەموو کەس سەر بە') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="mb-5 py-5" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); border-radius: 20px; color: white;">
        <div class="row text-center">
            <div class="col-lg-3 col-6 mb-4 mb-lg-0">
                <h2 class="display-4 fw-bold mb-2"><?= e(getSetting('stat_members')) ?>+</h2>
                <p class="mb-0"><?= __('Active Members', 'ئەندامی چالاک') ?></p>
            </div>
            <div class="col-lg-3 col-6 mb-4 mb-lg-0">
                <h2 class="display-4 fw-bold mb-2"><?= e(getSetting('stat_trainers')) ?>+</h2>
                <p class="mb-0"><?= __('Expert Trainers', 'ڕاهێنەری شارەزا') ?></p>
            </div>
            <div class="col-lg-3 col-6">
                <h2 class="display-4 fw-bold mb-2"><?= e(getSetting('stat_experience')) ?>+</h2>
                <p class="mb-0"><?= __('Years of Experience', 'ساڵ ئەزموون') ?></p>
            </div>
            <div class="col-lg-3 col-6">
                <h2 class="display-4 fw-bold mb-2">100%</h2>
                <p class="mb-0"><?= __('Satisfaction Rate', 'ڕێژەی ڕازیبوون') ?></p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center py-5">
        <h2 class="display-5 fw-bold mb-4"><?= __('Ready to Transform Your Life?', 'ئامادەیت ژیانەکەت بگۆڕیت؟') ?></h2>
        <p class="lead mb-5"><?= __('Join our community today and start your fitness journey with us', 'ئەمڕۆ بەشداری کۆمەڵگەکەمان ببە و گەشتی تەندروستیت لەگەڵمان دەست پێبکە') ?></p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="<?= SITE_URL ?>/register.php" class="btn btn-primary btn-lg px-5">
                <?= __('Join Now', 'ئێستا بەشداربە') ?>
            </a>
            <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline-primary btn-lg px-5">
                <?= __('Contact Us', 'پەیوەندیمان پێوەبکە') ?>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
