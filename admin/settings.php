<?php
/**
 * Admin - Site Settings Management
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireAdminLogin();

$admin = getCurrentAdmin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        foreach ($_POST as $key => $value) {
            if ($key !== 'csrf_token' && strpos($key, '_ku') === false) {
                $valueKu = $_POST[$key . '_ku'] ?? null;
                query("UPDATE settings SET setting_value=?, setting_value_ku=? WHERE setting_key=?", [sanitize($value), sanitize($valueKu), $key]);
            }
        }
        setFlash('success', 'Settings updated successfully!');
        redirect('settings.php');
    }
}

// Get all settings grouped by category
$settings = fetchAll("SELECT * FROM settings ORDER BY category, sort_order");
$grouped = [];
foreach ($settings as $s) {
    $grouped[$s['category']][] = $s;
}

$pageTitle = 'Site Settings';
include 'includes/header.php';
?>

<div class="admin-content">
<div class="page-header" style="background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(14, 165, 233, 0.3);">
    <div>
        <h1 class="page-title" style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
            <span class="icon" style="font-size: 2.5rem;">⚙️</span>
            Site Settings
        </h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Manage website settings and configuration</p>
    </div>
</div>

<?php displayFlash(); ?>

<form method="POST">
<?php echo csrfField(); ?>

<?php
$categoryColors = [
    'general' => ['#0ea5e9', '#06b6d4'],
    'contact' => ['#10b981', '#14b8a6'],
    'social' => ['#8b5cf6', '#a855f7'],
    'seo' => ['#f59e0b', '#f97316'],
    'default' => ['#64748b', '#475569']
];

foreach ($grouped as $category => $items):
    $colors = $categoryColors[$category] ?? $categoryColors['default'];
?>
<!-- <?php echo ucfirst($category); ?> Settings -->
<div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden;">
    <div style="background: linear-gradient(135deg, <?php echo $colors[0]; ?>, <?php echo $colors[1]; ?>); color: white; padding: 1rem 1.5rem;">
        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;"><?php echo ucfirst($category); ?> Settings</h3>
    </div>
    <div style="padding: 1.5rem;">
        <?php foreach ($items as $setting): ?>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: #1e293b; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">
                    <?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?>
                </label>

                <?php if (strlen($setting['setting_value']) > 100): ?>
                    <textarea name="<?php echo $setting['setting_key']; ?>" rows="3" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='<?php echo $colors[0]; ?>'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($setting['setting_value']); ?></textarea>
                <?php else: ?>
                    <input type="text" name="<?php echo $setting['setting_key']; ?>" value="<?php echo e($setting['setting_value']); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='<?php echo $colors[0]; ?>'" onblur="this.style.borderColor='#e2e8f0'">
                <?php endif; ?>

                <?php if ($setting['setting_value_ku'] !== null): ?>
                    <label style="display: block; color: #64748b; font-weight: 500; margin-top: 0.75rem; margin-bottom: 0.5rem; font-size: 0.85rem;">
                        Kurdish version (کوردی):
                    </label>
                    <?php if (strlen($setting['setting_value_ku']) > 100): ?>
                        <textarea name="<?php echo $setting['setting_key']; ?>_ku" rows="3" dir="rtl" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='<?php echo $colors[0]; ?>'" onblur="this.style.borderColor='#e2e8f0'"><?php echo e($setting['setting_value_ku']); ?></textarea>
                    <?php else: ?>
                        <input type="text" name="<?php echo $setting['setting_key']; ?>_ku" dir="rtl" value="<?php echo e($setting['setting_value_ku']); ?>" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='<?php echo $colors[0]; ?>'" onblur="this.style.borderColor='#e2e8f0'">
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<div style="display: flex; gap: 1rem;">
    <button type="submit" style="background: linear-gradient(135deg, #0ea5e9, #6366f1); color: white; padding: 0.875rem 2rem; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(14, 165, 233, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(14, 165, 233, 0.3)'">
        ✓ Save All Settings
    </button>
</div>
</form>
</div>

<script src="../assets/js/admin-modern.js"></script>

<?php include 'includes/footer.php'; ?>
