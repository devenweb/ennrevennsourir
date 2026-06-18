<?php
/**
 * WooCommerce Compatibility
 */

// General WooCommerce wrapper
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'kopizon_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'kopizon_wrapper_end', 10);

function kopizon_wrapper_start() {
    echo '<section class="py-5"><div class="container">';
}

function kopizon_wrapper_end() {
    echo '</div></section>';
}
