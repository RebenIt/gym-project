<?php
/**
 * Admin - Certificates Management
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
            'issuing_organization' => sanitize($_POST['issuing_organization'] ?? ''),
            'issuing_organization_ku' => sanitize($_POST['issuing_organization_ku'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'description_ku' => sanitize($_POST['description_ku'] ?? ''),
            'year_received' => intval($_POST['year_received'] ?? date('Y')),
            'certificate_type' => sanitize($_POST['certificate_type'] ?? 'certificate'),
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
                // Insert new certificate
                $sql = "INSERT INTO certificates (title, title_ku, issuing_organization, issuing_organization_ku,
                        description, description_ku, year_received, certificate_type, sort_order, is_active, image)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                query($sql, [
                    $data['title'], $data['title_ku'], $data['issuing_organization'], $data['issuing_organization_ku'],
                    $data['description'], $data['description_ku'], $data['year_received'],
                    $data['certificate_type'], $data['sort_order'], $data['is_active'],
                    $data['image'] ?? null
                ]);
                setFlash('success', 'Certificate added successfully!');
                redirect('certificates.php');

            } elseif ($_POST['form_action'] === 'edit' && $id) {
                // Update existing certificate
                $sql = "UPDATE certificates SET title=?, title_ku=?, issuing_organization=?, issuing_organization_ku=?,
                        description=?, description_ku=?, year_received=?, certificate_type=?, sort_order=?, is_active=?";
                $params = [
                    $data['title'], $data['title_ku'], $data['issuing_organization'], $data['issuing_organization_ku'],
                    $data['description'], $data['description_ku'], $data['year_received'],
                    $data['certificate_type'], $data['sort_order'], $data['is_active']
                ];

                if (isset($data['image'])) {
                    $sql .= ", image=?";
                    $params[] = $data['image'];
                }

                $sql .= " WHERE id=?";
                $params[] = $id;

                query($sql, $params);
                setFlash('success', 'Certificate updated successfully!');
                redirect('certificates.php');
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM certificates WHERE id = ?", [$id]);
    setFlash('success', 'Certificate deleted successfully!');
    redirect('certificates.php');
}

// Get certificate for editing
$certificate = null;
if ($action === 'edit' && $id) {
    $certificate = fetchOne("SELECT * FROM certificates WHERE id = ?", [$id]);
    if (!$certificate) {
        redirect('certificates.php');
    }
}

// Get all certificates for listing
$certificates = fetchAll("SELECT * FROM certificates ORDER BY sort_order ASC, year_received DESC, created_at DESC");

$pageTitle = 'Manage Certificates';
include 'includes/header.php';
?>

<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);">
<div>
<h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
<span class="icon" style="font-size: 2.5rem;">🏆</span>
Certificates & Awards
</h1>
<p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage all certificates and achievements</p>
</div>
<a href="?action=add" class="admin-btn" style="background: white; color: #6366f1; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s;">
<span class="icon" style="font-size: 1.2rem;">+</span>
Add New Certificate
</a>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
<div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
<div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Certificates</div>
<div style="font-size: 2rem; font-weight: 700;"><?php echo count($certificates); ?></div>
</div>
<div style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
<div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Active</div>
<div style="font-size: 2rem; font-weight: 700;"><?php echo count(array_filter($certificates, fn($c) => $c['is_active'])); ?></div>
</div>
<div style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
<div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Awards</div>
<div style="font-size: 2rem; font-weight: 700;"><?php echo count(array_filter($certificates, fn($c) => $c['certificate_type'] === 'award')); ?></div>
</div>
<div style="background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);">
<div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">This Year</div>
<div style="font-size: 2rem; font-weight: 700;"><?php echo count(array_filter($certificates, fn($c) => $c['year_received'] == date('Y'))); ?></div>
</div>
</div>

<div class="admin-card" style="border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; background: white;">
<div class="admin-table-wrapper">
<table class="admin-table-modern" style="width: 100%;">
<thead style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 2px solid #e2e8f0;">
<tr>
<th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Title</th>
<th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Organization</th>
<th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Year</th>
<th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Type</th>
<th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
<th style="padding: 1rem; text-align: right; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
</tr>
</thead>
<tbody style="background: white;">
<?php if (empty($certificates)): ?>
<tr>
<td colspan="6" class="empty-state">
<div class="empty-state-content">
<span class="empty-icon">🏆</span>
<p>No certificates found</p>
<a href="?action=add" class="admin-btn admin-btn-primary admin-btn-sm">Add your first certificate</a>
</div>
</td>
</tr>
<?php else: ?>
<?php foreach ($certificates as $cert): ?>
<tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
<td style="padding: 1rem;">
<div>
<div style="font-weight: 600; color: #1e293b; font-size: 0.95rem; margin-bottom: 0.25rem;"><?php echo e($cert['title']); ?></div>
<?php if ($cert['title_ku']): ?>
<div style="color: #64748b; font-size: 0.85rem; direction: rtl;"><?php echo e($cert['title_ku']); ?></div>
<?php endif; ?>
</div>
</td>
<td style="padding: 1rem;">
<div>
<div style="color: #475569; font-size: 0.875rem;"><?php echo e($cert['issuing_organization']); ?></div>
<?php if ($cert['issuing_organization_ku']): ?>
<div style="color: #94a3b8; font-size: 0.8rem; direction: rtl;"><?php echo e($cert['issuing_organization_ku']); ?></div>
<?php endif; ?>
</div>
</td>
<td style="padding: 1rem; color: #475569; font-size: 0.875rem;"><?php echo $cert['year_received']; ?></td>
<td style="padding: 1rem;">
<span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600; <?php
$typeColor = $cert['certificate_type'] === 'award' ? 'background: #fef3c7; color: #92400e;' :
              ($cert['certificate_type'] === 'achievement' ? 'background: #dbeafe; color: #1e40af;' :
              ($cert['certificate_type'] === 'recognition' ? 'background: #e0e7ff; color: #3730a3;' :
              'background: #d1fae5; color: #065f46;'));
echo $typeColor;
?>">
<?php echo ucfirst($cert['certificate_type']); ?>
</span>
</td>
<td style="padding: 1rem;">
<?php if ($cert['is_active']): ?>
<span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600; background: #d1fae5; color: #065f46;">✓ Active</span>
<?php else: ?>
<span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8125rem; font-weight: 600; background: #fee2e2; color: #991b1b;">✗ Inactive</span>
<?php endif; ?>
</td>
<td style="padding: 1rem; text-align: right;">
<div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
<a href="?action=edit&id=<?php echo $cert['id']; ?>" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; background: #6366f1; color: white; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 4px rgba(99,102,241,0.2);" onmouseover="this.style.background='#4f46e5'; this.style.boxShadow='0 4px 8px rgba(99,102,241,0.3)'" onmouseout="this.style.background='#6366f1'; this.style.boxShadow='0 2px 4px rgba(99,102,241,0.2)'">
✏️ Edit
</a>
<a href="?action=delete&id=<?php echo $cert['id']; ?>" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; background: #fee2e2; color: #dc2626; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" onclick="return confirm('Are you sure you want to delete this certificate?')">
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
            <?php echo $action === 'add' ? 'Add New Certificate' : 'Edit Certificate'; ?>
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Fill in the certificate details in both languages</p>
    </div>
    <a href="certificates.php" style="background: white; color: #6366f1; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
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
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Title (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="title" value="<?php echo e($certificate['title'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Title (Kurdish) ناونیشان</label>
                <input type="text" name="title_ku" dir="rtl" value="<?php echo e($certificate['title_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Issuing Organization (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="issuing_organization" value="<?php echo e($certificate['issuing_organization'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Issuing Organization (Kurdish)</label>
                <input type="text" name="issuing_organization_ku" dir="rtl" value="<?php echo e($certificate['issuing_organization_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Description (English)</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($certificate['description'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Description (Kurdish)</label>
                <textarea name="description_ku" rows="4" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($certificate['description_ku'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Certificate Details -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #f59e0b, #ef4444); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">🏆 Certificate Details</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Year Received <span style="color: #dc2626;">*</span></label>
                <input type="number" name="year_received" value="<?php echo e($certificate['year_received'] ?? date('Y')); ?>" min="1900" max="<?php echo date('Y'); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Year of issuance</p>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Certificate Type</label>
                <select name="certificate_type" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s; background: white; cursor: pointer;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="certificate" <?php echo ($certificate['certificate_type'] ?? '') === 'certificate' ? 'selected' : ''; ?>>Certificate</option>
                    <option value="award" <?php echo ($certificate['certificate_type'] ?? '') === 'award' ? 'selected' : ''; ?>>Award</option>
                    <option value="achievement" <?php echo ($certificate['certificate_type'] ?? '') === 'achievement' ? 'selected' : ''; ?>>Achievement</option>
                    <option value="recognition" <?php echo ($certificate['certificate_type'] ?? '') === 'recognition' ? 'selected' : ''; ?>>Recognition</option>
                </select>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Type of certificate</p>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Sort Order</label>
                <input type="number" name="sort_order" value="<?php echo e($certificate['sort_order'] ?? 0); ?>" min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Display order</p>
            </div>
        </div>
    </div>
</div>

<!-- Certificate Image -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #10b981, #14b8a6); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📷 Certificate Image</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div>
            <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Upload Certificate Image</label>
            <div id="imagePreview" style="margin-bottom: 1rem; border-radius: 8px; overflow: hidden; border: 2px dashed #e2e8f0;">
                <?php if (!empty($certificate['image'])): ?>
                    <img src="<?php echo uploadUrl($certificate['image']); ?>" alt="" style="width: 100%; max-height: 300px; object-fit: contain; background: #f8fafc;">
                <?php else: ?>
                    <div style="padding: 3rem; text-align: center; background: #f8fafc;">
                        <span style="font-size: 4rem; opacity: 0.3;">🏆</span>
                        <p style="color: #64748b; font-size: 1rem; margin: 0.5rem 0 0 0;">Upload certificate image or scan</p>
                    </div>
                <?php endif; ?>
            </div>
            <input type="file" name="image" accept="image/*" onchange="previewImage(this)" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s; background: white; cursor: pointer;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
            <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Upload a clear image or scan of the certificate</p>
        </div>
    </div>
</div>

<!-- Options -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #ec4899, #a855f7); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">⚙️ Options</h3>
    </div>
    <div style="padding: 1.5rem;">
        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; user-select: none;">
            <input type="checkbox" name="is_active" value="1" <?php echo ($certificate['is_active'] ?? 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #6366f1;">
            <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">Active (visible on website)</span>
        </label>
    </div>
</div>

<!-- Form Actions -->
<div style="display: flex; gap: 1rem;">
    <button type="submit" style="background: linear-gradient(135deg, #6366f1, #ec4899); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(99, 102, 241, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(99, 102, 241, 0.3)'">
        <?php echo $action === 'add' ? '✓ Add Certificate' : '✓ Update Certificate'; ?>
    </button>
    <a href="certificates.php" style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
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
