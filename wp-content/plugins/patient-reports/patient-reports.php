<?php
/**
 * Plugin Name: Patient Reports
 * Description: Custom reports for patient campaigns, calculating total amounts collected.
 * Version: 1.0
 * Author: Antigravity
 */

if (!defined('ABSPATH')) {
    exit;
}

class Patient_Reports {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_head', array($this, 'add_custom_styles'));
    }

    /**
     * Format a number as Mauritian Rupees: Rs 1,000,000.00
     */
    private function format_rs($amount) {
        return 'Rs ' . number_format((float)$amount, 2, '.', ',');
    }

    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_patient-reports' !== $hook) {
            return;
        }

        // DataTables CSS
        wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css');
        wp_enqueue_style('datatables-buttons-css', 'https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css');
        wp_enqueue_style('datatables-responsive-css', 'https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css');

        // DataTables JS
        wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', array('jquery'), '1.13.7', true);
        wp_enqueue_script('datatables-buttons-js', 'https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js', array('datatables-js'), '2.4.2', true);
        wp_enqueue_script('datatables-jszip-js', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', array('datatables-js'), '3.10.1', true);
        wp_enqueue_script('datatables-buttons-html5-js', 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js', array('datatables-buttons-js', 'datatables-jszip-js'), '2.4.2', true);
        wp_enqueue_script('datatables-responsive-js', 'https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js', array('datatables-js'), '2.5.0', true);
    }

    public function add_custom_styles() {
        echo '<style>
            .patient-report-header { margin-bottom: 20px; }
            .patient-report-stats { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
            .stat-box { background: white; padding: 15px; border-radius: 4px; border-left: 4px solid #2271b1; box-shadow: 0 1px 3px rgba(0,0,0,0.1); flex: 1; min-width: 200px; }
            .stat-box h3 { margin-top: 0; font-size: 14px; color: #646970; }
            .stat-box .value { font-size: 24px; font-weight: bold; color: #1d2327; }
            
            /* DataTables Layout Improvements */
            .dt-buttons { margin-bottom: 15px !important; float: left; }
            .dataTables_length { margin-bottom: 15px !important; margin-left: 20px; float: left; padding-top: 5px; }
            .dataTables_filter { margin-bottom: 15px !important; float: right; }
            #patient-reports-table_wrapper { background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
            
            @media (max-width: 768px) {
                .dt-buttons, .dataTables_length, .dataTables_filter { float: none; text-align: center; margin-left: 0; width: 100%; }
                .stat-box { flex: 1 1 100%; }
            }

            /* Export button colours */
            .dt-buttons .buttons-csv,
            .dt-buttons .buttons-csv:hover {
                background: #2271b1 !important;
                border-color: #2271b1 !important;
                color: #fff !important;
                box-shadow: none !important;
                text-shadow: none !important;
            }
            .dt-buttons .buttons-excel,
            .dt-buttons .buttons-excel:hover {
                background: #1e7e34 !important;
                border-color: #1e7e34 !important;
                color: #fff !important;
                box-shadow: none !important;
                text-shadow: none !important;
            }
        </style>';
    }

    public function add_admin_menu() {
        add_menu_page(
            'Patient Reports',
            'Patient Reports',
            'manage_options',
            'patient-reports',
            array($this, 'render_report_page'),
            'dashicons-chart-area',
            20
        );
    }

    public function handle_csv_export() {
        // We now use DataTables Buttons for export
    }

    public function render_report_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
        $end_date   = isset($_GET['end_date'])   ? sanitize_text_field($_GET['end_date'])   : '';
        $creator    = isset($_GET['creator'])    ? intval($_GET['creator'])                  : 0;
        $website_only = isset($_GET['website_only']) && $_GET['website_only'] === '1' ? true : false;

        echo '<div class="wrap">';
        echo '<div class="patient-report-header">';
        echo '<h1>Patient Campaign Reports</h1>';
        echo '</div>';

        echo '<p>Detailed financial reports for each patient campaign with full search, sorting, and mobile responsiveness.</p>';

        // Build list of campaign authors for the dropdown
        $campaign_posts = get_posts(array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'tax_query'      => array(array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'crowdfunding',
            )),
        ));
        $unique_author_ids = array_unique(array_map(function($p){ return $p->post_author; }, $campaign_posts));

        // Filter Form
        echo '<div style="background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">';
        echo '<form method="get" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">';
        echo '<input type="hidden" name="page" value="patient-reports" />';

        echo '<div class="filter-group">';
        echo '<label style="display: block; font-weight: bold; margin-bottom: 5px;">Start Date</label>';
        echo '<input type="date" name="start_date" value="' . esc_attr($start_date) . '" style="padding: 5px;" />';
        echo '</div>';

        echo '<div class="filter-group">';
        echo '<label style="display: block; font-weight: bold; margin-bottom: 5px;">End Date</label>';
        echo '<input type="date" name="end_date" value="' . esc_attr($end_date) . '" style="padding: 5px;" />';
        echo '</div>';

        // Created By Dropdown
        echo '<div class="filter-group">';
        echo '<label style="display: block; font-weight: bold; margin-bottom: 5px;">Created By</label>';
        echo '<select name="creator" style="padding: 5px; min-width: 180px;">';
        echo '<option value="0">— All Creators —</option>';
        foreach ($unique_author_ids as $uid) {
            $user = get_userdata($uid);
            if ($user) {
                $selected = ($creator === $uid) ? ' selected' : '';
                echo '<option value="' . esc_attr($uid) . '"' . $selected . '>' . esc_html($user->display_name) . '</option>';
            }
        }
        echo '</select>';
        echo '</div>';

        // Website Only Toggle
        echo '<div class="filter-group" style="display: flex; align-items: center; gap: 8px; padding-bottom: 8px;">';
        echo '<input type="checkbox" name="website_only" id="website_only" value="1" ' . ($website_only ? 'checked' : '') . ' />';
        echo '<label for="website_only" style="font-weight: bold;">Website Orders Only</label>';
        echo '</div>';

        echo '<div class="filter-actions" style="display: flex; gap: 10px;">';
        echo '<input type="submit" class="button button-primary" value="Filter" />';
        if ($start_date || $end_date || $creator) {
            echo '<a href="' . admin_url('admin.php?page=patient-reports') . '" class="button">Clear Filter</a>';
        }
        echo '</div>';
        echo '</form>';
        echo '</div>';

        $this->display_reports_table($start_date, $end_date, $creator, $website_only);

        echo '</div>';

        // Initialize DataTables
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('#patient-reports-table').DataTable({
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: 'Export CSV',
                        className: 'button button-primary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'button button-secondary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    }
                ],
                responsive: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                language: {
                    search: "Search Patients:",
                    lengthMenu: "Show _MENU_ records",
                    info: "Showing _START_ to _END_ of _TOTAL_ patients",
                }
            });
        });
        </script>
        <?php
    }

    private function display_reports_table($start_date = '', $end_date = '', $creator = 0, $website_only = false) {
        global $wpdb;

        // Query campaigns — optionally filtered by author
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'crowdfunding',
                ),
            ),
        );

        // Apply Created By filter
        if ($creator > 0) {
            $args['author'] = $creator;
        }

        $campaigns = new WP_Query($args);


        $total_overall_collected = 0;
        $active_campaigns = 0;

        if ($campaigns->have_posts()) {
            ob_start();
            echo '<table id="patient-reports-table" class="display nowrap" style="width:100%">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Patient Name</th>';
            echo '<th>Created By</th>';
            echo '<th>Funding Goal</th>';
            echo '<th>Total Collected</th>';
            echo '<th>Percent Raised</th>';
            echo '<th>Orders Count</th>';
            echo '<th>Last Donation</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($campaigns->have_posts()) {
                $campaigns->the_post();
                $post_id = get_the_ID();
                
                $goal = get_post_meta($post_id, '_nf_funding_goal', true);
                if (!$goal) {
                    $goal = get_post_meta($post_id, 'wpneo_funding_goal', true);
                }

                // Prepare Date Filter for SQL
                $date_filter = "";
                $prep_args = array();
                if ($start_date) {
                    $date_filter .= " AND orders.post_date >= %s";
                    $prep_args[] = $start_date . ' 00:00:00';
                }
                if ($end_date) {
                    $date_filter .= " AND orders.post_date <= %s";
                    $prep_args[] = $end_date . ' 23:59:59';
                }

                // Website Only Filter: 
                // 1. Website orders always have '_created_via' = 'checkout'
                // 2. We also check for '_payment_method' to be safe
                // 3. And explicitly exclude any order with our custom '_order_source' = 'excel'
                $source_filter = "";
                if ($website_only) {
                    $source_filter = " AND orders.ID IN (
                        SELECT post_id FROM {$wpdb->prefix}postmeta 
                        WHERE (meta_key = '_created_via' AND meta_value = 'checkout')
                        OR (meta_key = '_payment_method' AND meta_value != '')
                    ) AND orders.ID NOT IN (
                        SELECT post_id FROM {$wpdb->prefix}postmeta 
                        WHERE meta_key = '_order_source' AND meta_value = 'excel'
                    )";
                }

                $prep_args[] = $post_id; // Post ID is the last placeholder (%d)

                $data_query = $wpdb->prepare(
                    "SELECT SUM(order_item_meta.meta_value) as total_sum, MAX(orders.post_date) as last_date
                     FROM {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta
                     INNER JOIN {$wpdb->prefix}woocommerce_order_items as order_items ON order_item_meta.order_item_id = order_items.order_item_id
                     INNER JOIN {$wpdb->prefix}posts as orders ON order_items.order_id = orders.ID
                     WHERE order_item_meta.meta_key = '_line_total'
                     AND orders.post_type = 'shop_order'
                     AND orders.post_status IN ('wc-completed', 'wc-processing')
                     " . $date_filter . "
                     " . $source_filter . "
                     AND order_items.order_item_id IN (
                        SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta 
                        WHERE meta_key = '_product_id' AND meta_value = %d
                     )",
                    ...$prep_args
                );

                $result = $wpdb->get_row($data_query);
                $total_collected = $result->total_sum ? $result->total_sum : 0;
                $last_paid_date = $result->last_date;

                $total_collected = $total_collected ? $total_collected : 0;
                $total_overall_collected += $total_collected;
                $active_campaigns++;
                
                $percent = $goal > 0 ? round(($total_collected / $goal) * 100, 2) : 0;
                
                // Query Order Count
                $count_query = $wpdb->prepare(
                    "SELECT COUNT(DISTINCT order_items.order_id)
                     FROM {$wpdb->prefix}woocommerce_order_items as order_items
                     INNER JOIN {$wpdb->prefix}posts as orders ON order_items.order_id = orders.ID
                     WHERE orders.post_type = 'shop_order'
                     AND orders.post_status IN ('wc-completed', 'wc-processing')
                     " . $date_filter . "
                     " . $source_filter . "
                     AND order_items.order_item_id IN (
                        SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta 
                        WHERE meta_key = '_product_id' AND meta_value = %d
                     )",
                    ...$prep_args
                );
                $order_count = $wpdb->get_var($count_query);

                echo '<tr>';
                echo '<td><strong>' . get_the_title() . '</strong></td>';
                echo '<td>' . esc_html(get_the_author()) . '</td>';
                echo '<td>' . $this->format_rs($goal) . '</td>';
                echo '<td>' . $this->format_rs($total_collected) . '</td>';
                echo '<td>' . $percent . '%</td>';
                echo '<td>' . $order_count . '</td>';
                echo '<td data-order="' . esc_attr($last_paid_date) . '">' . ($last_paid_date ? date_i18n('d/m/Y', strtotime($last_paid_date)) : 'No donations') . '</td>';
                echo '<td>
                    <a href="' . get_edit_post_link($post_id) . '" class="button">Edit</a>
                    <a href="' . get_permalink($post_id) . '" class="button" target="_blank">View</a>
                </td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            $table_content = ob_get_clean();

            // Display Summary Stats
            $stats_title = ($start_date || $end_date) ? "Collected in Selected Period" : "Overall Collected";

            echo '<div class="patient-report-stats">';
            echo '<div class="stat-box"><h3>' . $stats_title . '</h3><div class="value">' . $this->format_rs($total_overall_collected) . '</div></div>';
            echo '<div class="stat-box"><h3>Campaigns Count</h3><div class="value">' . $active_campaigns . '</div></div>';
            echo '</div>';

            echo $table_content;

            wp_reset_postdata();
        } else {
            echo '<p>No patient campaigns found.</p>';
        }
    }
}

new Patient_Reports();
