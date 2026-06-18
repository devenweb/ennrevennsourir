<?php
defined( 'ABSPATH' ) || exit;

/**
 * HOMEPAGE CAMPAIGN PAGINATION FIX
 * Target: /homepage/ or /en/homepage/
 */
$current_url = $_SERVER['REQUEST_URI'];
$is_homepage = (strpos($current_url, '/homepage/') !== false || is_front_page());

if ($is_homepage) {
    global $wp_query;
    // We only want to modify the specific campaign loop if a limit hasn't been explicitly set
    $current_limit = (int)$wp_query->get('posts_per_page');
    if ($wp_query->get('post_type') === 'product' && ($current_limit == -1 || $current_limit == get_option('posts_per_page') || $current_limit == 9)) {
        $paged = max(1, get_query_var('paged'));
        if (!$paged || $paged == 1) {
            $paged = max(1, get_query_var('page'));
        }
        $paged = max(1, $paged);

        // Force exactly 9 items per page for the main homepage listing
        $wp_query->query_vars['posts_per_page'] = 9;
        $wp_query->query_vars['paged'] = $paged;
        
        // Re-execute the query locally for this loop
        $wp_query->get_posts();
        
        // Recalculate totals for pagination
        if ($wp_query->found_posts > 0) {
            $wp_query->max_num_pages = ceil($wp_query->found_posts / 9);
        }
    }
}

$col_num = get_option('number_of_collumn_in_row', 3);
$number = array( "2"=>"two","3"=>"three","4"=>"four" );
?>

<div class="wpneo-wrapper">
    <div class="wpneo-container">
        <?php do_action('wpcf_campaign_listing_before_loop'); ?>
        <div class="wpneo-wrapper-inner">
            <?php if (have_posts()): ?>
                <?php
                $i = 1;
                while (have_posts()) : the_post();
                    $class = '';
                    if( $i == $col_num ){
                        $class = 'last';
                        $i = 0;
                    }
                    if($i == 1){ $class = 'first'; }
                ?>
                    <div class="wpneo-listings <?php echo $number[$col_num]; ?> <?php echo $class; ?>">
                        <?php do_action('wpcf_campaign_loop_item_before_content'); ?>
                        <div class="wpneo-listing-content">
                            <?php do_action('wpcf_campaign_loop_item_content'); ?>
                        </div>
                        <?php do_action('wpcf_campaign_loop_item_after_content'); ?>
                    </div>
                <?php $i++; endwhile; ?>
            <?php
            else:
                wpcf_function()->template('include/loop/no-campaigns-found');
            endif;
            ?>
        </div>
        <?php 
            do_action('wpcf_campaign_listing_after_loop');
            wpcf_function()->template('include/pagination');
        ?>
    </div>
</div>