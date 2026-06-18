<?php
require_once('wp-load.php');

global $wpdb;
$table_name = $wpdb->prefix . 'snippets';

// Check if table exists
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    echo "Table $table_name does not exist.";
    exit;
}

$snippets = $wpdb->get_results("SELECT name, code FROM $table_name WHERE active = 1");

if ($snippets) {
    foreach ($snippets as $snippet) {
        echo "/* --- SNIPPET: {$snippet->name} --- */\n";
        echo $snippet->code . "\n\n";
    }
} else {
    echo "No active snippets found.";
}
