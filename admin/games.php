<?php
/**
 * Admin - Games/Exercises Management
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireAdminLogin();

$admin = getCurrentAdmin();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        $data = [
            'name' => sanitize($_POST['name'] ?? ''),
            'name_ku' => sanitize($_POST['name_ku'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'description_ku' => sanitize($_POST['description_ku'] ?? ''),
            'short_description' => sanitize($_POST['short_description'] ?? ''),
            'short_description_ku' => sanitize($_POST['short_description_ku'] ?? ''),
            'youtube_url' => sanitize($_POST['youtube_url'] ?? ''),
            'difficulty' => sanitize($_POST['difficulty'] ?? 'beginner'),
            'muscle_group' => sanitize($_POST['muscle_group'] ?? ''),
            'muscle_group_ku' => sanitize($_POST['muscle_group_ku'] ?? ''),
            'equipment_needed' => sanitize($_POST['equipment_needed'] ?? ''),
            'equipment_needed_ku' => sanitize($_POST['equipment_needed_ku'] ?? ''),
            'duration_minutes' => intval($_POST['duration_minutes'] ?? 0),
            'calories_burn' => intval($_POST['calories_burn'] ?? 0),
            'instructions' => sanitize($_POST['instructions'] ?? ''),
            'instructions_ku' => sanitize($_POST['instructions_ku'] ?? ''),
            'tips' => sanitize($_POST['tips'] ?? ''),
            'tips_ku' => sanitize($_POST['tips_ku'] ?? ''),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_beginner_friendly' => isset($_POST['is_beginner_friendly']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadResult = uploadFile($_FILES['image']);
            if ($uploadResult['success']) {
                $data['image'] = $uploadResult['filename'];
            } else {
                $error = $uploadResult['message'];
            }
        }
        
        if (empty($error)) {
            if ($_POST['form_action'] === 'add') {
                // Insert new game
                $sql = "INSERT INTO games (name, name_ku, description, description_ku, short_description, short_description_ku, 
                        youtube_url, difficulty, muscle_group, muscle_group_ku, equipment_needed, equipment_needed_ku,
                        duration_minutes, calories_burn, instructions, instructions_ku, tips, tips_ku, 
                        is_featured, is_beginner_friendly, is_active, image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                query($sql, [
                    $data['name'], $data['name_ku'], $data['description'], $data['description_ku'],
                    $data['short_description'], $data['short_description_ku'], $data['youtube_url'],
                    $data['difficulty'], $data['muscle_group'], $data['muscle_group_ku'],
                    $data['equipment_needed'], $data['equipment_needed_ku'], $data['duration_minutes'],
                    $data['calories_burn'], $data['instructions'], $data['instructions_ku'],
                    $data['tips'], $data['tips_ku'], $data['is_featured'], $data['is_beginner_friendly'],
                    $data['is_active'], $data['image'] ?? null
                ]);
                setFlash('success', 'Game added successfully!');
                redirect('games.php');
                
            } elseif ($_POST['form_action'] === 'edit' && $id) {
                // Update existing game
                $sql = "UPDATE games SET name=?, name_ku=?, description=?, description_ku=?, 
                        short_description=?, short_description_ku=?, youtube_url=?, difficulty=?,
                        muscle_group=?, muscle_group_ku=?, equipment_needed=?, equipment_needed_ku=?,
                        duration_minutes=?, calories_burn=?, instructions=?, instructions_ku=?,
                        tips=?, tips_ku=?, is_featured=?, is_beginner_friendly=?, is_active=?";
                $params = [
                    $data['name'], $data['name_ku'], $data['description'], $data['description_ku'],
                    $data['short_description'], $data['short_description_ku'], $data['youtube_url'],
                    $data['difficulty'], $data['muscle_group'], $data['muscle_group_ku'],
                    $data['equipment_needed'], $data['equipment_needed_ku'], $data['duration_minutes'],
                    $data['calories_burn'], $data['instructions'], $data['instructions_ku'],
                    $data['tips'], $data['tips_ku'], $data['is_featured'], $data['is_beginner_friendly'],
                    $data['is_active']
                ];
                
                if (isset($data['image'])) {
                    $sql .= ", image=?";
                    $params[] = $data['image'];
                }
                
                $sql .= " WHERE id=?";
                $params[] = $id;
                
                query($sql, $params);
                setFlash('success', 'Game updated successfully!');
                redirect('games.php');
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM games WHERE id = ?", [$id]);
    setFlash('success', 'Game deleted successfully!');
    redirect('games.php');
}

// Get game for editing
$game = null;
if ($action === 'edit' && $id) {
    $game = fetchOne("SELECT * FROM games WHERE id = ?", [$id]);
    if (!$game) {
        redirect('games.php');
    }
}

// Get all games for listing
$games = fetchAll("SELECT * FROM games ORDER BY sort_order, created_at DESC");

$pageTitle = 'Manage Games';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-content">
            <?php if ($action === 'list'): ?>
                <!-- List View -->
                <div class="admin-header">
                    <div>
                        <h1>Games / Exercises</h1>
                        <p style="color: var(--light-dim);">Manage all gym exercises and games</p>
                    </div>
                    <a href="?action=add" class="btn btn-primary">+ Add New Game</a>
                </div>
                
                <?php displayFlash(); ?>
                
                <div class="card">
                    <div class="card-body" style="padding: 0;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Difficulty</th>
                                    <th>Muscle Group</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($games)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--light-dim);">
                                            No games found. <a href="?action=add">Add your first game</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($games as $g): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo uploadUrl($g['image']); ?>" alt="" style="width: 60px; height: 45px; object-fit: cover; border-radius: 8px;">
                                            </td>
                                            <td>
                                                <strong><?php echo e($g['name']); ?></strong>
                                                <?php if ($g['name_ku']): ?>
                                                    <br><small style="color: var(--light-dim);"><?php echo e($g['name_ku']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $g['difficulty'] === 'beginner' ? 'success' : ($g['difficulty'] === 'intermediate' ? 'warning' : 'danger'); ?>">
                                                    <?php echo ucfirst($g['difficulty']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo e($g['muscle_group']); ?></td>
                                            <td>
                                                <?php if ($g['is_active']): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inactive</span>
                                                <?php endif; ?>
                                                <?php if ($g['is_featured']): ?>
                                                    <span class="badge badge-info">Featured</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="actions">
                                                <a href="?action=edit&id=<?php echo $g['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                                <a href="?action=delete&id=<?php echo $g['id']; ?>" class="btn btn-outline btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Add/Edit Form -->
                <div class="admin-header">
                    <div>
                        <h1><?php echo $action === 'add' ? 'Add New Game' : 'Edit Game'; ?></h1>
                        <p style="color: var(--light-dim);">Fill in the game details in both languages</p>
                    </div>
                    <a href="games.php" class="btn btn-secondary">← Back to List</a>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="admin-form-page">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="form_action" value="<?php echo $action; ?>">
                    
                    <!-- Basic Info -->
                    <div class="form-section">
                        <h3 class="form-section-title">Basic Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Name (English) *</label>
                                <input type="text" name="name" class="form-control" value="<?php echo e($game['name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Name (Kurdish) ناو</label>
                                <input type="text" name="name_ku" class="form-control" dir="rtl" value="<?php echo e($game['name_ku'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Short Description (English)</label>
                                <input type="text" name="short_description" class="form-control" value="<?php echo e($game['short_description'] ?? ''); ?>" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Short Description (Kurdish)</label>
                                <input type="text" name="short_description_ku" class="form-control" dir="rtl" value="<?php echo e($game['short_description_ku'] ?? ''); ?>" maxlength="255">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Full Description (English)</label>
                                <textarea name="description" class="form-control" rows="4"><?php echo e($game['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Full Description (Kurdish)</label>
                                <textarea name="description_ku" class="form-control" rows="4" dir="rtl"><?php echo e($game['description_ku'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Media -->
                    <div class="form-section">
                        <h3 class="form-section-title">Media</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Image</label>
                                <div class="image-preview <?php echo empty($game['image']) ? 'empty' : ''; ?>" id="imagePreview">
                                    <?php if (!empty($game['image'])): ?>
                                        <img src="<?php echo uploadUrl($game['image']); ?>" alt="">
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <div class="form-group">
                                <label class="form-label">YouTube Video URL</label>
                                <input type="url" name="youtube_url" class="form-control" value="<?php echo e($game['youtube_url'] ?? ''); ?>" placeholder="https://youtube.com/watch?v=...">
                                <small style="color: var(--light-dim);">Paste the full YouTube video URL</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Exercise Details -->
                    <div class="form-section">
                        <h3 class="form-section-title">Exercise Details</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Difficulty Level</label>
                                <select name="difficulty" class="form-control">
                                    <option value="beginner" <?php echo ($game['difficulty'] ?? '') === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                    <option value="intermediate" <?php echo ($game['difficulty'] ?? '') === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                    <option value="advanced" <?php echo ($game['difficulty'] ?? '') === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Duration (minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control" value="<?php echo e($game['duration_minutes'] ?? ''); ?>" min="0">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Muscle Group (English)</label>
                                <input type="text" name="muscle_group" class="form-control" value="<?php echo e($game['muscle_group'] ?? ''); ?>" placeholder="e.g., Chest, Shoulders, Triceps">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Muscle Group (Kurdish)</label>
                                <input type="text" name="muscle_group_ku" class="form-control" dir="rtl" value="<?php echo e($game['muscle_group_ku'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Equipment Needed (English)</label>
                                <input type="text" name="equipment_needed" class="form-control" value="<?php echo e($game['equipment_needed'] ?? ''); ?>" placeholder="e.g., Dumbbells, Barbell">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Equipment Needed (Kurdish)</label>
                                <input type="text" name="equipment_needed_ku" class="form-control" dir="rtl" value="<?php echo e($game['equipment_needed_ku'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Calories Burned (approximate)</label>
                            <input type="number" name="calories_burn" class="form-control" value="<?php echo e($game['calories_burn'] ?? ''); ?>" min="0" style="max-width: 200px;">
                        </div>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="form-section">
                        <h3 class="form-section-title">Instructions & Tips</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Instructions (English)</label>
                                <textarea name="instructions" class="form-control" rows="5" placeholder="Step by step instructions..."><?php echo e($game['instructions'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Instructions (Kurdish)</label>
                                <textarea name="instructions_ku" class="form-control" rows="5" dir="rtl"><?php echo e($game['instructions_ku'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Tips (English)</label>
                                <textarea name="tips" class="form-control" rows="3" placeholder="Helpful tips..."><?php echo e($game['tips'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tips (Kurdish)</label>
                                <textarea name="tips_ku" class="form-control" rows="3" dir="rtl"><?php echo e($game['tips_ku'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Options -->
                    <div class="form-section">
                        <h3 class="form-section-title">Options</h3>
                        
                        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                            <label class="form-check">
                                <input type="checkbox" name="is_active" value="1" <?php echo ($game['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                <span>Active (visible on website)</span>
                            </label>
                            <label class="form-check">
                                <input type="checkbox" name="is_featured" value="1" <?php echo ($game['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                <span>Featured (show on homepage)</span>
                            </label>
                            <label class="form-check">
                                <input type="checkbox" name="is_beginner_friendly" value="1" <?php echo ($game['is_beginner_friendly'] ?? 0) ? 'checked' : ''; ?>>
                                <span>Beginner Friendly</span>
                            </label>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? 'Add Game' : 'Update Game'; ?></button>
                        <a href="games.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        </main>
    </div>
    
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="">';
                    preview.classList.remove('empty');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
