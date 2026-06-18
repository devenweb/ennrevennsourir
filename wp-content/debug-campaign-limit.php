<?php
/**
 * Debug script to test homepage campaign limit
 * Place in wp-content/ and access via browser
 */

require_once('../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Access denied');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Homepage Campaign Limit Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .debug { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #0073aa; }
        .success { border-left-color: #46b450; }
        .error { border-left-color: #dc3232; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Homepage Campaign Limit Debug</h1>
    
    <div class="debug">
        <h3>Current Page Detection</h3>
        <p><strong>is_front_page():</strong> <?php echo is_front_page() ? '✓ TRUE' : '✗ FALSE'; ?></p>
        <p><strong>is_home():</strong> <?php echo is_home() ? '✓ TRUE' : '✗ FALSE'; ?></p>
        <p><strong>Current URL:</strong> <?php echo home_url($_SERVER['REQUEST_URI']); ?></p>
        <p><strong>Front Page ID:</strong> <?php echo get_option('page_on_front'); ?></p>
        <p><strong>Current Page ID:</strong> <?php echo get_the_ID(); ?></p>
    </div>
    
    <div class="debug">
        <h3>Plugin Option</h3>
        <p><strong>wpcf_listing_post_number:</strong> <?php echo get_option('wpcf_listing_post_number', 'NOT SET'); ?></p>
    </div>
    
    <div class="debug">
        <h3>Filter Test</h3>
        <?php
        // Test the shortcode_atts filter
        $test_atts = array('number' => 999);
        $filtered = apply_filters('shortcode_atts_wpcf_listing', $test_atts, array(), array(), 'wpcf_listing');
        ?>
        <p><strong>Original number:</strong> 999</p>
        <p><strong>Filtered number:</strong> <?php echo $filtered['number']; ?></p>
        <p><strong>Filter working:</strong> <?php echo $filtered['number'] == 12 ? '✓ YES' : '✗ NO'; ?></p>
    </div>
    
    <div class="debug">
        <h3>Active Filters</h3>
        <?php
        global $wp_filter;
        if (isset($wp_filter['shortcode_atts_wpcf_listing'])) {
            echo '<p>✓ shortcode_atts_wpcf_listing filter is registered</p>';
            echo '<pre>';
            print_r($wp_filter['shortcode_atts_wpcf_listing']);
            echo '</pre>';
        } else {
            echo '<p class="error">✗ shortcode_atts_wpcf_listing filter NOT registered</p>';
        }
        
        if (isset($wp_filter['pre_option_wpcf_listing_post_number'])) {
            echo '<p>✓ pre_option_wpcf_listing_post_number filter is registered</p>';
        } else {
            echo '<p class="error">✗ pre_option_wpcf_listing_post_number filter NOT registered</p>';
        }
        ?>
    </div>
    
    <div class="debug">
        <h3>Shortcode Test</h3>
        <?php
        echo '<p>Rendering shortcode: <code>[wpcf_listing]</code></p>';
        echo '<div style="background: #f9f9f9; padding: 10px; margin: 10px 0;">';
        echo do_shortcode('[wpcf_listing]');
        echo '</div>';
        ?>
    </div>
    
    <div class="debug">
        <h3>Recommendations</h3>
        <ul>
            <li>If <code>is_front_page()</code> is FALSE, the filter won't apply</li>
            <li>Clear all caches (WordPress, browser, server)</li>
            <li>Make sure you're viewing the actual homepage</li>
            <li>Check if Elementor or page builder is overriding the query</li>
        </ul>
    </div>
    
</body>
</html>
