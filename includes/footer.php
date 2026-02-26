    <footer class="footer" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); color: white; padding: 60px 0 20px;">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h3 class="fw-bold mb-3" style="color: #f97316;"><?= e(getSetting('site_name', $lang)) ?></h3>
                    <p class="text-light"><?= e(getSetting('site_description', $lang)) ?></p>
                    <div class="d-flex gap-2 mt-3">
                        <?php if ($fb = getSetting('social_facebook')): ?>
                            <a href="<?= e($fb) ?>" target="_blank" class="btn btn-sm btn-outline-light">Facebook</a>
                        <?php endif; ?>
                        <?php if ($ig = getSetting('social_instagram')): ?>
                            <a href="<?= e($ig) ?>" target="_blank" class="btn btn-sm btn-outline-light">Instagram</a>
                        <?php endif; ?>
                        <?php if ($yt = getSetting('social_youtube')): ?>
                            <a href="<?= e($yt) ?>" target="_blank" class="btn btn-sm btn-outline-light">YouTube</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="fw-bold mb-3"><?= __('Quick Links', 'لینکە خێراکان') ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= SITE_URL ?>/index.php" class="text-light text-decoration-none"><?= __('Home', 'سەرەتا') ?></a></li>
                        <li class="mb-2"><a href="<?= SITE_URL ?>/games.php" class="text-light text-decoration-none"><?= __('Exercises', 'ڕاهێنانەکان') ?></a></li>
                        <li class="mb-2"><a href="<?= SITE_URL ?>/trainers.php" class="text-light text-decoration-none"><?= __('Trainers', 'ڕاهێنەرەکان') ?></a></li>
                        <li class="mb-2"><a href="<?= SITE_URL ?>/about.php" class="text-light text-decoration-none"><?= __('About', 'دەربارە') ?></a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3"><?= __('Services', 'خزمەتگوزاریەکان') ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= SITE_URL ?>/beginners.php" class="text-light text-decoration-none"><?= __('Beginner Program', 'پڕۆگرامی سەرەتایی') ?></a></li>
                        <li class="mb-2"><a href="<?= SITE_URL ?>/tips.php" class="text-light text-decoration-none"><?= __('Tips & News', 'ئامۆژگاری و هەواڵ') ?></a></li>
                        <li class="mb-2"><a href="<?= SITE_URL ?>/certificates.php" class="text-light text-decoration-none"><?= __('Certificates', 'بڕوانامەکان') ?></a></li>
                        <li class="mb-2"><a href="<?= SITE_URL ?>/contact.php" class="text-light text-decoration-none"><?= __('Contact', 'پەیوەندی') ?></a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3"><?= __('Contact Info', 'زانیاری پەیوەندی') ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2 text-light"><?= e(getSetting('site_phone')) ?></li>
                        <li class="mb-2 text-light"><?= e(getSetting('site_email')) ?></li>
                        <li class="mb-2 text-light"><?= e(getSetting('site_address', $lang)) ?></li>
                        <li class="mb-2 text-light"><?= __('Hours:', 'کاتژمێر:') ?> <?= e(getSetting('working_hours', $lang)) ?></li>
                    </ul>
                </div>
            </div>

            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">

            <div class="text-center text-light">
                <p class="mb-0">&copy; <?= date('Y') ?> <?= e(getSetting('site_name', $lang)) ?>. <?= __('All rights reserved.', 'هەموو مافەکان پارێزراون.') ?></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
