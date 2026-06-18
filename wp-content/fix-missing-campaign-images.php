<?php
/**
 * WP Crowdfunding - Enhanced Diagnostic Script
 * Checks all possible post types and meta configurations
 */

// Load WordPress
require_once('../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>WP Crowdfunding - Enhanced Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #0073aa; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .status-ok { color: #46b450; font-weight: bold; }
        .status-error { color: #dc3232; font-weight: bold; }
        .code { background: #272822; color: #f8f8f2; padding: 15px; border-radius: 4px; overflow-x: auto; }
        img { max-width: 100px; height: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 WP Crowdfunding - Enhanced Diagnostic</h1>
        
        <?php
        // 1. Check all product post types
        echo '<div class="section">';
        echo '<h2>1️⃣ All Products (Any Type)</h2>';
        
        $all_products = new WP_Query(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        echo '<p><strong>Total Products Found:</strong> ' . $all_products->found_posts . '</p>';
        
        if ($all_products->have_posts()) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Title</th><th>Type</th><th>Featured Image</th><th>Meta Keys</th></tr>';
            
            while ($all_products->have_posts()) {
                $all_products->the_post();
                $post_id = get_the_ID();
                $product = wc_get_product($post_id);
                $product_type = $product ? $product->get_type() : 'N/A';
                $has_thumb = has_post_thumbnail($post_id);
                
                // Get all meta keys for this post
                $all_meta = get_post_meta($post_id);
                $meta_keys = array_keys($all_meta);
                $relevant_meta = array_filter($meta_keys, function($key) {
                    return strpos($key, 'wpcf') !== false || strpos($key, 'crowdfund') !== false;
                });
                
                echo '<tr>';
                echo '<td>' . $post_id . '</td>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . $product_type . '</td>';
                echo '<td>';
                if ($has_thumb) {
                    echo '<span class="status-ok">✓ Yes</span><br>';
                    echo get_the_post_thumbnail($post_id, 'thumbnail');
                } else {
                    echo '<span class="status-error">✗ No</span>';
                }
                echo '</td>';
                echo '<td>' . implode(', ', $relevant_meta) . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            wp_reset_postdata();
        } else {
            echo '<p class="status-error">No products found!</p>';
        }
        echo '</div>';
        
        // 2. Check for custom post types
        echo '<div class="section">';
        echo '<h2>2️⃣ All Post Types in Database</h2>';
        
        global $wpdb;
        $post_types = $wpdb->get_results("
            SELECT DISTINCT post_type, COUNT(*) as count 
            FROM {$wpdb->posts} 
            WHERE post_status = 'publish' 
            GROUP BY post_type
            ORDER BY count DESC
        ");
        
        echo '<table>';
        echo '<tr><th>Post Type</th><th>Count</th></tr>';
        foreach ($post_types as $pt) {
            echo '<tr><td>' . esc_html($pt->post_type) . '</td><td>' . $pt->count . '</td></tr>';
        }
        echo '</table>';
        echo '</div>';
        
        // 3. Search for crowdfunding-related meta keys
        echo '<div class="section">';
        echo '<h2>3️⃣ Crowdfunding Meta Keys</h2>';
        
        $cf_meta = $wpdb->get_results("
            SELECT DISTINCT meta_key, COUNT(*) as count 
            FROM {$wpdb->postmeta} 
            WHERE meta_key LIKE '%crowdfund%' OR meta_key LIKE '%wpcf%'
            GROUP BY meta_key
            ORDER BY count DESC
        ");
        
        if ($cf_meta) {
            echo '<table>';
            echo '<tr><th>Meta Key</th><th>Count</th></tr>';
            foreach ($cf_meta as $meta) {
                echo '<tr><td>' . esc_html($meta->meta_key) . '</td><td>' . $meta->count . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="status-error">No crowdfunding meta keys found!</p>';
        }
        echo '</div>';
        
        // 4. Check WooCommerce product types
        echo '<div class="section">';
        echo '<h2>4️⃣ WooCommerce Product Types</h2>';
        
        $product_types = $wpdb->get_results("
            SELECT pm.meta_value as product_type, COUNT(*) as count
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm.meta_key = '_product_type'
            GROUP BY pm.meta_value
        ");
        
        if ($product_types) {
            echo '<table>';
            echo '<tr><th>Product Type</th><th>Count</th></tr>';
            foreach ($product_types as $type) {
                echo '<tr><td>' . esc_html($type->product_type) . '</td><td>' . $type->count . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No WooCommerce product types found.</p>';
        }
        echo '</div>';
        
        // 5. Direct database query for campaigns
        echo '<div class="section">';
        echo '<h2>5️⃣ Search for "Zohra", "Issac", "Hloe"</h2>';
        
        $search_campaigns = $wpdb->get_results("
            SELECT p.ID, p.post_title, p.post_type, p.post_status,
                   (SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = p.ID AND meta_key = '_thumbnail_id' LIMIT 1) as thumbnail_id
            FROM {$wpdb->posts} p
            WHERE p.post_status = 'publish'
            AND (p.post_title LIKE '%Zohra%' OR p.post_title LIKE '%Issac%' OR p.post_title LIKE '%Hloe%')
        ");
        
        if ($search_campaigns) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Title</th><th>Post Type</th><th>Thumbnail ID</th><th>Status</th><th>Edit Link</th></tr>';
            foreach ($search_campaigns as $campaign) {
                echo '<tr>';
                echo '<td>' . $campaign->ID . '</td>';
                echo '<td>' . esc_html($campaign->post_title) . '</td>';
                echo '<td>' . $campaign->post_type . '</td>';
                echo '<td>' . ($campaign->thumbnail_id ? $campaign->thumbnail_id : 'N/A') . '</td>';
                echo '<td>' . ($campaign->thumbnail_id ? '<span class="status-ok">✓ Has Image</span>' : '<span class="status-error">✗ Missing</span>') . '</td>';
                echo '<td><a href="' . admin_url('post.php?post=' . $campaign->ID . '&action=edit') . '" target="_blank">Edit</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="status-error">No campaigns found with these names!</p>';
        }
        echo '</div>';
        
        // 6. Check plugin activation
        echo '<div class="section">';
        echo '<h2>6️⃣ Plugin Status</h2>';
        
        $active_plugins = get_option('active_plugins');
        $cf_plugin_active = false;
        
        echo '<ul>';
        foreach ($active_plugins as $plugin) {
            if (strpos($plugin, 'crowdfund') !== false) {
                echo '<li class="status-ok">✓ ' . esc_html($plugin) . '</li>';
                $cf_plugin_active = true;
            }
        }
        echo '</ul>';
        
        if (!$cf_plugin_active) {
            echo '<p class="status-error">⚠️ No crowdfunding plugin appears to be active!</p>';
        }
        echo '</div>';
        
        // 7. Recommendations
        echo '<div class="section">';
        echo '<h2>💡 Recommendations</h2>';
        echo '<ol>';
        echo '<li>Check the "All Products" section above to see if your campaigns are listed</li>';
        echo '<li>Look at the "Search for Zohra, Issac, Hloe" section to find specific campaigns</li>';
        echo '<li>If campaigns are found but marked as "Missing Image", click the Edit link to add featured images</li>';
        echo '<li>If no campaigns are found, they might be in a different post type or the plugin might not be properly configured</li>';
        echo '</ol>';
        echo '</div>';
        ?>
        
    </div>
</body>
</html>
