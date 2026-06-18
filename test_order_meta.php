<?php
define('WP_USE_THEMES', false);
require_once('C:\Users\deven\Local Sites\ennrev\app\public\wp-load.php');

global $wpdb;
$results = $wpdb->get_results("
    SELECT p.ID, pm.meta_key, pm.meta_value 
    FROM {$wpdb->prefix}posts p 
    JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id 
    WHERE p.post_type = 'shop_order' 
    AND pm.meta_key IN ('_created_via', '_payment_method') 
    ORDER BY p.ID DESC 
    LIMIT 20
");

foreach ($results as $row) {
    echo "Order ID: {$row->ID} | Key: {$row->meta_key} | Value: {$row->meta_value}\n";
}
