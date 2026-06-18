<?php
/*
Plugin Name: Campaign Excel Updater - Premium Wrapper
Description: Adds licensing, security, and premium features to the Campaign Excel Updater plugin.
Author: Deven Pawaray
Version: 1.0
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('CAMPAIGN_EXCEL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CAMPAIGN_EXCEL_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once CAMPAIGN_EXCEL_PLUGIN_DIR . 'admin-settings.php';
require_once CAMPAIGN_EXCEL_PLUGIN_DIR . 'license-handler.php';

// Add admin menu item for license activation
function campaign_excel_add_license_menu() {
    add_options_page(
        'Campaign Excel License',
        'Campaign Excel License',
        'manage_options',
        'campaign-excel-license',
        'campaign_excel_render_license_page'
    );
}
add_action('admin_menu', 'campaign_excel_add_license_menu');

// Render license activation page
function campaign_excel_render_license_page() {
    ?>
    <div class="wrap">
        <h1>Campaign Excel Updater - License</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('campaign_excel_license_group');
            do_settings_sections('campaign-excel-license');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Ensure license is checked on plugin load
function campaign_excel_check_license_on_load() {
    $license_key = get_option('campaign_excel_license_key');

    if (!$license_key || !campaign_excel_is_license_valid($license_key)) {
        add_action('admin_notices', 'campaign_excel_license_notice');
    }
}
add_action('admin_init', 'campaign_excel_check_license_on_load');

// Display admin notice for missing license
function campaign_excel_license_notice() {
    echo '<div class="notice notice-warning"><p>Please enter a valid license key for Campaign Excel Updater Premium.</p></div>';
}
