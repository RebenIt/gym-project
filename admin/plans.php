<?php
/**
 * Admin - Membership Plans Management
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
        // Convert features text to JSON array (one feature per line)
        $featuresText = sanitize($_POST['features'] ?? '');
        $featuresArray = array_filter(array_map('trim', explode("\n", $featuresText)));
        $featuresJson = !empty($featuresArray) ? json_encode(array_values($featuresArray)) : json_encode([]);

        $featuresKuText = sanitize($_POST['features_ku'] ?? '');
        $featuresKuArray = array_filter(array_map('trim', explode("\n", $featuresKuText)));
        $featuresKuJson = !empty($featuresKuArray) ? json_encode(array_values($featuresKuArray)) : json_encode([]);

        $data = [
            'name' => sanitize($_POST['name'] ?? ''),
            'name_ku' => sanitize($_POST['name_ku'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'description_ku' => sanitize($_POST['description_ku'] ?? ''),
            'price' => floatval($_POST['price'] ?? 0),
            'duration_days' => intval($_POST['duration_days'] ?? 30),
            'features' => $featuresJson,
            'features_ku' => $featuresKuJson,
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'is_popular' => isset($_POST['is_popular']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (empty($error)) {
            if ($_POST['form_action'] === 'add') {
                // Insert new plan
                $sql = "INSERT INTO plans (name, name_ku, description, description_ku, price, duration_days,
                        features, features_ku, sort_order, is_popular, is_active)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                query($sql, [
                    $data['name'], $data['name_ku'], $data['description'], $data['description_ku'],
                    $data['price'], $data['duration_days'], $data['features'], $data['features_ku'],
                    $data['sort_order'], $data['is_popular'], $data['is_active']
                ]);
                setFlash('success', 'Plan added successfully!');
                redirect('plans.php');

            } elseif ($_POST['form_action'] === 'edit' && $id) {
                // Update existing plan
                $sql = "UPDATE plans SET name=?, name_ku=?, description=?, description_ku=?,
                        price=?, duration_days=?, features=?, features_ku=?, sort_order=?,
                        is_popular=?, is_active=? WHERE id=?";
                query($sql, [
                    $data['name'], $data['name_ku'], $data['description'], $data['description_ku'],
                    $data['price'], $data['duration_days'], $data['features'], $data['features_ku'],
                    $data['sort_order'], $data['is_popular'], $data['is_active'], $id
                ]);
                setFlash('success', 'Plan updated successfully!');
                redirect('plans.php');
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM plans WHERE id = ?", [$id]);
    setFlash('success', 'Plan deleted successfully!');
    redirect('plans.php');
}

// Get plan for editing
$plan = null;
if ($action === 'edit' && $id) {
    $plan = fetchOne("SELECT * FROM plans WHERE id = ?", [$id]);
    if (!$plan) {
        redirect('plans.php');
    }
    // Convert JSON features back to text (one per line)
    if (!empty($plan['features'])) {
        $featuresArray = json_decode($plan['features'], true);
        $plan['features'] = is_array($featuresArray) ? implode("\n", $featuresArray) : '';
    }
    if (!empty($plan['features_ku'])) {
        $featuresKuArray = json_decode($plan['features_ku'], true);
        $plan['features_ku'] = is_array($featuresKuArray) ? implode("\n", $featuresKuArray) : '';
    }
}

// Get all plans for listing
$plans = fetchAll("SELECT * FROM plans ORDER BY sort_order, created_at DESC");

// Calculate stats
$totalPlans = count($plans);
$activePlans = count(array_filter($plans, fn($p) => $p['is_active']));
$popularPlans = count(array_filter($plans, fn($p) => $p['is_popular']));
$avgPrice = $totalPlans > 0 ? round(array_sum(array_column($plans, 'price')) / $totalPlans, 2) : 0;

$pageTitle = 'Manage Plans';
?>
<?php include 'includes/header.php'; ?>

<div class="admin-content">
<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">💳</span>
            Membership Plans
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage all membership plans and pricing</p>
    </div>
    <a href="?action=add" class="admin-btn" style="background: white; color: #8b5cf6; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
        <span class="icon" style="font-size: 1.2rem;">+</span>
        Add New Plan
    </a>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Plans</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $totalPlans; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Active Plans</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $activePlans; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Popular Plans</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $popularPlans; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Avg Price</div>
        <div style="font-size: 2rem; font-weight: 700;">$<?php echo number_format($avgPrice, 0); ?></div>
    </div>
</div>

<!-- Plans Table -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Plan Name</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Price</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Duration</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Order</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($plans)): ?>
                    <tr>
                        <td colspan="6" style="padding: 3rem; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                <span style="font-size: 4rem; opacity: 0.3;">💳</span>
                                <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No plans found</p>
                                <a href="?action=add" style="background: linear-gradient(135deg, #8b5cf6, #ec4899); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <span>+</span> Add your first plan
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($plans as $p): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem; margin-bottom: 0.25rem;"><?php echo e($p['name']); ?></div>
                                    <?php if ($p['name_ku']): ?>
                                        <div style="color: #64748b; font-size: 0.85rem; direction: rtl;"><?php echo e($p['name_ku']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="background: linear-gradient(135deg, #8b5cf6, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700; font-size: 1.1rem;">
                                    $<?php echo number_format($p['price'], 2); ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="color: #475569; font-size: 0.9rem;"><?php echo $p['duration_days']; ?> days</span>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="background: #e0e7ff; color: #6366f1; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;"><?php echo $p['sort_order']; ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <?php if ($p['is_active']): ?>
                                        <span style="background: #dcfce7; color: #16a34a; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Active</span>
                                    <?php else: ?>
                                        <span style="background: #fee2e2; color: #dc2626; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Inactive</span>
                                    <?php endif; ?>
                                    <?php if ($p['is_popular']): ?>
                                        <span style="background: #fef3c7; color: #f59e0b; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">⭐ Popular</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="?action=edit&id=<?php echo $p['id']; ?>" style="background: #8b5cf6; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#7c3aed'" onmouseout="this.style.background='#8b5cf6'">Edit</a>
                                    <a href="?action=delete&id=<?php echo $p['id']; ?>" onclick="return confirm('Delete this plan?')" style="background: #f1f5f9; color: #64748b; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">Delete</a>
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
<div class="page-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;"><?php echo $action === 'add' ? '➕' : '✏️'; ?></span>
            <?php echo $action === 'add' ? 'Add New Plan' : 'Edit Plan'; ?>
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Fill in the plan details in both languages</p>
    </div>
    <a href="plans.php" style="background: white; color: #8b5cf6; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
        ← Back to List
    </a>
</div>

<?php if ($error): ?>
    <div style="background: #fee2e2; border-left: 4px solid #dc2626; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <?php echo e($error); ?>
    </div>
<?php endif; ?>

<form method="POST">
<?php echo csrfField(); ?>
<input type="hidden" name="form_action" value="<?php echo $action; ?>">

<!-- Basic Information -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #8b5cf6, #ec4899); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📋 Basic Information</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Plan Name (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="name" value="<?php echo e($plan['name'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Plan Name (Kurdish)</label>
                <input type="text" name="name_ku" dir="rtl" value="<?php echo e($plan['name_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Description (English)</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($plan['description'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Description (Kurdish)</label>
                <textarea name="description_ku" rows="4" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($plan['description_ku'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Pricing & Duration -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #f59e0b, #f97316); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">💰 Pricing & Duration</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Price (USD) <span style="color: #dc2626;">*</span></label>
                <input type="number" step="0.01" name="price" value="<?php echo e($plan['price'] ?? ''); ?>" required min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">US Dollars</p>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Duration (Days) <span style="color: #dc2626;">*</span></label>
                <input type="number" name="duration_days" value="<?php echo e($plan['duration_days'] ?? 30); ?>" required min="1" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Plan validity</p>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Sort Order</label>
                <input type="number" name="sort_order" value="<?php echo e($plan['sort_order'] ?? 0); ?>" min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Display order</p>
            </div>
        </div>
    </div>
</div>

<!-- Plan Features -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #10b981, #14b8a6); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">✨ Plan Features</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Features (English)</label>
                <textarea name="features" rows="8" placeholder="Enter features, one per line" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($plan['features'] ?? ''); ?></textarea>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">One feature per line</p>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Features (Kurdish)</label>
                <textarea name="features_ku" rows="8" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($plan['features_ku'] ?? ''); ?></textarea>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">One feature per line (Kurdish)</p>
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
                <input type="checkbox" name="is_active" value="1" <?php echo ($plan['is_active'] ?? 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #8b5cf6;">
                <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">Active (visible on website)</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; user-select: none;">
                <input type="checkbox" name="is_popular" value="1" <?php echo ($plan['is_popular'] ?? 0) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #ec4899;">
                <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">⭐ Mark as Popular (highlighted)</span>
            </label>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div style="display: flex; gap: 1rem;">
    <button type="submit" style="background: linear-gradient(135deg, #8b5cf6, #ec4899); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(139, 92, 246, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.3)'">
        <?php echo $action === 'add' ? '✓ Add Plan' : '✓ Update Plan'; ?>
    </button>
    <a href="plans.php" style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
        Cancel
    </a>
</div>
</form>
<?php endif; ?>
</div>

<script src="../assets/js/admin-modern.js"></script>
<?php include 'includes/footer.php'; ?>
