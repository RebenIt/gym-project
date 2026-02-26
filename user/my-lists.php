<?php
require_once '../includes/functions.php';
requireLogin();

$currentUser = getCurrentUser();
$pageTitle = __('My Workout Lists', 'لیستەکانی ڕاهێنانم');
include 'includes/header.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_list'])) {
        $name = sanitize($_POST['name'] ?? '');
        if ($name) {
            query("INSERT INTO user_game_lists (user_id, name, created_at) VALUES (?, ?, NOW())", [$currentUser['id'], $name]);
            setFlash('success', __('List created successfully!', 'لیست بە سەرکەوتوویی دروستکرا!'));
            redirect('my-lists.php');
        }
    } elseif (isset($_POST['delete_list'])) {
        $listId = (int)$_POST['list_id'];
        query("DELETE FROM user_game_lists WHERE id = ? AND user_id = ?", [$listId, $currentUser['id']]);
        setFlash('success', __('List deleted!', 'لیست سڕایەوە!'));
        redirect('my-lists.php');
    }
}

$lists = fetchAll("SELECT ugl.*, COUNT(ugli.id) as item_count
                   FROM user_game_lists ugl
                   LEFT JOIN user_game_list_items ugli ON ugl.id = ugli.list_id
                   WHERE ugl.user_id = ?
                   GROUP BY ugl.id
                   ORDER BY ugl.created_at DESC", [$currentUser['id']]);
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm" style="border: none; border-radius: 15px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0"><?= __('My Workout Lists', 'لیستەکانی ڕاهێنانم') ?></h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createListModal">
                        + <?= __('New List', 'لیستی نوێ') ?>
                    </button>
                </div>

                <?php if (empty($lists)): ?>
                    <div class="text-center py-5">
                        <div style="font-size: 4rem;">📝</div>
                        <h5 class="mt-3"><?= __('No lists yet', 'هێشتا لیستت نییە') ?></h5>
                        <p class="text-muted"><?= __('Create your first workout list to get started', 'یەکەم لیستی ڕاهێنان دروست بکە') ?></p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($lists as $list): ?>
                            <div class="col-md-6">
                                <div class="card h-100" style="border: 2px solid #e5e7eb; border-radius: 10px;">
                                    <div class="card-body">
                                        <h5 class="fw-bold"><?= e($list['name']) ?></h5>
                                        <p class="text-muted mb-3">
                                            <?= $list['item_count'] ?> <?= __('exercises', 'ڕاهێنان') ?>
                                        </p>
                                        <div class="d-flex gap-2">
                                            <a href="../games.php?list=<?= $list['id'] ?>" class="btn btn-sm btn-primary">
                                                <?= __('Add Exercises', 'ڕاهێنان زیاد بکە') ?>
                                            </a>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('<?= __('Delete this list?', 'ئەم لیستە بسڕیتەوە؟') ?>')">
                                                <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                                                <button type="submit" name="delete_list" class="btn btn-sm btn-outline-danger">
                                                    <?= __('Delete', 'سڕینەوە') ?>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm" style="border: none; border-radius: 15px; background: linear-gradient(135deg, #fff8f0, #ffffff);">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">💡 <?= __('Tips', 'ئامۆژگاری') ?></h5>
                <ul class="list-unstyled">
                    <li class="mb-2">✓ <?= __('Create different lists for different goals', 'لیستی جیاواز بۆ ئامانجی جیاواز دروست بکە') ?></li>
                    <li class="mb-2">✓ <?= __('Add exercises from the exercises page', 'ڕاهێنان زیاد بکە لە لاپەڕەی ڕاهێنانەکان') ?></li>
                    <li class="mb-2">✓ <?= __('Track your progress daily', 'بەردەوام پێشکەوتنەکەت بەدواداچۆ بکە') ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Create List Modal -->
<div class="modal fade" id="createListModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><?= __('Create New List', 'لیستی نوێ دروست بکە') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><?= __('List Name', 'ناوی لیست') ?></label>
                        <input type="text" name="name" class="form-control" required placeholder="<?= __('e.g., Upper Body Workout', 'بۆ نموونە: ڕاهێنانی سەرەوەی جەستە') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('Cancel', 'پاشگەزبوونەوە') ?></button>
                    <button type="submit" name="create_list" class="btn btn-primary"><?= __('Create', 'دروستکردن') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
