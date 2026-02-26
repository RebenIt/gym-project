<?php
/**
 * Admin - Custom Pages Management
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
            'slug' => sanitize($_POST['slug'] ?? ''),
            'content' => sanitize($_POST['content'] ?? ''),
            'content_ku' => sanitize($_POST['content_ku'] ?? ''),
            'meta_description' => sanitize($_POST['meta_description'] ?? ''),
            'meta_description_ku' => sanitize($_POST['meta_description_ku'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        // Handle image upload
        if (!empty($_FILES['featured_image']['name'])) {
            $uploadResult = uploadFile($_FILES['featured_image']);
            if ($uploadResult['success']) {
                $data['featured_image'] = $uploadResult['filename'];
            } else {
                $error = $uploadResult['message'];
            }
        }

        if (empty($error)) {
            if ($_POST['form_action'] === 'add') {
                // Insert new page
                $sql = "INSERT INTO pages (title, title_ku, slug, content, content_ku,
                        meta_description, meta_description_ku, featured_image, is_active)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                query($sql, [
                    $data['title'], $data['title_ku'], $data['slug'],
                    $data['content'], $data['content_ku'],
                    $data['meta_description'], $data['meta_description_ku'],
                    $data['featured_image'] ?? null, $data['is_active']
                ]);
                setFlash('success', 'Page added successfully!');
                redirect('pages.php');

            } elseif ($_POST['form_action'] === 'edit' && $id) {
                // Update existing page
                $sql = "UPDATE pages SET title=?, title_ku=?, slug=?, content=?, content_ku=?,
                        meta_description=?, meta_description_ku=?, is_active=?";
                $params = [
                    $data['title'], $data['title_ku'], $data['slug'],
                    $data['content'], $data['content_ku'],
                    $data['meta_description'], $data['meta_description_ku'],
                    $data['is_active']
                ];

                if (isset($data['featured_image'])) {
                    $sql .= ", featured_image=?";
                    $params[] = $data['featured_image'];
                }

                $sql .= " WHERE id=?";
                $params[] = $id;

                query($sql, $params);
                setFlash('success', 'Page updated successfully!');
                redirect('pages.php');
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM pages WHERE id = ?", [$id]);
    setFlash('success', 'Page deleted successfully!');
    redirect('pages.php');
}

// Get page for editing
$page = null;
if ($action === 'edit' && $id) {
    $page = fetchOne("SELECT * FROM pages WHERE id = ?", [$id]);
    if (!$page) {
        redirect('pages.php');
    }
}

// Get all pages for listing
$pages = fetchAll("SELECT * FROM pages ORDER BY created_at DESC");

// Calculate stats
$totalPages = count($pages);
$activePages = count(array_filter($pages, fn($p) => $p['is_active']));
$inactivePages = $totalPages - $activePages;
$pagesWithImages = count(array_filter($pages, fn($p) => !empty($p['featured_image'])));

$pageTitle = 'Manage Pages';
include 'includes/header.php';
?>

<div class="admin-content">
<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">📄</span>
            Custom Pages
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage custom website pages</p>
    </div>
    <a href="?action=add" style="background: white; color: #6366f1; padding: 0.875rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(0,0,0,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'">
        <span style="font-size: 1.2rem;">+</span>
        Add New Page
    </a>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 2.5rem;">📄</div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo $totalPages; ?></div>
                <div style="opacity: 0.95; font-size: 0.95rem;">Total Pages</div>
            </div>
        </div>
    </div>
    <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 2.5rem;">✅</div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo $activePages; ?></div>
                <div style="opacity: 0.95; font-size: 0.95rem;">Active Pages</div>
            </div>
        </div>
    </div>
    <div style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(239, 68, 68, 0.3);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 2.5rem;">⏸️</div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo $inactivePages; ?></div>
                <div style="opacity: 0.95; font-size: 0.95rem;">Inactive Pages</div>
            </div>
        </div>
    </div>
    <div style="background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(14, 165, 233, 0.3);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 2.5rem;">🖼️</div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo $pagesWithImages; ?></div>
                <div style="opacity: 0.95; font-size: 0.95rem;">With Images</div>
            </div>
        </div>
    </div>
</div>

<!-- Pages Table -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: linear-gradient(135deg, #f5f3ff, #ede9fe); border-bottom: 2px solid #6366f1;">
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Image</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Title</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Slug</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Status</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Created</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600; color: #1e293b; font-size: 0.9rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pages)): ?>
                <tr>
                    <td colspan="6" style="padding: 3rem; text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                            <span style="font-size: 4rem; opacity: 0.5;">📄</span>
                            <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No pages found</p>
                            <a href="?action=add" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 0.5rem;">Add your first page</a>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($pages as $p): ?>
                <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='white'">
                    <td style="padding: 1rem;">
                        <?php if (!empty($p['featured_image'])): ?>
                            <img src="<?php echo uploadUrl($p['featured_image']); ?>" alt="" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <?php else: ?>
                            <div style="width: 60px; height: 60px; border-radius: 8px; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">📄</div>
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
                        <code style="background: #f1f5f9; color: #6366f1; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-family: 'Courier New', monospace;"><?php echo e($p['slug']); ?></code>
                    </td>
                    <td style="padding: 1rem;">
                        <?php if ($p['is_active']): ?>
                            <span style="background: #d1fae5; color: #059669; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">Active</span>
                        <?php else: ?>
                            <span style="background: #fee2e2; color: #dc2626; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 1rem;">
                        <span style="color: #475569; font-size: 0.9rem;"><?php echo date('M d, Y', strtotime($p['created_at'])); ?></span>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                            <a href="?action=edit&id=<?php echo $p['id']; ?>" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(99, 102, 241, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">Edit</a>
                            <a href="?action=delete&id=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure you want to delete this page?')" style="background: #f1f5f9; color: #475569; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'">Delete</a>
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
<div class="page-header" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;"><?php echo $action === 'add' ? '➕' : '✏️'; ?></span>
            <?php echo $action === 'add' ? 'Add New Page' : 'Edit Page'; ?>
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Fill in the page details in both languages</p>
    </div>
    <a href="pages.php" style="background: white; color: #6366f1; padding: 0.875rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
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
    <div style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Basic Information</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Title (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="title" value="<?php echo e($page['title'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Title (Kurdish) ناونیشان</label>
                <input type="text" name="title_ku" dir="rtl" value="<?php echo e($page['title_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Page Slug (URL) <span style="color: #dc2626;">*</span></label>
            <input type="text" name="slug" value="<?php echo e($page['slug'] ?? ''); ?>" required placeholder="e.g., about-us, privacy-policy" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
            <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">Lowercase letters, numbers, and hyphens only. This will be the page URL.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Meta Description (English)</label>
                <textarea name="meta_description" rows="2" maxlength="160" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($page['meta_description'] ?? ''); ?></textarea>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">For SEO (max 160 characters)</p>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Meta Description (Kurdish)</label>
                <textarea name="meta_description_ku" rows="2" dir="rtl" maxlength="160" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($page['meta_description_ku'] ?? ''); ?></textarea>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">For SEO (max 160 characters)</p>
            </div>
        </div>
    </div>
</div>

<!-- Content -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Page Content</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Content (English) <span style="color: #dc2626;">*</span></label>
                <textarea name="content" rows="15" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($page['content'] ?? ''); ?></textarea>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">HTML allowed. Use proper formatting.</p>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Content (Kurdish) ناوەڕۆک</label>
                <textarea name="content_ku" rows="15" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($page['content_ku'] ?? ''); ?></textarea>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">HTML allowed. Use proper formatting.</p>
            </div>
        </div>
    </div>
</div>

<!-- Media -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Featured Image</h3>
    </div>
    <div style="padding: 1.5rem;">
        <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Featured Image (Optional)</label>
        <div id="imagePreview" style="width: 300px; height: 180px; border: 3px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; overflow: hidden; background: #f8fafc;">
            <?php if (!empty($page['featured_image'])): ?>
                <img src="<?php echo uploadUrl($page['featured_image']); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <div style="text-align: center; color: #94a3b8;">
                    <span style="font-size: 3rem; display: block; margin-bottom: 0.5rem;">📷</span>
                    <p style="margin: 0; font-size: 0.9rem;">Upload featured image</p>
                </div>
            <?php endif; ?>
        </div>
        <input type="file" name="featured_image" accept="image/*" onchange="previewImage(this)" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; margin-bottom: 0.5rem;">
        <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Recommended size: 1200x630px for optimal display</p>
    </div>
</div>

<!-- Options -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #ec4899, #db2777); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Options</h3>
    </div>
    <div style="padding: 1.5rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; user-select: none;">
            <input type="checkbox" name="is_active" value="1" <?php echo ($page['is_active'] ?? 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer;">
            <span style="color: #1e293b; font-weight: 500;">Active (visible on website)</span>
        </label>
    </div>
</div>

<div style="display: flex; gap: 1rem;">
    <button type="submit" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(99, 102, 241, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(99, 102, 241, 0.3)'">
        <?php echo $action === 'add' ? '✓ Add Page' : '✓ Update Page'; ?>
    </button>
    <a href="pages.php" style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.3s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Cancel</a>
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
