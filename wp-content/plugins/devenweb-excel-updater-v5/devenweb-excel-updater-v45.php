<?php

/*

Plugin Name: Campaign Excel Updater

Description: Update campaign amounts from Excel file

Author: Deven Pawaray for devenweb.com

Version: 1.0

*/



function custom_price_format($price)

{

    return 'Rs ' . number_format($price, 2, '.', ',');

}



function enqueue_xlsx_script()

{

    if (isset($_GET['page']) && $_GET['page'] === 'excel-update') {

        wp_enqueue_script('xlsx', 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js', array(), null, false);

        wp_enqueue_script('jquery-datatables', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'), null, true);

        wp_enqueue_style('datatables-style', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');

    }

}

add_action('admin_enqueue_scripts', 'enqueue_xlsx_script');



function add_excel_update_page()

{

    add_menu_page(

        'Update from Excel',

        'Excel Update',

        'manage_options',

        'excel-update',

        'render_excel_update_page',

        'dashicons-update',

        21

    );

}

add_action('admin_menu', 'add_excel_update_page');



function render_excel_update_page()

{

    ?>

    <div class="wrap">

        <h1>Update Campaigns from Excel</h1>

        <div id="excel-update-container">

            <div class="upload-section">

                <input type="file" id="excel_file" accept=".xlsx,.xls">

                <button id="process-excel" class="button button-primary">Process Excel</button>

            </div>

            <div id="preview-section" style="display:none;">

                <h2>Preview and Confirm Updates</h2>

                <table id="campaigns-table" class="display">

                    <thead>

                        <tr>

                            <th>Campaign Name</th>

                            <th>Current Amount</th>

                            <th>Amount in Excel</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody id="product-list"></tbody>

                </table>

            </div>

        </div>

    </div>



    <style>

        #excel-update-container {

            max-width: 1200px;

            margin: 20px 0;

            background: #fff;

            padding: 20px;

            border-radius: 8px;

            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

        }



        .upload-section {

            margin-bottom: 20px;

        }



        .status {

            margin-left: 10px;

        }



        .success {

            color: green;

        }



        .error {

            color: red;

        }

    </style>



    <script>

        jQuery(document).ready(function ($) {

            $('#process-excel').click(function () {

                const file = $('#excel_file')[0].files[0];

                if (!file) {

                    alert('Please select an Excel file');

                    return;

                }



                const reader = new FileReader();

                reader.onload = function (e) {

                    const data = new Uint8Array(e.target.result);

                    const workbook = XLSX.read(data, { type: 'array' });

                    const worksheet = workbook.Sheets[workbook.SheetNames[0]];

                    const jsonData = XLSX.utils.sheet_to_json(worksheet);

                    processExcelData(jsonData);

                };

                reader.readAsArrayBuffer(file);

            });



            function processExcelData(data) {

                $.ajax({

                    url: ajaxurl,

                    type: 'POST',

                    data: {

                        action: 'process_excel_data',

                        excel_data: JSON.stringify(data),

                        security: '<?php echo wp_create_nonce("process_excel_data"); ?>'

                    },

                    success: function (response) {

                        if (response.success) {

                            $('#preview-section').show();

                            $('#product-list').html(response.data);

                            $('#campaigns-table').DataTable();

                        } else {

                            alert('Error processing data: ' + response.data);

                        }

                    }

                });

            }



            $('#product-list').on('click', '.update-campaign', function () {

                const row = $(this).closest('tr');

                const campaignId = $(this).data('id');

                const newAmount = prompt("Enter new amount:", row.find('td:eq(2)').text().replace(/[^0-9.]/g, ''));



                if (newAmount !== null) {

                    $.ajax({

                        url: ajaxurl,

                        type: 'POST',

                        data: {

                            action: 'update_campaign_excel',

                            campaign_id: campaignId,

                            amount: newAmount,

                            security: '<?php echo wp_create_nonce("update_campaign_excel"); ?>'

                        },

                        success: function (response) {

                            if (response.success) {

                                row.find('td:eq(2)').text('Rs ' + newAmount);

                            } else {

                                alert('Failed to update: ' + response.data);

                            }

                        }

                    });

                }

            });

        });

    </script>

    <?php

}



add_action('wp_ajax_process_excel_data', 'handle_excel_data');

function handle_excel_data()

{

    check_ajax_referer('process_excel_data', 'security');



    if (!current_user_can('manage_options')) {

        wp_send_json_error('Permission denied');

    }



    $data = json_decode(stripslashes($_POST['excel_data']), true);



    usort($data, function($a, $b) {

        return strcmp(trim($a['Assigned to']), trim($b['Assigned to']));

    });



    global $wpdb;

    $campaigns = get_posts(array(

        'post_type' => 'product',

        'numberposts' => -1,

        'orderby' => 'date',

        'order' => 'DESC'

    ));



    $html = '';

    foreach ($data as $row) {

        if (empty($row['Assigned to'])) continue;



        $name = trim($row['Assigned to']);

        $amount = floatval($row['Amount Fund Raised']);

        $found = false;



        if (preg_match('/\((.*?)\)/', $name, $matches)) {

            $bracketed_name = trim($matches[1]);



            foreach ($campaigns as $campaign) {

                $campaign_titles = [

                    'en' => get_post_meta($campaign->ID, '_product_title_en', true),

                    'fr' => get_post_meta($campaign->ID, '_product_title_fr', true)

                ];



                $campaign_titles[] = $campaign->post_title;



                foreach ($campaign_titles as $campaign_title) {

                    if (stripos($campaign_title, $bracketed_name) !== false) {

                        $found = true;

                        $total_raised = $wpdb->get_var($wpdb->prepare("

                            SELECT SUM(oim2.meta_value) 

                            FROM {$wpdb->prefix}woocommerce_order_items oi

                            JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id

                            JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim2 ON oi.order_item_id = oim2.order_item_id

                            JOIN {$wpdb->posts} p ON oi.order_id = p.ID

                            WHERE oim.meta_key = '_product_id' 

                            AND oim.meta_value = %d

                            AND oim2.meta_key = '_line_total'

                            AND p.post_status = 'wc-completed'

                        ", $campaign->ID));



                        $html .= '<tr>

                            <td>' . esc_html($campaign->post_title) . '</td>

                            <td>' . custom_price_format($total_raised ?: 0) . '</td>

                            <td>' . custom_price_format($amount) . '</td>

                            <td>

                                <button class="button update-campaign" data-id="' . esc_attr($campaign->ID) . '">Update</button>

                            </td>

                        </tr>';

                        break;

                    }

                }

            }

        }

    }



    wp_send_json_success($html);

}



add_action('wp_ajax_update_campaign_excel', 'update_campaign_excel');

function update_campaign_excel()

{

    check_ajax_referer('update_campaign_excel', 'security');



    if (!current_user_can('manage_options')) {

        wp_send_json_error('Permission denied');

    }



    $campaign_id = isset($_POST['campaign_id']) ? intval($_POST['campaign_id']) : 0;

    $new_amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;



    if (!$campaign_id || $new_amount <= 0) {

        wp_send_json_error('Invalid campaign or amount');

    }



    // Create the Order

    $order = wc_create_order();



    // Add the campaign product to the order

    $product = wc_get_product($campaign_id);



    if ($product) {

        // Add the product to the order with the updated amount

        $order->add_product($product, 1, ['subtotal' => $new_amount, 'total' => $new_amount]);



        // Update the order total

        $order->set_total($new_amount);

    }



    // Set order status to completed

    $order->update_status('completed', 'Campaign order created and completed', true);



    // Add a custom order note for "Adjustments"

    $order->add_order_note('Adjustment Order: Updated campaign amount to Rs ' . number_format($new_amount, 2));



    $order->save();



    wp_send_json_success('Order created successfully with updated amount');

}

