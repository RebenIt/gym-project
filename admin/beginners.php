<?php
/**
 * Admin - Beginner Programs Management
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
            'title' => sanitize($_POST['title'] ?? ''),
            'title_ku' => sanitize($_POST['title_ku'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'description_ku' => sanitize($_POST['description_ku'] ?? ''),
            'duration_weeks' => intval($_POST['duration_weeks'] ?? 0),
            'days_per_week' => intval($_POST['days_per_week'] ?? 0),
            'level' => sanitize($_POST['level'] ?? 'absolute_beginner'),
            'goal' => sanitize($_POST['goal'] ?? ''),
            'goal_ku' => sanitize($_POST['goal_ku'] ?? ''),
            'instructions' => sanitize($_POST['instructions'] ?? ''),
            'instructions_ku' => sanitize($_POST['instructions_ku'] ?? ''),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
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
                // Insert new beginner program
                $sql = "INSERT INTO beginner_programs (title, title_ku, description, description_ku,
                        duration_weeks, days_per_week, level, goal, goal_ku,
                        instructions, instructions_ku, is_featured, is_active, image)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                query($sql, [
                    $data['title'], $data['title_ku'], $data['description'], $data['description_ku'],
                    $data['duration_weeks'], $data['days_per_week'], $data['level'],
                    $data['goal'], $data['goal_ku'], $data['instructions'], $data['instructions_ku'],
                    $data['is_featured'], $data['is_active'], $data['image'] ?? null
                ]);
                setFlash('success', 'Beginner program added successfully!');
                redirect('beginners.php');

            } elseif ($_POST['form_action'] === 'edit' && $id) {
                // Update existing beginner program
                $sql = "UPDATE beginner_programs SET title=?, title_ku=?, description=?, description_ku=?,
                        duration_weeks=?, days_per_week=?, level=?, goal=?, goal_ku=?,
                        instructions=?, instructions_ku=?, is_featured=?, is_active=?";
                $params = [
                    $data['title'], $data['title_ku'], $data['description'], $data['description_ku'],
                    $data['duration_weeks'], $data['days_per_week'], $data['level'],
                    $data['goal'], $data['goal_ku'], $data['instructions'], $data['instructions_ku'],
                    $data['is_featured'], $data['is_active']
                ];

                if (isset($data['image'])) {
                    $sql .= ", image=?";
                    $params[] = $data['image'];
                }

                $sql .= " WHERE id=?";
                $params[] = $id;

                query($sql, $params);
                setFlash('success', 'Beginner program updated successfully!');
                redirect('beginners.php');
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM beginner_programs WHERE id = ?", [$id]);
    setFlash('success', 'Beginner program deleted successfully!');
    redirect('beginners.php');
}

// Get beginner program for editing
$program = null;
if ($action === 'edit' && $id) {
    $program = fetchOne("SELECT * FROM beginner_programs WHERE id = ?", [$id]);
    if (!$program) {
        redirect('beginners.php');
    }
}

// Get all beginner programs for listing
$programs = fetchAll("SELECT * FROM beginner_programs ORDER BY created_at DESC");

// Calculate stats
$totalPrograms = count($programs);
$activePrograms = count(array_filter($programs, fn($p) => $p['is_active']));
$featuredPrograms = count(array_filter($programs, fn($p) => $p['is_featured']));
$avgDuration = $totalPrograms > 0 ? round(array_sum(array_column($programs, 'duration_weeks')) / $totalPrograms) : 0;

$pageTitle = 'Manage Beginner Programs';
include 'includes/header.php';
?>

<div class="admin-content">
<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">🌟</span>
            Beginner Programs
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage beginner workout programs</p>
    </div>
    <a href="?action=add" style="background: white; color: #10b981; padding: 0.875rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(0,0,0,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'">
        <span style="font-size: 1.2rem;">+</span>
        Add New Program
    </a>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 2.5rem;">📚</div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo $totalPrograms; ?></div>
                <div style="opacity: 0.95; font-size: 0.95rem;">Total Programs</div>
            </div>
        </div>
    </div>
    <div style="background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(14, 165, 233, 0.3);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 2.5rem;">✅</div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo $activePrograms; ?></div>
                <div style="opacity: 0.95; font-size: 0.95rem;">Active Programs</div>
            </div>
        </div>
    </div>
    <div style="background: linear-gradient(135deg, #ec4899, #db2777); color: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(236, 72, 153, 0.3);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 2.5rem;">⭐</div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo $featuredPrograms; ?></div>
                <div style="opacity: 0.95; font-size: 0.95rem;">Featured Programs</div>
            </div>
        </div>
    </div>
    <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(245, 158, 11, 0.3);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 2.5rem;">📅</div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo $avgDuration; ?></div>
                <div style="opacity: 0.95; font-size: 0.95rem;">Avg. Duration (weeks)</div>
            </div>
        </div>
    </div>
</div>

<!-- Programs Table -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-bottom: 2px solid #10b981;">
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Image</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Title</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Duration</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Level</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Status</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($programs)): ?>
                <tr>
                    <td colspan="6" style="padding: 3rem; text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                            <span style="font-size: 4rem; opacity: 0.5;">🌟</span>
                            <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No beginner programs found</p>
                            <a href="?action=add" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 0.5rem;">Add your first program</a>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($programs as $p): ?>
                <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='white'">
                    <td style="padding: 1rem;">
                        <?php if ($p['image']): ?>
                            <img src="<?php echo uploadUrl($p['image']); ?>" alt="" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <?php else: ?>
                            <div style="width: 60px; height: 60px; border-radius: 8px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🌟</div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            <strong style="color: #1e293b; font-size: 0.95rem;"><?php echo e($p['title']); ?></strong>
                            <?php if ($p['title_ku']): ?>
                                <small style="color: #64748b; font-size: 0.85rem;" dir="rtl"><?php echo e($p['title_ku']); ?></small>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="color: #475569; font-size: 0.9rem;">
                            <div><?php echo $p['duration_weeks']; ?> weeks</div>
                            <div style="color: #64748b; font-size: 0.85rem;"><?php echo $p['days_per_week']; ?> days/week</div>
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <?php
                        $levelColors = [
                            'absolute_beginner' => ['#10b981', '#d1fae5'],
                            'beginner' => ['#0ea5e9', '#dbeafe'],
                            'early_intermediate' => ['#f59e0b', '#fef3c7']
                        ];
                        $colors = $levelColors[$p['level']] ?? ['#64748b', '#f1f5f9'];
                        ?>
                        <span style="background: <?php echo $colors[1]; ?>; color: <?php echo $colors[0]; ?>; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; display: inline-block;">
                            <?php echo ucfirst(str_replace('_', ' ', $p['level'])); ?>
                        </span>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <?php if ($p['is_active']): ?>
                                <span style="background: #d1fae5; color: #059669; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-align: center;">Active</span>
                            <?php else: ?>
                                <span style="background: #fee2e2; color: #dc2626; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-align: center;">Inactive</span>
                            <?php endif; ?>
                            <?php if ($p['is_featured']): ?>
                                <span style="background: #fef3c7; color: #d97706; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-align: center;">⭐ Featured</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                            <a href="?action=edit&id=<?php echo $p['id']; ?>" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">Edit</a>
                            <a href="?action=delete&id=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure you want to delete this program?')" style="background: #f1f5f9; color: #475569; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'">Delete</a>
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
<div class="page-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;"><?php echo $action === 'add' ? '➕' : '✏️'; ?></span>
            <?php echo $action === 'add' ? 'Add New Program' : 'Edit Program'; ?>
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Fill in the program details in both languages</p>
    </div>
    <a href="beginners.php" style="background: white; color: #10b981; padding: 0.875rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        ← Back to List
    </a>
</div>

<?php if ($error): ?>
<div style="background: #fee2e2; border-left: 4px solid #dc2626; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<?php echo csrfField(); ?>
<input type="hidden" name="form_action" value="<?php echo $action; ?>">

<!-- Basic Info -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Basic Information</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Title (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="title" value="<?php echo e($program['title'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Title (Kurdish) ناو</label>
                <input type="text" name="title_ku" dir="rtl" value="<?php echo e($program['title_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Description (English)</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($program['description'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Description (Kurdish)</label>
                <textarea name="description_ku" rows="4" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($program['description_ku'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Media -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Media</h3>
    </div>
    <div style="padding: 1.5rem;">
        <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Program Image</label>
        <div id="imagePreview" style="width: 200px; height: 200px; border: 3px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; overflow: hidden; background: #f8fafc;">
            <?php if (!empty($program['image'])): ?>
                <img src="<?php echo uploadUrl($program['image']); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <div style="text-align: center; color: #94a3b8;">
                    <span style="font-size: 3rem; display: block; margin-bottom: 0.5rem;">📷</span>
                    <p style="margin: 0; font-size: 0.9rem;">Upload program image</p>
                </div>
            <?php endif; ?>
        </div>
        <input type="file" name="image" accept="image/*" onchange="previewImage(this)" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem;">
    </div>
</div>

<!-- Program Details -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Program Details</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Duration (weeks)</label>
                <input type="number" name="duration_weeks" value="<?php echo e($program['duration_weeks'] ?? ''); ?>" min="1" max="52" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Days per Week</label>
                <input type="number" name="days_per_week" value="<?php echo e($program['days_per_week'] ?? ''); ?>" min="1" max="7" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Level</label>
                <select name="level" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="absolute_beginner" <?php echo ($program['level'] ?? '') === 'absolute_beginner' ? 'selected' : ''; ?>>Absolute Beginner</option>
                    <option value="beginner" <?php echo ($program['level'] ?? '') === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                    <option value="early_intermediate" <?php echo ($program['level'] ?? '') === 'early_intermediate' ? 'selected' : ''; ?>>Early Intermediate</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Goal (English)</label>
                <input type="text" name="goal" value="<?php echo e($program['goal'] ?? ''); ?>" placeholder="e.g., Build strength and confidence" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Goal (Kurdish)</label>
                <input type="text" name="goal_ku" dir="rtl" value="<?php echo e($program['goal_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>
    </div>
</div>

<!-- Instructions -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Instructions</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Instructions (English)</label>
                <textarea name="instructions" rows="6" placeholder="Detailed program instructions..." style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($program['instructions'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Instructions (Kurdish)</label>
                <textarea name="instructions_ku" rows="6" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($program['instructions_ku'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Options -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #ec4899, #db2777); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Options</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; user-select: none;">
                <input type="checkbox" name="is_active" value="1" <?php echo ($program['is_active'] ?? 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer;">
                <span style="color: #1e293b; font-weight: 500;">Active (visible on website)</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; user-select: none;">
                <input type="checkbox" name="is_featured" value="1" <?php echo ($program['is_featured'] ?? 0) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer;">
                <span style="color: #1e293b; font-weight: 500;">Featured (show on homepage)</span>
            </label>
        </div>
    </div>
</div>

<div style="display: flex; gap: 1rem;">
    <button type="submit" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'">
        <?php echo $action === 'add' ? '✓ Add Program' : '✓ Update Program'; ?>
    </button>
    <a href="beginners.php" style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.3s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Cancel</a>
</div>
</form>
<?php endif; ?>
</div>

<script src="../assets/js/admin-modern.js"></script>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="" style="width: 100%; height: 100%; object-fit: cover;">';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?>
