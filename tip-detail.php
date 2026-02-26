<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    redirect(SITE_URL . '/tips.php');
}

$tip = fetchOne("SELECT * FROM tips WHERE id = ? AND is_published = 1", [$id]);
if (!$tip) {
    redirect(SITE_URL . '/tips.php');
}

// Update view count
query("UPDATE tips SET view_count = view_count + 1 WHERE id = ?", [$id]);

// Get author
$author = null;
if ($tip['author_id']) {
    $author = fetchOne("SELECT * FROM admins WHERE id = ?", [$tip['author_id']]);
}

$pageTitle = getLocalized($tip, 'title');
include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb text-white">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/index.php" class="text-white"><?= __('Home', 'سەرەتا') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/tips.php" class="text-white"><?= __('Tips', 'ئامۆژگاری') ?></a></li>
                <li class="breadcrumb-item active text-white"><?= truncate(e(getLocalized($tip, 'title')), 50) ?></li>
            </ol>
        </nav>
        <div class="mb-3">
            <span class="badge bg-light text-dark"><?= ucfirst($tip['category']) ?></span>
            <span class="ms-3"><?= formatDate($tip['published_at'] ?? $tip['created_at'], 'F d, Y') ?></span>
            <span class="ms-3">👁️ <?= number_format($tip['view_count']) ?> <?= __('views', 'سەردان') ?></span>
        </div>
        <h1 class="display-4 fw-bold"><?= e(getLocalized($tip, 'title')) ?></h1>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if ($author): ?>
                <div class="mb-4 p-3" style="background: #f9fafb; border-radius: 10px;">
                    <small class="text-muted" style="color: #6b7280;">
                        <?= __('By', 'لەلایەن') ?> <strong style="color: #1f2937;"><?= e(getLocalized($author, 'full_name')) ?></strong>
                    </small>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px;">
                <div class="card-body p-5">
                    <div class="content" style="font-size: 1.1rem; line-height: 1.8; color: #4b5563;">
                        <?= nl2br(e(getLocalized($tip, 'content'))) ?>
                    </div>
                </div>
            </div>

            <!-- Share -->
            <div class="card shadow-sm" style="border: none; border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: #1f2937;"><?= __('Share this article', 'ئەم بابەتە هاوبەش بکە') ?></h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . '/tip-detail.php?id=' . $tip['id']) ?>" target="_blank" class="btn btn-outline-primary">
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(SITE_URL . '/tip-detail.php?id=' . $tip['id']) ?>&text=<?= urlencode(getLocalized($tip, 'title')) ?>" target="_blank" class="btn btn-outline-primary">
                            Twitter
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode(getLocalized($tip, 'title') . ' - ' . SITE_URL . '/tip-detail.php?id=' . $tip['id']) ?>" target="_blank" class="btn btn-outline-primary">
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="<?= SITE_URL ?>/tips.php" class="btn btn-outline-primary btn-lg">
                    ← <?= __('Back to Tips', 'گەڕانەوە بۆ ئامۆژگاریەکان') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
