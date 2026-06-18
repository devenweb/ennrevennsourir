<?php
/**
 * Audit Uploads Script
 * Identifies orphaned and duplicate files in wp-content/uploads.
 */

// Load WordPress
$path = dirname(__FILE__);
if (file_exists($path . '/wp-load.php')) {
    require_once($path . '/wp-load.php');
} else {
    die("Could not find wp-load.php at $path\n");
}

function audit_uploads() {
    global $wpdb;

    echo "--- Auditing Database Attachments ---\n";
    $attachments = $wpdb->get_results("SELECT post_title, guid FROM {$wpdb->posts} WHERE post_type = 'attachment'", ARRAY_A);
    $db_files = [];
    foreach ($attachments as $att) {
        $file_path = str_replace(get_site_url() . '/wp-content/uploads/', '', $att['guid']);
        $db_files[$file_path] = $att['post_title'];
    }
    echo "Found " . count($db_files) . " attachments in database.\n\n";

    echo "--- Scanning Uploads Directory ---\n";
    $upload_dir = wp_upload_dir()['basedir'];
    $all_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_dir));
    
    $file_hashes = [];
    $orphans = [];
    $total_files = 0;

    foreach ($all_files as $file) {
        if ($file->isDir()) continue;
        
        $total_files++;
        $full_path = $file->getPathname();
        $relative_path = str_replace($upload_dir . DIRECTORY_SEPARATOR, '', $full_path);
        // Normalize slashes
        $relative_path = str_replace(DIRECTORY_SEPARATOR, '/', $relative_path);

        // 1. Check if orphan
        $is_thumb = preg_match('/-\d+x\d+\.(jpg|jpeg|png|gif|webp)$/i', $relative_path);
        $base_file = preg_replace('/-\d+x\d+(\.(jpg|jpeg|png|gif|webp))$/i', '$1', $relative_path);

        if (!isset($db_files[$relative_path]) && !isset($db_files[$base_file])) {
            // Keep some exclusions
            if (strpos($relative_path, 'elementor/') === 0 || strpos($relative_path, 'fonts/') === 0) continue;
            $orphans[] = $relative_path;
        }

        // 2. Check for dupes (only for original files, ignore thumbnails for hash check)
        if (!$is_thumb && !strpos($relative_path, 'elementor/')) {
            $hash = md5_file($full_path);
            $file_hashes[$hash][] = $relative_path;
        }
    }

    echo "Total files scanned: $total_files\n";
    echo "Orphaned files identified: " . count($orphans) . "\n\n";

    if (!empty($orphans)) {
        echo "--- ORPHANED FILES (Sample 10) ---\n";
        foreach (array_slice($orphans, 0, 10) as $o) echo $o . "\n";
    }

    echo "\n--- DUPLICATE FILES ---\n";
    $dupe_count = 0;
    foreach ($file_hashes as $hash => $paths) {
        if (count($paths) > 1) {
            $dupe_count++;
            echo "Hash: $hash\n";
            foreach ($paths as $p) echo "  - $p\n";
        }
    }
    echo "Total duplicate groups found: $dupe_count\n";
}

audit_uploads();
