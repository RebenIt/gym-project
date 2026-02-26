<?php
require_once '../includes/functions.php';
requireLogin();

$currentUser = getCurrentUser();
$pageTitle = __('Daily Notes', 'تێبینیە ڕۆژانەکان');
include 'includes/header.php';

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note'])) {
    $noteDate = sanitize($_POST['note_date'] ?? date('Y-m-d'));
    $title = sanitize($_POST['title'] ?? '');
    $content = sanitize($_POST['content'] ?? '');
    $mood = sanitize($_POST['mood'] ?? 'good');
    $weight = $_POST['weight_kg'] ?? null;
    $workoutDone = isset($_POST['workout_done']) ? 1 : 0;

    // Check if note exists for this date
    $existing = fetchOne("SELECT id FROM user_notes WHERE user_id = ? AND note_date = ?", [$currentUser['id'], $noteDate]);

    if ($existing) {
        query("UPDATE user_notes SET title = ?, content = ?, mood = ?, weight_kg = ?, workout_done = ?, updated_at = NOW()
               WHERE id = ?", [$title, $content, $mood, $weight, $workoutDone, $existing['id']]);
    } else {
        query("INSERT INTO user_notes (user_id, note_date, title, content, mood, weight_kg, workout_done, created_at)
               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())", [$currentUser['id'], $noteDate, $title, $content, $mood, $weight, $workoutDone]);
    }

    setFlash('success', __('Note saved!', 'تێبینی پاشەکەوتکرا!'));
    redirect('notes.php');
}

// Get notes for calendar
$currentMonth = $_GET['month'] ?? date('Y-m');
$notes = fetchAll("SELECT * FROM user_notes WHERE user_id = ? AND DATE_FORMAT(note_date, '%Y-%m') = ? ORDER BY note_date DESC", [$currentUser['id'], $currentMonth]);
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm" style="border: none; border-radius: 15px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><?= __('Add/Edit Note', 'زیادکردن/دەستکاریکردنی تێبینی') ?></h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?= __('Date', 'بەروار') ?></label>
                        <input type="date" name="note_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= __('Title (Optional)', 'ناونیشان (ئیختیاری)') ?></label>
                        <input type="text" name="title" class="form-control" placeholder="<?= __('Daily workout', 'ڕاهێنانی ڕۆژانە') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= __('How do you feel?', 'کەیفیت چۆنە؟') ?></label>    
                        <select name="mood" class="form-select">
                            <option value="great">😄 <?= __('Great', 'نایاب') ?></option>
                            <option value="good" selected>🙂 <?= __('Good', 'باش') ?></option>
                            <option value="okay">😐 <?= __('Okay', 'باشە') ?></option>
                            <option value="tired">😴 <?= __('Tired', 'ماندوو') ?></option>
                            <option value="bad">😞 <?= __('Bad', 'خراپ') ?></option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= __('Notes', 'تێبینیەکان') ?></label>
                        <textarea name="content" class="form-control" rows="6" placeholder="<?= __('Write about your day, workout, diet, etc.', 'دەربارەی ڕۆژەکەت، ڕاهێنان، خواردن، هتد بنووسە') ?>"></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label"><?= __('Weight (kg)', 'کێش (کگ)') ?></label>
                            <input type="number" step="0.1" name="weight_kg" class="form-control" placeholder="70.5">
                        </div>
                        <div class="col-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="workout_done" class="form-check-input" id="workoutDone">
                                <label class="form-check-label" for="workoutDone">
                                    <?= __('Workout completed', 'ڕاهێنان تەواوبوو') ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="save_note" class="btn btn-primary w-100">
                        <?= __('Save Note', 'پاشەکەوتکردنی تێبینی') ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm" style="border: none; border-radius: 15px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><?= __('Your Notes', 'تێبینیەکانت') ?></h5>

                <div class="mb-4">
                    <input type="month" class="form-control" value="<?= e($currentMonth) ?>" onchange="window.location='?month='+this.value">
                </div>

                <?php if (empty($notes)): ?>
                    <div class="text-center py-5">
                        <div style="font-size: 4rem;">📝</div>
                        <h6 class="mt-3"><?= __('No notes for this month', 'هیچ تێبینیەک نییە بۆ ئەم مانگە') ?></h6>
                    </div>
                <?php else: ?>
                    <?php foreach ($notes as $note): ?>
                        <div class="card mb-3" style="border-left: 4px solid #f97316;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <?= formatDate($note['note_date'], 'F d, Y') ?>
                                            <?php if ($note['title']): ?>
                                                - <?= e($note['title']) ?>
                                            <?php endif; ?>
                                        </h6>
                                        <div class="mb-2">
                                            <?php
                                            $moods = ['great' => '😄', 'good' => '🙂', 'okay' => '😐', 'tired' => '😴', 'bad' => '😞'];
                                            echo $moods[$note['mood']] ?? '🙂';
                                            ?>
                                            <span class="badge" style="background: linear-gradient(135deg, #f97316, #dc2626); margin-left: 10px;">
                                                <?= ucfirst($note['mood']) ?>
                                            </span>
                                            <?php if ($note['workout_done']): ?>
                                                <span class="badge bg-success ms-2">✓ <?= __('Workout Done', 'ڕاهێنان تەواوبوو') ?></span>
                                            <?php endif; ?>
                                            <?php if ($note['weight_kg']): ?>
                                                <span class="badge bg-secondary ms-2"><?= $note['weight_kg'] ?> kg</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-0"><?= nl2br(e($note['content'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
