<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Function to check license validity
function campaign_excel_is_license_valid($key) {
    return (strlen($key) === 14 && $key === 'PREMIUMACCESS1'); // Custom validation logic
}

// AJAX handler for license validation
function campaign_excel_validate_license() {
    check_ajax_referer('campaign_excel_license_nonce', 'security');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
    }

    $license_key = isset($_POST['license_key']) ? sanitize_text_field($_POST['license_key']) : '';

    if (campaign_excel_is_license_valid($license_key)) {
        update_option('campaign_excel_license_key', $license_key);
        wp_send_json_success(['message' => 'License activated successfully!']);
    } else {
        wp_send_json_error(['message' => 'Invalid license key!']);
    }
}
add_action('wp_ajax_validate_license', 'campaign_excel_validate_license');

