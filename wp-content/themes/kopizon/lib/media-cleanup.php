<?php
/**
 * Media Cleanup Utility
 * Identifies orphan files (files not in DB) and duplicate images in wp-content/uploads.
 */

// Load WordPress environment
$path = $_SERVER['DOCUMENT_ROOT'];
if (file_exists($path . '/wp-load.php')) {
    require_once($path . '/wp-load.php');
} else {
    $path = dirname(__FILE__, 5);
    require_once($path . '/wp-load.php');
}

if (!is_user_logged_in() && !defined('WP_CLI')) {
    die('Unauthorized access.');
}

function kopizon_media_cleanup($dry_run = true) {
    global $wpdb;
    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'];

    echo "--- Media Cleanup Audit ---\n";
    echo "Base Directory: $base_dir\n";
    echo "Mode: " . ($dry_run ? "DRY RUN (No files will be deleted)" : "LIVE (DELETION ENABLED)") . "\n\n";

    // 1. Get all files in uploads
    $all_files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));
    $file_list = [];
    foreach ($all_files as $file) {
        if ($file->isFile()) {
            $file_list[] = str_replace('\\', '/', $file->getPathname());
        }
    }

    // 2. Get all attachments from DB
    $attachments = $wpdb->get_col("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file'");
    $db_files = [];
    foreach ($attachments as $rel_path) {
        $db_files[] = str_replace('\\', '/', $base_dir . '/' . $rel_path);
        
        // Also consider generated thumbnails (this is simplified, real logic would use wp_get_attachment_metadata)
    }

    // 3. Identify Orphans
    $orphans = [];
    foreach ($file_list as $file) {
        // Skip some standard WP files
        if (basename($file) === '.htaccess' || basename($file) === 'index.php') continue;
        
        $found = false;
        foreach ($db_files as $db_file) {
            if (strpos($file, $db_file) === 0) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $orphans[] = $file;
        }
    }

    echo "Found " . count($orphans) . " potential orphan files.\n";
    foreach ($orphans as $orphan) {
        echo "[ORPHAN] " . str_replace($base_dir, '', $orphan) . "\n";
        if (!$dry_run) {
            unlink($orphan);
        }
    }

    // 4. Identify Duplicates (by MD5)
    echo "\nScanning for duplicates...\n";
    $hashes = [];
    $duplicates = [];
    foreach ($file_list as $file) {
        if (getimagesize($file)) {
            $hash = md5_file($file);
            if (isset($hashes[$hash])) {
                $duplicates[] = [
                    'original' => $hashes[$hash],
                    'duplicate' => $file
                ];
            } else {
                $hashes[$hash] = $file;
            }
        }
    }

    echo "Found " . count($duplicates) . " duplicate images.\n";
    foreach ($duplicates as $dup) {
        echo "[DUPLICATE] " . str_replace($base_dir, '', $dup['duplicate']) . " (Same as " . str_replace($base_dir, '', $dup['original']) . ")\n";
    }

    echo "\nCleanup process complete.\n";
}

// Trigger
$dry_run = isset($_GET['dry_run']) || (isset($argv) && in_array('--dry-run', $argv)) || !isset($_GET['execute']);
kopizon_media_cleanup($dry_run);
