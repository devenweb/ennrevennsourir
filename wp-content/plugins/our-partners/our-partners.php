<?php
/**
 * Plugin Name: Our Partners
 * Description: Enhanced Elementor widget to display partners list
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: our-partners
 */

if (!defined('ABSPATH')) {
    exit;
}

function register_our_partners_widget($widgets_manager) {
    require_once(__DIR__ . '/widgets/our-partners-widget.php');
    $widgets_manager->register(new \Elementor_Our_Partners_Widget());
}

function our_partners_dependency_check() {
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', function() {
            $message = sprintf(
                esc_html__('Our Partners widget requires Elementor plugin. %1$s', 'our-partners'),
                '<a href="' . esc_url(admin_url('plugin-install.php?s=Elementor&tab=search&type=term')) . '">Install Elementor</a>'
            );
            echo '<div class="notice notice-warning is-dismissible"><p>' . $message . '</p></div>';
        });
        return;
    }
    
    add_action('elementor/widgets/register', 'register_our_partners_widget');
}
add_action('plugins_loaded', 'our_partners_dependency_check');

function our_partners_styles() {
    wp_enqueue_style(
        'our-partners',
        plugins_url('assets/style.css', __FILE__),
        [],
        '1.0.0'
    );
    // Enqueue FontAwesome for icons
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'
    );
}
add_action('wp_enqueue_scripts', 'our_partners_styles');