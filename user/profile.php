<?php
require_once '../includes/functions.php';
requireLogin();

$currentUser = getCurrentUser();
$pageTitle = __('My Profile', 'پڕۆفایلم');
include 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitize($_POST['first_name'] ?? '');
    $lastName = sanitize($_POST['last_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $dateOfBirth = $_POST['date_of_birth'] ?? null;
    $gender = $_POST['gender'] ?? 'male';
    $bio = sanitize($_POST['bio'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($email)) {
        $error = __('Please fill in required fields', 'تکایە خانە پێویستەکان پڕبکەوە');
    } elseif (!isValidEmail($email)) {
        $error = __('Invalid email', 'ئیمەیڵی هەڵە');
    } else {
        // Check if email is taken by another user
        $emailCheck = fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $currentUser['id']]);
        if ($emailCheck) {
            $error = __('Email is already taken', 'ئیمەیڵ بەکارهاتووە');
        } else {
            query("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?,
                   date_of_birth = ?, gender = ?, bio = ?, updated_at = NOW()
                   WHERE id = ?",
                [$firstName, $lastName, $email, $phone, $dateOfBirth, $gender, $bio, $currentUser['id']]);

            $success = __('Profile updated successfully!', 'پڕۆفایل بە سەرکەوتوویی نوێکرایەوە!');
            $currentUser = getCurrentUser(); // Refresh
        }
    }

    // Handle password change
    if (!empty($_POST['new_password'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $currentUser['password'])) {
            $error = __('Current password is incorrect', 'وشەی نهێنی ئێستا هەڵەیە');
        } elseif ($newPassword !== $confirmPassword) {
            $error = __('Passwords do not match', 'وشەی نهێنییەکان یەکناگرنەوە');
        } elseif (strlen($newPassword) < 6) {
            $error = __('Password must be at least 6 characters', 'وشەی نهێنی لانیکەم ٦ پیت بێت');
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            query("UPDATE users SET password = ? WHERE id = ?", [$hashedPassword, $currentUser['id']]);
            $success = __('Password changed successfully!', 'وشەی نهێنی بە سەرکەوتوویی گۆڕا!');
        }
    }
}
?>

<div class="row g-4">
    <div class="col-lg-8">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <!-- Profile Information -->
        <div class="card shadow-sm mb-4" style="border: none; border-radius: 15px; background: white;">
            <div class="card-body p-4" style="background: white;">
                <h5 class="fw-bold mb-4" style="color: #1f2937;"><?= __('Profile Information', 'زانیاری پڕۆفایل') ?></h5>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="color: #1f2937;"><?= __('First Name', 'ناوی یەکەم') ?> *</label>
                            <input type="text" name="first_name" class="form-control" value="<?= e($currentUser['first_name']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="color: #1f2937;"><?= __('Last Name', 'ناوی دووەم') ?> *</label>
                            <input type="text" name="last_name" class="form-control" value="<?= e($currentUser['last_name']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="color: #1f2937;"><?= __('Email', 'ئیمەیڵ') ?> *</label>
                            <input type="email" name="email" class="form-control" value="<?= e($currentUser['email']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="color: #1f2937;"><?= __('Phone', 'تەلەفۆن') ?></label>
                            <input type="tel" name="phone" class="form-control" value="<?= e($currentUser['phone']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="color: #1f2937;"><?= __('Date of Birth', 'ڕێکەوتی لەدایکبوون') ?></label>
                            <input type="date" name="date_of_birth" class="form-control" value="<?= e($currentUser['date_of_birth']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="color: #1f2937;"><?= __('Gender', 'ڕەگەز') ?></label>
                            <select name="gender" class="form-select">
                                <option value="male" <?= $currentUser['gender'] === 'male' ? 'selected' : '' ?>><?= __('Male', 'نێر') ?></option>
                                <option value="female" <?= $currentUser['gender'] === 'female' ? 'selected' : '' ?>><?= __('Female', 'مێ') ?></option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label" style="color: #1f2937;"><?= __('Bio', 'دەربارەت') ?></label>
                            <textarea name="bio" class="form-control" rows="3"><?= e($currentUser['bio']) ?></textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><?= __('Update Profile', 'نوێکردنەوەی پڕۆفایل') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card shadow-sm" style="border: none; border-radius: 15px; background: white;">
            <div class="card-body p-4" style="background: white;">
                <h5 class="fw-bold mb-4" style="color: #1f2937;"><?= __('Change Password', 'گۆڕینی وشەی نهێنی') ?></h5>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="color: #1f2937;"><?= __('Current Password', 'وشەی نهێنی ئێستا') ?></label>
                            <input type="password" name="current_password" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="color: #1f2937;"><?= __('New Password', 'وشەی نهێنی نوێ') ?></label>
                            <input type="password" name="new_password" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" style="color: #1f2937;"><?= __('Confirm Password', 'دووپاتکردنەوە') ?></label>
                            <input type="password" name="confirm_password" class="form-control">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><?= __('Change Password', 'گۆڕینی وشەی نهێنی') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm" style="border: none; border-radius: 15px; background: linear-gradient(135deg, #f97316, #dc2626); color: white;">
            <div class="card-body p-4 text-center">
                <div style="width: 100px; height: 100px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 3rem; color: #f97316;">
                    <?= substr($currentUser['username'], 0, 1) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= e($currentUser['first_name']) ?> <?= e($currentUser['last_name']) ?></h5>
                <p class="mb-2"><?= e($currentUser['email']) ?></p>
                <small><?= __('Member since', 'ئەندام لە') ?> <?= formatDate($currentUser['created_at'], 'M Y') ?></small>
            </div>
        </div>

        <div class="card shadow-sm mt-4" style="border: none; border-radius: 15px; background: white;">
            <div class="card-body p-4" style="background: white;">
                <h6 class="fw-bold mb-3" style="color: #1f2937;"><?= __('Account Stats', 'ئامارەکانی هەژمار') ?></h6>
                <div class="mb-2">
                    <small style="color: #6b7280;"><?= __('Status:', 'باری:') ?></small>
                    <span class="badge bg-success float-end"><?= ucfirst($currentUser['status']) ?></span>
                </div>
                <div class="mb-2">
                    <small style="color: #6b7280;"><?= __('Last login:', 'دوایین چوونەژوورەوە:') ?></small>
                    <small class="float-end" style="color: #4b5563;"><?= timeAgo($currentUser['last_login']) ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
