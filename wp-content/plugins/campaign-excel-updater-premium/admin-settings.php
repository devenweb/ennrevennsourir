<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register the settings section
function campaign_excel_register_license_settings() {
    register_setting('campaign_excel_license_group', 'campaign_excel_license_key');
    
    add_settings_section(
        'campaign_excel_license_section',
        'License Activation',
        'campaign_excel_license_section_callback',
        'campaign-excel-license'
    );

    add_settings_field(
        'campaign_excel_license_key',
        'License Key',
        'campaign_excel_license_key_callback',
        'campaign-excel-license',
        'campaign_excel_license_section'
    );
}
add_action('admin_init', 'campaign_excel_register_license_settings');

function campaign_excel_license_section_callback() {
    echo '<p>Enter your license key to activate premium features.</p>';
}

function campaign_excel_license_key_callback() {
    $license_key = get_option('campaign_excel_license_key', '');
    ?>
    <input type="text" id="license_key" name="campaign_excel_license_key" value="<?php echo esc_attr($license_key); ?>" maxlength="14" />
    <button id="activate-license" class="button button-primary">Activate License</button>
    <script>
        var campaign_excel_license_nonce = "<?php echo wp_create_nonce('campaign_excel_license_nonce'); ?>";
    </script>
    <?php
}
