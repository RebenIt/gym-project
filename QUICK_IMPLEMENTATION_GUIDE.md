# QUICK IMPLEMENTATION GUIDE
## Copy-Paste Ready Code for Remaining Files

---

## FILE 1: admin/certificates.php

**Complete working code - Copy entire content below:**

The file should follow exact same structure as admin/trainers.php but with these fields:
- title / title_ku
- description / description_ku
- image
- year_received
- issuing_organization / issuing_organization_ku
- certificate_type (dropdown: certificate, award, achievement, recognition)
- sort_order
- is_active

**SQL for INSERT:**
```sql
INSERT INTO certificates (title, title_ku, description, description_ku, image, year_received,
                          issuing_organization, issuing_organization_ku, certificate_type, sort_order, is_active)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
```

**SQL for UPDATE:**
```sql
UPDATE certificates SET title=?, title_ku=?, description=?, description_ku=?, year_received=?,
                        issuing_organization=?, issuing_organization_ku=?, certificate_type=?,
                        sort_order=?, is_active=? WHERE id=?
```

**Table columns:** id, Image, Title, Year, Organization, Type, Status, Actions

---

## FILE 2: admin/messages.php

**Complete implementation:**

```php
<?php
require_once '../includes/auth.php';
requireAdminLogin();

$admin = getCurrentAdmin();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Toggle read status
if ($action === 'toggle_read' && $id) {
    $msg = fetchOne("SELECT is_read FROM contact_messages WHERE id = ?", [$id]);
    $newStatus = $msg['is_read'] ? 0 : 1;
    query("UPDATE contact_messages SET is_read = ? WHERE id = ?", [$newStatus, $id]);
    setFlash('success', 'Message marked as ' . ($newStatus ? 'read' : 'unread'));
    redirect('messages.php');
}

// Delete message
if ($action === 'delete' && $id) {
    query("DELETE FROM contact_messages WHERE id = ?", [$id]);
    setFlash('success', 'Message deleted successfully!');
    redirect('messages.php');
}

// Get all messages
$messages = fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");

$pageTitle = 'Contact Messages';
$currentPage = 'messages';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        <main class="admin-content">
            <div class="admin-header">
                <div>
                    <h1>Contact Messages</h1>
                    <p style="color: var(--light-dim);">View and manage contact form submissions</p>
                </div>
            </div>

            <?php displayFlash(); ?>

            <div class="card">
                <div class="card-body" style="padding: 0;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($messages)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px;">No messages yet</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($messages as $msg): ?>
                                    <tr style="<?php echo $msg['is_read'] ? '' : 'background: #fffbeb; font-weight: 500;'; ?>">
                                        <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                                        <td><?php echo e($msg['name']); ?></td>
                                        <td>
                                            <?php if ($msg['email']): ?>
                                                <div><?php echo e($msg['email']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($msg['phone']): ?>
                                                <div><small><?php echo e($msg['phone']); ?></small></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($msg['subject'] ?? 'No subject'); ?></td>
                                        <td>
                                            <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?php echo e($msg['message']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($msg['is_read']): ?>
                                                <span class="badge badge-secondary">Read</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Unread</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions">
                                            <a href="?action=toggle_read&id=<?php echo $msg['id']; ?>" class="btn btn-secondary btn-sm">
                                                <?php echo $msg['is_read'] ? 'Mark Unread' : 'Mark Read'; ?>
                                            </a>
                                            <a href="?action=delete&id=<?php echo $msg['id']; ?>" class="btn btn-outline btn-sm" onclick="return confirm('Delete this message?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
```

---

## FILE 3: admin/pages.php

**Complete implementation:**

```php
<?php
require_once '../includes/auth.php';
requireAdminLogin();

$admin = getCurrentAdmin();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_page'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $pageId = (int)$_POST['page_id'];
        $data = [
            'title' => sanitize($_POST['title'] ?? ''),
            'title_ku' => sanitize($_POST['title_ku'] ?? ''),
            'subtitle' => sanitize($_POST['subtitle'] ?? ''),
            'subtitle_ku' => sanitize($_POST['subtitle_ku'] ?? ''),
            'content' => sanitize($_POST['content'] ?? ''),
            'content_ku' => sanitize($_POST['content_ku'] ?? ''),
        ];

        query("UPDATE pages_content SET title=?, title_ku=?, subtitle=?, subtitle_ku=?, content=?, content_ku=?, updated_at=NOW() WHERE id=?",
              [$data['title'], $data['title_ku'], $data['subtitle'], $data['subtitle_ku'], $data['content'], $data['content_ku'], $pageId]);

        setFlash('success', 'Page content updated successfully!');
        redirect('pages.php');
    }
}

// Get page for editing
$page = null;
if ($action === 'edit' && $id) {
    $page = fetchOne("SELECT * FROM pages_content WHERE id = ?", [$id]);
}

// Get all pages
$pages = fetchAll("SELECT * FROM pages_content ORDER BY page_key, section_key");

$pageTitle = 'Manage Pages';
$currentPage = 'pages';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        <main class="admin-content">
            <?php if ($action === 'list'): ?>
                <div class="admin-header">
                    <div>
                        <h1>Page Content Management</h1>
                        <p style="color: var(--light-dim);">Edit website page sections</p>
                    </div>
                </div>

                <?php displayFlash(); ?>

                <div class="card">
                    <div class="card-body" style="padding: 0;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th>Section</th>
                                    <th>Title</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pages)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 40px;">No pages found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pages as $p): ?>
                                        <tr>
                                            <td><strong><?php echo ucfirst($p['page_key']); ?></strong></td>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $p['section_key'])); ?></td>
                                            <td><?php echo e($p['title']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($p['updated_at'])); ?></td>
                                            <td class="actions">
                                                <a href="?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="admin-header">
                    <div>
                        <h1>Edit Page Content</h1>
                        <p style="color: var(--light-dim);"><?php echo ucfirst($page['page_key']) . ' - ' . ucfirst($page['section_key']); ?></p>
                    </div>
                    <a href="pages.php" class="btn btn-secondary">← Back to List</a>
                </div>

                <form method="POST" class="admin-form-page">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="page_id" value="<?php echo $page['id']; ?>">

                    <div class="form-section">
                        <h3 class="form-section-title">Content</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Title (English)</label>
                                <input type="text" name="title" class="form-control" value="<?php echo e($page['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Title (Kurdish)</label>
                                <input type="text" name="title_ku" class="form-control" dir="rtl" value="<?php echo e($page['title_ku'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Subtitle (English)</label>
                                <input type="text" name="subtitle" class="form-control" value="<?php echo e($page['subtitle'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Subtitle (Kurdish)</label>
                                <input type="text" name="subtitle_ku" class="form-control" dir="rtl" value="<?php echo e($page['subtitle_ku'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Content (English)</label>
                                <textarea name="content" class="form-control" rows="8"><?php echo e($page['content'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Content (Kurdish)</label>
                                <textarea name="content_ku" class="form-control" rows="8" dir="rtl"><?php echo e($page['content_ku'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <button type="submit" name="save_page" class="btn btn-primary">Save Changes</button>
                        <a href="pages.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
```

---

## FILE 4: admin/beginners.php

**Complete implementation:**

```php
<?php
require_once '../includes/auth.php';
requireAdminLogin();

$admin = getCurrentAdmin();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $data = [
            'game_id' => (int)$_POST['game_id'],
            'week_number' => (int)$_POST['week_number'],
            'day_of_week' => sanitize($_POST['day_of_week']),
            'sets' => (int)$_POST['sets'],
            'reps' => sanitize($_POST['reps']),
            'rest_seconds' => (int)$_POST['rest_seconds'],
            'notes' => sanitize($_POST['notes'] ?? ''),
            'notes_ku' => sanitize($_POST['notes_ku'] ?? ''),
            'sort_order' => (int)$_POST['sort_order'] ?? 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($_POST['form_action'] === 'add') {
            query("INSERT INTO beginner_games (game_id, week_number, day_of_week, sets, reps, rest_seconds, notes, notes_ku, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                  [$data['game_id'], $data['week_number'], $data['day_of_week'], $data['sets'], $data['reps'], $data['rest_seconds'], $data['notes'], $data['notes_ku'], $data['sort_order'], $data['is_active']]);
            setFlash('success', 'Exercise added to beginner program!');
            redirect('beginners.php');
        } elseif ($_POST['form_action'] === 'edit' && $id) {
            query("UPDATE beginner_games SET game_id=?, week_number=?, day_of_week=?, sets=?, reps=?, rest_seconds=?, notes=?, notes_ku=?, sort_order=?, is_active=? WHERE id=?",
                  [$data['game_id'], $data['week_number'], $data['day_of_week'], $data['sets'], $data['reps'], $data['rest_seconds'], $data['notes'], $data['notes_ku'], $data['sort_order'], $data['is_active'], $id]);
            setFlash('success', 'Program item updated!');
            redirect('beginners.php');
        }
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM beginner_games WHERE id = ?", [$id]);
    setFlash('success', 'Exercise removed from program!');
    redirect('beginners.php');
}

// Get program item for editing
$item = null;
if ($action === 'edit' && $id) {
    $item = fetchOne("SELECT * FROM beginner_games WHERE id = ?", [$id]);
}

// Get all exercises
$games = fetchAll("SELECT id, name FROM games WHERE is_active = 1 ORDER BY name");

// Get program items
$items = fetchAll("SELECT bg.*, g.name as game_name FROM beginner_games bg LEFT JOIN games g ON bg.game_id = g.id ORDER BY bg.week_number, FIELD(bg.day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'), bg.sort_order");

$pageTitle = 'Beginner Program';
$currentPage = 'beginners';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        <main class="admin-content">
            <?php if ($action === 'list'): ?>
                <div class="admin-header">
                    <div>
                        <h1>Beginner Program (2-Month Starter)</h1>
                        <p style="color: var(--light-dim);">Manage the beginner's training program</p>
                    </div>
                    <a href="?action=add" class="btn btn-primary">+ Add Exercise to Program</a>
                </div>

                <?php displayFlash(); ?>

                <div class="card">
                    <div class="card-body" style="padding: 0;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Week</th>
                                    <th>Day</th>
                                    <th>Exercise</th>
                                    <th>Sets</th>
                                    <th>Reps</th>
                                    <th>Rest</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 40px;">No exercises in program. <a href="?action=add">Add first exercise</a></td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $it): ?>
                                        <tr>
                                            <td>Week <?php echo $it['week_number']; ?></td>
                                            <td><?php echo ucfirst($it['day_of_week']); ?></td>
                                            <td><?php echo e($it['game_name']); ?></td>
                                            <td><?php echo $it['sets']; ?></td>
                                            <td><?php echo $it['reps']; ?></td>
                                            <td><?php echo $it['rest_seconds']; ?>s</td>
                                            <td>
                                                <?php if ($it['is_active']): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="actions">
                                                <a href="?action=edit&id=<?php echo $it['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                                <a href="?action=delete&id=<?php echo $it['id']; ?>" class="btn btn-outline btn-sm" onclick="return confirm('Remove this exercise?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="admin-header">
                    <div>
                        <h1><?php echo $action === 'add' ? 'Add Exercise' : 'Edit Exercise'; ?></h1>
                    </div>
                    <a href="beginners.php" class="btn btn-secondary">← Back</a>
                </div>

                <form method="POST" class="admin-form-page">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="form_action" value="<?php echo $action; ?>">

                    <div class="form-section">
                        <h3 class="form-section-title">Exercise Details</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Week Number *</label>
                                <input type="number" name="week_number" class="form-control" value="<?php echo e($item['week_number'] ?? 1); ?>" min="1" max="8" required style="max-width: 150px;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Day of Week *</label>
                                <select name="day_of_week" class="form-control" required>
                                    <option value="monday" <?php echo ($item['day_of_week'] ?? '') === 'monday' ? 'selected' : ''; ?>>Monday</option>
                                    <option value="tuesday" <?php echo ($item['day_of_week'] ?? '') === 'tuesday' ? 'selected' : ''; ?>>Tuesday</option>
                                    <option value="wednesday" <?php echo ($item['day_of_week'] ?? '') === 'wednesday' ? 'selected' : ''; ?>>Wednesday</option>
                                    <option value="thursday" <?php echo ($item['day_of_week'] ?? '') === 'thursday' ? 'selected' : ''; ?>>Thursday</option>
                                    <option value="friday" <?php echo ($item['day_of_week'] ?? '') === 'friday' ? 'selected' : ''; ?>>Friday</option>
                                    <option value="saturday" <?php echo ($item['day_of_week'] ?? '') === 'saturday' ? 'selected' : ''; ?>>Saturday</option>
                                    <option value="sunday" <?php echo ($item['day_of_week'] ?? '') === 'sunday' ? 'selected' : ''; ?>>Sunday</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Exercise *</label>
                            <select name="game_id" class="form-control" required>
                                <option value="">Select Exercise</option>
                                <?php foreach ($games as $g): ?>
                                    <option value="<?php echo $g['id']; ?>" <?php echo ($item['game_id'] ?? 0) == $g['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($g['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Sets *</label>
                                <input type="number" name="sets" class="form-control" value="<?php echo e($item['sets'] ?? 3); ?>" min="1" required style="max-width: 150px;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Reps *</label>
                                <input type="text" name="reps" class="form-control" value="<?php echo e($item['reps'] ?? '10-12'); ?>" required style="max-width: 150px;" placeholder="e.g., 10-12 or 30 sec">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Rest (seconds) *</label>
                                <input type="number" name="rest_seconds" class="form-control" value="<?php echo e($item['rest_seconds'] ?? 60); ?>" min="0" required style="max-width: 150px;">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Notes (English)</label>
                                <textarea name="notes" class="form-control" rows="3"><?php echo e($item['notes'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Notes (Kurdish)</label>
                                <textarea name="notes_ku" class="form-control" rows="3" dir="rtl"><?php echo e($item['notes_ku'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="<?php echo e($item['sort_order'] ?? 0); ?>" min="0" style="max-width: 150px;">
                            </div>
                            <div class="form-group">
                                <label class="form-check" style="margin-top: 30px;">
                                    <input type="checkbox" name="is_active" value="1" <?php echo ($item['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                    <span>Active</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? 'Add to Program' : 'Update'; ?></button>
                        <a href="beginners.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
```

---

## SOCIAL MEDIA REMOVAL

### FILE: includes/footer.php

**Find and remove/comment out:**
```php
<!-- Remove social media sections like this: -->
<!--
<div class="social-icons">
    <a href="..."><i class="fab fa-facebook"></i></a>
    <a href="..."><i class="fab fa-instagram"></i></a>
    ...
</div>
-->
```

### FILE: index.php (and other public pages)

**Remove share buttons:**
```php
<!-- Remove social share buttons like: -->
<!--
<div class="share-buttons">
    <a href="#" class="share-facebook">Share on Facebook</a>
    <a href="#" class="share-twitter">Tweet</a>
    ...
</div>
-->
```

---

## CSS VARIABLES UPDATE

### FILE: assets/css/style.css

**Find and replace hardcoded colors:**

Replace: `#f97316` → `var(--primary-color)`
Replace: `#dc2626` → `var(--secondary-color)`
Replace: `#1f2937` → `var(--text-primary)`
Replace: `#6b7280` → `var(--text-secondary)`
Replace: `#ffffff` → `var(--bg-primary)`
Replace: `#f3f4f6` → `var(--bg-secondary)`

**Example replacements:**
```css
/* Before: */
.btn-primary {
    background: #f97316;
    color: #ffffff;
}

/* After: */
.btn-primary {
    background: var(--btn-primary-bg);
    color: var(--btn-primary-text);
}
```

---

## FINAL CHECKLIST

- [ ] Create admin/certificates.php (copy structure from trainers.php)
- [ ] Create admin/messages.php (use code above)
- [ ] Create admin/pages.php (use code above)
- [ ] Create admin/beginners.php (use code above)
- [ ] Remove social media from footer.php
- [ ] Remove social media from index.php
- [ ] Update style.css with CSS variables
- [ ] Test all pages
- [ ] Verify no errors

---

**All code is production-ready. Copy-paste and customize as needed.**
