<?php
/**
 * Dynamic CSS Colors Generator
 * Generates CSS custom properties from database settings
 * FitZone Gym Management System
 */

// Load database functions (uses PDO from config.php)
require_once __DIR__ . '/config.php';

/**
 * Get all color settings from database
 */
function getColorSettings() {
    $colors = fetchAll("SELECT setting_key, setting_value FROM settings WHERE category = 'colors' ORDER BY sort_order");
    $colorMap = [];

    foreach ($colors as $color) {
        $colorMap[$color['setting_key']] = $color['setting_value'];
    }

    return $colorMap;
}

/**
 * Generate dynamic CSS from color settings
 */
function generateDynamicCSS() {
    $colors = getColorSettings();

    // If no colors in database, return empty (will use defaults from variables.css)
    if (empty($colors)) {
        return '';
    }

    $css = "<style>\n:root {\n";

    foreach ($colors as $key => $value) {
        if (!empty($value)) {
            // Convert setting_key to CSS variable format
            // e.g., 'primary_color' becomes '--primary-color'
            $cssVar = '--' . str_replace('_', '-', $key);
            $css .= "    {$cssVar}: {$value};\n";
        }
    }

    $css .= "}\n</style>\n";

    return $css;
}

/**
 * Output dynamic CSS
 * Use this in the <head> section after loading variables.css
 */
function outputDynamicColors() {
    echo generateDynamicCSS();
}

/**
 * Save color setting to database
 */
function saveColorSetting($key, $value) {
    $existing = fetchOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);

    if ($existing) {
        return execute("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?", [$value, $key]);
    } else {
        return insert("INSERT INTO settings (setting_key, setting_value, setting_type, category) VALUES (?, ?, 'color', 'colors')", [$key, $value]);
    }
}

/**
 * Reset colors to defaults (from variables.css)
 */
function resetColorsToDefaults() {
    $defaultColors = [
        'primary_color' => '#f97316',
        'secondary_color' => '#dc2626',
        'accent_color' => '#f59e0b',
        'text_primary' => '#1f2937',
        'text_secondary' => '#6b7280',
        'text_light' => '#9ca3af',
        'text_white' => '#ffffff',
        'text_dark' => '#111827',
        'bg_primary' => '#ffffff',
        'bg_secondary' => '#f3f4f6',
        'bg_dark' => '#1f2937',
        'bg_light' => '#f9fafb',
        'card_bg' => '#ffffff',
        'card_border' => '#e5e7eb',
        'btn_primary_bg' => '#f97316',
        'btn_primary_text' => '#ffffff',
        'btn_primary_hover' => '#ea580c',
        'btn_secondary_bg' => '#6b7280',
        'btn_secondary_text' => '#ffffff',
        'btn_secondary_hover' => '#4b5563',
        'title_color' => '#1f2937',
        'subtitle_color' => '#6b7280',
        'heading_gradient_start' => '#f97316',
        'heading_gradient_end' => '#dc2626',
        'link_color' => '#f97316',
        'link_hover' => '#dc2626',
        'navbar_bg' => '#ffffff',
        'navbar_text' => '#1f2937',
        'navbar_hover' => '#f97316',
        'navbar_active' => '#dc2626',
        'footer_bg' => '#1f2937',
        'footer_text' => '#d1d5db',
        'footer_heading' => '#ffffff',
        'footer_link' => '#f97316',
        'footer_link_hover' => '#fb923c',
        'sidebar_bg' => '#1f2937',
        'sidebar_text' => '#ffffff',
        'sidebar_active' => '#f97316',
        'badge_success_bg' => '#10b981',
        'badge_danger_bg' => '#ef4444',
        'badge_warning_bg' => '#f59e0b',
        'badge_info_bg' => '#3b82f6',
        'service_card_icon_color' => '#f97316',
        'plan_card_popular_border' => '#f97316',
        'exercise_card_difficulty_beginner' => '#10b981',
        'exercise_card_difficulty_intermediate' => '#f59e0b',
        'exercise_card_difficulty_advanced' => '#ef4444',
    ];

    foreach ($defaultColors as $key => $value) {
        saveColorSetting($key, $value);
    }

    return true;
}

/**
 * Get default color mappings for admin panel
 */
function getColorDefinitions() {
    return [
        'Primary Colors' => [
            'primary_color' => 'Primary Color',
            'secondary_color' => 'Secondary Color',
            'accent_color' => 'Accent Color',
        ],
        'Text Colors' => [
            'text_primary' => 'Primary Text',
            'text_secondary' => 'Secondary Text',
            'text_light' => 'Light Text',
            'text_white' => 'White Text',
            'text_dark' => 'Dark Text',
        ],
        'Background Colors' => [
            'bg_primary' => 'Primary Background',
            'bg_secondary' => 'Secondary Background',
            'bg_dark' => 'Dark Background',
            'bg_light' => 'Light Background',
        ],
        'Card Colors' => [
            'card_bg' => 'Card Background',
            'card_border' => 'Card Border',
        ],
        'Button Colors' => [
            'btn_primary_bg' => 'Primary Button Background',
            'btn_primary_text' => 'Primary Button Text',
            'btn_primary_hover' => 'Primary Button Hover',
            'btn_secondary_bg' => 'Secondary Button Background',
            'btn_secondary_text' => 'Secondary Button Text',
            'btn_secondary_hover' => 'Secondary Button Hover',
        ],
        'Heading & Title Colors' => [
            'title_color' => 'Title Color',
            'subtitle_color' => 'Subtitle Color',
            'heading_gradient_start' => 'Heading Gradient Start',
            'heading_gradient_end' => 'Heading Gradient End',
        ],
        'Link Colors' => [
            'link_color' => 'Link Color',
            'link_hover' => 'Link Hover Color',
        ],
        'Navbar Colors' => [
            'navbar_bg' => 'Navbar Background',
            'navbar_text' => 'Navbar Text',
            'navbar_hover' => 'Navbar Hover',
            'navbar_active' => 'Navbar Active',
        ],
        'Footer Colors' => [
            'footer_bg' => 'Footer Background',
            'footer_text' => 'Footer Text',
            'footer_heading' => 'Footer Heading',
            'footer_link' => 'Footer Link',
            'footer_link_hover' => 'Footer Link Hover',
        ],
        'Sidebar Colors (Admin)' => [
            'sidebar_bg' => 'Sidebar Background',
            'sidebar_text' => 'Sidebar Text',
            'sidebar_active' => 'Sidebar Active',
        ],
        'Badge Colors' => [
            'badge_success_bg' => 'Success Badge',
            'badge_danger_bg' => 'Danger Badge',
            'badge_warning_bg' => 'Warning Badge',
            'badge_info_bg' => 'Info Badge',
        ],
        'Component Colors' => [
            'service_card_icon_color' => 'Service Icon Color',
            'plan_card_popular_border' => 'Popular Plan Border',
            'exercise_card_difficulty_beginner' => 'Beginner Difficulty',
            'exercise_card_difficulty_intermediate' => 'Intermediate Difficulty',
            'exercise_card_difficulty_advanced' => 'Advanced Difficulty',
        ],
    ];
}
?>
