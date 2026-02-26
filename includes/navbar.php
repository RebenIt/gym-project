<?php
$lang = getCurrentLang();
$isLoggedIn = isLoggedIn();
$currentUser = $isLoggedIn ? getCurrentUser() : null;
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= SITE_URL ?>/index.php" style="font-size: 1.5rem;">
            <?= e(getSetting('site_name', $lang)) ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>" href="<?= SITE_URL ?>/index.php">
                        <?= __('Home', 'سەرەتا') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'games' ? 'active' : '' ?>" href="<?= SITE_URL ?>/games.php">
                        <?= __('Exercises', 'ڕاهێنانەکان') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'trainers' ? 'active' : '' ?>" href="<?= SITE_URL ?>/trainers.php">
                        <?= __('Trainers', 'ڕاهێنەرەکان') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'tips' ? 'active' : '' ?>" href="<?= SITE_URL ?>/tips.php">
                        <?= __('Tips', 'ئامۆژگاری') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'beginners' ? 'active' : '' ?>" href="<?= SITE_URL ?>/beginners.php">
                        <?= __('Beginner', 'سەرەتایی') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'certificates' ? 'active' : '' ?>" href="<?= SITE_URL ?>/certificates.php">
                        <?= __('Certificates', 'بڕوانامە') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>" href="<?= SITE_URL ?>/contact.php">
                        <?= __('Contact', 'پەیوەندی') ?>
                    </a>
                </li>

                <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <?= e($currentUser['username']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/user/dashboard.php"><?= __('Dashboard', 'داشبۆرد') ?></a></li>
                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/user/profile.php"><?= __('Profile', 'پڕۆفایل') ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/logout.php"><?= __('Logout', 'چوونەدەرەوە') ?></a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/login.php"><?= __('Login', 'چوونەژوورەوە') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light btn-sm ms-2" href="<?= SITE_URL ?>/register.php">
                            <?= __('Register', 'تۆمارکردن') ?>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item ms-3">
                    <a href="?lang=<?= $lang === 'en' ? 'ku' : 'en' ?>" class="btn btn-sm btn-outline-light">
                        <?= $lang === 'en' ? 'کوردی' : 'English' ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
