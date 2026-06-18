<?php
/**
 * DB Maintenance: Clear Transients
 * Run from terminal: php scripts/clear-transients.php
 */

require_once(__DIR__ . '/../wp-load.php');

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the CLI.');
}

global $wpdb;
$count = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");

echo "Success: Cleared $count transients from the database.\n";
