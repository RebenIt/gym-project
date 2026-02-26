<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();
$pageTitle = __('Home', 'سەرەتا');

// Fetch data
$services = fetchAll("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order LIMIT 6");
$plans = fetchAll("SELECT * FROM plans WHERE is_active = 1 ORDER BY sort_order");
$reviews = fetchAll("SELECT * FROM reviews WHERE is_approved = 1 AND is_featured = 1 ORDER BY created_at DESC LIMIT 6");
$trainers = fetchAll("SELECT * FROM trainers WHERE is_active = 1 ORDER BY sort_order LIMIT 3");

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 100px 0; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>

    <div class="container" style="position: relative; z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white" data-aos="fade-right">
                <h1 class="display-3 fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                    <?= e(getSetting('hero_title', $lang)) ?>
                </h1>
                <p class="lead mb-4" style="font-size: 1.3rem;">
                    <?= e(getSetting('hero_description', $lang)) ?>
                </p>
                <div class="d-flex gap-3">
                    <a href="<?= SITE_URL ?>/register.php" class="btn btn-light btn-lg px-4 py-3">
                        <?= __('Join Now', 'ئێستا بەشداربە') ?>
                    </a>
                    <a href="<?= SITE_URL ?>/games.php" class="btn btn-outline-light btn-lg px-4 py-3">
                        <?= __('View Exercises', 'ڕاهێنانەکان ببینە') ?>
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div style="text-align: center; padding: 40px;">
                    <div style="font-size: 200px; opacity: 0.9;">💪</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section" style="background: white; padding: 60px 0; margin-top: -30px; box-shadow: 0 -10px 30px rgba(0,0,0,0.1);">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card p-4">
                    <div style="font-size: 3rem; margin-bottom: 10px;">👥</div>
                    <h3 class="fw-bold mb-0" style="color: #f97316; font-size: 2.5rem;"><?= e(getSetting('stat_members')) ?>+</h3>
                    <p class="mb-0" style="color: white;"><?= __('Active Members', 'ئەندامی چالاک') ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card p-4">
                    <div style="font-size: 3rem; margin-bottom: 10px;">🏋️</div>
                    <h3 class="fw-bold mb-0" style="color: #f97316; font-size: 2.5rem;"><?= e(getSetting('stat_trainers')) ?>+</h3>
                    <p class="mb-0" style="color: white;"><?= __('Expert Trainers', 'ڕاهێنەری شارەزا') ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card p-4">
                    <div style="font-size: 3rem; margin-bottom: 10px;">⭐</div>
                    <h3 class="fw-bold mb-0" style="color: #f97316; font-size: 2.5rem;"><?= e(getSetting('stat_experience')) ?>+</h3>
                    <p class="mb-0" style="color: white;"><?= __('Years Experience', 'ساڵ ئەزموون') ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card p-4">
                    <div style="font-size: 3rem; margin-bottom: 10px;">🎯</div>
                    <h3 class="fw-bold mb-0" style="color: #f97316; font-size: 2.5rem;">100%</h3>
                    <p class="mb-0" style="color: white;"><?= __('Success Rate', 'ڕێژەی سەرکەوتن') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section" style="padding: 80px 0; background: #f3f4f6;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3" style="color: #1f2937;"><?= __('Our Services', 'خزمەتگوزاریەکانمان') ?></h2>
            <p class="lead" style="color: #6b7280;"><?= __('Comprehensive fitness services tailored to your needs', 'خزمەتگوزاریەکانی تەندروستی گونجاو بۆ پێداویستیەکانت') ?></p>
        </div>

        <div class="row g-4">
            <?php foreach ($services as $service): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card" style="background: #ffffff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s; height: 100%;">
                        <div style="font-size: 3rem; margin-bottom: 20px;"><?= e($service['icon'] ?? '🏃') ?></div>
                        <h4 class="fw-bold mb-3" style="color: #1f2937;"><?= e(getLocalized($service, 'name')) ?></h4>
                        <p style="color: #6b7280;"><?= e(getLocalized($service, 'short_description')) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Plans Section -->
<section class="plans-section" style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3" style="color: #1f2937;"><?= __('Membership Plans', 'پلانەکانی ئەندامێتی') ?></h2>
            <p class="lead" style="color: #6b7280;"><?= __('Choose the perfect plan for your fitness journey', 'پلانی گونجاو هەڵبژێرە بۆ گەشتی تەندروستیت') ?></p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ($plans as $plan): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="plan-card" style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); <?= $plan['is_popular'] ? 'border: 3px solid #f97316; transform: scale(1.05);' : '' ?>">
                        <?php if ($plan['is_popular']): ?>
                            <div style="background: linear-gradient(135deg, #f97316, #dc2626); color: white; padding: 5px 15px; border-radius: 20px; display: inline-block; margin-bottom: 15px; font-weight: 600;">
                                <?= __('Popular', 'بەناوبانگ') ?>
                            </div>
                        <?php endif; ?>

                        <h3 class="fw-bold mb-3"><?= e(getLocalized($plan, 'name')) ?></h3>
                        <div class="mb-4">
                            <span style="font-size: 3rem; font-weight: 800; color: #f97316;">$<?= number_format($plan['price'], 0) ?></span>
                            <span class="text-muted">/<?= __('month', 'مانگ') ?></span>
                        </div>

                        <p class="text-muted mb-4"><?= e(getLocalized($plan, 'description')) ?></p>

                        <?php
                        $features = json_decode($plan['features'], true);
                        $featuresKu = json_decode($plan['features_ku'], true);
                        $featuresList = $lang === 'ku' && $featuresKu ? $featuresKu : $features;
                        ?>
                        <ul class="list-unstyled mb-4" style="color: #1f2937;">
                            <?php foreach ($featuresList as $feature): ?>
                                <li class="mb-2" style="color: #1f2937; font-size: 0.95rem;">✓ <?= e($feature) ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <a href="<?= SITE_URL ?>/register.php" class="btn <?= $plan['is_popular'] ? 'btn-primary' : 'btn-outline-primary' ?> w-100">
                            <?= __('Get Started', 'دەست پێبکە') ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Trainers Section -->
<?php if (!empty($trainers)): ?>
<section class="trainers-section" style="padding: 80px 0; background: #f3f4f6;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3" style="color: #1f2937;"><?= __('Meet Our Trainers', 'ڕاهێنەرەکانمان') ?></h2>
            <p class="lead" style="color: #6b7280;"><?= __('Expert trainers dedicated to your success', 'ڕاهێنەری شارەزا بۆ سەرکەوتنەکەت') ?></p>
        </div>

        <div class="row g-4">
            <?php foreach ($trainers as $trainer): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="trainer-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                        <div style="height: 300px; background: linear-gradient(135deg, #f97316, #dc2626); display: flex; align-items: center; justify-content: center; font-size: 100px;">
                            👤
                        </div>
                        <div class="p-4">
                            <h4 class="fw-bold mb-2" style="color: #1f2937;">
                                <?= e(getLocalized($trainer, 'first_name')) ?> <?= e(getLocalized($trainer, 'last_name')) ?>
                            </h4>
                            <p class="mb-3" style="color: #6b7280; font-weight: 500;"><?= e(getLocalized($trainer, 'specialization')) ?></p>
                            <p class="mb-3" style="color: #4b5563;"><?= truncate(e(getLocalized($trainer, 'bio')), 100) ?></p>
                            <a href="<?= SITE_URL ?>/trainer-detail.php?id=<?= $trainer['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <?= __('View Profile', 'پڕۆفایل ببینە') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= SITE_URL ?>/trainers.php" class="btn btn-primary btn-lg">
                <?= __('View All Trainers', 'هەموو ڕاهێنەرەکان ببینە') ?>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Reviews Section -->
<?php if (!empty($reviews)): ?>
<section class="reviews-section" style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3" style="color: #1f2937;"><?= __('What Our Members Say', 'بۆچوونی ئەندامانمان') ?></h2>
            <p class="lead" style="color: #6b7280;"><?= __('Real stories from real people', 'چیرۆکی ڕاستەقینە لە کەسانی ڕاستەقینە') ?></p>
        </div>

        <div class="row g-4">
            <?php foreach ($reviews as $review): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="review-card" style="background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); height: 100%;">
                        <div class="mb-3">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                <span style="color: #f97316; font-size: 1.2rem;">★</span>
                            <?php endfor; ?>
                        </div>
                        <p class="mb-4" style="color: #4b5563;">"<?= e(getLocalized($review, 'review_text')) ?>"</p>
                        <div class="d-flex align-items-center">
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #f97316, #dc2626); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin-right: 15px;">
                                <?= substr($review['reviewer_name'], 0, 1) ?>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0" style="color: #1f2937;"><?= e(getLocalized($review, 'reviewer_name')) ?></h6>
                                <small style="color: #9ca3af;"><?= __('Member', 'ئەندام') ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact Section -->
<section class="contact-section" style="padding: 80px 0; background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="display-5 fw-bold mb-4"><?= __('Ready to Start Your Journey?', 'ئامادەیت گەشتەکەت دەست پێ بکەیت؟') ?></h2>
                <p class="lead mb-4"><?= __('Join us today and transform your fitness goals into reality', 'ئەمڕۆ بەشداربە و ئامانجەکانت بگۆڕە بۆ ڕاستی') ?></p>
                <div class="d-flex gap-3">
                    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-light btn-lg">
                        <?= __('Contact Us', 'پەیوەندیمان پێوە بکە') ?>
                    </a>
                    <a href="<?= SITE_URL ?>/register.php" class="btn btn-outline-light btn-lg">
                        <?= __('Register Now', 'ئێستا تۆمار بکە') ?>
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-4 bg-white bg-opacity-10 rounded">
                            <div style="font-size: 2rem; margin-bottom: 10px;">📞</div>
                            <small><?= __('Phone', 'تەلەفۆن') ?></small>
                            <p class="mb-0 fw-bold"><?= e(getSetting('site_phone')) ?></p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-white bg-opacity-10 rounded">
                            <div style="font-size: 2rem; margin-bottom: 10px;">✉️</div>
                            <small><?= __('Email', 'ئیمەیڵ') ?></small>
                            <p class="mb-0 fw-bold"><?= e(getSetting('site_email')) ?></p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-4 bg-white bg-opacity-10 rounded">
                            <div style="font-size: 2rem; margin-bottom: 10px;">🕐</div>
                            <small><?= __('Working Hours', 'کاتژمێری کار') ?></small>
                            <p class="mb-0 fw-bold"><?= e(getSetting('working_hours', $lang)) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.service-card:hover, .plan-card:hover, .trainer-card:hover, .review-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #f97316, #dc2626);
    border: none;
    transition: transform 0.3s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(249, 115, 22, 0.4);
}

.btn-outline-primary {
    border-color: #f97316;
    color: #f97316;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #f97316, #dc2626);
    border-color: transparent;
    color: white;
}
</style>

<?php include 'includes/footer.php'; ?>
