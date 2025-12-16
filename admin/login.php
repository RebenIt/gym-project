<?php
/**
 * Admin Login Page
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/index.php');
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Please enter your email and password.';
        } else {
            $result = loginAdmin($email, $password);
            
            if ($result['success']) {
                redirect(SITE_URL . '/admin/index.php');
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo getSetting('site_name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-logo">
                    <span class="logo"><?php echo getSetting('site_name'); ?></span>
                </div>
                
                <h1 class="auth-title">Admin Panel</h1>
                <p class="auth-subtitle">Sign in to manage your website</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <?php echo csrfField(); ?>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email or Username</label>
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="admin@fitzone.com"
                            value="<?php echo e($_POST['email'] ?? ''); ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Enter your password"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </form>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="../index.php" style="color: var(--light-dim); font-size: 0.9rem;">
                        ← Back to Website
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
