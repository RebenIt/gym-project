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
include 'includes/header.php';
?>

<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);">
<div>
<h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
<span class="icon" style="font-size: 2.5rem;">💪</span>
Games / Exercises
</h1>
<p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage all gym exercises and games</p>
</div>
<a href="?action=add" class="admin-btn" style="background: white; color: #6366f1; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s;">
<span class="icon" style="font-size: 1.2rem;">+</span>
Add New Game
</a>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
<div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
<div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Exercises</div>
<div style="font-size: 2rem; font-weight: 700;"><?php echo count($games); ?></div>
</div>
<div style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
<div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Active</div>
<div style="font-size: 2rem; font-weight: 700;"><?php echo count(array_filter($games, fn($g) => $g['is_active'])); ?></div>
</div>
<div style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
<div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Featured</div>
<div style="font-size: 2rem; font-weight: 700;"><?php echo count(array_filter($games, fn($g) => $g['is_featured'])); ?></div>
</div>
<div style="background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);">
<div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Beginner Friendly</div>
<div style="font-size: 2rem; font-weight: 700;"><?php echo count(array_filter($games, fn($g) => $g['is_beginner_friendly'])); ?></div>
</div>
</div>

<div class="admin-card" style="border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; background: white;">
<div class="admin-table-wrapper">
<table class="admin-table-modern" style="width: 100%;">
<thead style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 2px solid #e2e8f0;">
<tr>
<th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Name</th>
<th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Difficulty</th>
<th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Muscle Group</th>
<th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
<th style="padding: 1rem; text-align: right; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
</tr>
</thead>
<tbody style="background: white;">
<?php if (empty($games)): ?>
<tr>
<td colspan="5" class="empty-state">
<div class="empty-state-content">
<span class="empty-icon">💪</span>
<p>No games found</p>
<a href="?action=add" class="admin-btn admin-btn-primary admin-btn-sm">Add your first game</a>
</div>
</td>
</tr>
<?php else: ?>
<?php foreach ($games as $g): ?>
<tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
<td style="padding: 1rem;">
<div>
<div style="font-weight: 600; color: #1e293b; font-size: 0.95rem; margin-bottom: 0.25rem;"><?php echo e($g['name']); ?></div>
<?php if ($g['name_ku']): ?>
<div style="color: #64748b; font-size: 0.85rem; direction: rtl;"><?php echo e($g['name_ku']); ?></div>
<?php endif; ?>
</div>
</td>
<td style="padding: 1rem;">
<span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600; <?php
$badgeColor = $g['difficulty'] === 'beginner' ? 'background: #d1fae5; color: #065f46;' :
              ($g['difficulty'] === 'intermediate' ? 'background: #fef3c7; color: #92400e;' :
              'background: #fee2e2; color: #991b1b;');
echo $badgeColor;
?>">
<?php echo ucfirst($g['difficulty']); ?>
</span>
</td>
<td style="padding: 1rem; color: #475569; font-size: 0.875rem;"><?php echo e($g['muscle_group']); ?></td>
<td style="padding: 1rem;">
<div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
<?php if ($g['is_active']): ?>
<span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600; background: #d1fae5; color: #065f46;">✓ Active</span>
<?php else: ?>
<span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600; background: #fee2e2; color: #991b1b;">✗ Inactive</span>
<?php endif; ?>
<?php if ($g['is_featured']): ?>
<span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600; background: #dbeafe; color: #1e40af;">⭐ Featured</span>
<?php endif; ?>
</div>
</td>
<td style="padding: 1rem; text-align: right;">
<div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
<a href="?action=edit&id=<?php echo $g['id']; ?>" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; background: #6366f1; color: white; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(99,102,241,0.2);" onmouseover="this.style.background='#4f46e5'; this.style.boxShadow='0 4px 8px rgba(99,102,241,0.3)'" onmouseout="this.style.background='#6366f1'; this.style.boxShadow='0 2px 4px rgba(99,102,241,0.2)'">
✏️ Edit
</a>
<a href="?action=delete&id=<?php echo $g['id']; ?>" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; background: #fee2e2; color: #dc2626; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" onclick="return confirm('Are you sure you want to delete this game?')">
🗑️ Delete
</a>
</div>
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
<div class="page-header" style="background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;"><?php echo $action === 'add' ? '➕' : '✏️'; ?></span>
            <?php echo $action === 'add' ? 'Add New Exercise' : 'Edit Exercise'; ?>
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Fill in the exercise details in both languages</p>
    </div>
    <a href="games.php" style="background: white; color: #6366f1; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
        ← Back to List
    </a>
</div>

<?php if ($error): ?>
    <div style="background: #fee2e2; border-left: 4px solid #dc2626; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <?php echo e($error); ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<?php echo csrfField(); ?>
<input type="hidden" name="form_action" value="<?php echo $action; ?>">

<!-- Basic Information -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #6366f1, #ec4899); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📋 Basic Information</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Exercise Name (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="name" value="<?php echo e($game['name'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Exercise Name (Kurdish) ناو</label>
                <input type="text" name="name_ku" dir="rtl" value="<?php echo e($game['name_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Short Description (English)</label>
                <input type="text" name="short_description" value="<?php echo e($game['short_description'] ?? ''); ?>" maxlength="255" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Short Description (Kurdish)</label>
                <input type="text" name="short_description_ku" dir="rtl" value="<?php echo e($game['short_description_ku'] ?? ''); ?>" maxlength="255" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Full Description (English)</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($game['description'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Full Description (Kurdish)</label>
                <textarea name="description_ku" rows="4" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($game['description_ku'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Media -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #10b981, #14b8a6); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📷 Media & Video</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Exercise Image</label>
                <div id="imagePreview" style="margin-bottom: 1rem; border-radius: 8px; overflow: hidden; border: 2px dashed #e2e8f0;">
                    <?php if (!empty($game['image'])): ?>
                        <img src="<?php echo uploadUrl($game['image']); ?>" alt="" style="width: 100%; max-height: 250px; object-fit: cover; background: #f8fafc;">
                    <?php else: ?>
                        <div style="padding: 3rem; text-align: center; background: #f8fafc;">
                            <span style="font-size: 4rem; opacity: 0.3;">📷</span>
                            <p style="color: #64748b; font-size: 1rem; margin: 0.5rem 0 0 0;">Upload exercise image</p>
                        </div>
                    <?php endif; ?>
                </div>
                <input type="file" name="image" accept="image/*" onchange="previewImage(this)" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s; background: white; cursor: pointer;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">YouTube Video URL</label>
                <input type="url" name="youtube_url" value="<?php echo e($game['youtube_url'] ?? ''); ?>" placeholder="https://youtube.com/watch?v=..." style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Paste the full YouTube video URL for tutorial</p>
            </div>
        </div>
    </div>
</div>

<!-- Exercise Details -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #f59e0b, #ef4444); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">💪 Exercise Details</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Difficulty Level</label>
                <select name="difficulty" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s; background: white; cursor: pointer;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="beginner" <?php echo ($game['difficulty'] ?? '') === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                    <option value="intermediate" <?php echo ($game['difficulty'] ?? '') === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                    <option value="advanced" <?php echo ($game['difficulty'] ?? '') === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                </select>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Duration (minutes)</label>
                <input type="number" name="duration_minutes" value="<?php echo e($game['duration_minutes'] ?? ''); ?>" min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Calories Burned</label>
                <input type="number" name="calories_burn" value="<?php echo e($game['calories_burn'] ?? ''); ?>" min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Approximate calories</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Muscle Group (English)</label>
                <input type="text" name="muscle_group" value="<?php echo e($game['muscle_group'] ?? ''); ?>" placeholder="e.g., Chest, Shoulders, Triceps" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Muscle Group (Kurdish)</label>
                <input type="text" name="muscle_group_ku" dir="rtl" value="<?php echo e($game['muscle_group_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Equipment Needed (English)</label>
                <input type="text" name="equipment_needed" value="<?php echo e($game['equipment_needed'] ?? ''); ?>" placeholder="e.g., Dumbbells, Barbell" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Equipment Needed (Kurdish)</label>
                <input type="text" name="equipment_needed_ku" dir="rtl" value="<?php echo e($game['equipment_needed_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>
    </div>
</div>

<!-- Instructions & Tips -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #3b82f6, #6366f1); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📝 Instructions & Tips</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Instructions (English)</label>
                <textarea name="instructions" rows="5" placeholder="Step by step instructions..." style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($game['instructions'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Instructions (Kurdish)</label>
                <textarea name="instructions_ku" rows="5" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($game['instructions_ku'] ?? ''); ?></textarea>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Tips (English)</label>
                <textarea name="tips" rows="3" placeholder="Helpful tips..." style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($game['tips'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Tips (Kurdish)</label>
                <textarea name="tips_ku" rows="3" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($game['tips_ku'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Options -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #ec4899, #a855f7); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">⚙️ Options</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; user-select: none;">
                <input type="checkbox" name="is_active" value="1" <?php echo ($game['is_active'] ?? 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #6366f1;">
                <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">Active (visible on website)</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; user-select: none;">
                <input type="checkbox" name="is_featured" value="1" <?php echo ($game['is_featured'] ?? 0) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #ec4899;">
                <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">⭐ Featured (show on homepage)</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; user-select: none;">
                <input type="checkbox" name="is_beginner_friendly" value="1" <?php echo ($game['is_beginner_friendly'] ?? 0) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #10b981;">
                <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">Beginner Friendly</span>
            </label>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div style="display: flex; gap: 1rem;">
    <button type="submit" style="background: linear-gradient(135deg, #6366f1, #ec4899); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(99, 102, 241, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(99, 102, 241, 0.3)'">
        <?php echo $action === 'add' ? '✓ Add Exercise' : '✓ Update Exercise'; ?>
    </button>
    <a href="games.php" style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
        Cancel
    </a>
</div>
</form>
<?php endif; ?>

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

<?php include 'includes/footer.php'; ?>
