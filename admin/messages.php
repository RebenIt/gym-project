<?php
/**
 * Admin - Contact Messages Viewer
 * View and manage contact form submissions
 * FitZone Gym Management System
 */

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

// Calculate stats
$totalMessages = count($messages);
$unreadMessages = count(array_filter($messages, fn($m) => !$m['is_read']));
$readMessages = count(array_filter($messages, fn($m) => $m['is_read']));
$todayMessages = count(array_filter($messages, fn($m) => date('Y-m-d', strtotime($m['created_at'])) === date('Y-m-d')));

$pageTitle = 'Contact Messages';
$currentPage = 'messages';
?>
<?php include 'includes/header.php'; ?>

<div class="admin-content">
<!-- List View -->
<div class="page-header" style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">📧</span>
            Contact Messages
            <?php if ($unreadMessages > 0): ?>
                <span style="background: rgba(255,255,255,0.25); padding: 0.375rem 0.875rem; border-radius: 20px; font-size: 0.9rem; margin-left: 1rem; font-weight: 600;"><?php echo $unreadMessages; ?> Unread</span>
            <?php endif; ?>
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">View and manage contact form submissions</p>
    </div>
</div>

<?php displayFlash(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Messages</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $totalMessages; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Unread Messages</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $unreadMessages; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Read Messages</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $readMessages; ?></div>
    </div>

    <div style="background: linear-gradient(135deg, #ec4899 0%, #a855f7 100%); padding: 1.5rem; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);">
        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Today's Messages</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo $todayMessages; ?></div>
    </div>
</div>

<!-- Messages Table -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Date & Time</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Name</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Contact</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Subject</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Message</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr>
                        <td colspan="7" style="padding: 3rem; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                <span style="font-size: 4rem; opacity: 0.3;">📧</span>
                                <p style="color: #64748b; font-size: 1.1rem; margin: 0;">No messages yet</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s; cursor: pointer; <?php echo $msg['is_read'] ? '' : 'background: #fffbeb;'; ?>" onmouseover="this.style.background='<?php echo $msg['is_read'] ? '#f8fafc' : '#fef3c7'; ?>'" onmouseout="this.style.background='<?php echo $msg['is_read'] ? 'white' : '#fffbeb'; ?>'">
                            <td style="padding: 1rem;">
                                <div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.9rem; margin-bottom: 0.25rem;">
                                        <?php echo date('M d, Y', strtotime($msg['created_at'])); ?>
                                    </div>
                                    <div style="color: #64748b; font-size: 0.8rem;">
                                        <?php echo date('h:i A', strtotime($msg['created_at'])); ?>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="font-weight: 600; color: #1e293b; font-size: 0.95rem;"><?php echo e($msg['name']); ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-size: 0.85rem;">
                                    <?php if ($msg['email']): ?>
                                        <div style="margin-bottom: 0.25rem;">
                                            <a href="mailto:<?php echo e($msg['email']); ?>" style="color: #10b981; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                                📧 <?php echo e($msg['email']); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($msg['phone']): ?>
                                        <div style="color: #64748b;">
                                            📞 <?php echo e($msg['phone']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="color: #475569; font-size: 0.9rem; font-weight: 500;"><?php echo e($msg['subject'] ?? 'No subject'); ?></span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #64748b; font-size: 0.9rem;" title="<?php echo e($msg['message']); ?>">
                                    <?php echo e($msg['message']); ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($msg['is_read']): ?>
                                    <span style="background: #e0f2fe; color: #0284c7; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Read</span>
                                <?php else: ?>
                                    <span style="background: #fef3c7; color: #f59e0b; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">Unread</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="?action=toggle_read&id=<?php echo $msg['id']; ?>" style="background: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s; white-space: nowrap;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                                        <?php echo $msg['is_read'] ? 'Mark Unread' : 'Mark Read'; ?>
                                    </a>
                                    <a href="?action=delete&id=<?php echo $msg['id']; ?>" onclick="return confirm('Delete this message?')" style="background: #f1f5f9; color: #64748b; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script src="../assets/js/admin-modern.js"></script>
<?php include 'includes/footer.php'; ?>
