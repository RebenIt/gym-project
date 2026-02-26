<?php
/**
 * Admin - Tips & News Management
 * Complete CRUD functionality for fitness tips
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
            'content' => sanitize($_POST['content'] ?? ''),
            'content_ku' => sanitize($_POST['content_ku'] ?? ''),
            'excerpt' => sanitize($_POST['excerpt'] ?? ''),
            'excerpt_ku' => sanitize($_POST['excerpt_ku'] ?? ''),
            'category' => sanitize($_POST['category'] ?? 'other'),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
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
                $sql = "INSERT INTO tips (title, title_ku, content, content_ku, excerpt, excerpt_ku, image, category,
                        author_id, is_featured, is_published, published_at, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $publishedAt = $data['is_published'] ? date('Y-m-d H:i:s') : null;
                query($sql, [
                    $data['title'], $data['title_ku'], $data['content'], $data['content_ku'],
                    $data['excerpt'], $data['excerpt_ku'], $data['image'] ?? null, $data['category'],
                    $admin['id'], $data['is_featured'], $data['is_published'], $publishedAt
                ]);
                setFlash('success', 'Tip added successfully!');
                redirect('tips.php');

            } elseif ($_POST['form_action'] === 'edit' && $id) {
                $sql = "UPDATE tips SET title=?, title_ku=?, content=?, content_ku=?, excerpt=?, excerpt_ku=?,
                        category=?, is_featured=?, is_published=?";
                $params = [
                    $data['title'], $data['title_ku'], $data['content'], $data['content_ku'],
                    $data['excerpt'], $data['excerpt_ku'], $data['category'], $data['is_featured'], $data['is_published']
                ];

                // Update published_at if status changed
                $currentTip = fetchOne("SELECT is_published FROM tips WHERE id = ?", [$id]);
                if ($data['is_published'] && !$currentTip['is_published']) {
                    $sql .= ", published_at = NOW()";
                }

                if (isset($data['image'])) {
                    $sql .= ", image=?";
                    $params[] = $data['image'];
                }

                $sql .= " WHERE id=?";
                $params[] = $id;

                query($sql, $params);
                setFlash('success', 'Tip updated successfully!');
                redirect('tips.php');
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM tips WHERE id = ?", [$id]);
    setFlash('success', 'Tip deleted successfully!');
    redirect('tips.php');
}

// Handle toggle published
if ($action === 'toggle_publish' && $id) {
    $tip = fetchOne("SELECT is_published FROM tips WHERE id = ?", [$id]);
    $newStatus = $tip['is_published'] ? 0 : 1;
    $publishedAt = $newStatus ? date('Y-m-d H:i:s') : null;
    query("UPDATE tips SET is_published = ?, published_at = ? WHERE id = ?", [$newStatus, $publishedAt, $id]);
    setFlash('success', 'Tip ' . ($newStatus ? 'published' : 'unpublished') . ' successfully!');
    redirect('tips.php');
}

// Get tip for editing
$tip = null;
if ($action === 'edit' && $id) {
    $tip = fetchOne("SELECT * FROM tips WHERE id = ?", [$id]);
    if (!$tip) {
        redirect('tips.php');
    }
}

// Get all tips
$tips = fetchAll("SELECT t.*, a.full_name as author_name
                  FROM tips t
                  LEFT JOIN admins a ON t.author_id = a.id
                  ORDER BY t.created_at DESC");

// Calculate stats
$totalTips = count($tips);
$publishedTips = count(array_filter($tips, fn($t) => $t['is_published']));
$featuredTips = count(array_filter($tips, fn($t) => $t['is_featured']));
$totalViews = array_sum(array_column($tips, 'view_count'));

$pageTitle = 'Manage Tips & News';
?>
<?php include 'includes/header.php'; ?>
<div class="admin-content">
<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #f59e0b 0%, #dc2626 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">💡</span>
            Fitness Tips & News
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage fitness tips and articles</p>
    </div>
    <a href="?action=add" class="admin-btn" style="background: white; color: #f59e0b; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
        <span class="icon" style="font-size: 1.2rem;">+</span>
        Add New Tip
    </a>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Tips</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $totalTips; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Published</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $publishedTips; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Featured</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $featuredTips; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Views</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo number_format($totalViews); ?></div>
    </div>
</div>

<!-- Tips Table -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Image</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Title</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Category</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Author</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Views</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tips)): ?>
                    <tr>
                        <td colspan="7" style="padding: 3rem; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                <span style="font-size: 4rem; opacity: 0.3;">💡</span>
                                <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No tips found</p>
                                <a href="?action=add" style="background: linear-gradient(135deg, #f59e0b, #dc2626); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <span>+</span> Add your first tip
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tips as $t): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <?php if ($t['image']): ?>
                                    <img src="<?php echo uploadUrl($t['image']); ?>" alt="" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <?php else: ?>
                                    <div style="width: 60px; height: 60px; border-radius: 8px; background: linear-gradient(135deg, #f59e0b, #dc2626); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">💡</div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem; margin-bottom: 0.25rem;"><?php echo e($t['title']); ?></div>
                                    <?php if ($t['title_ku']): ?>
                                        <div style="color: #64748b; font-size: 0.85rem; direction: rtl;"><?php echo e($t['title_ku']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($t['is_featured']): ?>
                                    <span style="background: #fef3c7; color: #f59e0b; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; margin-top: 0.25rem; display: inline-block;">⭐ Featured</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="background: #dbeafe; color: #2563eb; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;"><?php echo ucfirst($t['category']); ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="color: #64748b; font-size: 0.9rem;"><?php echo e($t['author_name'] ?? 'N/A'); ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="color: #475569; font-size: 0.9rem; font-weight: 600;"><?php echo number_format($t['view_count']); ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($t['is_published']): ?>
                                    <span style="background: #dcfce7; color: #16a34a; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Published</span>
                                <?php else: ?>
                                    <span style="background: #fee2e2; color: #dc2626; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="?action=edit&id=<?php echo $t['id']; ?>" style="background: #f59e0b; color: white; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s; white-space: nowrap;" onmouseover="this.style.background='#f97316'" onmouseout="this.style.background='#f59e0b'">Edit</a>
                                    <a href="?action=toggle_publish&id=<?php echo $t['id']; ?>" style="background: #e0f2fe; color: #0284c7; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s; white-space: nowrap;" onmouseover="this.style.background='#bae6fd'" onmouseout="this.style.background='#e0f2fe'"><?php echo $t['is_published'] ? 'Unpublish' : 'Publish'; ?></a>
                                    <a href="?action=delete&id=<?php echo $t['id']; ?>" onclick="return confirm('Delete this tip?')" style="background: #f1f5f9; color: #64748b; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">Delete</a>
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
<div class="page-header" style="background: linear-gradient(135deg, #f59e0b 0%, #dc2626 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;"><?php echo $action === 'add' ? '➕' : '✏️'; ?></span>
            <?php echo $action === 'add' ? 'Add New Tip' : 'Edit Tip'; ?>
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Fill in the tip details in both languages</p>
    </div>
    <a href="tips.php" style="background: white; color: #f59e0b; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
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

<!-- Content -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #f59e0b, #dc2626); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📝 Content</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Title (English) <span style="color: #dc2626;">*</span></label>
                <input type="text" name="title" value="<?php echo e($tip['title'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Title (Kurdish)</label>
                <input type="text" name="title_ku" dir="rtl" value="<?php echo e($tip['title_ku'] ?? ''); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Excerpt (English)</label>
                <textarea name="excerpt" rows="2" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($tip['excerpt'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Excerpt (Kurdish)</label>
                <textarea name="excerpt_ku" rows="2" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($tip['excerpt_ku'] ?? ''); ?></textarea>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Content (English) <span style="color: #dc2626;">*</span></label>
                <textarea name="content" rows="12" required style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($tip['content'] ?? ''); ?></textarea>
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Content (Kurdish)</label>
                <textarea name="content_ku" rows="12" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($tip['content_ku'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Media & Categories -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #10b981, #14b8a6); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">📷 Media & Categories</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Featured Image</label>
                <div id="imagePreview" style="width: 100%; height: 180px; border: 2px dashed #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; overflow: hidden; background: #f8fafc;">
                    <?php if (!empty($tip['image'])): ?>
                        <img src="<?php echo uploadUrl($tip['image']); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="text-align: center; color: #94a3b8;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.25rem;">📷</div>
                            <p style="margin: 0; font-size: 0.85rem;">Upload tip image</p>
                        </div>
                    <?php endif; ?>
                </div>
                <input type="file" name="image" accept="image/*" onchange="previewImage(this)" style="width: 100%; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem;">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem;">Category</label>
                <select name="category" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s; background: white; cursor: pointer;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="nutrition" <?php echo ($tip['category'] ?? '') === 'nutrition' ? 'selected' : ''; ?>>Nutrition</option>
                    <option value="exercise" <?php echo ($tip['category'] ?? '') === 'exercise' ? 'selected' : ''; ?>>Exercise</option>
                    <option value="lifestyle" <?php echo ($tip['category'] ?? '') === 'lifestyle' ? 'selected' : ''; ?>>Lifestyle</option>
                    <option value="news" <?php echo ($tip['category'] ?? '') === 'news' ? 'selected' : ''; ?>>News</option>
                    <option value="motivation" <?php echo ($tip['category'] ?? '') === 'motivation' ? 'selected' : ''; ?>>Motivation</option>
                    <option value="other" <?php echo ($tip['category'] ?? 'other') === 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Select the category that best describes this tip</p>
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
                <input type="checkbox" name="is_featured" value="1" <?php echo ($tip['is_featured'] ?? 0) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #f59e0b;">
                <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">⭐ Featured (show on homepage)</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; user-select: none;">
                <input type="checkbox" name="is_published" value="1" <?php echo ($tip['is_published'] ?? 0) ? 'checked' : ''; ?> style="width: 20px; height: 20px; cursor: pointer; accent-color: #10b981;">
                <span style="color: #1e293b; font-weight: 500; font-size: 0.95rem;">Published (visible to users)</span>
            </label>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div style="display: flex; gap: 1rem;">
    <button type="submit" style="background: linear-gradient(135deg, #f59e0b, #dc2626); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(245, 158, 11, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.3)'">
        <?php echo $action === 'add' ? '✓ Add Tip' : '✓ Update Tip'; ?>
    </button>
    <a href="tips.php" style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
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
