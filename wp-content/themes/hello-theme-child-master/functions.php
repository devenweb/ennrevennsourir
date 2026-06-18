<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0');

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles()
{
    // Google Font: Outfit (primary brand font)
    wp_enqueue_style(
        'ennrev-outfit-font',
        'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap',
        [],
        null
    );

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
            'ennrev-outfit-font',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

    // Premium JS: highlight selected predefined donation button
    $premium_js = "
    document.addEventListener('DOMContentLoaded', function() {
        // Predefined pledge amount active state
        var pledgeBtns = document.querySelectorAll('.wpcf_predefined_pledge_amount a');
        var donateInput = document.querySelector('.wpneo_donate_amount_field');
        pledgeBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                pledgeBtns.forEach(function(b){ b.classList.remove('selected'); });
                btn.classList.add('selected');
            });
        });

        // Animate progress bars on page load
        var bars = document.querySelectorAll('#neo-progressbar > div, .wpneo-progress-percent');
        bars.forEach(function(bar) {
            var width = bar.style.width;
            bar.style.width = '0';
            setTimeout(function() { bar.style.width = width; }, 300);
        });
    });
    ";
    wp_add_inline_script( 'jquery', $premium_js );
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20);








/**
 * CUSTOM SHORTCODE OVERRIDE: [wpcf_popular_campaigns]
 * Preserves 'limit' and 'category' support even if WPCrowdfunding is updated.
 */
if ( ! function_exists( 'popular_campaigns_callback_custom' ) ) {
    function popular_campaigns_callback_custom( $atts, $shortcode ) {
        $a = shortcode_atts(array(
            'number'        => -1,
            'limit'         => -1,
            'order'         => 'DESC',
            'category'      => '',
        ), $atts, $shortcode );

        $posts_per_page = ($a['limit'] != -1) ? $a['limit'] : $a['number'];

        $paged = 1;
        if ( get_query_var('paged') ) {
            $paged = absint( get_query_var('paged') );
        } elseif (get_query_var('page')) {
            $paged = absint( get_query_var('page') );
        }

        $query_args = array(
            'post_type'             => 'product',
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 1,
            'meta_key'              => 'total_sales',
            'posts_per_page'        => (int)$posts_per_page,
            'paged'                 => $paged,
            'orderby'               => 'meta_value_num',
            'order'                 => $a['order'],
            'meta_query' => array(
                array(
                    'key'           => 'total_sales',
                    'value'         => 0,
                    'compare'       => '>',
                )
            ),
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'crowdfunding',
                ),
            )
        );

        if (!empty($a['category'])) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => explode(',', $a['category']),
            );
        }

        query_posts($query_args);

        ob_start();
        if (function_exists('wpcf_function')) {
            wpcf_function()->template('wpneo-listing');
        }
        $html = ob_get_clean();
        wp_reset_query();
        return $html;
    }
}

add_action('init', function() {
    remove_shortcode('wpcf_popular_campaigns');
    add_shortcode('wpcf_popular_campaigns', 'popular_campaigns_callback_custom');
}, 20);

/**
 * Shortcode to display WPCF Dashboard if logged in, otherwise Registration form.
 */
if ( ! function_exists( 'custom_registration_dashboard_display' ) ) {
    function custom_registration_dashboard_display($atts) {
        if (is_user_logged_in()) {
            return do_shortcode('[wpcf_dashboard]');
        } else {
            return do_shortcode('[wpcf_registration]');
        }
    }
}
add_shortcode('registration_or_dashboard', 'custom_registration_dashboard_display');

/**
 * BEGIN: Mobile Field for Registration and Profile
 * Added to extend WP Crowdfunding functionality.
 */

// 1. Add mobile field to registration form
add_filter('wpcf_user_registration_fields', function($fields) {
    $fields[] = array(
        'id'            => 'mobile',
        'label'         => __('Mobile Number *', 'wp-crowdfunding'),
        'type'          => 'text',
        'placeholder'   => __('Enter Mobile Number eg. 0023059099219', 'wp-crowdfunding'),
        'value'         => '',
        'class'         => 'required',
        'warpclass'     => 'wpneo-first-half',
        'autocomplete'  => 'off',
        'pattern'       => '[0-9]{8}',
        'maxlength'     => '12'
    );
    return $fields;
});

// 2. Add validation for mobile field
add_action('wpcf_before_user_registration_action', function() {
    if (empty($_POST['mobile'])) {
        global $reg_errors;
        if (is_object($reg_errors) && method_exists($reg_errors, 'add')) {
            $reg_errors->add('mobile', __('Mobile number is required', 'wp-crowdfunding'));
        }
    } elseif (!preg_match('/^[0-9]{8,12}$/', $_POST['mobile'])) {
        global $reg_errors;
        if (is_object($reg_errors) && method_exists($reg_errors, 'add')) {
            $reg_errors->add('mobile', __('Please enter a valid mobile number', 'wp-crowdfunding'));
        }
    }
});

// 3. Save mobile number after registration
add_action('wpcf_after_user_registration', function($user_id) {
    if (isset($_POST['mobile'])) {
        $mobile = sanitize_text_field($_POST['mobile']);
        update_user_meta($user_id, 'mobile_number', $mobile);
    }
});

// 4. Add mobile number to user profile
if ( ! function_exists( 'add_mobile_to_profile' ) ) {
    function add_mobile_to_profile($user) {
        ?>
        <h3><?php _e('Mobile Number', 'wp-crowdfunding'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="mobile_number"><?php _e('Mobile Number', 'wp-crowdfunding'); ?></label></th>
                <td>
                    <input type="tel" 
                           name="mobile_number" 
                           id="mobile_number" 
                           value="<?php echo esc_attr(get_user_meta($user->ID, 'mobile_number', true)); ?>" 
                           class="regular-text"
                           pattern="[0-9]{8,12}"
                           maxlength="12"
                    />
                    <p class="description"><?php _e('Enter mobile number', 'wp-crowdfunding'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
}
add_action('show_user_profile', 'add_mobile_to_profile');
add_action('edit_user_profile', 'add_mobile_to_profile');

// 5. Save mobile number in profile
if ( ! function_exists( 'save_mobile_in_profile' ) ) {
    function save_mobile_in_profile($user_id) {
        if (current_user_can('edit_user', $user_id)) {
            if (isset($_POST['mobile_number'])) {
                $mobile = sanitize_text_field($_POST['mobile_number']);
                update_user_meta($user_id, 'mobile_number', $mobile);
            }
        }
    }
}
add_action('personal_options_update', 'save_mobile_in_profile');
add_action('edit_user_profile_update', 'save_mobile_in_profile');

// Add mobile field after Last Name in My Information Dashboard
add_filter('wpcf_dashboard_fields', function($fields) {
    $user_id = get_current_user_id();
    $mobile = get_user_meta($user_id, 'mobile_number', true);
    
    $new_fields = array();
    foreach ($fields as $field) {
        $new_fields[] = $field;
        if ($field['id'] === 'lname') {
            $new_fields[] = array(
                'id' => 'mobile_number',
                'label' => __('Mobile Number', 'wp-crowdfunding'),
                'type' => 'text',
                'value' => $mobile,
                'placeholder' => __('Enter Mobile Number', 'wp-crowdfunding'),
                'class' => 'required',
                'pattern' => '[0-9]{8,12}',
                'maxlength' => '12'
            );
        }
    }
    return $new_fields;
});

// Save mobile number along with other dashboard fields
add_action('wpcf_update_dashboard_fields', function($user_id) {
    if (isset($_POST['mobile_number'])) {
        $mobile = sanitize_text_field($_POST['mobile_number']);
        update_user_meta($user_id, 'mobile_number', $mobile);
    }
});

/**
 * Add GPT-Trainer ChatBot widget to the footer.
 */
if ( ! function_exists( 'add_chatbot_widget_to_footer' ) ) {
    function add_chatbot_widget_to_footer() {
        ?>
        <script>
            window.GPTTConfig = {
                uuid: "f9da9a58a5104e33affca15197fc33e2"
            }
        </script>
        <script
            src="https://app.gpt-trainer.com/widget-asset.min.js"
            defer>
        </script>
        <?php
    }
}
add_action('wp_footer', 'add_chatbot_widget_to_footer');

/**
 * CUSTOM SHORTCODE: [wpcf_cat_order_campaigns]
 * Lists campaigns from a selected category, ordered by date.
 * Example: [wpcf_cat_order_campaigns cat="category-slug" number="3" columns="3"]
 */
if ( ! class_exists( 'Cat_Order_Campaigns_Custom' ) ) {
    class Cat_Order_Campaigns_Custom {

        function __construct() {
            add_shortcode( 'wpcf_cat_order_campaigns', array( $this, 'cat_order_campaigns_callback' ) );
        }

        function cat_order_campaigns_callback( $atts, $shortcode ) {
            
            $a = shortcode_atts(array(
                'cat'     => null,
                'number'  => -1,
                'order'   => 'DESC',
                'rows'    => 1,
                'columns' => 1,
            ), $atts, $shortcode );

            $paged = max(1, get_query_var('paged', get_query_var('page', 1)));

            $query_args = array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => $a['number'],
                'paged'          => $paged,
                'orderby'        => 'date',
                'order'          => $a['order'],
                'tax_query'      => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'product_type',
                        'field'    => 'slug',
                        'terms'    => 'crowdfunding',
                    ),
                ),
            );

            if ( $a['cat'] ) {
                $cat_array                 = explode( ',', $a['cat'] );
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $cat_array,
                );
            }

            $query = new WP_Query($query_args);
            ob_start();

            if ($query->have_posts()) {
                echo '<div class="campaign-grid wpcf-responsive-grid" style="gap: 20px;">';
                while ($query->have_posts()) {
                    $query->the_post();
                    echo '<div class="campaign-item">';
                    if (function_exists('wpcf_function')) {
                        wpcf_function()->template('wpneo-listing');
                    } else {
                        echo '<h3>' . get_the_title() . '</h3>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No campaigns found.</p>';
            }
            
            $html = ob_get_clean();
            wp_reset_postdata();
            return $html;
        }
    }

    new Cat_Order_Campaigns_Custom();
}

/**
 * CUSTOM WOOCOMMERCE CHECKOUT MODIFICATIONS
 * Simplifies checkout for donations by removing unnecessary fields and merging name fields.
 */

// 1. Remove unnecessary Billing Fields
add_filter( 'woocommerce_billing_fields', function( $fields ) {
    unset($fields['billing_country']);   // Remove Country
    unset($fields['billing_address_1']); // Remove Street Address
    unset($fields['billing_address_2']); // Remove Address Line 2 (optional)
    unset($fields['billing_city']);      // Remove Town/City
    return $fields;
});

// 2. Change "Your order" heading to "Your Donations"
add_action( 'wp_footer', function() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('#order_review_heading').text('Your Donations');
        });
    </script>
    <?php
});

// 3. Move the heading 25px to the right for better alignment
add_action( 'wp_footer', function() {
    ?>
    <style type="text/css">
        #order_review_heading {
            margin-left: 25px;
        }
    </style>
    <?php
});


// 4. Simplify First/Last Name into a single Full Name field
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    // Remove default first name and last name fields
    unset( $fields['billing']['billing_first_name'] );
    unset( $fields['billing']['billing_last_name'] );

    // Add a new Full Name field
    $fields['billing']['billing_full_name'] = array(
        'type'        => 'text',
        'get_id'      => 'full_name',
        'label'       => __('Full Name', 'woocommerce'),
        'placeholder' => __('Enter your full name', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-wide'),
        'priority'    => 10,
    );

    return $fields;
});

// 5. Modify the email field to be full-width
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    if (isset($fields['billing']['billing_email'])) {
        $fields['billing']['billing_email']['class'] = array('form-row-wide');
    }
    return $fields;
});

// 6. Split Full Name back into First/Last Name for compatibility
add_action( 'woocommerce_checkout_process', function() {
    if ( isset($_POST['billing_full_name']) ) {
        $full_name = sanitize_text_field($_POST['billing_full_name']);
        $name_parts = explode(' ', $full_name, 2);

        $_POST['billing_first_name'] = $name_parts[0];
        $_POST['billing_last_name']  = isset($name_parts[1]) ? $name_parts[1] : '-';
    }
});

// 7. Save Full Name Correctly in Orders
add_action( 'woocommerce_checkout_update_order_meta', function( $order_id ) {
    if ( isset($_POST['billing_full_name']) ) {
        $full_name = sanitize_text_field($_POST['billing_full_name']);
        
        // Save the raw full name for display
        update_post_meta($order_id, 'billing_full_name', $full_name);
        
        // Split for WooCommerce standard meta consistency
        $name_parts = explode(' ', $full_name, 2);
        update_post_meta($order_id, '_billing_first_name', $name_parts[0]);
        update_post_meta($order_id, '_billing_last_name', isset($name_parts[1]) ? $name_parts[1] : '');
    }
});

// 8. Display Full Name in Admin Order Details
if ( ! function_exists( 'display_full_name_in_admin_order' ) ) {
    function display_full_name_in_admin_order( $order ) {
        $full_name = get_post_meta($order->get_id(), 'billing_full_name', true);
        if ( $full_name ) {
            echo '<p><strong>Full Name:</strong> ' . esc_html($full_name) . '</p>';
        }
    }
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_full_name_in_admin_order' );

// 9. Make the email field optional
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    if (isset($fields['billing']['billing_email'])) {
        $fields['billing']['billing_email']['required'] = false;
    }
    return $fields;
});


add_filter( 'woocommerce_billing_fields', function( $fields ) {
    if ( isset( $fields['billing_email'] ) ) {
        $fields['billing_email']['required'] = false;
    }
    return $fields;
});

/**
 * Global option to remove all billing fields from checkout.
 * NOTE: This will remove everything in the 'billing' section.
 */
if ( ! function_exists( 'hide_billing_details' ) ) {
    function hide_billing_details( $fields ) {
        // Remove all billing fields
        unset($fields['billing']);
        return $fields;
    }
}
add_filter( 'woocommerce_checkout_fields', 'hide_billing_details', 20 );

/**
 * Display a custom message before the checkout form.
 */
if ( ! function_exists( 'display_custom_text_before_checkout' ) ) {
    function display_custom_text_before_checkout() {
        echo '<div class="custom-checkout-message" style="margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #d9534f;">';
        echo '<p>To complete your donation smoothly, please make sure you have your credit card with you.</p>';
        echo '</div>';
    }
}
add_action( 'woocommerce_before_checkout_form', 'display_custom_text_before_checkout' );

/**
 * POLYLANG WOOCOMMERCE INTEGRATION
 * Ensures WooCommerce points to the correct translated pages (Cart, Checkout, etc.)
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    if (function_exists('pll_get_post')) { // is Polylang activated?
        
        // Add filters to ensure WooCommerce gets the correct translated pages
        add_filter('woocommerce_get_cart_page_id', 'pll_woocommerce_get_page_id');
        add_filter('woocommerce_get_checkout_page_id', 'pll_woocommerce_get_page_id');
        add_filter('woocommerce_get_edit_address_page_id', 'pll_woocommerce_get_page_id');
        add_filter('woocommerce_get_myaccount_page_id', 'pll_woocommerce_get_page_id');
        add_filter('woocommerce_get_pay_page_id', 'pll_woocommerce_get_page_id');
        add_filter('woocommerce_get_shop_page_id', 'pll_woocommerce_get_page_id');
        add_filter('woocommerce_get_terms_page_id', 'pll_woocommerce_get_page_id');
        add_filter('woocommerce_get_view_order_page_id', 'pll_woocommerce_get_page_id');

        // Function to return the translated page ID
        if ( ! function_exists( 'pll_woocommerce_get_page_id' ) ) {
            function pll_woocommerce_get_page_id($id) {
                return pll_get_post($id); // translate the page to the current language
            }
        }
    }
}

/**
 * LATEST UI: Scroll Reveal Script
 * Automatically adds reveal animations to common elements.
 */
add_action('wp_footer', function() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-apply reveal class to common elements
            const selectors = [
                '.campaign-item', 
                '.elementor-section', 
                '.wpneo-listing-content',
                '.woocommerce-LoopProduct-link'
            ];
            
            selectors.forEach(selector => {
                document.querySelectorAll(selector).forEach(el => {
                    el.classList.add('reveal-on-scroll');
                });
            });

            // Intersection Observer for the reveal effect
            const observerOptions = {
                threshold: 0.15,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        // Once revealed, no need to observe anymore
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
    <?php
}, 100);


/**
 * Precision Header Fix - Robust Full-Width & No-Wrap Edition
 */
if (!function_exists('ennrev_precision_header_fix')) {
    add_action('wp_head', function() {
        ?>
        <style id="ennrev-header-precision-fix">
            /* 1. TOP BAR (PINK) - TRUE FULL-WIDTH & NO-WRAP */
            .elementor-50 .elementor-element.elementor-element-1046487 {
                padding: 1px 0 !important;
                min-height: 40px !important;
                width: 100vw !important;
                max-width: 100vw !important;
                margin-left: calc(-50vw + 50%) !important;
                margin-right: calc(-50vw + 50%) !important;
                position: relative !important;
                left: 0 !important;
                right: 0 !important;
                overflow: hidden !important;
            }
            
            .elementor-50 .elementor-element.elementor-element-1046487 > .elementor-container {
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 20px !important;
                padding-right: 20px !important;
                display: flex !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                justify-content: space-between !important;
            }

            /* 2. MENU BAR (WHITE) - AGGRESSIVE HEIGHT REDUCTION */
            .elementor-50 .elementor-element.elementor-element-39c6171 {
                padding-top: 0px !important;
                padding-bottom: 0px !important;
                min-height: auto !important;
                height: auto !important;
            }

            /* Logo Column & Wrapper - TOTAL STRIP (No Padding/Margin) */
            .elementor-50 .elementor-element.elementor-element-7bdf666,
            .elementor-50 .elementor-element.elementor-element-7bdf666 > .elementor-widget-wrap,
            .elementor-50 .elementor-element.elementor-element-7eeb4ad .elementor-widget-container {
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
            }

            /* Main Menu Column Padding Fix */
            .elementor-50 .elementor-element.elementor-element-26ca28c > .elementor-widget-wrap {
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
                display: flex !important;
                align-items: center !important;
            }

            /* Target Logo Image strictly */
            .elementor-50 .elementor-element.elementor-element-7eeb4ad img {
                max-height: 45px !important; /* Slightly larger but flush */
                width: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                display: block !important;
            }

            /* 4. DUPLICATE NAV FIX
               Two Elementor nav-menu widgets are stacked in the header.
               The first (26ca28c) is desktop. The second (bfcdbd3) is
               a Polylang-generated duplicate — hide it on desktop. */
            .elementor-element-bfcdbd3 {
                display: none !important;
            }

            /* Also hide any second elementor-widget-nav-menu in the header */
            [data-elementor-type="header"] .elementor-widget-nav-menu + .elementor-widget-nav-menu {
                display: none !important;
            }
        </style>
        <?php
    }, 999);
}
