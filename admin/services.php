<?php
/**
 * Admin - Services Management
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
            'short_description' => sanitize($_POST['short_description'] ?? ''),
            'short_description_ku' => sanitize($_POST['short_description_ku'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'description_ku' => sanitize($_POST['description_ku'] ?? ''),
            'icon' => sanitize($_POST['icon'] ?? ''),
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
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
                // Insert new service
                query("INSERT INTO services (name, name_ku, short_description, short_description_ku, description, description_ku, icon, image, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$data['name'], $data['name_ku'], $data['short_description'], $data['short_description_ku'], $data['description'], $data['description_ku'], $data['icon'], $data['image'] ?? null, $data['sort_order'], $data['is_active']]);
                setFlash('success', 'Service added successfully!');
                redirect('services.php');

            } elseif ($_POST['form_action'] === 'edit' && $id) {
                // Update existing service
                $sql = "UPDATE services SET name=?, name_ku=?, short_description=?, short_description_ku=?, description=?, description_ku=?, icon=?, sort_order=?, is_active=?";
                $params = [$data['name'], $data['name_ku'], $data['short_description'], $data['short_description_ku'], $data['description'], $data['description_ku'], $data['icon'], $data['sort_order'], $data['is_active']];

                if (isset($data['image'])) {
                    $sql .= ", image=?";
                    $params[] = $data['image'];
                }

                $sql .= " WHERE id=?";
                $params[] = $id;

                query($sql, $params);
                setFlash('success', 'Service updated successfully!');
                redirect('services.php');
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM services WHERE id = ?", [$id]);
    setFlash('success', 'Service deleted successfully!');
    redirect('services.php');
}

// Get service for editing
$service = null;
if ($action === 'edit' && $id) {
    $service = fetchOne("SELECT * FROM services WHERE id = ?", [$id]);
    if (!$service) {
        redirect('services.php');
    }
}

// Get all services for listing
$services = fetchAll("SELECT * FROM services ORDER BY sort_order, created_at DESC");

// Calculate stats
$totalServices = count($services);
$activeServices = count(array_filter($services, fn($s) => $s['is_active']));

$pageTitle = 'Manage Services';
?>

<?php include 'includes/header.php'; ?>

<div class="admin-content">
<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #06b6d4 0%, #a855f7 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(6, 182, 212, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">🏋️</span>
            Services Management
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage gym services and offerings</p>
    </div>
    <a href="?action=add" class="admin-btn" style="background: white; color: #06b6d4; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
        <span class="icon" style="font-size: 1.2rem;">+</span>
        Add New Service
    </a>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Services</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $totalServices; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Active Services</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $activeServices; ?></div>
    </div>
</div>

<!-- Services Table -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Icon</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Name</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Description</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Order</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="6" style="padding: 3rem; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                <span style="font-size: 4rem; opacity: 0.3;">🏋️</span>
                                <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No services found</p>
                                <a href="?action=add" style="background: linear-gradient(135deg, #06b6d4, #a855f7); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <span>+</span> Add your first service
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($services as $s): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <div style="font-size: 2.5rem;"><?php echo e($s['icon'] ?? '🏋️'); ?></div>
                            </td>
                            <td style="padding: 1rem;">
                                <div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem; margin-bottom: 0.25rem;"><?php echo e($s['name']); ?></div>
                                    <?php if ($s['name_ku']): ?>
                                        <div style="color: #64748b; font-size: 0.85rem; direction: rtl;"><?php echo e($s['name_ku']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #64748b; font-size: 0.9rem;">
                                    <?php echo e($s['short_description'] ?? substr($s['description'] ?? '', 0, 100)); ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="background: #e0f2fe; color: #0284c7; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;"><?php echo $s['sort_order']; ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($s['is_active']): ?>
                                    <span style="background: #dcfce7; color: #16a34a; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Active</span>
                                <?php else: ?>
                                    <span style="background: #fee2e2; color: #dc2626; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="?action=edit&id=<?php echo $s['id']; ?>" style="background: #06b6d4; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#0891b2'" onmouseout="this.style.background='#06b6d4'">Edit</a>
                                    <a href="?action=delete&id=<?php echo $s['id']; ?>" onclick="return confirm('Delete this service?')" style="background: #f1f5f9; color: #64748b; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">Delete</a>
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
<div class="page-header" style="background: linear-gradient(135deg, #06b6d4 0%, #a855f7 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(6, 182, 212, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;"><?php echo $action === 'add' ? '➕' : '✏️'; ?></span>
            <?php echo $action === 'add' ? 'Add New Service' : 'Edit Service'; ?>
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Fill in the service details in both languages</p>
    </div>
    <a href="services.php" style="background: white; color: #06b6d4; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
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

<!-- Service Information -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #06b6d4, #a855f7); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📋 Service Information</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Name (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="name" value="<?php echo e($service['name'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#06b6d4'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Name (Kurdish)</label>
                <input type="text" name="name_ku" dir="rtl" value="<?php echo e($service['name_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#06b6d4'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Short Description (English)</label>
                <input type="text" name="short_description" value="<?php echo e($service['short_description'] ?? ''); ?>" maxlength="255" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#06b6d4'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Short Description (Kurdish)</label>
                <input type="text" name="short_description_ku" dir="rtl" value="<?php echo e($service['short_description_ku'] ?? ''); ?>" maxlength="255" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#06b6d4'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Full Description (English)</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#06b6d4'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($service['description'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Full Description (Kurdish)</label>
                <textarea name="description_ku" rows="4" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#06b6d4'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($service['description_ku'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Icon & Image -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #10b981, #14b8a6); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">🎨 Icon & Image</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Icon Emoji</label>
                <input type="text" name="icon" value="<?php echo e($service['icon'] ?? ''); ?>" placeholder="🏋️" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s; text-align: center; font-size: 2rem;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Emoji for this service</p>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Service Image</label>
                <div id="imagePreview" style="width: 100%; height: 150px; border: 2px dashed #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; overflow: hidden; background: #f8fafc;">
                    <?php if (!empty($service['image'])): ?>
                        <img src="<?php echo uploadUrl($service['image']); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="text-align: center; color: #94a3b8;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.25rem;">📷</div>
                            <p style="margin: 0; font-size: 0.85rem;">Upload service image</p>
                        </div>
                    <?php endif; ?>
                </div>
                <input type="file" name="image" accept="image/*" onchange="previewImage(this)" style="width: 100%; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem;">
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
        <div style="display: flex; gap: 2rem; align-items: center;">
            <div style="flex: 0 0 200px;">
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Sort Order</label>
                <input type="number" name="sort_order" value="<?php echo e($service['sort_order'] ?? 0); ?>" min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#ec4899'" onblur="this.style.borderColor='#e2e8f0'">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Lower numbers appear first</p>
            </div>
            <div>
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; user-select: none;">
                    <input type="checkbox" name="is_active" value="1" <?php echo ($service['is_active'] ?? 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #ec4899;">
                    <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">Active (visible on website)</span>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div style="display: flex; gap: 1rem;">
    <button type="submit" style="background: linear-gradient(135deg, #06b6d4, #a855f7); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(6, 182, 212, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.3)'">
        <?php echo $action === 'add' ? '✓ Add Service' : '✓ Update Service'; ?>
    </button>
    <a href="services.php" style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
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
