<?php
	/**
	* Plugin Name: MiPS Payment Gateway for WooCommerce
	* Plugin URI: https://www.mips.mu
	* Description: MiPS Payment Gateway for WooCommerce in Mauritius
	* Version: 1.2.6
	* Author: MIPS
	* Text Domain: mips-payment-gateway-for-woocommerce
	*/
	
	add_action( 'plugins_loaded', 'mips_init');
	
	function mips_init()
	{ 
		wp_enqueue_style( 'plugin-name-styles', plugin_dir_url( __FILE__ ) . 'css/mips.css' );

		if(!class_exists('WC_Payment_Gateway')) return;

		function mips_add_to_gateways( $gateways ) {
			$gateways[] = 'MIPS_Gateway';
			return $gateways;
		}

		add_filter( 'woocommerce_payment_gateways', 'mips_add_to_gateways' );
		
	
    function declare_cart_checkout_blocks_compatibility() {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
        }
    }

	add_action('before_woocommerce_init', 'declare_cart_checkout_blocks_compatibility');


	add_action('woocommerce_blocks_loaded', 'mips_register_payment_method_type');

	function mips_register_payment_method_type() {
		if (!class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
				return;
		}

		require_once plugin_dir_path(__FILE__) . 'class-mips-gateway-blocks.php';

	add_action('woocommerce_blocks_payment_method_type_registration', function(Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
	    
					$payment_method_registry->register(new Mips_Gateway_Blocks());
					
				}
			);
	}		
		
	
		if ( ! is_admin() ) {
			add_filter( 'woocommerce_gateway_title', 'change_payment_gateway_title_mips', 100, 2 );
		}

		function change_payment_gateway_title_mips( $response, $payment_id ) {
			if ( $payment_id !== 'mips' ) {
				return $response;
			}

			
			$a = get_option( 'woocommerce_mips_settings', [] );
			if ( ! is_array( $a ) || empty( $a['authentication_code'] ) ) {
				return $response; 
			}

			$auth_string = $a['authentication_code'];

			$mips_secret_machine = new Secret_machine_mips();
			$mips_wrapper_api = new MIPS_Wrapper( $auth_string );

			$auth_id_values = [
				'id_merchant'          => $mips_wrapper_api->extract_value_from_auth_params( 'short_id_merchant' ),
				'id_entity'            => $mips_wrapper_api->extract_value_from_auth_params( 'short_id_form' ),
				'operator_id'          => $mips_wrapper_api->extract_value_from_auth_params( 'operator_id' ),
				'remote_user_password' => $mips_wrapper_api->extract_value_from_auth_params( 'remote_user_password' ),
				'basic_username'       => $mips_wrapper_api->extract_value_from_auth_params( 'basic_username' ),
				'basic_password'       => $mips_wrapper_api->extract_value_from_auth_params( 'basic_password' ),
			];

			$complete_array_message = [
				'authentify' => [
					'id_merchant'      => $auth_id_values['id_merchant'],
					'id_entity'        => $auth_id_values['id_entity'],
					'id_operator'      => $auth_id_values['operator_id'],
					'operator_password'=> $auth_id_values['remote_user_password']
				]
			];

			$api_url = $mips_secret_machine->decrypt_with_key_set(
				'7D5jnVTt2F5zfD3AKc3tNqsS5JFMrLTyVcrUy3R1yBHHzlXzUoFcZ6eCcmla-oo-UkJkZGdpVjAyeGYzd0kvdGdhOHNrVFBKaDU2QjFjMEpRODdkVnA2a21ndXY1TGhhaWFrVU9ZZFBNR2t1dzBOQ2JqYWRuRHVnc3N2NmNEOUVaR0VWRkE9PQ=='
			);

			$request_args = [
				'method'      => 'POST',
				'timeout'     => 45,
				'sslverify'   => true,
				'httpversion' => '1.0',
				'headers'     => [
					'Authorization' => 'Basic ' . base64_encode( $auth_id_values['basic_username'] . ':' . $auth_id_values['basic_password'] ),
				],
				'body'        => wp_json_encode( $complete_array_message ),
			];

			$response_data = wp_remote_request( $api_url, $request_args );

			$icon_padlock = '<img src="https://my.mips.mu/Core_images/universal-woo/padlock-icon.png" width="16px" alt="Secure Payment" />';

			if ( is_wp_error( $response_data ) ) {
				$padlock_txt_and_images = $icon_padlock . 'Secure Payment<br/><img src="' . plugin_dir_url( __FILE__ ) . 'images/deffault-1.png" />';
			} else {
				$responce_data = json_decode( wp_remote_retrieve_body( $response_data ), true );

				if ( empty( $responce_data ) || ! isset( $responce_data['text'], $responce_data['image'] ) ) {
					$padlock_txt_and_images = $icon_padlock . 'Secure Payment<br/><img src="' . plugin_dir_url( __FILE__ ) . 'images/deffault-1.png" />';
				} else {
					$padlock_and_txt = $icon_padlock . ' ' . esc_html( $responce_data['text'] ) . '<br/><br/>';

					$url_of_images_sof = '';
					foreach ( (array) $responce_data['image'] as $display_sof_images ) {
						$url_of_images_sof .= '<img style="height:25px; margin-right:25px;" src="' . esc_url( $display_sof_images ) . '">';
					}

					$padlock_txt_and_images = $padlock_and_txt . $url_of_images_sof;
				}
			}

			return $padlock_txt_and_images;
		}


		class MIPS_Gateway extends WC_Payment_Gateway
		{
			function __construct()
			{
				$mips_secret_machine = new Secret_machine_mips();
				
				$this->id = 'mips';
				$this->medthod_title = 'MiPS';
				$this->has_fields = false;
				$this->method_description = 'MiPS Payment Gateway for WooCommerce in Mauritius';
				$this->mips_init_form_fields();
				$this->init_settings();
				$this->pathimgmips = plugin_dir_url( __FILE__ );
				$this->title = 'MiPS';
				$this->authentication_code = $this->settings['authentication_code'];
				$this->liveurl = $mips_secret_machine->decrypt_with_key_set('jDBRnrjxKC7s99RXCAPMRVb2UkBVQuB09fuDIxRiSNQnNMoMuM7T6NhbOXu9-oo-RktMZENlSW5tNCswNVBtSHVFVXR1TlZWVlpJdnVYL1Y2a1RJdlVXUXpoST0=');
				$this->msg['message'] = "";
				$this->msg['class'] = "";

				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
				add_action('woocommerce_receipt_mips', array(&$this, 'receipt_page'));
				
			}
			

			public function mips_init_form_fields()
			{
				$this->form_fields = array(
					'enabled'				=> array(
					'title' 				=> __('Enable/Disable', 'mips'),
					'type' 					=> 'checkbox',
					'label' 				=> __('Enable MiPS Payment Gateway for WooCommerce', 'mips'),
					'default' 				=> 'no'),
					'authentication_code' 	=> array(
					'title' 				=> __('Authentication code', 'mips'),
					'type'					=> 'text',
					'description' 			=> __('Contact us to get your authentication string at <a href="mailto:support@mips.mu">support@mips.mu</a>')),
				);
			}

			function payment_fields()
			{
				if($this->description) echo esc_attr(wpautop(wptexturize($this->description)));
			}

			function receipt_page($order)
			{  
			    
			    static $called = false;

                if ( $called ) {
                    return;  
                }
                $called = true;
 
				echo '<p>'.__('Please proceed with payment to validate your order', 'mips-payment-gateway-for-woocommerce').'</p>';
				echo esc_attr($this->mips_generate_form($order));
		 
			}

			public function mips_generate_form($order_id)
			{
				$checkout_page_url = get_permalink( wc_get_page_id( 'checkout' ) );
				$order_obj = new WC_Order($order_id);
				$amount = $order_obj->get_total();
				$currency = $order_obj->get_currency();
				$site_url_no_protocol = str_replace(array('https://','http://'), '', $checkout_page_url);				
					
				$is_custom_redirection = 'yes';
				$additional_redirect_param_array = [
							'cust_url' 	=> $site_url_no_protocol.'/order-received/'.$order_id.'/?key='.$order_obj->get_order_key(),
							'is_ssl'	=> 'yes'
						];	
						
				$param_redirection = serialize($additional_redirect_param_array);	
				$mips_wrapper = new MIPS_Wrapper($this->authentication_code);
				$mips_wrapper->mips_display_iframe($order_id, $currency, $amount, $is_custom_redirection, $param_redirection);
				
				//update payment method to avoid displaying image url
				update_post_meta( $order_id, '_payment_method_title', 'Secure Payment');
			}

			function process_payment($order_id){
				$order = new WC_Order($order_id);
				return array(
					'result' 	=> 'success',
					'redirect' 	=> add_query_arg('order-pay',$order->get_id(), add_query_arg('key', $order->get_order_key(), ''))
				);
			}

			function showMessage($content)
			{
				return '<div class="box '.$this->msg['class'].'-box">'.$this->msg['message'].'</div>'.$content;
			}
			
			public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
				if ( $this->instructions && ! $sent_to_admin && 'offline' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
					echo esc_attr(wpautop( wptexturize( $this->instructions ) ) . PHP_EOL);
				}
			}
		}

		class MIPS_Wrapper
		{
			private $authentification_code = null;
			private $authentification_params = array();
			private $separation_string_params = '-xx-';
			private $equal_string_params = '-==-';
			private $main_param_in_id_string = 'id_merchant';
			private $secret_machine;

			public function __construct($authentication_code = null)
			{
				$this->set_secret_machine(new Secret_machine_mips());
				if ($authentication_code !== null)
					$this->set_authentication_code($authentication_code);
				elseif ($this->get_authentication_code() === 'PUT HERE YOUR AUTHENTICATION CODE YOU GOT FROM YOUR MIPS BACK OFFICE')
					exit("You did not insert your Authentication code");

				$this->mips_populate_params_from_authentication_code();
			}

			public function mips_display_iframe($order_id,$currency = null,$amount = null, $is_custom_redirection='no', $param_redirection = null)
			{ 
									
				if(empty($this->get_authentication_code())){
						echo '<p><strong>Please finish MIPS configuration</strong></p>';
				} else {
					echo $this->mips_generate_iframe_html($order_id, $currency, $amount, $is_custom_redirection, $param_redirection);
				}
			}

			public function mips_generate_iframe_html($order_id,$currency = null,$amount = null, $is_custom_redirection='no', $param_redirection = null)
			{
				$iframe_html = $this->mips_generate_iframe_url($order_id,$currency,$amount, $is_custom_redirection, $param_redirection);
				return $iframe_html; 
			}	

			public function mips_generate_iframe_url($order_id,$currency = null,$amount = null,  $is_custom_redirection='no', $param_redirection = null)
			{
				$mips_secret_machine = new Secret_machine_mips();
				
				$a = get_option( 'woocommerce_mips_settings', 'yes' );
				$auth_string =  $a["authentication_code"];
			

				$mips_wrapper_api = new MIPS_Wrapper($auth_string);
				$auth_id_values =
					[
						'id_merchant' 			=> $mips_wrapper_api->extract_value_from_auth_params('short_id_merchant'),
						'id_entity' 			=> $mips_wrapper_api->extract_value_from_auth_params('short_id_form'),
						'operator_id' 			=> $mips_wrapper_api->extract_value_from_auth_params('operator_id'),
						'remote_user_password' 	=> $mips_wrapper_api->extract_value_from_auth_params('remote_user_password'),
						'basic_username' 		=> $mips_wrapper_api->extract_value_from_auth_params('basic_username'),
						'basic_password' 		=> $mips_wrapper_api->extract_value_from_auth_params('basic_password')
					];
			
				$id_merchant = $auth_id_values["id_merchant"];
				$id_entity = $auth_id_values["id_entity"];
				$operator_id = $auth_id_values["operator_id"];
				$remote_user_password = $auth_id_values["remote_user_password"];
				$basic_username = $auth_id_values["basic_username"];
				$basic_password = $auth_id_values["basic_password"];

				$order_obj = new WC_Order($order_id);
				$checkout_page_url = get_permalink( wc_get_page_id( 'checkout' ) );

				$checkout_page_url = rtrim($checkout_page_url, '/');
				
				$complete_array_message = [
					'authentify' 					=> [
						'id_merchant' 				=> $id_merchant,
						'id_entity' 				=> $id_entity,
						'id_operator' 				=> $operator_id,
						'operator_password' 		=> $remote_user_password
					],
					"order" 						=> [
						"id_order" 					=> $order_id,
						"currency" 					=> $currency,
						"amount" 					=> $amount
					],
					"iframe_behavior" 				=> [
						"height" 					=> '700px',
						"width" 					=> '100%',
						"custom_redirection_url" 	=> $checkout_page_url.'/order-received/'.$order_id.'/?key='.$order_obj->get_order_key(),
						"language" 					=> "EN"
					],
					"request_mode" 					=> "simple",
					"touchpoint" 					=> "web"
				];

				$complete_array_message = json_encode($complete_array_message);

				$api_url_iframe = $mips_secret_machine->decrypt_with_key_set('PZkrvlt1zUDR7pIGOfFrbuqRV1qBIza4V04ZYDhRzRGQyBjhhRSqrecOWsW8-oo-dkt2ajF6WG9kUlhmaXRTQXJCOFRwL1pKa0N2eUVRSC9yVDNkbGN6Q29Mcmo0Uk1jTTMzdVJXSCs5a2poMitBNg==');

				$response_iframe = wp_remote_request(
					$api_url_iframe,
					array(
						'method' 					=> 'POST',
						'timeout' 					=> 45,
						'sslverify' 				=> true,
						'httpversion' 				=> '1.0',
						'headers' 					=> array(
							'Authorization' 		=> 'Basic ' . base64_encode( $basic_username . ':' . $basic_password ),
						),
						'body' 						=> $complete_array_message	
					)		
				);
				
				$responce_data_iframe = json_decode(wp_remote_retrieve_body( $response_iframe ), TRUE );
				
				if ( is_wp_error( $response_iframe ) ) {
				   $error_message = $response_iframe->get_error_message();
				   echo "Something went wrong: $error_message";
				} else {
					$iframe_url = $responce_data_iframe["answer"]["payment_zone_data"];
				} 
				
				return $iframe_url;
			}

			public function mips_generate_authentification_string($authentification_array)
			{
				$authentification_string= '';
				foreach ($authentification_array as $key => $param)
				{
					if (!is_null($param))
					{
						if (!next($authentification_array))
							$authentification_string .= $key . $this->get_equal_string_params() . $param;
						else
							$authentification_string .= $key . $this->get_equal_string_params() . $param . $this->get_separation_string_params();
					}
				}
				return $this->get_secret_machine()->encrypt_with_key_set($authentification_string);
			}

			private function mips_generate_identification_string($all_params)
			{
				$secure_hash = $this->get_secret_machine()->hash_with_salt($all_params,$this->extract_value_from_auth_params('salt'));
				$operation_string = $this->generate_operation_string($all_params,$secure_hash);
				$crypted_operation_string = $this->get_secret_machine()->encrypt_mips($operation_string, $this->extract_value_from_auth_params('cipher_key'));
				$identification_string = $all_params[$this->get_main_param_in_id_string()] . $this->get_separation_string_params() . $crypted_operation_string;
				$crypted_identification_string = $this->get_secret_machine()->encrypt_with_key_set($identification_string);
				return $crypted_identification_string;
			}
			private function mips_generate_operation_string($all_params, $confirmation_string)
			{
				$operation_string = '';

				foreach ($all_params as $key => $param)
				{
					if ($key != $this->get_main_param_in_id_string())
					{
						if (!is_null($param))
							$operation_string .= $key . $this->get_equal_string_params() . $param . $this->get_separation_string_params();
					}
				}
				$operation_string .= 'secure_hash' . $this->get_equal_string_params() . $confirmation_string;
				return $operation_string;
			}

			private function mips_populate_params_from_authentication_code()
			{
				$coded_params = $this->get_secret_machine()->decrypt_with_key_set($this->get_authentication_code());
				$params = $this->get_secret_machine()->extract_coded_params($coded_params, $this->get_separation_string_params(), $this->get_equal_string_params());
				$this->set_authentification_params($params);
			}

			public function extract_value_from_auth_params($key)
			{
				if (array_key_exists($key, $this->get_authentification_params()))
					return $this->get_authentification_params()[$key];
				else
					return false;
			}

			private function get_secret_machine()
			{
				return $this->secret_machine;
			}

			private function set_secret_machine($secret_machine)
			{
				$this->secret_machine = $secret_machine;
			}

			private function get_authentication_code()
			{
				return $this->authentification_code;
			}

			private function set_authentication_code($authentication_code)
			{
				$this->authentification_code = $authentication_code;
			}

			private function get_authentification_params()
			{
				return $this->authentification_params;
			}

			private function set_authentification_params($authentification_params)
			{
				$this->authentification_params = $authentification_params;
			}

			private function get_separation_string_params()
			{
				return $this->separation_string_params;
			}

			private function get_equal_string_params()
			{
				return $this->equal_string_params;
			}

			private function get_main_param_in_id_string()
			{
				return $this->main_param_in_id_string;
			}

			private function get_form_url()
			{
				return $this->form_url;
			}
		}

		class Secret_machine_mips
		{
			protected $cipher_keys	=
				[
					'PZkrvlt1zUDR7pIGOfFrbuqRV1qBIza4V04ZYDhRzRGQyBjhhRSqrecOWsW8' => 'oAHpH2a8OU6c3au3wvSjwYeciJ7DQgi9CBoc4SEJZiiNlbVERn4Gllt2PvrawiJDgn2Sos68VtFulqLQLqboQ12mF1kiSabhgL',
					'lUV6qvuGEKoho6tTEnDo0ITl6lPWhFN5Dpg0ZW3kVWX94HxIYerwVGIYhfV6' => 'FqLAW2koRAZMEhMgEfyrikJNHkF38iU2SGXCH0tm02GRiQ1OUCr7HZqcSXwgPt08hN4kzZEUKB71HrZ2IiLHDBmW8jBwO9YmLv',
					'ktHLenTAF5GrS5Pmr6AySjIB4aPSJbc4fDLB1nGgWQyxUuVMqkiUG0vbfpij' => 'fhofV5BNabxgayuai66vKNHFjO8vw2xsPhrU37s6gee6yzY7qfRBtAwShtPfa7nbZEPXb7okYlvc4tNUVEbxPZuzDAowc7lLnp',
					'nW2Zy0MjGnHhsqzeo9CRj37nyRvQKMTjuc9zzPq7CugktyHoWEmyzRyFGgTc' => 'ZmlUxe96z3kpkzl8Ezzvjqtk69r4DCcbrkAJ6XEL6apLhep2Ifc90Hvc8RnbMIZ7Ul8EA9ERABSalWl0W5rsqIMpPFemGMikI7',
					'UCgR4X24CBIrwTmlTeW7W8v2kKrwCIMw1wqChPmmJywXhfal0NmuwQxszjXF' => 'Dcvkck1xGWtcR4NZ1GfrWB8txyu917yDv0DfrrIUOS62wQBpPcg9WUeyB0toFsjw199Z1SylNPllJW18Nbl7XFZqrEyGVjNQyW',
					'XPhLuO6iRJjSMx6r3thz4HVIysDK4wJeLFzivB6rkIWHE2cwwinmvq14Qroa' => 'mAvs79wMOwuA9C8JlMC0TuYMIpqVGkyCFY54fRWs5qkpr4Yn0HWguL3IkYkGweDn5N89qj7T8eTqpy4QvM9wGuH1DGM0n5HD4R',
					'gChtHHTBoC2YPkRptJyjDBELEOclrb16AQ6LEBL1IUHth8ytV812paY9yUrE' => 'FINsyu7hhlFGoZC8c7nvfoI9uaD6syrIzhVPlBsDbztrUmqBtnSa54qk1vkRLF4ZGVBK1ELaqkjw28DpuZnIzFwr59MTufTrsW',
					'imkKw9gpOc91qe9SNMbOkBiKG9mgcl14BIPplM3BnqJ79AXc3G4gZ67xchiN' => '7pOtpyWzeg2DamUAohmWWjuWqbexnY20jbTb8XrklTI4gC7k7YnxDTpsza3FNsnHvuLiOlaAGApHKzG86VZn6NkEI9OvU4HMYm',
					'4AbJWUBM4grXP76zrX7SjGSRgGqurSyjKFSj36NfxFC7riARClFOGiTE01cG' => 'PBC8spD60PP4mUSADrpAqMepNYSvIZi5jwsgPh5QxZK1002qLOkWuZU4OX0TwqayavgE1538ENByu3IPtNaBJSK7KqJTOKqh6y',
					'Ic0iI2PfSAk7yL5sIkgwG88cAU9U5OctZ2mWLiYfpVkDtjGkmOrgWCrSicaK' => 'peesbWpGOR6LMM4ieEVffHP2oYWMUcZYB7Hmy3sY2gwC5RGUNbmkV3qOGiJAkuVNX8G6MsIyRVuUYIN9fU1HyIXp5w8vTQhxn8',
					'zszgHa3jv5neHf8uE3vNhNkcKLzzuWqcD6t31p4FBZ6WcYiS6OhlRow9BomH' => 'MgvKYgNLspN8iZX1st0uOTqjmjE89G6jt6tPvLTpNhGGEX7KDQo1fyQWK11Cgzbrujc4kcrnRq3urUPne2ToW1YvxhfttbhRqG',
					'XEgtTsPDfLiPXnjQy9FxwLG7I5hYP7qDpVcYKYonC5UrUHCkrOFCcShr5Tnf' => '4DzAEFzAs2noTTKGKsSNvu75g8KQ2QA8LzmWt0fZ7QXxOUCr7FcuJRNAv2xMuUa5fxffNDK8VVHB54elipxp8texFyn6GhWSE1',
					'EcEn1RE1MPSE6Iu0CiIaVrRW3WHlna5L4g3zUD7t7tEbWYbK3XqDNekS3SDz' => 'AZxkvyo4xZ4iHsICp8HvRBzMynQhetblllpwA7F12jU4wQeVNl8WFIE5Zagf5iQwV7sZC1aQjrFNqRjjcqtmeYyDb7FMzsO45v',
					'MPNlwZc2waITbDBsSXC8Ds07y3E2hBItRM0Ti4DterpcGDIsz3XTXbFaEx4N' => 'puxbDCDBwLv8hOHgaIh19FDjAzYLTyVi0uA3S9pklhLL5LSR1GuZj1rACueFLZIk6hDc32nSpkhIZ03PvT78RucLf5Zl8iCi9I',
					'LaifAkiG4ufsu9ESiQ8l6WvpuinGzh3MZ94a54TAq8hBw8P8KjkomqeRsE9C' => 'lFyX1QRjvfQskY7RGez96emtQSA0BT3OmrnRCM9uDaNvDQa4FY1VvlCxf0cQqMwn5yyqM1PFYA1afymDw8oektFhwFJhLEUaZJ',
					'Dl0azxSMkplO9F3enzfjJFIGTVLsYaETBB4pKmEYTKpeWufzoc1sxo2shwa7' => 'yojNh9rUFvNHymp2i4wr1ZHEvX2XlSvwI8q6fMLfeErI0UUGUMTAwpOzFsViK7Z226rUng58aCGyaxIxQDcqlnoHMIZJaYBSem',
					'oAAgiLw2ZMzpyLa6AcSs7uH5iFqKMR8wDJ337Gbklp852MyqmTinXZDLvDD0' => 'aooGhfkcIm83H4GVtOlAG50AAQrGgVFY2B08qgNXpySkrJGIDYiJ42ZCcSr6Bsh7ZJ1EiizRWiBPDGhFKIEwAEYPY60aL1LeCI',
					'CpqG4APuxS8cJ7MC5sERHL90vEDFOYW0fNCwm2B9c5rMinYTWmg2EvXzgS3f' => 'MqkmT3wqyy13L6DGOTPkSMSiuKb72iBJhw1w4oGgVPHavY3sUDWcf3utgmDvzhtrsCWu5VBwA3QHF5bpXircVDKCUZ3tFcyS7H',
					'BY3job3ieOfow6YraMWXqKpRIu0G3NooiEA5jr9NAmchjFqI435AvXKSUMVE' => '3y12DUrWebtuqccFKVq0w2DmyazsKZmYxW5X59TW6fAbVtTmLYNbOHwjsm2OKH37wuLiR9zqhVwc8hcqLWhCQeHQMexAZOvJfN',
					'5Am46pt10pnPGtlb1RUJlWKIiPmQmn96UUphwha8Au52Vkr7ml2caurEFviq' => 'iTXKA1PXUUKjkJriCOgB4uxE9mYno3vQTEysw2T2b49vbhnuzZDw58afHRBMuAoxVtFzSCyODcv8fu246JW4EtpxhMxCMwR6aj',
					'wDBT3UltkvcPLV0rixDJSHCA09E4i4qe0GkWgPz5whFiGqAYq9nBI57AuH3O' => 'w22uAyVBG7EHsAZo8r2Amj87lA31TCwNiXuHr9C7PIf5J5anjkx4LwXQs45hzzFXS0Kp3MGF9NiurwGfC5nhhRVC3QPOiC7M3i',
					'5W51JFJWDLozjtm0KbPHN6fSHmXbMZCtGZjPiQvIHYCbWEtZhBqKyYEQ7EUo' => 'nNhlEHoE8Yy9qEQxWqhfG2zsmyWa2tRGNnGA6tUH82TgjuS3QZYhG1sl7YhMzNPWvupK9Jrq8WacefemQVDjnR05zXOb8iTUQf',
					'7zzuyvRWGjPOoMi1VjQz2kSCefRG7upsuLEo89i1wTYouj84DgLVkE6Uqpuo' => 'LYJ4zx7IFNPva6hPlNpph1mSkfluQFbonM95JBZJkDM2qGDD5rfslvXnqHcPXaEu1R52ReSmuEc3ZnRaCilmJjiZXS79oabITf',
					'mp6rJ58ZHFl1wjGnUIGRB1tooHbSJj7WCZW1H3BBvKaw5iktiBPGeWy5OlNn' => 'iCt4VV18PMEjumFqW8YgrLF0UiyZhmZ5jONxf3PINUnsRaepMQvlY21sBLx3cHiPDIWcFqHQYkuCXWnsRepvI3BfFywUPQ2C81',
					'M0QJIh6BOJ0kJtcBRyagGckUx4ow3c3mYih2tk9MjjHx6iqTFBHNPzL70r70' => '9lXo20wrNfcMYZJXi5z2YVv9M3kypCuHRlbgS5ueSEj8fvRChp3CIR2FjSfapko5Sa0F4kSIP2MRKS50Frc0tfyCEHRfR4CAh6',
					'3VSBAtJpu6JlCRztYtIsfYwrRF0iTmjux9RPqh7o9A6BiR2zvqO4qWZSrh7T' => 'BxLqmEcgUPnJcUGfWMZuk1bHBLcvJgPXR8Dn9I0HsPe0ee9NQ2cFAQ68nnIn6gBtWP06E6kgFS8WE6eLURIbXkoboyr9BxGcis',
					'BmKw4RBMUeP8FLhFBR4QE4yI5POeroiK7YBKt01mNlNk7ZCqQAran9MzF9jc' => 'Kq4XjbC0PwOItUItDYVljPQNc1aKtZ62BIpEDqs82v6SKJHJG5aYoYDz29beGHSRJmZYvYSUUhH6eEsahWhpTza6u0JoyUPI8j',
					'P5k6MJ4x5eEOJ4AZz9W0iXSE0eHKAfMUjhgjTXEv2UOuhiX38ixvw7hjkBAR' => 'DwjA3sTVFwuN8vKDZvSQ4XhM1x0obCh9g4VWWTwbieCaRZJRwhZ2yDqFH0FeJyUagNaY7KCCR7UC0uWYpF7ZV21UbmlNzekCTy',
					'lmDThl4YeJjmXL7Y16PZSzuYOw5OTZAzjlPEDyJzWw0kUzDY2K4ccpkgaFlO' => '9shfwc0RaR2mKDrOZLygzZAiB7tuUM5QjyNuFQQhvj4fO4hbFklclfli3IVkVOKgnE0O60rClPFW6KrYDAwqegCJt4khkwlhqp',
					'5WLygR7lNF3gwXehV37BxPSblOMh9QnogUtGo1CbpkKCxUSO4JzrWGrWTjLj' => 'iSap3HMYcFe9qyTQMTAANequW3axXpCwth720ZhuslKaGguYzGGk0K0bFjDzIRPxnUf1useDnCh72nw33ec66v6ycnp78MtZaE',
					'eyb4lX9A9enWNneC8I2H4FYn3ziaXxT4uiuVORK5MXXjG8aw6EbRegMvsaWZ' => 'BGNlIJDiiqLC0LOgWBJaQVYFFq2RNEyQhEh9N8cvLvMmmJj9oCRY6KY4OMgTT66sEjIigZQVzXxNyX177JGJAem8BiLZu8ZkPe',
					'1l9JJ8lSzPCTBRHfshtLkzRFbT3EfbzgsfjGabuXnLAeyaCiWwkg6ybcJ0RP' => 'a6ZwrJOqFQk4Zl3s9nDOyDlQjRN7aKrGp3phrKaKWXnbk5ai3NVuTsc4FOUUzJa0T2h8nre5vMmVIk9gcrELuzHoc31gbexUX1',
					'5sGfSIWe50GOEb5IsnJFlW906MsrCh8xHxg84SgZnWQMfPFpfZGzUHLPFNMz' => 'Ugw5Yl39LhZDb1Y3hfhz0H8ZwcaFBaDqs0Y8w70onuaeNUIMHQfqw3ECpFo6GMBkVuB9orZnl6TYjhS4FMlgV1c2SgSXExNuR2',
					'eSa8Q9vhcpLo6F7XGR107CnthTPmhkn9t2T3w2B4WPviaST2CsQgKFGKNmMk' => '8JLBqtBgMghwkVPL0mDqZDQDzVgbhpI5LiuVVYDnjT2gXKQIrYc6V6tTXtTCxuwWTEung6ZRaO2rXKpmbbRm07DqAakhDhWxNw',
					'NiyLWb5H21oZR7O14tBWJJamSmOl31GZanr5YZhHnkMIPgq01QyjKkcT8H7E' => 'pKUh6gst6O2I8kFPgMvtaaOUtIF0rhL5ilnpsVSww06o8w4mlI5JYc0ymZMCpFIph2i7Whgkfgz14gDGX1QGn7t8qBMH3oU3te',
					'YrajACc4j1h6AjjgcjBJvH9qRI5isp8gRHqBjigfU6q7QhAqikuhhonPHive' => 'bn0NSGNqgLhQCQTIo1IwpbGUeJwiJADjMsiXrwtaOKuMXh1GhPRRKkmTMZgHfVK1p0f2wpPhcS7wIn1Am4ijCStwT0EIwUmySb',
					'ZE8LF8NCHi0ZJ54kuRBJE5ccRwwEGj9cfakxWv02BbBlG0tJjrGDmWXJXf7J' => 'P2RpiWGFPfkCtYPgpPI1tOZCw9Fj3YG9O87c0UmlnHI73BXG3OTlIkiveC5ZuH2mRRxAwywsQFThYHxjHHQGJmEKZY5en6ccvO',
					'Y79XFGB8c5lA1IonFYp1BFlNP6OY90lGhvBccgAa439SZngzWvJSS9sBTPKp' => '3g1KlB7uqRkDMLLiJPyZ4Q011UVCKcyGuni7IJmsWRiUFy4jYAo1XUFODqsgTFLB5AhhfgzSn6h389NSYe6bDHo2AvMVA9wwAZ',
					'1YfCoEVvxBQxWV9oZH35bcmh4QWHknSSRHAOMhc8CBTBzuKmqVmMyDwNkmz0' => '1L22VKQ7vnKpPRSmLaQQ8VU1KUOsZGySutXBL6CjJDvvxfCP3JIZA7xyk9hfQvw3yTPZBbobQKMFKcl6cGbsejj9nJPNi7z88M',
					'01HXkR0UVRSCHwlEOYoN1Es3eP9wXD15lUMNQpN2p0oiTNS4YlLqix8zJtwH' => 'TItathFKbk2NZp1HKcyiHJbtZWvhjvWsJLGTnFFXvR3rEWTXcjmBiJLkJ1RpYS7saK1YnNOav5MRLHea9m7UDhpOlKkSiK0R7H',
					'8Kwybe1qIB0ul119AF4QL2hIbFAAWQaPsNaEvb9asvu6KYYLkYkoi9CFQk5k' => 'fWITS3yaBZ5kJfrJVaTy6KM4jGqv2LIc6nLNgMPhsufrFEfw2lkK04xIfKSpXq5B03l7EI89iNrpWUPxjAQEA0ygmVD0m2HJOt',
					'ozG5PC548rsW48p9vCngaLRoNucz2ufp3yGgDfj6mmr5KV9lRZLlcsbJvojT' => 'cQxSYDO2HvMffQCDx01SKEAcmpOAHEWcA9meOgw0yZjxSNz9M64ugaR507cw0ktuiR4M7NXTkO5yok3MuJW3SffmQHoDUGmP7Q',
					'iVNWFvin5rh5CxQbVbRNQHVGsbsi7YfVzpSFw6JxtL5H74axWX3gDbUIXPeH' => 'LvNk64fzLnXgkTfsByr2Pi2KVo9g7SvUsHGeuAVg7BhhRRuBsf8HoLtWajoHuM0j5HtGQDLgSx4QcvQCYBpeAZrtVHaGurYpnb',
					'i4ogfEPB3KuIjSxr08cXYAoMWzp4Z8GCNRJoCtfESAEhkUSGsG0vK2oihmzF' => 'zZBlyWgRDsUW0ssey5aj786ROFacK5LHyQf6eB5CEauiubvTZpi5yBXW8MMDoIrvFy4G9cXgEKKtQ2V2XyAg3LvTaBFS48x2Ap',
					'U3nz0GVp8sIUvo3ejy9uNnKkC1LpANiZB4uPEPQXP1vXzYBCDQqSyQoHSpaj' => 'pC80RIm4wYnF5ufkQfAfxiz01R6s5P6ilbqTf7rztNeDYQNVyPvBhoQxCtaTTyeBfiDYvMKWzY7JhOhI23b5Drx7BjnPXA4TrY',
					'aMNX7sNAPzsCq14Jo3h2h3le2WGjg3kl5yZQf1go8OvVWWVDzQ3P0XTanztb' => 'VH1Hr7Xv8ZpOR9wcFBQuGFDRNcnOg9H23uZnF3R3cjn7STvIwTYgN6QDr7iO3lLlhq0tFlrlEU2zuCQ3rusb58tjJMFmO5tLKK',
					'5rpNTojmpXtUhgaJz7inTY8zij0ib6uM42NVNuxBYomJ4qZmh5FJeWag0Ftg' => 'vThRasY5bMlHV2Cs6gxmzDVarrthLi7pYxZ7K0GGfTQ8YuFcLuHvRMr9Ch3z6XytqKKjUuG961J90WKqt8uzFbSUrMyYDxqkQV',
					'vtUoEZBHRL3RD3jTVx2CUKr76syYf2itFsx3ekMUWIGeT7ACLZB1hjD7XVpE' => 'f6JZikw7ME8cWZq4hIBcNwwgoWmzIiZJzowOwTcTTD1GoY18TFRGu7PqLJX4XoFA3XwYZIgqty3hKn9EKFJuJBPgoyNTxknSYl',
					'NAaalyYgNyuW0i9ozNpAJz82Ik0LDNk2ZDngOxFFRpMmA7m9A6UaGW65TBkl' => 'zUQVcTKatggCDWaUDiPr5qM7rtLVmsogXhPTJsIph8LkM0lKmDNYbO8mzwaaDyUj4TQjLcaGlaACQZmvGMIcOZlB6Di67UQ4FL',
					'm5WwFtzkSmUwfAvIvIitssJm5E8Ee51vhRz4cgr89zmInE7tw2uK6Mt4COQh' => 'egPGneMMC4bRcSay5Lnz9aDtZF0WDVltjoDpm272xAGyVhj382FGSW8DnPIEfcnBY8ZbOpEWE3vOIgRvBj2GrYDhoPP15EYUEK',
					'qgGPVKkMfaQsV3ft0NG0ImM2SWrnbomM01ylZyeRJjoR6BYPePGlHIarENhm' => 'funlFIk5Zttt1PDzGju8a67HJ0BlCmoTqWRLQTt8ikTeBKZUaNvwUnotbKDWIivpW5V8VsBOney9teyLFIifoZDTmWfxOLiE70',
					'MwLyMPacvbOuHRoo2Q7uxDs5SQtxl84tLkX442rkrXfo9Q1r0snastVnDDHR' => 'JW9wYS3IO36WIOFjawyTM9Iotz0M9walsMamjcO7PJD4Bg9455Msgy8BF4Ss5F5BB5h6c7ECu0ahAiR3Y6AXIuaIyAur3XXuKj',
					'quEtm0EAV4JXOM82RE2lisSEa7MYNU7YnDfUwOzDfji9vgDXYUxSNhCfWIAK' => 'qAnApfzxf4PMqUkJNrgtRhIe23LU3Bi1fcfaROGk9UUEieiwVcITABt8H2OD5Mkxs3zChyWzEq3QPECY4zEpuXJf3ZbM4MPgo8',
					'eM1nNHatxOOL5RmN72U9pi6HDs8uouYcHkq2tN175RrCAlZgGq8bTEpLqmGU' => '1smDy3zt0FfXfwiJzDVoZaM5utZoWU4j6hOYlKixlhbciv6VmuBFjBsoGTwTttUzJxG6FjkyOluc0Qa4K3SPPkw1pyEI3O7eL6',
					'rRYxp5agTizJlnbRGp564YcPUMCn5rX7gmDllUzsLrJ9Km9LDlgeFxWVYaVq' => 'eYV8RBpUCIRqToUWss485I1EKp07luyT3BjNR5MjOO4ZrxzRh2kbRDQfGVK6K6YWJNcRlQbFFq9Qqk2QZ1qjMy2DJ7xba0hMp5',
					'Cw8zrfIFWaDOn0YSGYl5mBznKgTG2JN7oJNP1KwYPG66CBSAjJL9rUilRFTS' => '6ZmpWHIkNwsc2zCGefehLncxDuN2wwCxyBSrLh1ZDL2NaClxeKak14MQlFFLeGy370clK3a5smZBtpaJKC6IqxLmQeWw0e4IER',
					'FJDGlDf2JZnxSh3HWvb7UHzjuDFOluPjESUPQKHAACBCZbFvF7HG5TfqOWmU' => 'MPC2OqOqYz9LDH7UA0GPmmBfnKAplZfLZWrVjch2xvY02uDSLU0LslZ2nSLfcp5bCMpP6VRpGn41Yc7I3Hmgu0Q5xKGYIlV4Dm',
					'KNEwoWH79aXQp8tNE4c938j9lrBKralW36FiYGYr7UXy38HcVgZorBAsHTIY' => 'IPeFyjXBs3r44fCoUFavCaLmLMfpzMqV2bne1bwVykzcwt8s4jCkZqMz1cYywQUwAEbU0wHq8NZH1RYbAzeUZQjeJUescCCvXp',
					'GNcK2EAvPyPvfeQC9wNMatcaKcVoLbjBb0YVWj2rvn2qrOwuoFtcMKVGU06I' => 'iL5uHEhEmOnNV7GGsuQfHPzrfFc7hxTigxlVARIy1g8qY7obzCqvIIK2V366w984u8qXHZBEsk5sE7yEitxFBluu6Xa7car291',
					'DuwlADxaMunJsqn5v7L3wFY4RyevwljI1iMWHqGfhKNL3ikxVxsFYqmc4x9S' => '5GB9LvmxLpcfQjgJq5Z4gaGlpMAlZTX0SGROiDknAl2MJefQopEGN2OD2bpJ2gVtZe9elLShQ3SjGXWpCgONvuPS0p1Yo7PFrV',
					'XFfaz7WfG8RHB1SUw62iVuPOr8UhTvN3HUYrR2ggIv8bo8hlI2XIpKU0yK4x' => 'QbL2hBCqSSTN70GlCrOvyGI369JBkUGKEQNrQoNqyQfN67uHBk7KNtJl9LYicQ50nRyqK6g3LuUFVr3ZNGPLQwTqWyTPYPqPBC',
					'ZrlLy6ie1kminI0FICVyXJaeuHJuBDVlzH7wa4Mvir0qgjMyLI0tv7v9LpwV' => 'fXh4Yxzab5C4lwqO85jDf4VtApyzZPweAc1ZRVa4RqMKNtMUMyetvqG7fGWgOoteWBLDK2kU7CoQmqVwu6ywIvbaZxXrb3ly7T',
					'hw7K0BNMJAQGyncNiKDl8s187gO5J29iD1UmUGS4RmWMIA7Sg9ODJ2Ivq0lK' => 'qvnGBUV9Ue6sa0P1tl5paYnUGbkhPAXWUarXzzs8uz7tvRxM3uZ4xLo0NblRUOscH8cOaGtUcCjo1biOlSWcxtfLjUiF5QND3e',
					'w1ErIY3ubJXGRkvr4vam0GXbneDhz7hFFPtBoeYk5qUbJObBjBGNi4nJOY0O' => 'k4QC5Af5lfuHhin9F7RYEja4YFaCoqHACGrPGyPPv7ZkwkXUmrpvOrTc10ikfsCF3Iv3L8TjIirxaRKxhk1bzksK1sbyyWsZum',
					'PuGP1mUPx5BaaAc0zheMTlC5ZT2iL2klYOhbZIRJAPUaxSYMWn87eeTwTcZ5' => 'bz6MiruHyvjem75cM1V3eozTZ53tEUf1caLChMbNCNPwpIMQQgL8a8NHzbVz5VcvyRSHlfMOH9YsMP3hs4tneuhkNprGANFfGm',
					'T0h0gEu6nRRGZEQ9JQD01KQqBVbznDs0sFfgpqgrzRGIwKNalQ7XP1yVjgKA' => 'GtqSWEs5HS8yHTifh8GkPPMK2ehsLlJwNZUTMYl5IjyBkuk2Wfu8ppnGNKYXBz4WUx8mWzfFGIGPFxphSu4FO7zbUNeWJk4R6T',
					'ffDzSLtKi0mR5frZIqGGL0DLRZhWjlPcGyGH6K3VzLUZqDi9mNMH2bBgBRAr' => 'n5w2EHYrSVCIQs0QmGmemyJoHBeuTgl5Fc1UMeiI1VQDmpFbA5maMiUrX8LxIy5ByTmmUrpXRfRhQzn5AjGeONMhbKGLj0QJ4y',
					'xBEAH3an3KFN8mb73KYuCBYACOFvAUf8UzUqNjwKEzFpfnLpHYv67mJAVVZt' => 'zgKyl9GNsbm4Z7qeFqhlYukH0pgkHooSDpRwU8HsZ4a92xFDkScz3Krf7bsGxyRSXgCXF8tsLk3zcDOkq0a6j9lCn1sETD9YUu',
					'CyGObkrSHFBOjNs2qOjrAFzZAOqpZXbtwiYA3eThIsN2nynV7faHImMotcar' => 'BtvaZf1oKVsS6hvirDeqGfa2Bz0Ne3BeK17S9UlQ20BJiElULXqIR4xLmAnJmNO858HOiFNJWYIK8HnDBcT1xIlM7s8m4hCVqG',
					'g0WBVAOKmEyRL64KokbfwgTtw5mGmbcxPoxOxCkVnCUBr2o7Krsf6xWqng3e' => 'cI37Ok4hs2YZk9LIFGEtqSTVrMRKhr0bgFnyt9HgkW5CODncirDAJlSuHkZjoxRlOGpmlsnrGQZJ3CMwbJhgaSf4KKtUsM4ZjM',
					'xWXJrD42uaEqk0VVXLrJgOOAmDhqxkuwhgkVPey6Ha4rSMaekRDkyhh1nUxX' => 'AvZFLTDVzARRoxOXoKzpcTBgCarwvLLe59yBkNt5ElZ6gEEIDkFDkXNIWDHNKbqv23T5EKv8b7jo4qrpNxYKDpMy15ta58OqE1',
					'Ew833BJIkv66KTegLfb829pSt7Sb59qX9EKClTEkUFybKONb4zD8eBMkGN5Q' => '396hssvp8js6tyl6PNUH5IADgl6J1i8pq9DmQ271FviDz3ojMeZLtl5OfaiafjcWzuPEmRNQmQQAcswAZIBIYb8yyFMXl0lYSq',
					'jDBRnrjxKC7s99RXCAPMRVb2UkBVQuB09fuDIxRiSNQnNMoMuM7T6NhbOXu9' => '1Biwqkgjm5ZR9lgcYGF3WKy7PSxWnAHPQHOaLfkJAWIslvL2GkOn8cgMj4v1hW8y9Lj8iFNBjLrpeUXEkVY0PcjQaLHhr9vpAP',
					'Cmlo6TKYXrqAAaR1wCzh7TLmkeIr5Ug2pAFsfsM21bE3s4uhJ5QkwCgjp6kr' => 'nrJcHvhokTqrTp9uGtT97eIVtcLhuJK7yhWvnfIrwZaRGQFvewtlPMuk60RmLEIcP67lPGYilR8JOttRmsPocMMMH3KKqmeu4H',
					'TpWuCFVWXph0G5HKi7LTUBVOpv4oYA0MUyYR3NMqukOMKKmkAXkzBhlNmUY6' => '6KVbPrHpeRMW00pPy8SXGMHe2kCGp6gZ6YYg7kT8xvjZYxOVhaMsVOWvvUtFprIWKZBU29BAeXLDLLhIQR7TDT42KbUfOMsC4W',
					'cwt7vE0aho4mznP7SSfuv6nB877UNp6sLHB9RXmR7iGKO2y4iWft2IYocEFx' => 'Tigsl1yYObyHNJbLqmF4tDgq4FftWwb2ORek2enzP4RkFvMjJqt2lfcmxPMvtzKBhAif8S8WYJ3jgTBtIhSaQSk8qJQKXReR7I',
					'5cyCiMVwGXA117uJAGSMZqpwcYL1ovWhf3O7J54ysJTXM4QsQtFDFfoJJio4' => 'ZVUoeRmtVzqmHbqJy3CpTBLKsRrgmKaRjoaTnWwzPSrKnziPMiUs0FB032n0aX1jX3wfI0ZMxiqzwgprVsE0man6eeZTMPWN6o',
					'G1WF7lwri7c3wuKGPNAwvQ1GUOuARZCS9X2YDgIQcZD4y3bNVlqrtDPknDXf' => 'hCltSOrz3oJEGKPA53TGVwOHNUpUGpNILVat8HF3vFCmIiogvwBfls96W6IRfK3sVDV36jgPjGsPYLcqOrALNVI1hjgaLgSASw',
					'Pps6Igv5U6h8S0wXlDaJKcX1kAG9QFmUecExZuCCorSTLera3txMUq1h9yzZ' => 'QgLfnvqC9yFeXlYefxIWuLuMD18sSGMhfy21hsBDteFnRv7oMv63Cbt7xOCl9O30EhW79Uf9XgUOCoI6EYh9FakSaR6PoImAUw',
					'8ra2CDFY0lzeTOAMipfJrQbjnWQEDYrrAJioLWKQjxkgTBOvfXZ0ZeeSHUHZ' => 'AaChzzPDr43XjW04mZlyCP8hmrmpYGG2fjSjDr9Xc3pOP3DaRmaNMJ6T6GrWLJSN7vGQ5jQZcQGgFXqcTxfF9q0u46arhvmTRH',
					'MQ8lgqJpa3OPW5R4POChAOMSi9OJGhqxD2lPOxI6K9uGDjcS3g3G8EZjN5Lk' => 'hTgte0GtbrjBIjnYbwi7MfFNnm6pR3WQuvzUClwOT1hWA09JrreDIvoI1NT8uQiYPp52L2Txb62XN9XLiL4EhkSnvewO3GNQl6',
					'0nJB8JX2auktuH3xi17YuExZVBu16q5RFrMtbixQ7eN91pU1BqXi802vDoXp' => 'jXtV7mwa2PuTesZMUIqGJYAPCtkhDpQJMziJ8HyzhnCN75BFqIiZq2K49maJsyNUl1UBefWgJQrScPgKOhU7cMkSz5EWwVvMp8',
					'LELI7L9XYqV8UILet0lMJS1KacWrNFToMyH3DEyERArfVIaNN1ECXbSkeens' => 'SgER6JipqnS7VjEohWzMKjJpVXGaT4XeBe8U7IDiJVmn8OKi5PIsOkUpJikZAxcxSDRYUEvNAAagZyNeofN59jFLEVr3SMeWNS',
					'qzCOIPtPlFPSS3mvHtZ0JrDfNLwTuTnN8GsqybZFG4iZaFRrJ0qODTKhZzYU' => 'oZmIzYahP4aLmcCYv87B3ScflTzRqFyICtnWior5Re38ZR1HjRFmrQSA34a8FUkO3F8sc5cEO9mG839hU9hvDk4hZkDaMfUsfl',
					'O7hjO5NEUbz5RG4cxITLR3bGaV3WLsYuENa45AIigM8otwLYqpoWCo1GB3Jk' => 'Thl64vyXfPp0Fy1a8FteXZzvjD3kVFwuGYSlr1SVEAv9qAwIZgwh4AoxpnPnDB8kjxRvNbfwbnOvxobpE01JkyA7pXvyNyeerA',
					'j2pYDQKiXcoBoq9gohOklfYEsVPAGKKPpX7DsYS8nZQDHcbfSfI1K3z0DJb7' => 'IqC6yzl2fmvLb953xzf600wyQ9yLU4zBXy4zV1JSSwkMviP8Kvghhb8HGe1K6Y9Exwt8H51mpTZtxczFli3wKFPEywA1zfKJXP',
					'MxlCwt1kJw47KjXt6mCurHGnoRvckNqDmuHR5GDh3tFaf0G4EQ9612Fvr7BD' => 'vpmRzkPyNhV3QOJt43um8lKOksDWwekILUwaUS6RU4gTJiTwrASpenjinbgbBC1LaGnzKb5YoagMPi6rIg256yLbYf8EFl7suw',
					'ArXp4XCcg8gtmYxEu9t2vnCpCJNFPtNG3UL0jL1yvSo2H71OVoaV4Z1fXTUT' => '5Q5yue9aeE7CLsO6Noi9Xta8uc0ablYZZ2EeEQzatOu6kIPpSeAS8786FrhNu1LaFWxPKYj7opTqMPGDKlQV4mjfla94YTh8gJ',
					'kscpwgrfEymzM5E8ARFoBFXw7SIWiPFcugeM5ZO6ODsOEDD3E9Y9sFJo6OfY' => '3QhYLyKo3foknwHGX68oBXARy3A8HWc4gyeyYntSTrZTyGm1YW2UUhYQ8zGH6yxYr4J49ccrCwRqZDSiXEC0ygzjpW52k3QQOj',
					'kv0pthOoE6UO1tS3Pe174Oeysei7PqvUQ9uRVCeSY9YZmS2N32pqBmv8RGZe' => 'R8Hlkcy17bm8Y2TycFJ0YoEe14CcDhtCTEEvsRsqfYFjqqH1LZciCb4Z9nBrfo0NsjUSgNShtH2sgNj1FzvXYbxcO1LSkG9TEw',
					'R3SFgJT0JravRl2pVWuxS3au8tS3Tc2OE6j73SUoWmx3L5ENKcQaTsPHz8bf' => 'kPnZv6NVicCZTu6blNn641IGKuLE29NZL9aY1ZWXbpZAjZDiqBvXmDiL51Ghs5BCsFa3YG5QLPV2knL2I98K0KtNIjeflqW3sE',
					'TKXaQ4i2movTaurL1tz1XIiTF1VJJxW5rjqoAy0HQPKb5uAPerwusoSXIvvY' => 'T4rfOQOEu0gf8lnK0P3P8XLuea52ELUBLg07vMP5p70fXinVHzse5Y6OUy9Kp1JNR24wplENIBDaN4nvaClQbHt3FzrKDgpp4R',
					'ppwIKMKSyUoeVsjKK4XqzAnkM4hptgE7kRNNpHvGQJvh1Woj07ZBPE2MnstH' => 'WKJEtQVx5K80lfYCJOlaVzvbuVjFN7wQeZALZiLVU8TCSthBpFpfe0QSy7UcwcTUraSJ7s9knVksSkUTE96alXZwZZqf3ry4Jn',
					'KjIMsmLuI2MNRCODK10eFy63jSwP22Kq23JWOAuVzxc3k0jRhfPVz3VycZHW' => 'ZX7LnJY5jJt0DCPQo7fjhsweyHRD5jDMPlQTt5ahGnEfrN9XTyey353GG97OVPDJ5njIW3SCxajWIv35oQGu69sZphUlUEisWD',
					'viwXElsAaBil0gML6wPVGMjCzPE3uJkPYTcJ5x3K7DxFkm96m8nmpNQ968Tk' => 'iff7EEZxskLeVkvKwU8Ltnw8YhLAr18x24kWvV5LaVr03f5mMDvAEB2MY6BjBNOgzP5sf6M4iYTKTLrmoPxl5IKMpgtIyywQv4',
					'2uI2br2ke3LqAXztKxwZojWpWI9vov7BAPgsw5wnyPbPGMqnXomfy0zD2AsM' => 'bquBfnvwDu8gV6AGmBEenH2VhYkpaBNpBkxvTMIScVFYwBv4P3hzWlLJIbskJp91GcT7WvnhNzbnTMfB1sDW45V4Eif4W6SQhP',
					'i18iA2IZIPqU0i7BMPTTmWRy6rKxMFlH4TsHn2rL230HwmSXjQXGqmE64ZLI' => 'gX3Btgz0H4GoGOReBOh5jp0BNtb76bNqTuyS6INfMnEPByRS7scinW21v87TeLuvwSeFKrqJClsei908hzizZ88buzENPO9gCw',
					'aqaAuRtfruwshLZb0Ql8pGHZo9PTpfvpbrzm6ojHR88poNtOntJAa9EeIvfC' => 'WLDzTyCIRkVcprV9ZKU3xrrnO9u5H1tIUb5KaO8euZq1lGeEyP0N3quD8Ys5bplbb6aopkUz3CXzzJ3WaVJrtqK6QgX0yZMw2Z',
					'HNyyUFWfG6cc9AnO1JVcfGSkuaAaJBQwq7QGm2zvTJG0gakgusSYZMt0uDjK' => 'K6WpK5Px9bYMaF7r7qxCDhpQvu2niiBvZYmgLTHfYOUSRnhmL8mCFCCvJOlirkrtNQE4Cq13kWGjT254DgFRalWnGcIT2JlrrU',
					'hpcEvtFSLTHpB2GDTuhzPzr1AzJUlxXLt2BYW9eQK9zvuZGOlhu5EA1AQpqT' => '9ZRD8lmGjPJRv35yBUR3VoujfbjPBJrbXuZEIFm6Y3PcAxDo1eqBQrmalpqDBAC4xybPiaUwar6fuZoHJVbY3Dyz35guwPW81I',
					'0zutM36owmvfoBFmesmxpneoRSbVDSCMiF2JNUr4TwQYXtt04R6cF2PukJ9X' => '8mCJHpCUZE5zOSMF0UxSZb1fP9suFyZaNSyxkElS7HAlNMcY6I6akgWN7ZPC19sRZmThti58OxanG5997tZ97sqoCRDsiT7MKm',
					'aY78rsLin6QkLEYtb7yPl3p6eJUMvE7WIWKwwwBZ7BBWTbNOfMFLnGFq4vhi' => 'gbGh6B3lP5QlzAJsaMJjMSne17ljSZNwASMDk0ONWQR1a6Sx1aJuWMUbvx4e4PMG1QPL26jEMx5RUbU6ROrjJyiuqmtbx4uKpl',
					'a4M01XMP7AiHVRLKmqwMCcSwXgoCc4hsGwJ3Vl54uhMfarBlxr8iyrhLOELq' => 'iMRQI7XFB2zrVXuTIbDWJtqSUOSxt97FHbHagrGyNrteqtgMaH4wZ2rLSqCwk6TFDXjeG7CYWqw087YjtcJpLSHqNF3otm49vi',
					'ErszMtIAAIOuHaQuo1TK534ZRXi4Skj9ucjXSuQDeg7YXujupVATXQQguTF1' => 'IJxZPnVHT2wWseEwAtUYGM8cRxHTchPiE3Jc4eqhSEU2vtxKx0yrp0OQpmyj4aS73SuX9tZjkCspqBUwq2o0ejpCqpCega5TzR',
					'aIZ0VXcrPOEGbkKfsuGj6RQDMLvAKUTnt8T76gxa8N9QTBstEB8S96mjRvDh' => '7XSfz42jQLI0hqg922z21tiEec2cm0ozUGY09LK9fV5kBllVpombslZGML04BFPaU6FSQn3rZG3j9N3aePEm26iwucIE7QCUbT',
					'hjf58jDeSvhw27meYaTJh8MfVcXSGlWlAgU8QT1MuBFNSjt64m6aZ7O8HaID' => 'jfO01C2Iil2zAJJnURfYjvTfmKo1ihFvUxjtXO7zEYUufGLWPC7chb5OX7EGHwDhYMgED1Qq5VjWOGX8zVYXLnfbYaMbWfaYjg',
					'U3PiecDN4cS0kw2xDyUSAjbZihD5RE67xmey6Itni5YVzgsI7SuBstE38xqE' => 'L4RjEOJ84fAqKwbgDuWH8MavGabuNqGPb7SnYY1Pej2AGrAAkxvQgQXp7bf2DnI0XHYEcpevZiChwq6Ja7W0jiD7aHOrx3ZPWa',
					'mLy1UYntiTuHn71G8VQ1VGmcPtHWubUCY6NfaVNEKNtjspmzYxlliGAMA9YY' => 'gNlXcLNQX0IRVPQlnFEQ93BlTargwsWrTDGhSsGqNKOkGDfn11GoF8UmzaPTWKZ5hTlMymDvSXbOach7En58SV0BMOyBzwWHtS',
					'zXzmg9uFgtzc2wSMT3KHaiURoqh2JahayeQJJKl19E4UT962giJG2RaPFkGD' => 'zN9RQN6QIblimWqxtpjnTjq0O6WcCcKTwtsi1YPij3tMBetC9qw0erMNqqUsQcYVRaur2oUGIT5uWLvCO1Q6fiVZ5Txyw3s5n9',
					'gDY8nyBa4kZBRK0qNBh209SlH9iS6SNuIVwqf2GDMPMvfvacNYy0mTfQUkFP' => 'bbak3PF2CbaGzyPXfwnXnznYF2ErfjtRMLWYkMmwabT3ImwtJrh4pDxC73WwY5bv3ZQM3V93zE5GOy7ptgB7QgLThhjVCwhLSm',
					'FzVmYchDxXaO4iskla99p76UamKTIqbtbBZP0MacRu5IMsMF8AEP5LOGrztD' => 'WNCYLp69ZzHPe5D9w3SEHRVwq7W8K9EtClkOPP771z7nk2l2Zt303l2eyMMzm5GtDnzzEREq9iY0O2sAU5glHnFawQAI3ScT4h',
					'tVPMiquLu38jG7JqRzPMAMsk4Rq0uXzN0oHMcZ7TfHaUfW0FH7YICmlULZyv' => 'mxNlIInKzDiTc8RWGqY1wpwZiogwhMybG1T7OkjuqCtrIx0csmu5RhySnz8qTkni89iTjUcOhvebF8M9PgIBzXlJaoaAK3729X',
					'9UXkstj03bNyXovTZbze7L6pWvJNXyQoqqsnv4x6KekFjyX62PAakFSgv4lI' => 'HDMYjXMj55pbHtXeQtjnTxeFowEqV5smJNZNfLCZsozwA3RqChCB6jx1AwZF5wwG59ZZf50owYCrN4KNVuFn9OWUBbSc37etWM',
					'o5szBCnu6W7ilk8jpxt65cPmpwSZgTVnzIpOzBGmcxXtAZuAMrmvwbvZh0MM' => 'i72gv5KSxfG6pBIqCclPzr9TwE272KUwopG7b06WvAKKf5RGOxlpm1y0oHY9TvghPTblbjqXqDzOoAvD3K9NO2LbLjpkEjeWjG',
					'RUVksSogCb2kjbOJjo28FlJj7oUfDhEuJT8EzT1i1eM62IWhBu6SKlj76rcr' => 'vGFcu7hGZUVbOJjTh5qJNRT5mPGgqltjtZpZewmSRmLuLBOzs0aKB7isFXJk2KNv1VXhODjZkFIeDWp3PEt1KLxNtkEDKBjHcv',
					'mF45PtbmP4MpUmN6f22rDne9B7HAcJtIehADx6WFtZBUyK2GRR37MTYezrbr' => 'NkVy065FJBy9Zp7pSKrHvwfbKxa64neVJO7Rt8Esr6piRwURO4UrsMqZL8RhCgrTkAnIzQK3wpoSRhcFt6WhVUf0AeMtKxSrT8',
					'aoZpOx1JKNWCykxDujyF6nn2o44Iyqmclip4kOCzRS1zpo4WcRY8zw92Nu8a' => 'u2GNMm9gkoyPfAnczHnhLyCguiJVZSLZpHH6JMbWqrXaWISt1wALIjjCiWF5SlURuVzR3AEh3PPyPeTY1IRoiSVF7qqAbsYIIB',
					'sVbt45fnr0YZgtGrWfqY7tzAEiTzh7uH72xGT1qVKj4ko9aPtTxtF6ER2vA0' => 'Rr1XWMkYr2owKBn1EvonUHgeycAWR3UOwHc9nkyUI13jFEDpDJlNg9zpQSziHWfrpiQr98X5ApCxlzAz9tvlVkvOJfI5l92UPx',
					'zsFMS7mCOUDoJqDlHhaLb11JpN3ZBRibby2qHLoUBLos7OBCjELxu2JmASOD' => 'n9L8wFToij6j8PifRAsGyol3ivDKst5KfH7tCbzMGASLLX5cvoVW2wrvYafCC3wmYyjhgxvFggYB7JuQaVfZD1i9ESg0nX2ybc',
					'kOqtnVNeHD1BVUDz8QDLDFM1pETRlg2WcYSaIetpP5t2H28ELxCKnBbcG2DK' => '2In2iovcCcHT4XfX8I9GDxP5PBqIBeAt6AM3ZNIjJafxsRcaFoZZryiOaHMqNfRwMVJzvkeRbRaHmFkZ9yWNL45XuWnB7VVIFk',
					'MXQtxiV3GMVDr9LPp7ftgsl9hNFTSmJyFolZjkfksTjtenMufpinkjjDkm0B' => 'HmXD9gaq67wEf3vTMwsucPsubJHyD9Lphkb2R9tUeJTSs7eMR27KTWf52Z3yHDqbEx1DmpPFLsJwpksm4z8DVO00DjhVh7tLAL',
					'wZpI6tKNhBlZfIa4s7nD4mSjfKgqoNYBcAtKBLn9iijXF8LklrfAx6tsUSS0' => 'oxU7bBLxPH5ZmoA7ImYokyGbUBbACZDCQNkjhhBSEGiebcIjtKoaPbkb3OHCSfoX4B05e2X23zUVCDS1ipSlvzR29NutNcNxav',
					'YMnboJkqQTMx8xNh9GbQlEgqIYTI7PHOZVypeBKBwcLlwZKEGNqMtQwHHSIw' => 'IqCDOn3FqwC4Ki9PI8PIQMia5NW3W6FHfWj0UUtoVPMJ1LQBtbs6uTkFPbD487qc0TEQyEmjXXwOsy5pwQJuUtF0eSTSfNeNGO',
					'emxjJ37MgFgDJLAKeZQLkGhUHplSMNTMx87zKe8fTN25XfrwbzgYrk3U8lp8' => '1E7UnFZ43th6e01WUmIHlJr2zwUCavV70u96vjx7DvWNpcmDybEuklXyV5OwEHEc02ZMPEvPlhJHQN93DGxoo7RWyOoipoADLr',
					'l41m6rDR0RkDg6JNGmxQxncvHAfZEEom61Rt0ZTRjYVQpi1EnLAA0eeIQYTV' => 'uimjBWwAkOLnDVWDvESIIYRwibXbMPWao8S4qgUwtqEKtBzTmamAn3cZG46HwTFM11ocqrj6OWQAGjs53xzuvp04eAqebhnqWw',
					'2heyXrxkSnkU3GNv9vgCYKG6jIQpn0orXk1I3AHnZ7c2pNFwgTvajomBq8BN' => 'surYHzY3tVKsaxeg8Ex0irxa7GfIeM1WXEjXZG3qYNikPZzRRzcZuDqFl2OuVjHiNLr81AJzGzZc8pBOBCpzUQw9vUfTvg027y',
					'Z75R6tNipGBKAhvNtucPAO5Dns5aGPbsaSrVLK1f6qBL7ePmLr9qIqDYwtzN' => 'Way3w1y6E7UQP62j9rawO5Dqczg8WKPpKHqFLRQi9YzKHt9me0gb15f8sWDqE0VRbSh9S62EE0jEQ1ZjSQxc8s7e5KgllkxXwV',
					'2kqarJe1BRHkhytSpPZIpJr5hECyeBYBCjBtRYp4RpaWrT1AnKsSppnhumoh' => 'lKmHcAwUWSwwz1nRXiBhRxzCGcRiFVbEopBETOXjiItzj6iAVVuCZTewjktyfMD5aIPfFk8SMz5a9Gyes0RnvbPD2ykXkAHGSn',
					'yoF5Sn7bkyiNVJGja3pAhsIE5OPEpzHTBFsqkUGfBZEDQJmNCIoPUJeFqe8x' => 'xxgucAm4wYkrYUGEMwtCZ0VUxpuSjozJa4DL2zjflsqPMGnJHc1XXcjI7bgC6N5bOo5JnKyo4th3YKjLUZorMcOY90vb7ifDrk',
					'gbjN1t6MF0aUHnqRBCDi69BBop7NjUtnetvUFsPwF6fsHMRS7c6eVV5kyGWo' => '0RFzj89nmEAq4rLfjXlW8CxgxAwRjQuyLR9qUPQelcChvuKO9vE1pLuzC3AkihAvE5ulN68JyNS7zzlbJ9wtjKANktDICXgPxq',
					'YaKeLPJMDBRqCLbK8t5wyQJOyPHjCf2krppoPeZmkrjP41EhaeFAKvZktIUD' => 'iHIJwQkvaTxmK84eb66ZmLGsvIjCg9K8VyFNtUS8yUnc2Rv1kp5MDWBrPPqbwqpYvPMvHPfQ4AqNQ2Di0Ifg0Qs2xT9PtAtVjo',
					'QlGz6j2rELY0FZB4hVDs2ocOaK0UHSBEumK6nQ9Fug9bfgy4li3hVDOC3Q17' => '8JrJh6knRrtywVFJl2wUzGxRXwTCJ8NSG7zILL5ba5jZHqzAN5QbInvUO2rRryuT5IXk6Yz1RBnHn2gnnGIEAARLuiPmvythsC',
					'Ki65lEFTGDnHEj0MMYvEKnQ2uBeHI42JhLE9II9QkBKxrAk4rotQe7cFsESn' => 'ew7uV6CYotiwmpZC39lAmk2HcYOS1LecsjNBIP1BZZuUWLrTK4qRhm08y2bftRruRIiL9mV24W1VgQZS9tNRFkLq75braEuVXn',
					'8BfUsAnph0FX9oY8jUxI3o3cbZl1k1Fx1fW0hvgJuw96vw55Fih72U4Ge30z' => 'WtP4HaZNgHEa9YMwZ3aqmzmIpee0rMlosVvooL0TtmXKn2n5NSS6hrEHokFG3lFQ1xRx2njwCg2Cr6sulR2SrhfLObEWVyNNjQ',
					'JFOzBAuDOuS86QjDx728ZJJsGYkvnTzB5002RctRr4T54r2kJTU6PxJ08mgy' => 'bDm8GEBD9AHaJjRgiFc1f4rRrAVBW3pqbJhrJMNYFvPGEt7iKEqS5en85cKLYqZoUzKY6sgC7JvmQe1NVxDHm5wW93zJjJf6Nt',
					'2PvfFGjnUcc7kVc9MtiSssbHka69bAa7efspifTSwzG4qQY4yAKzW04ovDKo' => 'XwzgBUAn0lGWuEi6w3xcujvms4neeG481iWcSQr5N6qiB7I5qTKvvzbCLirp5UQDvkt8XzqPKbLv58syzBhbGUKwLGpWUDCtLj',
					'tWFil60nBbxyDnuOvJhfAbK7rMnOnyuLYlzH6Jv6YqZR09XBcrtKEDl64c7D' => '7GO2N4hi6rl39whmCVAiJMUOmGAxpL37tLm9DJsDJaeeiUW3Y3DU1peyT8AWyeUlnAVUrkrK7riKZFjYOqZiJL91Y6yw3UbZV1',
					'VHaxvj00Lb4MYqjZyWFzKrRC5MrqBGzKMyHpyvknSD0h0si2lRA2uRUUXKwe' => '6vWp1QMzT8bQmBT6Isi9Q0xQAqqT44fCnhOEZvhEBqiLX13JUpIwBBTouHfjriF6MfTUB5YZl5pIqCRLNTbWECwzsPSmF44197',
					'uuOOmGac9lOn1YOjY21Me94ZPUuBBjz2yG60gFQDFTKaMawUHjYs1zTJITfX' => 'N09B74FluRwMxaXMPOZLMoVZIcQFkekkVbhaqD3eQ4JlykWzH3iFOLvbV42p0HJtgPC4UmQyKpZZIWG3gYr1DI9QigoC4lT3Nx',
					'7D5jnVTt2F5zfD3AKc3tNqsS5JFMrLTyVcrUy3R1yBHHzlXzUoFcZ6eCcmla' => 'k5uV43N400UY4ViC63rhtinP9ONRPmFxaQZUISea3WrgGrTGz8v57TObGnsaEvN6MfajAUN1ljEZovXiYfPaBiE6VcGq5Pgm1b',
					'B7l6OpYQGnnkMtqx5Po4LNGwuGVglkOFtErRbTzFCJgqJTqccc7b1wYESesL' => 'jUZHPzOUCBmH2vY9NzFmZke1QsVhgBnQBqZZM9NRqzWfZoNLrDpN7qVm3ScBMAFRvTI9k7hv4beM3RJlgJ8ylxHVc2CJtgJk8H',
					'hpBZiE4cmN68l40qeDCOAQG1k472KZZIECmnqHnYnqOo8yXk4Y4wu2nf6pSq' => '3m3vnbKivNTYal2gC7YSsZ48gugR1QUeNUApRDN0wAV3ogcau97ju8HmKlVxMtH0KU0otWLheSXSy1sPjGRaZIkHryXF4o652S',
					'6kTRVs9nnSsD94A8xo1S80vQEsMhqXhGxtBDFOIUlwcSIbcuR8FzjAPcHWsJ' => 'ouzc3RKscSYKgTD5E5XQ3Ii6ApQekkiNpcxX1GjaJjJXsZmhxkYGTQTrHHTDY7UFNUVbhSK1ukrVpEjBwWBCFusfbzkCNtv0un',
					'g0orL9xgFJl4L7zT3ns9hOwDifF3DKPFwDHKV935LSo8jUVvKxcF1uj7xJgY' => '5w0lIANJbMesFXAb7jl8P3woqK67XYGWTMh2ZkzYbbqlsqwBkfXgYKOWwrw2DQixJOA3EG5MTjn0RVJFVy12oKiNmA2cRqZwMv',
					'bU0QfwHulWOh04AurgQjbcRiySzLZKkwGuUWV75kYaIu1XElzCVgoJMIQkZI' => 'Jh44TPomJRYIwpHWJGsz4m9MXp6GXYW2qclYLIm7NRmYxa8B8PMnMYHJbcrnmhFP0MUr6Zu3a2rRlkcXUkbr3OQkGJKP2uwTSl',
					'Wa0eNlxBNPXZnp9qrSOWyZIoCKDCVbfva6kChSUVYcRG8VfetpbYNxZEFliy' => 'ZrOmblqOEfLK8UbgGF16tgImw7aefMFLbUPCDDxCy6NO0MzWqCcQjvkVXfZ9Z031DUAJoQF46QkWv06MwgYKqwNkOEqfxaa0KG',
					'syQ47gmFfcFj4Ico1h7A02jYjs9mth6FGpXtnMLgFu4kkNNuH7hyRJ9ZpliT' => 'pGjej1afrauzpE9nvmK44baCRI7MpvPmjoviJF1seuFUTNWSwCzlL6gHZ9OHJ47AxuG89AFMZ5T3Z2Lk8KSLP73l0ZE5ihbFQ4',
					'N61awhRrHF1iA1eaiinRyeELjUbqQgtl185a7lxKY0sNMow99QUeGfxiIS6T' => 'EsZ18kmRJqmfREetOXtE7WkRlP4QMRMVi4b1t1yzII0WyFPa0x4B4GmvaNU7tuGxDE7Om8RtmiWwBCSRZouH0j6avissbu6QyX',
					'ZwKyYk11MXTMj1L3jvNfM1qMeEe25UCp7cjmh9wM5a2GQOVqmuKB58xt6WI3' => 'EJcqvn79AQIgWhimkTGfhlvGRzjBMnjiqX33pkRo8ujCH7cbom7WCsgE2Im9TQZv5lc1kwqS1ZyfwCOIOpy5FLWBDNKpFKQ2Cm',
					'5oQs2x5ErR0InDBwL6GNb7Z1fvJNx56nJ8IQNlyY7wPxSYG284w6H4T4t1tz' => 'kF7lpiVNZcWfXFu7UQhbsWM41hvZeS5pZMgZTTYRmyvF76VF6pDJzWgKbPulAnMGTAOQQv09EQxO6jjIULveNvRX60JWqpoMn0',
					'ox7ayB7IC1NX3npoINQqf97fUuaVkbLLC59t3kmDwND6MJWlA57Y4BH7vaZy' => 'nN2lz1No6ryj1LbeUBXEEqoqWZf5mO76VZt5W2NgM95ANqxD1Y5rGI7zfM4IHlk3PhFr5UaGWfjJOSMkuTyWj4X7mEpsypeqka',
					'RZNtGBvpX6Nr7l4VAAOBF9RBuPctza1Xprnh0UNxpfHaAz8b5Dxzi0JON0nk' => '4StlGgsIWMqZZmctVXq9VheiX5J6yNCI5gloOm92Ry3lrvPgDNMa1rPtNrWCRh6ZQao0zMoxD0fnMsP5GcC8x0zVzgWKtlp86F',
					'lrmZgtOcwr8tuuSIOKqTv5JZoXU8BBPb0k0Vwv4JsImNsh1avC31EfBSRAtp' => 'yU4kBSQe2jIAQbzlT4LAfu6SusNCmGWTu2GCUTQparWZAE6SPWz0UeWHbCPrTOINonYvnZOYvNp40TRxCuPTjaraGyvgYA7FYK',
					'9KVATs5XVs94mlMLoQiTSQZeYURraPw97kGpn3D6LunanPoiCa8ozYMRABrA' => 'ahZVqwwOpKxg6Vj2jn6eU8umK88T8Dubtf7mKIYExIOEV3rjlsMJbekhHk1e0rk9LkeYHPe3i6P4uCoBG2Tpro4UCw2eIpVYSg',
					'UOFmXGkEa9CUB5YT82zwqqmjFKGNy9sAJ0Y8lwxCID7uLpgBE8AReyWZzkjA' => 'SuRSWyIw9GgT383gxTHeszfESo0JuVD7lzUtR6j12Hk8bOGPpDJmfjUWHGPOyuG6mEnWi206ySXvKD1XMEuqsbTm3a5n5Eu8sg',
					'ghDVSzSaYLBF04gzyvl4Rnjh0xavXIm6YtJfJoAQu2TXF21WOipDXMIOezQc' => 'NSOoAtyQHOrOUua0fc93sOCwZNkYCS96njmKARBEe3Bz6Av8oJp8FMy6CaQzRMWSbaIMEw5oRmkLVvP5rIguTm9XLDgkBzfVzl',
					'ajCtt5kPLobuV6tKYVXqlEsmKXPPRjD50nP9cjxUhp0naM74lmrZBZIAVvlv' => 'LwMJt1syvuZlHbOxSAGOuZp3I3Xn85ZFwrrnqsBf9maastW9XZOyZsM7NB6UjwpkOXUlEFQHKvEFReuDzjRlxbkuvQuPgi9YhN',
					'09m9A7iiIJekfarZrKWfXgfIMPJxHWPn0n5rslEax45oBfZhLF705kvwOV2i' => '9NZFcwLFuFaU1GybzYXftCNGTi4XeFPotuTQSxj9swLB6pER2FnHDQrame1zT9ow3RbAbkXrZq3SU3BAYf5ViYIgm7oZvBPLYv',
					'9qi1RE6lkNrccZj7KBm8j0oiwqVh7qAQhmwz2BIxi8VRUZGhgvKOTKDxSmIa' => 'xmWORkWattENeoJUFv0zwmnYXIljysnh52BJOiWZbIDyAHGDZHm9ynFTVZ4xtjlZ5lffCxCayqZ4lc4RA03oYUOBElAxaCJyIk',
					'9J5wVzaF5biionyKjqPV1MqQxubfyYWiXXCWIRlHVTn5LqfzlV94lTKxG8oE' => '8GmNDOAApjqpWowLE6AA0OiXLEjClyYyWB6lVRk2uEP5GQjwkqrJKh5q4R1Fz1AAbW06HIBo7wz4vhlYvhCgqbF1urXhXi6pD2',
					'QK9rJfNNh7cAZMzp8CjAV0uDHxEVmc5WogJAC6RRGSpEOWW2urJ6U5ExSA9r' => 'iRJ115RG9XkmP44MC0FVhHxrParxMlvFbXauitRjfptcI15uEpRHY34vcQgO8zoSipUxm5QcJuxVXOVQSOXavwY9F5xJFXqSOR',
					'r4otxKqIju76zybLrLjFIXAu8O9EwTqLAalUINDGbYqhygJcBG6QZp7PQETW' => 'qZEJY3vQaSUNGx53zRbbIs1nHaPMTqrDqxmlaCQUuboc4XIyHq1enwy7q7ACVm1DIULxDyqhMD9VcHx6ItFJJERB3zeXtXUqgw',
					'GzMeovuqbyk5GHSe1WgV6BZeb7qQIRmjTsAyeeNoepOi6HtfceFbEKosU8fK' => 'vz9PLslQCwkOs0pDZpUAvboytwkTbnFGjlZNjKbHM0PSQWt7hqbqVt4e4MypHm0qB8IMshbn5L1lQgX0ZgOXFq0LMnHwg42ohG',
					'nHHaQKga40XIcly7B12j0vrUTCZwjwC85E4DTVbphzKgRUC2vnp9Hpr76Ewh' => '1KSQAQ3ZDtrCWPSG8RaskenGBfa3v4hoL8JPrTlrU442F7F4hbYLc2CCT2H4o7RHcvpSzPSPE3wTAmNpAs5VE2aBaICkS1nmE2',
					'G0XZBg3eAhX4nfHU6qg1h9JQwz8M8AcXE6Nel2Yfxf7wIZ5nE96SRpsWekVa' => 'e3wDu2DqHP5RvbZaEaFRvZsICiiG7lAojXenSBoKHXlJv0fCoI5bFSLlH8kFzRiQ7HQ1frlDpgCOmIr4Ja6iqAtvgxHZVgsJSI',
					'89zHccsgJ0Hjg8QF18YZUGkCymfcJz8nXJRDFSRUI27zeMTQHAbceDTYqbA1' => 'yy1rZvzj4cJTJNNTwWnRwpLG72HLrkILaKiY5gTyJXZi8IrBP4Wnx66mtuttHi8htTJY8bzjDAJhvy33jVN9ha7TsqV6C2oFJS',
					'QbNhriwmU7qtKgh2szfECYuzWsH7pcZiAnRyea5PjBgjoGZjGZaoIM4ACV3F' => 'rSerE94y3TGzAIXyhHOnF88YsIIm2L6l9f37JI47peTbTEhJXlvxhYuICXYKAQsEjvkhowgvuvTn93H8s9zh5oFsWG8OaYocqj',
					'u8e0znWqBzTLBxhv3k7zHD1rhmgbSuZ6Dv7HllVnwznG0LPMAk6mlJ7uYxNx' => '92V9ateO0mUDgUOUwiiqiacwtiAxeJyzh9J8aBIraiw7Ub6z9F7HZjBSa9pR1nYTKe6pCfWQVLRRBS9NmxboF2gRmzWmnc9xv0',
					'0ULzWe8VJDlQ4grqkqfshuQeQ7TT8WUSfNznKZApNeeCD8g80WqKgXCwUrAa' => 'MNmgkeIW7kuIONayAgKBEjX4cxthr1n4Hy5Ep14QSuNaceaAgbqPzuNhgFNhopgDDioHwCPDk3bPUeOMaTRvLmiteatbLYqCE3',
					'LDTRqj6KxfpteLPNjWp23xq4sutGZN1pZtLPrIy8eIqVpHgjTNFbvmmCN3JB' => 'XrMlrmRUrf6ALbpDezt1oCm2YchoqC1EpzTbNCCRk3jYOfEA1mRJZRDzG4D1reCRTjWaqIqpnI7un8fgFRPHSMZE2vS37LBoR6',
					'W1cq5syTaP5CyCKX68FC9uhANPAOlFAcP8l4qchp5SK7HB3rxRm2UZXwGeCn' => 'R5uxPwM22bhJJWRuVWvIFaqU5lroitPwE3UrJzIUqCxeipkGA4Eg5RaiSfftcGflPyy2Mv2zNetROZnq3nwDwozgyehziI4NMx',
					'AaA6NS3PTihvZ3bnywPKXJxqFKxTu2E96IvFggof8YsjixuA0PlnJCfILZ0z' => 'GKFY2Dif2CtPZ6JXUCRrKvkcpqII1PHUtXP2fkFQQPTe49rUz0mL71lKBD2PjvKE02wjoqMQjiXVZeeFIYAsIRs9wi10ahLWls',
					'u7ZhQn4kzE6JtKnGYMAwb4YEor6KD8mLh2moeMDRZ4Kf2LjFPI2Ug5jGLuTW' => 'XfxUs3SDDrDEpj0rfGY91Liy9KF3oqrZMKSxglCvEbFBE8DxjoO6wGL5bFs4ar6Bm1G89VCByiqvooHpbAsbV2v4Wxfkc36CYb',
					'Zt49h75O1mnmRv7ufyfBIow1wDaccVaoan2eA8Tywtae6pvn72sYgJ10Tpcu' => '2mWCxUT26ZRQ21KRaQ71qWAcSvoWUNk2wRDAjYp1yjuoBKVfPhxi5685z7m8b0nxuo7gOKwaDY5kvY1FXHzmgDhzYaUMJqy9Ps',
					'uy4tMAw3YimuLiZNtMxo7T6TawHa6mSfvlcwIkjA8RDWMvxna0z3AVkZeWLK' => 'bnw4XWkGRWlH2euGYCA7iGksgY0lxp8AepsMyjI0lp6ajZLn3umu5B02v4hqnVvu1XRaCtTbf24XNjCAaSsPGMq9O6FepD8gZ2',
					'798xTscVJXpPSlyEExh2uImu9kDr2ty9ZP8jOElgHul0q5LZtitLloMUE6MI' => 'eDcUhFRRH2keYcXt07aJm1uZrK1T7kHX8G1TPjefMHHsfxlLYzic4XTRrV4E0JJC2kvjL7lmUBZ7JaklPQPLv1RpwI5JsBN0p9',
					'RC9hlN1pSHynIxjoQDZeUoXBDB2SGMqQRhHj8ooINC7EeMrrHmMt3q3cXsp8' => 'vT93ulQVCRcrFT0lQmXXZpbq5cAovDEXFfQxi7mtKOlBWRq61gJTSLRuXjbsALUEXxRYFIiCHWutChAlCuk5jaU6Jkw7GjrFkr',
					'6QXyiYnZPLDT8833FQ0LRxeZ27UPI85hEcGL2HPjrkQZAK8rBEyhSAWBOUVu' => '1LrA4Hx3puMBPnjzvz5xT3Ha7CIAg02LSlUASSZQ6vel8rW59XAjOg5ZkP1VC8braYSC5YIRcPVxttxcSr6jm3oRoyKWlrhegt',
					'igEhq63xbcVOq9A9lA15UHI6YieaIfayl0V7Wgt8GeXYutQoI41ALGPYCNo1' => 'cLz86iX3XzVMyfipvWhCnL1mb0wMEJQj3xccOnJEv5OsrWezQ28mqmFvFjcUXSDrKxFaaMiTon8rRHNgIrsJ0TzAlJn98rUT2X',
					'tGvVG1r0VXicikoSQpRVTqVbInWOPoAOgJ6Bqwl0zNChyuSIDuR4OkBI5a2c' => '9CWFvjciqZTWMnbsg0EPV8r650CKWnabnmEI82OKHUpo5ASwuvn845SIPhWD7wIBlEXbof0iKQNZDHCSxKJ8F9uyGJRJ8C6PCs',
					'wZ1c8LG3DiMoh8i7ZN45pMH2hFDGjFoZeUanNKFnZpKNgXXYtbIkOlFzRKN5' => 'HKMlYPDprPwS3laGybYi1Njy4DQiru0DHV8VQ9byvyirE99h72o3rmJfhZJFUarjGsiFVaT2niFIBBj2lZHYlk70Ap4BUIg82h',
					'0QwEDl6UP8koUt9yI85HezwmVylXm4fUpuWQoskt9zRkqTnoiICeJUKFXleX' => 'cQ2xv67uytT1WLZ0Q2etJqo698QOWaQmFyikElkgUwnvM8ZWmzAVQUspEPg2M0gGSRNewunUVpiPhYyFIP3gJx6rzuOnxCnOyc',
					'lfhCm6TjF5gzEEtTJ7JyA48Ww4bSSnOZ8WOLtl7ru7fZEStKMHavLsTa13vO' => 'EWZsNGIKqebYImn7sQIiJ26jM6827GIivzO3TmaMnPb8C6m4wpVQ8uOLV7xeLkGu1cWwC1aArOT2CCutT2UHsNfXJOkfymoByJ',
					'nIpwxKqzEyr6ROWfk8GwEOUD0VDD7WCYaWRMyUoQtbPqNcGBEzfVYE9peslG' => 'YnZGGcOkPuUoVUrIcmEiWlrXmC2pSoOFstkfqpL7NypkzZN5QvktN3KLyuLk73O7CzrHuk530a7AM4kQMOVCxx92iuO0vhQcrX',
					'N2osVk5j7sPixbliEegqsZ0Pz8uDqRmOU3EcBV3gr6Uog5ok0exFkNJ0CIIi' => 'voYoyzecy9f7uPZ5SNJ20lJ3vbBNmVvu9IvItLjbh9pS56ah5AQVjJSfRLe9IFnbf77ZHF51AtgiybemmyJKJMFKgcosEAWwDm',
					'5w8YrrT8ffygMHPb6T3laMaLR3pL50OfnDVZ26IhDpVZmsStaKAKG6A85ZX5' => 'eSMxTRtKyBhXwbIbLoNxLI1CFWrYh5XsRSZfACxC4IPE0SeVMh9V1vbn0gWFCekkSicTsqY2W9bskL6k6kT0kVuwR8RWK7S4Dz',
					'y85IXbv3O3LF4i9ieb0hAU9Hyh7kFeQSK2UzDmwgyVFGuXcSQWEySBLOHzxq' => 'G2VRQHzt6P5GU0ZDbhmMnGeoAIhpXI8KK644O8P8FFLA58TozqSMRGUBA205coJ1n7DmFSZ4GQ95S4BbRaYFrQ6hBcz1YLa86L',
					'BsSMjZFgBEs1PbJXWmIawsx3E1riM9hiqfc7KIsEL423WBb0UOR1ikgEno7h' => 'wACDuBXNNbp31apxVvYe9JaRoKxCIf53halSfyQhKmrrMT3muFY9yLAtYAIzDQWmWoaM0OHcbvSOliy1piWc0eFWi2kzMy96aO',
					'zI2NFOyER27r0gNfNMNCuYO7PKVJahsSfJhtuwP3QN7tWstrmSCcMwVigfjM' => '1aqxOJIyveTeq5MUEFkrsOhonEU8PT61tz2xbXreSzBh0bUwNhwKzU7xe2aLbW70CaUHEMgUyA0NzGropHerp1k9RVsTLGr1wo',
					'5pEFGfsDZA4847A2Uo50xwvbNsTDJcEWGNzKsQAqQvcjpOAjrgLYK5EPucjM' => 'K1ixfi42JsqOychJCUKU5VH76W7vm5zut1hTssi2KTNBzVZmWuaReZNrcqYlSGXRyttia1fvcepKkuOXR1B2PFFLLS5buRm8Zy',
					'Oyc4v4lwT4tcn9lUqQK0OGWuDS70yjVouQFVVihYx2Z82Je3cfGCBkLktgIW' => '1H4Qq6H2bJgE1eFWmziftavsLlwOUNFAi8RBeRG2Qoj5R7yDB3DVUxkI4UvFvQzLhcua0hTjVryYrl1cGQOUOOo3PQPQIp5G5W',
					'GIf56juphAjiaHbWhD1wgbinWyGTZ7LUPTpMs96b7OV988GXRCU0yVnirWgg' => 'phyMA9IwRPleiJJuArWvMPYpX3RRm1k4ksh3tmMmCN0TcN0fOQJRwMRKzfJRv9UmcGoY1uICMSJMeaela5ff4jb4792Xb06jy3',
					'gx7v8m8oeyvzhZ2Ul2Ut0tT8ckOXqLJW2ahenMIp1vnrHeuN01MWx2o4QGkk' => 'NO2b4wAc4nYPSOqXv5hXyeKpDWpNK0ELYt5Y6pN9PKl9Xwb7K4BlogeVrVvvC9Rpp3whKiYQMp6wAKC76FSPycavJC6ZBXRTk5',
					'6bfvvpljumasZEEN4ChVn8QS4D3HZNc1JITDh4GVPZUtXq2CqHKe0uPeqavT' => '5PMXJyM7XYlnvhy7JfTx0ZpjtxKIveaI92Wa1LPKJqrY8asTgkmKaiSyi4A63W3ktLlwvchxnzEQkkMrqfD25ZbEmryLsMaVBu',
					'yrNt1lMTz9P2R4LiN4mPaOrxJKhOwNaNDmhbXFvztzQeiPbGCIXiVKqnKwai' => 'HaGazcwVizYn2aRLXDHD6LJnaC4FHTV5hs34mWObmiqvf8z89TnnMooDeru3SwUkI5eIlrPzrl7CMuPcHxlRZ9o7b24r507QeF',
					'kAxNi0BWOfnayA0nSJDkbNFICNNpkJD7Retgfg08t4PW14Tmh8ORTGc6yMUO' => 'w8IXvapZ80zDyTFc4thcR51ePovQTnJve7kKsNFTCx0fkL0bfn3DrbLh6nyYgkK57NsQnfeYD3LnqhZReBBqKNhkpTzTaAB0QO',
					'aFar5pb9klzbXxs6kRmGjk3U0wc6rEn8fPAtg8BSgRtASeZt8zOmmi1A8Wcm' => 'HzB3N4X8bxQBZEIl0Y6LD4Zrr4Qj1bKHk9aWmVY5bXvKpa33qvtK9KHb5oW6vcVWo9oLeui7b8eUPWTMw14sY5u87XRf2mRwqW',
					'RFTnhW37fX2MANgsSmuKHkRb2RSEJX4qSuNffnq0nseFXPHqsNxymwXjfcpl' => 'pqDNkfNQKvQN9UqQ7cWFLx1sgkH8rex0D1SYOU4522UyD60Kv6BqVEYmaxrLOFMh1284FszrsI0LI5Cusjwa3ES2BgqswvPwwM',
					'pk9YteS1w4JYuT4hxhLDgc3iW9Nxl1Ojemiy1gnAru0x0ENp1qcMMp1ySmAG' => 'Mb45uW8MntCuzkAQErnb9oq3ZOw537tVhUFpIbPzeEYJXkV95bNe1nfYBNpJR7tlFW5eiyAQelKBL2Jga1V6ZQr8aBNraVwZya',
					'BLz8i0erUfTsLCIczNtEusc24eHH861IlPc7ISaok58iBLu1tQwteiZ0OSqw' => '6K9TVhysRFYlDQIT5NGTVB9rwHKDUFUaS1sCaEpgqg9guYOul8SGrVEWVe4GLStWLPCh0tDnnSxM4qA7yVRjFtAH56QvArJS7s',
					'2iWvemZFY7lrMMhKQBuNfx0UNZBWSASMBzofysF6nRAkhoIH6LXpzykKvAC7' => 'FgbFaQIosDR9Yt5FDzT21CBhJP2JyiNUJn1mKi9SlY3ubEYS0gtYw9aSIn8Pm1nYSw10KW36U9C4csAHLV8B70KWlUmRIQKlbj',
					'aaageK94bQRXIAPewQk8NpuvTgmiUacHlerazAe6StKj3m4lkAAU153m7Iw9' => 'PZ0iJhkLw36zTTViS1SbSijaJX6sBnmqTOvAzwIXYeNjyyT81shSqDzMhvGj2pHFLwO7sPyrEYcKELxzg0s9tA69LpWkJmkm08',
					'4j6VQnjpYAztCkNvmqpWnuSQhPplvv1Wn4h1qpkjAVj4PsjXRxkc8NyWLiYB' => 'l5BeZuVI56rWoGHEqNBFtlH5G76amex933CHp7Qy4tmRe9hGnNPnOkf8677boJDQfTHsTNCD6GBPeBskrQL8jt23PlJmXempHL',
					'CAGAxjglqDNJ01awiXfNw2DwRASSXh91OBuOHvlK4m28W01F3liE9CRrWnkU' => 'kKAwPW2fLwUtWZhn8felHcfEOupBBilr3NhZ1BLMk5ToBHyQJ7AzVnOEKmaRYQAA5DO89GlbKyuFt32PqTfyUSsXYvOv3NPGVt',
					'oHo8MEzfCyNkuqeqj2Asj2whPzQZURDLy2tZBOeZ5aRqvHZiAK7rUMOSvcCt' => 'QxY7Cwg3poZYjk5j99fNt3m40ATj9pMcPEVBRATQcg9AzjFpOiYQ4P5rjDhAuNS0Bbif08LYQSwFOI5HXJBV5UzDBRInw9xmiZ',
					'YrQWQ5v90QrxnMJaCO66BOcFjWwRvy1lLiBqtCy5t9OuZNCsYa1U95HMgIBp' => 'eu6DA0qrnmeY5yf2zExWkCJJJFWRmYIY5x2vqxTc4jz5GW4qiH4KVqfrQwmfSn9xb5hJ0pLC5KQ7LxYWThDLxCNZeSrlaemJie',
					'FR43WEgTlR8cYVzDXBDSAMMN0BAfgpjBfvyMUT772wrPZpaO3bTsy1A2MUmI' => 'Cx66ekutymAu6laKT22o9huQ8VmMw1cJIB7rRtbRMEHF7Rmp969mGGgtYFhu0jE7NjTCXNkADoMxuE9ggInxle0bvmkN9YUIab',
					'vPJoac9METsbBV2qJq4yFpaAcYEqZKvj1tXhncYij7psSVuJa6c6iKyVVUlY' => 'X7qEyE2F75yRCFMEAaVs1nuBSHrAe5qYrAooIVwbjzuIMYsyXNwxARnzsQNvhQV7tjKLaZ4MkMBvgeCahaLSE7rCwsI2fG4rrQ',
					'BNt9nHieramTvFRHv2HnnfvWAUfIBMZL9qAPbMrujDKPB49pEJSbLGRMcrME' => '2XM1WNIlgV3c3W7gAOuyMRenw60QKHEVuVT8K1ghxKzZp3yYDxfJ3ZN3Whe3UnNW8v0BvGVf1VGE5fAQ2Xn4oBwxeHv1ZmPk3B',
					'LNcOSEZF7mM0zmzoXBaugEr89GC2ukNUUaYaUhK92rw4rIrvSWNw3W55cb9I' => 'huWKr51Ogqq6ql5Tg4OLqPs5Bv6n5S3lWeTjomSkmUUMAQL9NtGjFDbZjOY266c6R8Tcc02OYKOkIEqUyMBAu6Qq8CXYkHDyPJ',
					'8I9OBJKN6Gh6LXyvBKw86Uv5bXym3IzqkZZ9rYZ1rm5r5ULP037qXbM82m9J' => 'hUotmgcPDsbnH1Qtomu9QU6nOj1uy6zQ7UwgJfRiqBcjjozM1CIFgZ9q50UQMwVuNKDEWCXfEOpA4eYgj0HMrXq2O2FcAGZaFi',
					'IHv6S0geNzH7o1unOmIY8e9YwsnGTTzCvNDWa4PaPTVe1InqzTZ2KScsy7ni' => 'jHeLMVx0LR5lts9O8txov8BBYXqpKAkwnz6qnDVWbbuj11P2etFA82zVcXp9Ml2mHz7ON8smqBDWMSmlqxW80xc8A202EhoVel',
					'aQUe0NSNhVokIFIlpJB4FPYY8JUujp7YmrnM5JcTsNU72FQaHLgZsWxe840T' => 'mqxXYTKUFZ22wMlkBeQaPIyk09zsas1i2PB6xx5HSNPI54kkDSVStG4O4TUHAajMNLUb7q0OJ997mWNLQ7lOeH0b19GJVQpRF8',
					'62rgv8Z8aBWVpN3eDsLTTLsD3MCelgmZavWSi3iATw5hflKr0KVOFQ3210aN' => 'hfmCEIu2cJnc9x7u72zlEZsOxzjmvB8WiKX97JkniPUnVAhq8WfkqaotTl2lSBx1ibBsjfN5tJVmRkLx0yQECDTN2PYHTQ1oqN',
					'RowZJftT9byW2riAkjKSJr2WF0se9Gc6PrGhzXl0LOyORNtCV5MOi9bx6fxa' => '7CiO1ZYu8Xkiy283s7n7wZ0R9MK8YrswtsOvkrtUPrtDSREDBZYYwj6szulSjeajEjXVurfwkcjcBID44EbuAfPt3vxXyyHhBW',
					'HuATU4iz01CzyvaJk8WoCKZbxq83ML1JDXhsbjsTOP54VWDOyGol3Q0GHtxG' => 'ryPzx4SpJriFRXAUv2koNPsPIwDKkrTMsf8DSOK3Sw14CwYS3rFFIJKgHYlsPhCA4CQPMyMIS14Ja6WuA382ZlLvh7ZADOPCXS',
					'P2NBUX8IwifmVStxDAg6L1gZ4FYbPai9tWpx4W0b5T3HlD1YRZDJP56rxotk' => 'Jb7fzI4fXo37uGlmMANjswVhxmB5V1t49ieqPkFCQ1Zu93jIEiaDi50URoXtDIhzJ3oeVA3IS3Qfiv8FBTg8ie4wL4O3hpZoRn',
					'fh9JTBTSAFtnqA2lvaF3cB18OWjbsvT8RHRBGIgXLJmEkMkCX02n85hUlpJq' => 'TVg27obsK6r036nmZmfTIqxPXelfKPJHVlE3UDiZpNit7GYhGkl2SkZaGZNXjbCsHBzJ69OmcWqazqKnH6ACA0AyaknrK322Gk',
					'lVeiGGfF4tOxeFyKts9blkROS4D9NEOvI1sFzEJStLwcOASkK5zbSobVFf4r' => 'ofY6FQYmEK0o9IJDXLmH14smXfNKk7pcmDyg3VOpWyHwlYalpVERrsxtTuymtKKw12DrCPcmXb9oJDvaiGvHMurUws9gbyyMwx',
					'qV4VlfB4ZyQ8ZE2XrICmThhJPCrYyD2pBJkPcPcogS6auFG98wXK29tSYcyA' => 'APX6fXmII2P3bW5gIOXeygj3FO6yfS2ZKto1WPgOn6nTTb6sj1sYQDUUs4MADmNS7scZnCwzRyleZigjs8XIaZaCrl2Y0JKTwy',
				];

			protected $est_inclure_num_cle 				= 	true;
			protected $is_base64						=	true;
			protected $encryption_method 				=	'AES-128-ECB';
			protected $separation_string_cipher_num		=	'-oo-';
			protected $separation_string_params 		= 	'-xx-';
			protected $equal_string_params 				= 	'-==-';
			protected $hash_algo						= 	'sha256';
			protected $is_salt_at_end 					= 	true;
			protected $sort_array_to_hash 				= 	true;
			
			public function __construct()
			{
				
			}

			public function encrypt_mips($value, $key)
			{
				$encrypted_value = openssl_encrypt($value,$this->get_encryption_method(),$key);
				if ($this->get_is_base64())
					$encrypted_value = base64_encode($encrypted_value);
				return $encrypted_value;
			}

			public function encrypt_with_key_set($string_to_encrypt, $numero_cle_cryptage = null)
			{
				if ($numero_cle_cryptage == null)
					$numero_cle_cryptage = array_rand($this->get_cipher_keys());

				$cle_cryptage = $this->get_cipher_keys()[$numero_cle_cryptage];
				$encrypted_string = $this->encrypt_mips($string_to_encrypt,$cle_cryptage);

				if ($this->is_est_inclure_num_cle() === true)
					return $numero_cle_cryptage . $this->get_separation_string_cipher_num() . $encrypted_string;
				else
					return $encrypted_string;
			}

			public function decrypt_mips($value, $key)
			{
				if ($this->get_is_base64())
					$value = base64_decode($value);
				return openssl_decrypt($value, $this->get_encryption_method(), $key);
			}

			public function decrypt_with_key_set($param)
			{
				$numero_cle_cryptage = substr($param,0,strrpos($param ,$this->get_separation_string_cipher_num()));
				$cle_cryptage = $this->get_cipher_keys()[$numero_cle_cryptage];
				$param = substr($param,strrpos($param , $this->get_separation_string_cipher_num())+ strlen($this->get_separation_string_cipher_num()));
				return $this->decrypt_mips($param, $cle_cryptage);
			}

			public function hash_with_salt($values, $salt = null)
			{
				$string_to_hash = '';

				if($this->get_is_sort_array_to_hash())
					ksort($values);

				foreach ($values as $value)
				{
					if ($value != null)
						$string_to_hash .= $value;
				}
				if ($this->get_is_salt_at_end())
					$string_to_hash = $string_to_hash . $salt;
				else
					$string_to_hash = $salt . $string_to_hash;

				return hash($this->get_hash_algo(), $string_to_hash);
			}

			public function extract_coded_params($coded_string, $separation_string, $equality_string)
			{
				
				$param_list = array();
				$couple_params = explode($separation_string, $coded_string);
				foreach ($couple_params as $couple_param)
				{
					$segmented_param = explode($equality_string, $couple_param);
					
					if ( ! isset($segmented_param[1])) {
					   $segmented_param[1] = null;
					}
					
					$param_list[$segmented_param[0]] = $segmented_param[1];
				}
				
				return $param_list;
			}

			public function verify_hashing_equality($hashed_string, $values, $salt = null, $is_end = true)
			{
				if ($this->hash_with_salt($values, $salt) == $hashed_string)
					return true;
				else
					return false;
			}

			public function check_hash_integrity($hashed_string, $hash_algo)
			{
				if (strlen($hashed_string) != $this->hash_algo_lenght($hash_algo))
					return false;
				return true;
			}

			public function hash_algo_lenght($hash_algo)
			{
				$hash_algo_lengths =
					[
						'md2'      		=>	32,
						'md4'      		=>	32,
						'md5'     		=>	32,
						'sha1'      	=>	40,
						'sha256'    	=>	64,
						'sha384'    	=>	96,
						'sha512'    	=>	128,
						'ripemd128' 	=>	32,
						'ripemd160' 	=>	40,
						'ripemd256' 	=>	64,
						'ripemd320' 	=>	80,
						'whirlpool' 	=>	128,
						'tiger128,3'	=>	32,
						'tiger160,3'	=>	40,
						'tiger192,3'	=>	48,
						'tiger128,4'	=>	32,
						'tiger160,4'	=>	40,
						'tiger192,4'	=>	48,
						'snefru'    	=>	64,
						'gost'      	=>	64,
						'adler32'   	=>	8,
						'crc32'     	=>	8,
						'crc32b'    	=>	8,
						'haval128,3'	=>	32,
						'haval160,3'	=>	40,
						'haval192,3'	=>	48,
						'haval224,3'	=>	56,
						'haval256,3'	=>	64,
						'haval128,4'	=>	32,
						'haval160,4'	=>	40,
						'haval192,4'	=>	48,
						'haval224,4'	=>	56,
						'haval256,4'	=>	64,
						'haval128,5'	=>	32,
						'haval160,5'	=>	40,
						'haval192,5'	=>	48,
						'haval224,5'	=>	56,
						'haval256,5'	=>	64
					];
				return $hash_algo_lengths[$hash_algo];
			}

			public function build_digit_unique_id($longueur)
			{
				$identifier = '';
				$i = 0;
				while ($i < $longueur)
				{
					$identifier .= mt_rand(0, 9);
					$i++;
				}
				return $identifier;
			}

			public function build_alphanum_unique_id($length)
			{
				if ($length % 2 != 0)
					exit('Invalid lenght Cryptographic uniq id should be a even lenght');
				else
					return bin2hex(random_bytes($length / 2));
			}

			public function get_cipher_keys()
			{
				return $this->cipher_keys;
			}

			public function set_cipher_keys($cipher_keys)
			{
				$this->cipher_keys = $cipher_keys;
			}

			public function is_est_inclure_num_cle()
			{
				return $this->est_inclure_num_cle;
			}

			public function set_est_inclure_num_cle($est_inclure_num_cle)
			{
				$this->est_inclure_num_cle = $est_inclure_num_cle;
			}

			public function get_encryption_method()
			{
				return $this->encryption_method;
			}

			public function set_encryption_method($encryption_method)
			{
				$this->encryption_method = $encryption_method;
			}

			public function get_separation_string_cipher_num()
			{
				return $this->separation_string_cipher_num;
			}

			public function set_separation_string_cipher_num($separation_string_cipher_num)
			{
				$this->separation_string_cipher_num = $separation_string_cipher_num;
			}

			public function get_separation_string_params()
			{
				return $this->separation_string_params;
			}

			public function set_separation_string_params($separation_string_params)
			{
				$this->separation_string_params = $separation_string_params;
			}

			public function get_equal_string_params()
			{
				return $this->equal_string_params;
			}

			public function set_equal_string_params($equal_string_params)
			{
				$this->equal_string_params = $equal_string_params;
			}

			public function get_hash_algo()
			{
				return $this->hash_algo;
			}

			public function set_hash_algo($hash_algo)
			{
				$this->hash_algo = $hash_algo;
			}

			public function get_is_base64()
			{
				return $this->is_base64;
			}

			public function set_is_base64($is_base64)
			{
				$this->is_base64 = $is_base64;
			}

			public function get_is_salt_at_end()
			{
				return $this->is_salt_at_end;
			}

			public function set_is_salt_at_end($is_salt_at_end)
			{
				$this->is_salt_at_end = $is_salt_at_end;
			}

			public function get_is_sort_array_to_hash()
			{
				return $this->sort_array_to_hash;
			}

			public function set_sort_array_to_hash($sort_array_to_hash)
			{
				$this->sort_array_to_hash = $sort_array_to_hash;
			}
			
			public function array_keys_multi(array $array)
			{
				$keys = array();

				foreach ($array as $key => $value) {
					$keys[] = $key;

					if (is_array($value)) {
						$keys = array_merge($keys, array_keys_multi($value));
					}
				}

				return $keys;
			}
		}
	}