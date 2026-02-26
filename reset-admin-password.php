<?php
/**
 * Reset Admin Password Script
 * Run this once to set admin password to "admin123"
 */

require_once 'includes/config.php';

// Generate password hash for "admin123"
$newPassword = 'admin123';
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

// Update admin password
$sql = "UPDATE admins SET password = ? WHERE email = ?";
$stmt = db()->prepare($sql);
$stmt->execute([$hashedPassword, 'admin@fitzone.com']);

if ($stmt->rowCount() > 0) {
    echo "<h2 style='color: green;'>✅ SUCCESS!</h2>";
    echo "<p><strong>Admin password has been reset!</strong></p>";
    echo "<p>You can now login with:</p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@fitzone.com</li>";
    echo "<li><strong>Password:</strong> admin123</li>";
    echo "</ul>";
    echo "<p><a href='admin/login.php' style='display:inline-block; background:#f97316; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; margin-top:20px;'>Go to Admin Login</a></p>";
    echo "<hr>";
    echo "<p style='color: red;'><strong>⚠️ IMPORTANT:</strong> Delete this file (reset-admin-password.php) after you login!</p>";
} else {
    echo "<h2 style='color: red;'>❌ ERROR!</h2>";
    echo "<p>Could not update password. Make sure the database is imported.</p>";
}
?>
