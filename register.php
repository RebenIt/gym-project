<?php
/**
 * User Registration Page
 * FitZone Gym Management System
 */

require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/user/dashboard.php');
}

$error = '';
$errors = [];

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $data = [
            'username' => sanitize($_POST['username'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'first_name' => sanitize($_POST['first_name'] ?? ''),
            'first_name_ku' => sanitize($_POST['first_name_ku'] ?? ''),
            'last_name' => sanitize($_POST['last_name'] ?? ''),
            'last_name_ku' => sanitize($_POST['last_name_ku'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'gender' => sanitize($_POST['gender'] ?? 'male'),
        ];
        
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validation
        if (empty($data['username'])) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!isValidEmail($data['email'])) {
            $errors['email'] = 'Invalid email address';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        } elseif ($data['password'] !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
        
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }
        
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }
        
        // If no validation errors, proceed with registration
        if (empty($errors)) {
            $result = registerUser($data);
            
            if ($result['success']) {
                redirect(SITE_URL . '/login.php?registered=1');
            } else {
                $error = $result['message'];
            }
        }
    }
}

$lang = getCurrentLang();
$pageTitle = $lang === 'ku' ? 'تۆمارکردن' : 'Register';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang === 'ku' ? 'ckb' : 'en'; ?>" dir="<?php echo $lang === 'ku' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo getSetting('site_name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Language Switcher -->
    <div class="lang-switcher" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <a href="?lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
        <a href="?lang=ku" class="lang-btn <?php echo $lang === 'ku' ? 'active' : ''; ?>">کوردی</a>
    </div>

    <div class="auth-page">
        <div class="auth-container" style="max-width: 550px;">
            <div class="auth-card">
                <div class="auth-logo">
                    <a href="index.php" class="logo"><?php echo getSetting('site_name', $lang); ?></a>
                </div>
                
                <h1 class="auth-title">
                    <?php echo $lang === 'ku' ? 'هەژمارێک دروست بکە' : 'Create Account'; ?>
                </h1>
                <p class="auth-subtitle">
                    <?php echo $lang === 'ku' ? 'پێکەوە گەشتی تەندروستیت دەست پێ بکەین' : 'Start your fitness journey with us'; ?>
                </p>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <?php echo csrfField(); ?>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label" for="first_name">
                                <?php echo $lang === 'ku' ? 'ناوی یەکەم *' : 'First Name *'; ?>
                            </label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                class="form-control" 
                                placeholder="<?php echo $lang === 'ku' ? 'ناوت' : 'John'; ?>"
                                value="<?php echo e($_POST['first_name'] ?? ''); ?>"
                                required
                            >
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="form-error"><?php echo e($errors['first_name']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="last_name">
                                <?php echo $lang === 'ku' ? 'ناوی کۆتایی *' : 'Last Name *'; ?>
                            </label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                class="form-control" 
                                placeholder="<?php echo $lang === 'ku' ? 'ناوی باوک' : 'Doe'; ?>"
                                value="<?php echo e($_POST['last_name'] ?? ''); ?>"
                                required
                            >
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="form-error"><?php echo e($errors['last_name']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label" for="first_name_ku">
                                <?php echo $lang === 'ku' ? 'ناوی یەکەم (کوردی)' : 'First Name (Kurdish)'; ?>
                            </label>
                            <input 
                                type="text" 
                                id="first_name_ku" 
                                name="first_name_ku" 
                                class="form-control" 
                                dir="rtl"
                                placeholder="ناوت بە کوردی"
                                value="<?php echo e($_POST['first_name_ku'] ?? ''); ?>"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="last_name_ku">
                                <?php echo $lang === 'ku' ? 'ناوی کۆتایی (کوردی)' : 'Last Name (Kurdish)'; ?>
                            </label>
                            <input 
                                type="text" 
                                id="last_name_ku" 
                                name="last_name_ku" 
                                class="form-control" 
                                dir="rtl"
                                placeholder="ناوی باوکت بە کوردی"
                                value="<?php echo e($_POST['last_name_ku'] ?? ''); ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="username">
                            <?php echo $lang === 'ku' ? 'ناوی بەکارهێنەر *' : 'Username *'; ?>
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-control" 
                            placeholder="<?php echo $lang === 'ku' ? 'ناوی بەکارهێنەر' : 'username'; ?>"
                            value="<?php echo e($_POST['username'] ?? ''); ?>"
                            required
                        >
                        <?php if (isset($errors['username'])): ?>
                            <div class="form-error"><?php echo e($errors['username']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">
                            <?php echo $lang === 'ku' ? 'ئیمەیڵ *' : 'Email *'; ?>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="you@example.com"
                            value="<?php echo e($_POST['email'] ?? ''); ?>"
                            required
                        >
                        <?php if (isset($errors['email'])): ?>
                            <div class="form-error"><?php echo e($errors['email']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">
                            <?php echo $lang === 'ku' ? 'ژمارەی مۆبایل' : 'Phone Number'; ?>
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="form-control" 
                            placeholder="+964 750 123 4567"
                            value="<?php echo e($_POST['phone'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="gender">
                            <?php echo $lang === 'ku' ? 'ڕەگەز' : 'Gender'; ?>
                        </label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="male" <?php echo ($_POST['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>
                                <?php echo $lang === 'ku' ? 'نێر' : 'Male'; ?>
                            </option>
                            <option value="female" <?php echo ($_POST['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>
                                <?php echo $lang === 'ku' ? 'مێ' : 'Female'; ?>
                            </option>
                        </select>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label" for="password">
                                <?php echo $lang === 'ku' ? 'وشەی نهێنی *' : 'Password *'; ?>
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control" 
                                placeholder="<?php echo $lang === 'ku' ? 'لانی کەم ٦ پیت' : 'Min 6 characters'; ?>"
                                required
                            >
                            <?php if (isset($errors['password'])): ?>
                                <div class="form-error"><?php echo e($errors['password']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="password_confirm">
                                <?php echo $lang === 'ku' ? 'دووبارەکردنەوە *' : 'Confirm *'; ?>
                            </label>
                            <input 
                                type="password" 
                                id="password_confirm" 
                                name="password_confirm" 
                                class="form-control" 
                                placeholder="<?php echo $lang === 'ku' ? 'دووبارە بنووسەوە' : 'Confirm password'; ?>"
                                required
                            >
                            <?php if (isset($errors['password_confirm'])): ?>
                                <div class="form-error"><?php echo e($errors['password_confirm']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="terms" value="1" required>
                            <span>
                                <?php echo $lang === 'ku' ? 'ڕازیم بە مەرجەکان و سیاسەتی تایبەتمەندی' : 'I agree to the Terms of Service and Privacy Policy'; ?>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <?php echo $lang === 'ku' ? 'تۆمار بکە' : 'Create Account'; ?>
                    </button>
                </form>
                
                <div class="auth-footer">
                    <?php echo $lang === 'ku' ? 'پێشتر هەژمارت هەیە؟' : 'Already have an account?'; ?>
                    <a href="login.php"><?php echo $lang === 'ku' ? 'بچۆ ژوورەوە' : 'Sign In'; ?></a>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" style="color: var(--light-dim); font-size: 0.9rem;">
                    ← <?php echo $lang === 'ku' ? 'گەڕانەوە بۆ ماڵەوە' : 'Back to Home'; ?>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
