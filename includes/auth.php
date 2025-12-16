<?php
/**
 * Authentication Functions
 * FitZone Gym Management System
 */

require_once __DIR__ . '/functions.php';

/**
 * Register new user
 */
function registerUser($data) {
    // Validate required fields
    $required = ['username', 'email', 'password', 'first_name', 'last_name'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => "Field {$field} is required"];
        }
    }
    
    // Validate email
    if (!isValidEmail($data['email'])) {
        return ['success' => false, 'message' => 'Invalid email address'];
    }
    
    // Check if username exists
    $existing = fetchOne("SELECT id FROM users WHERE username = ?", [$data['username']]);
    if ($existing) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    // Check if email exists
    $existing = fetchOne("SELECT id FROM users WHERE email = ?", [$data['email']]);
    if ($existing) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Validate password strength
    if (strlen($data['password']) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);
    
    // Generate verification token
    $verificationToken = bin2hex(random_bytes(32));
    
    // Insert user
    $sql = "INSERT INTO users (username, email, password, first_name, first_name_ku, last_name, last_name_ku, phone, gender, verification_token) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    try {
        query($sql, [
            $data['username'],
            $data['email'],
            $hashedPassword,
            $data['first_name'],
            $data['first_name_ku'] ?? null,
            $data['last_name'],
            $data['last_name_ku'] ?? null,
            $data['phone'] ?? null,
            $data['gender'] ?? 'male',
            $verificationToken
        ]);
        
        $userId = lastInsertId();
        
        // Create default game list for user
        query("INSERT INTO user_game_lists (user_id, name, name_ku, is_default) VALUES (?, 'My Workout', 'ڕاهێنانەکەم', 1)", [$userId]);
        
        return [
            'success' => true, 
            'message' => 'Registration successful', 
            'user_id' => $userId,
            'verification_token' => $verificationToken
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

/**
 * Login user
 */
function loginUser($email, $password, $remember = false) {
    // Find user by email or username
    $user = fetchOne("SELECT * FROM users WHERE email = ? OR username = ?", [$email, $email]);
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Check password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Check if user is active
    if ($user['status'] !== 'active') {
        return ['success' => false, 'message' => 'Your account is not active'];
    }
    
    // Update last login
    query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_username'] = $user['username'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    
    // Handle remember me
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
        // In production, store token hash in database
    }
    
    return ['success' => true, 'message' => 'Login successful', 'user' => $user];
}

/**
 * Logout user
 */
function logoutUser() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_username']);
    unset($_SESSION['user_name']);
    
    // Clear remember cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    return true;
}

/**
 * Login admin
 */
function loginAdmin($email, $password) {
    $admin = fetchOne("SELECT * FROM admins WHERE email = ? OR username = ?", [$email, $email]);
    
    if (!$admin) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    if (!password_verify($password, $admin['password'])) {
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    if (!$admin['is_active']) {
        return ['success' => false, 'message' => 'Account is disabled'];
    }
    
    // Update last login
    query("UPDATE admins SET last_login = NOW() WHERE id = ?", [$admin['id']]);
    
    // Set session
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_name'] = $admin['full_name'];
    $_SESSION['admin_role'] = $admin['role'];
    
    return ['success' => true, 'message' => 'Login successful', 'admin' => $admin];
}

/**
 * Logout admin
 */
function logoutAdmin() {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_role']);
    return true;
}

/**
 * Update user password
 */
function updateUserPassword($userId, $currentPassword, $newPassword) {
    $user = fetchOne("SELECT password FROM users WHERE id = ?", [$userId]);
    
    if (!$user) {
        return ['success' => false, 'message' => 'User not found'];
    }
    
    if (!password_verify($currentPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Current password is incorrect'];
    }
    
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'message' => 'New password must be at least 6 characters'];
    }
    
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
    query("UPDATE users SET password = ? WHERE id = ?", [$hashedPassword, $userId]);
    
    return ['success' => true, 'message' => 'Password updated successfully'];
}

/**
 * Request password reset
 */
function requestPasswordReset($email) {
    $user = fetchOne("SELECT id, email FROM users WHERE email = ?", [$email]);
    
    if (!$user) {
        // Don't reveal if email exists
        return ['success' => true, 'message' => 'If the email exists, a reset link has been sent'];
    }
    
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    query("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?", 
          [$token, $expires, $user['id']]);
    
    // In production, send email with reset link
    // mail($email, 'Password Reset', "Reset link: " . SITE_URL . "/reset-password.php?token={$token}");
    
    return ['success' => true, 'message' => 'If the email exists, a reset link has been sent', 'token' => $token];
}

/**
 * Reset password with token
 */
function resetPassword($token, $newPassword) {
    $user = fetchOne("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()", [$token]);
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid or expired reset token'];
    }
    
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
    query("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?", 
          [$hashedPassword, $user['id']]);
    
    return ['success' => true, 'message' => 'Password has been reset'];
}

/**
 * Update user profile
 */
function updateUserProfile($userId, $data) {
    $allowedFields = ['first_name', 'first_name_ku', 'last_name', 'last_name_ku', 'phone', 'date_of_birth', 'gender', 'address', 'address_ku', 'bio', 'bio_ku'];
    
    $updates = [];
    $params = [];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updates[] = "{$field} = ?";
            $params[] = $data[$field];
        }
    }
    
    if (empty($updates)) {
        return ['success' => false, 'message' => 'No data to update'];
    }
    
    $params[] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    
    query($sql, $params);
    
    return ['success' => true, 'message' => 'Profile updated successfully'];
}

/**
 * Update user avatar
 */
function updateUserAvatar($userId, $file) {
    $result = uploadFile($file, ['jpg', 'jpeg', 'png', 'gif', 'webp'], 2097152); // 2MB max
    
    if (!$result['success']) {
        return $result;
    }
    
    // Get old avatar to delete
    $user = fetchOne("SELECT avatar FROM users WHERE id = ?", [$userId]);
    if ($user && $user['avatar'] !== 'default-avatar.png') {
        deleteFile($user['avatar']);
    }
    
    query("UPDATE users SET avatar = ? WHERE id = ?", [$result['filename'], $userId]);
    
    return ['success' => true, 'message' => 'Avatar updated', 'filename' => $result['filename']];
}
