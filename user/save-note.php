<?php
/**
 * Save Note Handler
 * FitZone Gym Management System
 */

require_once '../includes/auth.php';
requireLogin();

$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request');
        redirect('dashboard.php');
    }

    $noteDate = sanitize($_POST['note_date'] ?? date('Y-m-d'));
    $mood = sanitize($_POST['mood'] ?? 'good');
    $content = sanitize($_POST['content'] ?? '');
    $weightKg = !empty($_POST['weight_kg']) ? floatval($_POST['weight_kg']) : null;
    $workoutDone = isset($_POST['workout_done']) ? 1 : 0;

    // Check if note exists for this date
    $existing = fetchOne("SELECT id FROM user_notes WHERE user_id = ? AND note_date = ?", [$user['id'], $noteDate]);

    if ($existing) {
        // Update existing note
        query("UPDATE user_notes SET mood = ?, content = ?, weight_kg = ?, workout_done = ?, updated_at = NOW()
               WHERE id = ?", [$mood, $content, $weightKg, $workoutDone, $existing['id']]);
        setFlash('success', 'Note updated successfully!');
    } else {
        // Insert new note
        query("INSERT INTO user_notes (user_id, note_date, mood, content, weight_kg, workout_done, created_at)
               VALUES (?, ?, ?, ?, ?, ?, NOW())", [$user['id'], $noteDate, $mood, $content, $weightKg, $workoutDone]);
        setFlash('success', 'Note saved successfully!');
    }
} else {
    setFlash('error', 'Invalid request method');
}

redirect('dashboard.php');
