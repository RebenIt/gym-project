<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$lang = getCurrentLang();
$pageTitle = __('Tips & News', 'ئامۆژگاری و هەواڵ');

// Filter
$category = $_GET['category'] ?? '';

$where = "is_published = 1";
$params = [];

if ($category) {
    $where .= " AND category = ?";
    $params[] = $category;
}

$tips = fetchAll("SELECT * FROM tips WHERE $where ORDER BY published_at DESC, created_at DESC", $params);

include 'includes/header.php';
?>

<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); padding: 60px 0; color: white;">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?= __('Tips & News', 'ئامۆژگاری و هەواڵ') ?></h1>
        <p class="lead"><?= __('Stay updated with latest fitness tips, news, and motivation', 'بە دوای کۆتا ئامۆژگاری، هەواڵ و هاندانەوە بگەڕێ') ?></p>
    </div>
</div>

<div style="background: white; min-height: 100vh;">
<div class="container py-5">
    <!-- Categories -->
    <div class="mb-4">
        <div class="btn-group flex-wrap" role="group">
            <a href="?category=" class="btn <?= !$category ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= __('All', 'هەموو') ?>
            </a>
            <a href="?category=nutrition" class="btn <?= $category === 'nutrition' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= __('Nutrition', 'خۆراک') ?>
            </a>
            <a href="?category=exercise" class="btn <?= $category === 'exercise' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= __('Exercise', 'ڕاهێنان') ?>
            </a>
            <a href="?category=lifestyle" class="btn <?= $category === 'lifestyle' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= __('Lifestyle', 'شێوازی ژیان') ?>
            </a>
            <a href="?category=motivation" class="btn <?= $category === 'motivation' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= __('Motivation', 'هاندان') ?>
            </a>
            <a href="?category=news" class="btn <?= $category === 'news' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= __('News', 'هەواڵ') ?>
            </a>
        </div>
    </div>

    <?php if (empty($tips)): ?>
        <div class="alert alert-info text-center">
            <h5><?= __('No tips found', 'هیچ ئامۆژگارییەک نەدۆزرایەوە') ?></h5>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($tips as $tip): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm tip-card" style="border: none; border-radius: 15px; overflow: hidden; transition: transform 0.3s; background: white;">
                        <div style="height: 200px; background: linear-gradient(135deg, #f97316, #dc2626); display: flex; align-items: center; justify-content: center; font-size: 60px; color: white;">
                            <?php
                            $icons = ['nutrition' => '🥗', 'exercise' => '🏋️', 'lifestyle' => '🌟', 'motivation' => '💪', 'news' => '📰', 'other' => '📝'];
                            echo $icons[$tip['category']] ?? '📝';
                            ?>
                        </div>
                        <div class="card-body p-4" style="background: white;">
                            <div class="mb-2">
                                <span class="badge" style="background: linear-gradient(135deg, #f97316, #dc2626);">
                                    <?= ucfirst($tip['category']) ?>
                                </span>
                                <small class="ms-2" style="color: #9ca3af;">
                                    <?= formatDate($tip['published_at'] ?? $tip['created_at'], 'M d, Y') ?>
                                </small>
                            </div>

                            <h5 class="card-title fw-bold mb-3" style="color: #1f2937;"><?= e(getLocalized($tip, 'title')) ?></h5>

                            <p class="card-text mb-4" style="color: #6b7280;">
                                <?= truncate(e(getLocalized($tip, 'excerpt') ?: getLocalized($tip, 'content')), 120) ?>
                            </p>

                            <a href="<?= SITE_URL ?>/tip-detail.php?id=<?= $tip['id'] ?>" class="btn btn-primary w-100">
                                <?= __('Read More', 'زیاتر بخوێنەوە') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</div>

<style>
.tip-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
}
</style>

<?php include 'includes/footer.php'; ?>
