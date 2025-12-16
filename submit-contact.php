<?php
/**
 * Contact Form Handler
 * Saves messages to database
 */

require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Get form data
$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$message = sanitize($_POST['message'] ?? '');

// Validation
if (empty($name)) {
    jsonResponse(['success' => false, 'message' => 'Name is required']);
}

if (empty($message)) {
    jsonResponse(['success' => false, 'message' => 'Message is required']);
}

if (!empty($email) && !isValidEmail($email)) {
    jsonResponse(['success' => false, 'message' => 'Invalid email address']);
}

// Save to database
try {
    $sql = "INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)";
    query($sql, [$name, $email, $phone, $message]);
    
    jsonResponse(['success' => true, 'message' => 'Thank you! Your message has been sent successfully.']);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to send message. Please try again.'], 500);
}
