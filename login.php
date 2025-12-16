<?php
/**
 * User Login Page
 * FitZone Gym Management System
 */

require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/user/dashboard.php');
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        if (empty($email) || empty($password)) {
            $error = 'Please enter your email and password.';
        } else {
            $result = loginUser($email, $password, $remember);
            
            if ($result['success']) {
                // Redirect to dashboard or original destination
                $redirect = $_SESSION['redirect_after_login'] ?? SITE_URL . '/user/dashboard.php';
                unset($_SESSION['redirect_after_login']);
                redirect($redirect);
            } else {
                $error = $result['message'];
            }
        }
    }
}

$lang = getCurrentLang();
$pageTitle = $lang === 'ku' ? 'چوونە ژوورەوە' : 'Login';
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
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-logo">
                    <a href="index.php" class="logo"><?php echo getSetting('site_name', $lang); ?></a>
                </div>
                
                <h1 class="auth-title">
                    <?php echo $lang === 'ku' ? 'بەخێربێیتەوە' : 'Welcome Back'; ?>
                </h1>
                <p class="auth-subtitle">
                    <?php echo $lang === 'ku' ? 'بچۆ ژوورەوە بۆ هەژمارەکەت' : 'Sign in to your account'; ?>
                </p>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">
                        <?php echo $lang === 'ku' ? 'تۆمارکردن سەرکەوتوو بوو! ئێستا بچۆ ژوورەوە.' : 'Registration successful! Please login.'; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <?php echo csrfField(); ?>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">
                            <?php echo $lang === 'ku' ? 'ئیمەیڵ یان ناوی بەکارهێنەر' : 'Email or Username'; ?>
                        </label>
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="<?php echo $lang === 'ku' ? 'ئیمەیڵەکەت بنووسە' : 'Enter your email'; ?>"
                            value="<?php echo e($_POST['email'] ?? ''); ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <?php echo $lang === 'ku' ? 'وشەی نهێنی' : 'Password'; ?>
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="<?php echo $lang === 'ku' ? 'وشەی نهێنی بنووسە' : 'Enter your password'; ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                        <label class="form-check">
                            <input type="checkbox" name="remember" value="1">
                            <span><?php echo $lang === 'ku' ? 'بمهێڵەوە' : 'Remember me'; ?></span>
                        </label>
                        <a href="forgot-password.php" style="font-size: 0.9rem;">
                            <?php echo $lang === 'ku' ? 'وشەی نهێنیت لەبیرچووە؟' : 'Forgot password?'; ?>
                        </a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <?php echo $lang === 'ku' ? 'چوونە ژوورەوە' : 'Sign In'; ?>
                    </button>
                </form>
                
                <div class="auth-footer">
                    <?php echo $lang === 'ku' ? 'هەژمارت نییە؟' : "Don't have an account?"; ?>
                    <a href="register.php"><?php echo $lang === 'ku' ? 'تۆمار بکە' : 'Sign Up'; ?></a>
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
