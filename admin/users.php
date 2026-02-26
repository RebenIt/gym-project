<?php
/**
 * Admin - Users Management
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireAdminLogin();

$admin = getCurrentAdmin();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit' && $id) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $status = sanitize($_POST['status'] ?? 'active');
        query("UPDATE users SET status=? WHERE id=?", [$status, $id]);
        setFlash('success', 'User updated successfully!');
        redirect('users.php');
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    query("DELETE FROM user_notes WHERE user_id = ?", [$id]);
    query("DELETE FROM user_list_games WHERE list_id IN (SELECT id FROM user_game_lists WHERE user_id = ?)", [$id]);
    query("DELETE FROM user_game_lists WHERE user_id = ?", [$id]);
    query("DELETE FROM users WHERE id = ?", [$id]);
    setFlash('success', 'User deleted successfully!');
    redirect('users.php');
}

// Get user for editing
$user = null;
if ($action === 'edit' && $id) {
    $user = fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    if (!$user) {
        redirect('users.php');
    }

    // Get user stats
    $userLists = fetchOne("SELECT COUNT(*) as count FROM user_game_lists WHERE user_id = ?", [$id])['count'] ?? 0;
    $userNotes = fetchOne("SELECT COUNT(*) as count FROM user_notes WHERE user_id = ?", [$id])['count'] ?? 0;
}

// Get all users for listing
$users = fetchAll("SELECT * FROM users ORDER BY created_at DESC");

// Calculate stats
$totalUsers = count($users);
$activeUsers = count(array_filter($users, fn($u) => $u['status'] === 'active'));
$suspendedUsers = count(array_filter($users, fn($u) => $u['status'] === 'suspended'));
$bannedUsers = count(array_filter($users, fn($u) => $u['status'] === 'banned'));

$pageTitle = 'Manage Users';

include 'includes/header.php';
?>

<div class="admin-content">
<?php if ($action === 'list'): ?>
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">👥</span>
            User Management
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage registered users and their accounts</p>
    </div>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Users</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $totalUsers; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Active Users</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $activeUsers; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Suspended</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $suspendedUsers; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Banned</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $bannedUsers; ?></div>
    </div>
</div>

<!-- Users Table -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Username</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Full Name</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Email</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Joined</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="padding: 3rem; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                <span style="font-size: 4rem; opacity: 0.3;">👥</span>
                                <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No users found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem;">
                                        <?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                    </div>
                                    <span style="font-weight: 600; color: #1e293b;"><?php echo e($u['username']); ?></span>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="color: #475569; font-size: 0.95rem;"><?php echo e($u['first_name'] . ' ' . $u['last_name']); ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="color: #64748b; font-size: 0.9rem;">📧 <?php echo e($u['email']); ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($u['status'] === 'active'): ?>
                                    <span style="background: #dcfce7; color: #16a34a; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Active</span>
                                <?php elseif ($u['status'] === 'suspended'): ?>
                                    <span style="background: #fef3c7; color: #f59e0b; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Suspended</span>
                                <?php else: ?>
                                    <span style="background: #fee2e2; color: #dc2626; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;"><?php echo ucfirst($u['status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="color: #64748b; font-size: 0.9rem;"><?php echo formatDate($u['created_at']); ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="?action=edit&id=<?php echo $u['id']; ?>" style="background: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">Edit</a>
                                    <a href="?action=delete&id=<?php echo $u['id']; ?>" onclick="return confirm('Delete user and all their data?')" style="background: #f1f5f9; color: #64748b; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">Delete</a>
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
<!-- Edit Form -->
<div class="page-header" style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">✏️</span>
            Edit User
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Update user status and view account details</p>
    </div>
    <a href="users.php" style="background: white; color: #3b82f6; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; text-decoration: none;">
        ← Back to List
    </a>
</div>

<!-- User Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #3b82f6;">
        <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.5rem;">Account Status</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #1e293b;"><?php echo ucfirst($user['status']); ?></div>
    </div>

    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #10b981;">
        <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.5rem;">Game Lists</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #1e293b;"><?php echo $userLists ?? 0; ?></div>
    </div>

    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #f59e0b;">
        <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.5rem;">Notes Created</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #1e293b;"><?php echo $userNotes ?? 0; ?></div>
    </div>

    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-left: 4px solid #ec4899;">
        <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.5rem;">Last Login</div>
        <div style="font-size: 0.95rem; font-weight: 600; color: #1e293b;"><?php echo $user['last_login'] ? timeAgo($user['last_login']) : 'Never'; ?></div>
    </div>
</div>

<form method="POST">
<?php echo csrfField(); ?>

<!-- User Information -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">👤 User Information</h3>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Username</label>
                <input type="text" value="<?php echo e($user['username'] ?? ''); ?>" disabled style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #64748b;">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Email</label>
                <input type="text" value="<?php echo e($user['email'] ?? ''); ?>" disabled style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #64748b;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">First Name</label>
                <input type="text" value="<?php echo e($user['first_name'] ?? ''); ?>" disabled style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #64748b;">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Last Name</label>
                <input type="text" value="<?php echo e($user['last_name'] ?? ''); ?>" disabled style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #64748b;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Joined Date</label>
                <input type="text" value="<?php echo formatDate($user['created_at']); ?>" disabled style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #64748b;">
            </div>
            <div>
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Last Login</label>
                <input type="text" value="<?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Never'; ?>" disabled style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #64748b;">
            </div>
        </div>

        <div style="max-width: 300px;">
            <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Account Status <span style="color: #dc2626;">*</span></label>
            <select name="status" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s; background: white; cursor: pointer;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                <option value="active" <?php echo ($user['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="suspended" <?php echo ($user['status'] ?? '') === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                <option value="banned" <?php echo ($user['status'] ?? '') === 'banned' ? 'selected' : ''; ?>>Banned</option>
            </select>
            <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">Change the user's account status</p>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div style="display: flex; gap: 1rem; justify-content: flex-start;">
    <button type="submit" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(59, 130, 246, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.3)'">
        ✓ Update Status
    </button>
    <a href="users.php" style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 8px; font-weight: 600; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
        Cancel
    </a>
</div>
</form>
<?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
