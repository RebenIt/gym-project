<?php
/**
 * Admin - Trainers Management
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
            'first_name' => sanitize($_POST['first_name'] ?? ''),
            'first_name_ku' => sanitize($_POST['first_name_ku'] ?? ''),
            'last_name' => sanitize($_POST['last_name'] ?? ''),
            'last_name_ku' => sanitize($_POST['last_name_ku'] ?? ''),
            'specialization' => sanitize($_POST['specialization'] ?? ''),
            'specialization_ku' => sanitize($_POST['specialization_ku'] ?? ''),
            'bio' => sanitize($_POST['bio'] ?? ''),
            'bio_ku' => sanitize($_POST['bio_ku'] ?? ''),
            'experience_years' => intval($_POST['experience_years'] ?? 0),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'social_facebook' => sanitize($_POST['social_facebook'] ?? ''),
            'social_instagram' => sanitize($_POST['social_instagram'] ?? ''),
            'social_youtube' => sanitize($_POST['social_youtube'] ?? ''),
            'sort_order' => intval($_POST['sort_order'] ?? 0),
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
                $sql = "INSERT INTO trainers (first_name, first_name_ku, last_name, last_name_ku, specialization, specialization_ku,
                        bio, bio_ku, experience_years, phone, email, social_facebook, social_instagram, social_youtube, sort_order, is_active, avatar)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                query($sql, [
                    $data['first_name'], $data['first_name_ku'], $data['last_name'], $data['last_name_ku'],
                    $data['specialization'], $data['specialization_ku'], $data['bio'], $data['bio_ku'],
                    $data['experience_years'], $data['phone'], $data['email'], $data['social_facebook'],
                    $data['social_instagram'], $data['social_youtube'], $data['sort_order'], $data['is_active'],
                    $data['image'] ?? null
                ]);
                setFlash('success', 'Trainer added successfully!');
                redirect('trainers.php');

            } elseif ($_POST['form_action'] === 'edit' && $id) {
                $sql = "UPDATE trainers SET first_name=?, first_name_ku=?, last_name=?, last_name_ku=?,
                        specialization=?, specialization_ku=?, bio=?, bio_ku=?, experience_years=?,
                        phone=?, email=?, social_facebook=?, social_instagram=?, social_youtube=?, sort_order=?, is_active=?";
                $params = [
                    $data['first_name'], $data['first_name_ku'], $data['last_name'], $data['last_name_ku'],
                    $data['specialization'], $data['specialization_ku'], $data['bio'], $data['bio_ku'],
                    $data['experience_years'], $data['phone'], $data['email'], $data['social_facebook'],
                    $data['social_instagram'], $data['social_youtube'], $data['sort_order'], $data['is_active']
                ];

                if (isset($data['image'])) {
                    $sql .= ", avatar=?";
                    $params[] = $data['image'];
                }

                $sql .= " WHERE id=?";
                $params[] = $id;

                query($sql, $params);
                setFlash('success', 'Trainer updated successfully!');
                redirect('trainers.php');
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM trainers WHERE id = ?", [$id]);
    setFlash('success', 'Trainer deleted successfully!');
    redirect('trainers.php');
}

// Get trainer for editing
$trainer = null;
if ($action === 'edit' && $id) {
    $trainer = fetchOne("SELECT * FROM trainers WHERE id = ?", [$id]);
    if (!$trainer) {
        redirect('trainers.php');
    }
}

// Get all trainers
$trainers = fetchAll("SELECT * FROM trainers ORDER BY sort_order, created_at DESC");

// Calculate stats
$totalTrainers = count($trainers);
$activeTrainers = count(array_filter($trainers, fn($t) => $t['is_active']));
$uniqueSpecializations = count(array_unique(array_filter(array_column($trainers, 'specialization'))));
$avgExperience = $totalTrainers > 0 ? round(array_sum(array_column($trainers, 'experience_years')) / $totalTrainers, 1) : 0;

$pageTitle = 'Manage Trainers';
include 'includes/header.php';
?>

<div class="admin-content">
<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(249, 115, 22, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">👨‍🏫</span>
            Trainers Management
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage your gym trainers and their specializations</p>
    </div>
    <a href="?action=add" class="admin-btn" style="background: white; color: #f97316; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
        <span class="icon" style="font-size: 1.2rem;">+</span>
        Add New Trainer
    </a>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Trainers</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $totalTrainers; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Active Trainers</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $activeTrainers; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Specializations</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $uniqueSpecializations; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #ec4899 0%, #a855f7 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Avg Experience</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $avgExperience; ?> yrs</div>
    </div>
</div>

<!-- Trainers Table -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Photo</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Name</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Specialization</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Experience</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Contact</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trainers)): ?>
                    <tr>
                        <td colspan="7" style="padding: 3rem; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                <span style="font-size: 4rem; opacity: 0.3;">👨‍🏫</span>
                                <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No trainers found</p>
                                <a href="?action=add" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <span>+</span> Add your first trainer
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($trainers as $t): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <img src="<?php echo uploadUrl($t['avatar']); ?>" alt="" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            </td>
                            <td style="padding: 1rem;">
                                <div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem; margin-bottom: 0.25rem;">
                                        <?php echo e($t['first_name'] . ' ' . $t['last_name']); ?>
                                    </div>
                                    <?php if ($t['first_name_ku']): ?>
                                        <div style="color: #64748b; font-size: 0.85rem; direction: rtl;">
                                            <?php echo e($t['first_name_ku'] . ' ' . $t['last_name_ku']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <div>
                                    <div style="color: #475569; font-size: 0.9rem;"><?php echo e($t['specialization']); ?></div>
                                    <?php if ($t['specialization_ku']): ?>
                                        <div style="color: #94a3b8; font-size: 0.8rem; direction: rtl;"><?php echo e($t['specialization_ku']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="background: linear-gradient(135deg, #f97316, #dc2626); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    <?php echo $t['experience_years']; ?> years
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-size: 0.85rem;">
                                    <?php if ($t['phone']): ?>
                                        <div style="color: #475569; margin-bottom: 0.25rem;">📞 <?php echo e($t['phone']); ?></div>
                                    <?php endif; ?>
                                    <?php if ($t['email']): ?>
                                        <div style="color: #64748b;">📧 <?php echo e($t['email']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($t['is_active']): ?>
                                    <span style="background: #dcfce7; color: #16a34a; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Active</span>
                                <?php else: ?>
                                    <span style="background: #fee2e2; color: #dc2626; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="?action=edit&id=<?php echo $t['id']; ?>" style="background: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">Edit</a>
                                    <a href="?action=delete&id=<?php echo $t['id']; ?>" onclick="return confirm('Are you sure you want to delete this trainer?')" style="background: #f1f5f9; color: #64748b; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">Delete</a>
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
<div class="page-header" style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(249, 115, 22, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;"><?php echo $action === 'add' ? '➕' : '✏️'; ?></span>
            <?php echo $action === 'add' ? 'Add New Trainer' : 'Edit Trainer'; ?>
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Fill in the trainer details below</p>
    </div>
    <a href="trainers.php" style="background: white; color: #f97316; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
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

<!-- Personal Information -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #f97316, #dc2626); color: white; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">👤 Personal Information</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">First Name (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="first_name" value="<?php echo e($trainer['first_name'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">First Name (Kurdish)</label>
                <input type="text" name="first_name_ku" dir="rtl" value="<?php echo e($trainer['first_name_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Last Name (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="last_name" value="<?php echo e($trainer['last_name'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Last Name (Kurdish)</label>
                <input type="text" name="last_name_ku" dir="rtl" value="<?php echo e($trainer['last_name_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Specialization (English)</label>
                <input type="text" name="specialization" value="<?php echo e($trainer['specialization'] ?? ''); ?>" placeholder="e.g., Personal Training, Yoga, CrossFit" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Specialization (Kurdish)</label>
                <input type="text" name="specialization_ku" dir="rtl" value="<?php echo e($trainer['specialization_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="max-width: 250px;">
            <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Experience Years</label>
            <input type="number" name="experience_years" value="<?php echo e($trainer['experience_years'] ?? 0); ?>" min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
    </div>
</div>

<!-- Bio -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📝 Bio</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Bio (English)</label>
                <textarea name="bio" rows="5" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($trainer['bio'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Bio (Kurdish)</label>
                <textarea name="bio_ku" rows="5" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($trainer['bio_ku'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Contact & Media -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #10b981, #14b8a6); color: white; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📞 Contact & Media</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Trainer Photo</label>
                <div id="imagePreview" style="width: 100%; height: 200px; border: 2px dashed #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; overflow: hidden; background: #f8fafc;">
                    <?php if (!empty($trainer['avatar'])): ?>
                        <img src="<?php echo uploadUrl($trainer['avatar']); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="text-align: center; color: #94a3b8;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">📷</div>
                            <p style="margin: 0; font-size: 0.9rem;">Upload trainer photo</p>
                        </div>
                    <?php endif; ?>
                </div>
                <input type="file" name="image" accept="image/*" onchange="previewImage(this)" style="width: 100%; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem;">
            </div>
            <div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Phone</label>
                    <input type="tel" name="phone" value="<?php echo e($trainer['phone'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Email</label>
                    <input type="email" name="email" value="<?php echo e($trainer['email'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Facebook URL</label>
                <input type="url" name="social_facebook" value="<?php echo e($trainer['social_facebook'] ?? ''); ?>" placeholder="https://facebook.com/..." style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Instagram URL</label>
                <input type="url" name="social_instagram" value="<?php echo e($trainer['social_instagram'] ?? ''); ?>" placeholder="https://instagram.com/..." style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="max-width: 500px;">
            <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">YouTube URL</label>
            <input type="url" name="social_youtube" value="<?php echo e($trainer['social_youtube'] ?? ''); ?>" placeholder="https://youtube.com/..." style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
    </div>
</div>

<!-- Options -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #ec4899, #a855f7); color: white; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">⚙️ Options</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="margin-bottom: 1.5rem; max-width: 250px;">
            <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Sort Order</label>
            <input type="number" name="sort_order" value="<?php echo e($trainer['sort_order'] ?? 0); ?>" min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#ec4899'" onblur="this.style.borderColor='#e2e8f0'">
            <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Lower numbers appear first</p>
        </div>

        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; user-select: none;">
            <input type="checkbox" name="is_active" value="1" <?php echo ($trainer['is_active'] ?? 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #ec4899;">
            <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">Active (visible on website)</span>
        </label>
    </div>
</div>

<!-- Form Actions -->
<div style="display: flex; gap: 1rem; justify-content: flex-start;">
    <button type="submit" style="background: linear-gradient(135deg, #f97316, #dc2626); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(249, 115, 22, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(249, 115, 22, 0.3)'">
        <?php echo $action === 'add' ? '✓ Add Trainer' : '✓ Update Trainer'; ?>
    </button>
    <a href="trainers.php" style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
        Cancel
    </a>
</div>
</form>
<?php endif; ?>
</div>

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
