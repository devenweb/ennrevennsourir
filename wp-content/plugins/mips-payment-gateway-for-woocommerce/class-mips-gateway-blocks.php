<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class Mips_Gateway_Blocks extends AbstractPaymentMethodType {

    private $gateway;
    protected $name = 'mips';

    public function initialize() {
        $this->settings = get_option('woocommerce_mips_settings', []);
        $this->gateway = new MIPS_Gateway();
    }

    public function is_active() {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles() {
        wp_register_script(
            'mips-gateway-blocks-integration',
            plugin_dir_url(__FILE__) . 'js/mips-gateway-block.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
                'wp-i18n',
            ],
            null,
            true
        );

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('mips-gateway-blocks-integration', 'mips_gateway');
        }

        return ['mips-gateway-blocks-integration'];
    }

	public function get_payment_method_data() {
		$payment_method_data = [
			'title' => $this->gateway->title,
			'description' => $this->gateway->method_description,
			'supports' => $this->gateway->supports,
		];

		if (!is_admin()) {
			$payment_id = 'mips';
			$custom_response = change_payment_gateway_title_mips($this->gateway->title, $payment_id);
			$payment_method_data['title'] = $custom_response;
			$payment_method_data['description'] = ''; 
		}

		return $payment_method_data;
	}




}
