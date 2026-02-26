<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();
$pageTitle = __('Contact Us', 'پەیوەندیمان پێوەبکە');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($message)) {
        $error = __('Please fill in all required fields', 'تکایە هەموو خانە پێویستەکان پڕبکەوە');
    } elseif (!empty($email) && !isValidEmail($email)) {
        $error = __('Invalid email address', 'ئیمەیڵی نادروست');
    } else {
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";
        if (query($sql, [$name, $email, $phone, $subject, $message])) {
            $success = __('Thank you! Your message has been sent successfully.', 'سوپاس! پەیامەکەت بە سەرکەوتوویی نێردرا.');
            // Clear form
            $_POST = [];
        } else {
            $error = __('Something went wrong. Please try again.', 'هەڵەیەک ڕوویدا. تکایە دووبارە هەوڵبدەوە.');
        }
    }
}

include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?= __('Contact Us', 'پەیوەندیمان پێوەبکە') ?></h1>
        <p class="lead"><?= __('Get in touch with us. We would love to hear from you!', 'پەیوەندیمان پێوەبکە. خۆشحاڵ دەبین پەیامتمان پێ بگات!') ?></p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm h-100" style="border: none; border-radius: 15px; background: white;">
                <div class="card-body p-4" style="background: white;">
                    <h4 class="fw-bold mb-4" style="color: #1f2937;"><?= __('Contact Information', 'زانیاری پەیوەندی') ?></h4>

                    <div class="mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <div style="font-size: 2rem; color: #f97316; margin-right: 15px;">📞</div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #1f2937;"><?= __('Phone', 'تەلەفۆن') ?></h6>
                                <p class="mb-0" style="color: #6b7280;"><?= e(getSetting('site_phone')) ?></p>
                                <?php if ($phone2 = getSetting('site_phone_secondary')): ?>
                                    <p class="mb-0" style="color: #6b7280;"><?= e($phone2) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <div style="font-size: 2rem; color: #f97316; margin-right: 15px;">✉️</div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #1f2937;"><?= __('Email', 'ئیمەیڵ') ?></h6>
                                <p class="mb-0" style="color: #6b7280;"><?= e(getSetting('site_email')) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <div style="font-size: 2rem; color: #f97316; margin-right: 15px;">📍</div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #1f2937;"><?= __('Address', 'ناونیشان') ?></h6>
                                <p class="mb-0" style="color: #6b7280;"><?= e(getSetting('site_address', $lang)) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <div style="font-size: 2rem; color: #f97316; margin-right: 15px;">🕐</div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #1f2937;"><?= __('Working Hours', 'کاتژمێری کار') ?></h6>
                                <p class="mb-0" style="color: #6b7280;"><?= e(getSetting('working_hours', $lang)) ?></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h6 class="fw-bold mb-3" style="color: #1f2937;"><?= __('Follow Us', 'فۆڵۆومان بکە') ?></h6>
                        <div class="d-flex gap-2">
                            <?php if ($fb = getSetting('social_facebook')): ?>
                                <a href="<?= e($fb) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Facebook</a>
                            <?php endif; ?>
                            <?php if ($ig = getSetting('social_instagram')): ?>
                                <a href="<?= e($ig) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Instagram</a>
                            <?php endif; ?>
                            <?php if ($yt = getSetting('social_youtube')): ?>
                                <a href="<?= e($yt) ?>" target="_blank" class="btn btn-sm btn-outline-primary">YouTube</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm" style="border: none; border-radius: 15px; background: white;">
                <div class="card-body p-5" style="background: white;">
                    <h4 class="fw-bold mb-4" style="color: #1f2937;"><?= __('Send Us a Message', 'پەیامێکمان بۆ بنێرە') ?></h4>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= e($success) ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="color: #1f2937;"><?= __('Your Name', 'ناوت') ?> <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                       value="<?= e($_POST['name'] ?? '') ?>"
                                       placeholder="<?= __('Enter your name', 'ناوت بنووسە') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" style="color: #1f2937;"><?= __('Email Address', 'ئیمەیڵ') ?></label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= e($_POST['email'] ?? '') ?>"
                                       placeholder="<?= __('your@email.com', 'ئیمەیڵەکەت') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" style="color: #1f2937;"><?= __('Phone Number', 'ژمارەی تەلەفۆن') ?></label>
                                <input type="tel" name="phone" class="form-control"
                                       value="<?= e($_POST['phone'] ?? '') ?>"
                                       placeholder="<?= __('Your phone number', 'ژمارەی تەلەفۆنەکەت') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" style="color: #1f2937;"><?= __('Subject', 'بابەت') ?></label>
                                <input type="text" name="subject" class="form-control"
                                       value="<?= e($_POST['subject'] ?? '') ?>"
                                       placeholder="<?= __('Message subject', 'بابەتی پەیام') ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label" style="color: #1f2937;"><?= __('Message', 'پەیام') ?> <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="6" required
                                          placeholder="<?= __('Write your message here...', 'پەیامەکەت لێرە بنووسە...') ?>"><?= e($_POST['message'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <?= __('Send Message', 'ناردنی پەیام') ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map or CTA Section -->
<section style="padding: 80px 0; background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); color: white; margin-top: 60px;">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4"><?= __('Visit Our Gym', 'سەردانی جیمەکەمان بکە') ?></h2>
        <p class="lead mb-5"><?= __('Come see our facilities and meet our team. We would love to show you around!', 'وەرە و ئامێرەکانمان ببینە و تیمەکەمان بناسە. خۆشحاڵ دەبین پیشانت بدەین!') ?></p>
        <a href="<?= SITE_URL ?>/register.php" class="btn btn-light btn-lg px-5">
            <?= __('Join Today', 'ئەمڕۆ بەشداربە') ?>
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
