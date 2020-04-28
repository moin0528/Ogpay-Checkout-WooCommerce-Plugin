<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
class WC_Gateway_OneGlobal extends WC_Payment_Gateway {
	public
	function __construct() {
		$this->setup_properties();
		$this->init_form_fields();
		$this->init_settings();
		$this->title = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->instructions = $this->get_option( 'instructions' );
		$this->paymentMechantName = $this->get_option( 'paymentMechantName' );
		$this->paymentAuthKey = $this->get_option( 'paymentAuthKey' );
		$this->paymentMasterKey = $this->get_option( 'paymentMasterKey' );
		$this->paymentMasterIV = $this->get_option( 'paymentMasterIV' );
		$this->paymentEncryptedSaltEncript = $this->get_option( 'paymentEncryptedSaltEncript' );
		$this->paymentEncryptedSaltDecrypt = $this->get_option( 'paymentEncryptedSaltDecrypt' );
		$this->paymentChannel1 = $this->get_option( 'paymentChannel1' );
		$this->paymentChannelKent = $this->get_option( 'paymentChannelKent' );
		$this->paymentTunnelKent = $this->get_option( 'paymentTunnelKent' );
		$this->paymentCurrencyKent = $this->get_option( 'paymentCurrencyKent' );
		$this->paymentChannel2 = $this->get_option( 'paymentChannel2' );
		$this->paymentChannelVisa = $this->get_option( 'paymentChannelVisa' );
		$this->paymentChannel3 = $this->get_option( 'paymentChannel3' );
		$this->paymentChannelCode3 = $this->get_option( 'paymentChannelCode3' );
		$this->paymentChannel4 = $this->get_option( 'paymentChannel4' );
		$this->paymentChannelCode4 = $this->get_option( 'paymentChannelCode4' );
		$this->paymentChannel5 = $this->get_option( 'paymentChannel5' );
		$this->paymentChannelCode5 = $this->get_option( 'paymentChannelCode5' );
		$this->paymentChannel6 = $this->get_option( 'paymentChannel6' );
		$this->paymentChannelCode6 = $this->get_option( 'paymentChannelCode6' );
		$this->paymentLanguage = $this->get_option( 'paymentLanguage' );
		$this->paymentCountry = $this->get_option( 'paymentCountry' );
		$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );
		$this->enable_for_virtual = $this->get_option( 'enable_for_virtual', 'yes' ) === 'yes';
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'change_payment_complete_order_status' ), 10, 3 );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
	}
	protected
	function setup_properties() {
		$this->id = 'oneglobal';
		$this->icon = apply_filters( 'woocommerce_cod_icon', '' );
		$this->method_title = __( 'One Global', 'woocommerce' );
		$this->method_description = __( 'For Checkout Documentation please visit link. <a href="https://pay-it.mobi/payitcheckout/" target="_blank">https://pay-it.mobi/payitcheckout/</a>', 'woocommerce' );
		$this->has_fields = false;
	}
	public
	function init_form_fields() {
		$options = array();
		/*$data_store = WC_Data_Store::load( 'shipping-zone' );
		$raw_zones = $data_store->get_zones();
		foreach ( $raw_zones as $raw_zone ) {
			$zones[] = new WC_Shipping_Zone( $raw_zone );
		}
		$zones[] = new WC_Shipping_Zone( 0 );
		foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
			$options[ $method->get_method_title() ] = array();
			$options[ $method->get_method_title() ][ $method->id ] = sprintf( __( 'Any &quot;%1$s&quot; method', 'woocommerce' ), $method->get_method_title() );
			foreach ( $zones as $zone ) {
				$shipping_method_instances = $zone->get_shipping_methods();
				foreach ( $shipping_method_instances as $shipping_method_instance_id => $shipping_method_instance ) {
					if ( $shipping_method_instance->id !== $method->id ) {
						continue;
					}
					$option_id = $shipping_method_instance->get_rate_id();
					$option_instance_title = sprintf( __( '%1$s (#%2$s)', 'woocommerce' ), $shipping_method_instance->get_title(), $shipping_method_instance_id );
					$option_title = sprintf( __( '%1$s &ndash; %2$s', 'woocommerce' ), $zone->get_id() ? $zone->get_zone_name() : __( 'Other locations', 'woocommerce' ), $option_instance_title );
					$options[ $method->get_method_title() ][ $option_id ] = $option_title;
				}
			}
		}*/
		$this->form_fields = array( 'enabled' => array( 'title' => __( 'Enable/Disable', 'woocommerce' ), 'label' => __( 'Enable online OneGlobal', 'woocommerce' ), 'type' => 'checkbox', 'description' => '', 'default' => 'no', ), 'title' => array( 'title' => __( 'Title', 'woocommerce' ), 'type' => 'text', 'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce' ), 'default' => __( 'Debit / Atm / Credit Cards', 'woocommerce' ), 'desc_tip' => true, ), 'description' => array( 'title' => __( 'Description', 'woocommerce' ), 'type' => 'textarea', 'description' => __( 'Payment method description that the customer will see on your website.', 'woocommerce' ), 'default' => __( "You'll be redirected to payment gateway secure page.", 'woocommerce' ), 'desc_tip' => true, ), 'paymentMechantName' => array( 'title' => __( 'Merchant Name', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentAuthKey' => array( 'title' => __( 'Auth Key', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentMasterKey' => array( 'title' => __( 'Master Key', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentMasterIV' => array( 'title' => __( 'Master IV', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentEncryptedSaltEncript' => array( 'title' => __( 'Encrypted Salt(encript)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentEncryptedSaltDecrypt' => array( 'title' => __( 'Encrypted Salt(decrypt)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentLanguage' => array( 'title' => __( 'Payment Language', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentCountry' => array( 'title' => __( 'Payment Country', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentTunnelKent' => array( 'title' => __( 'Payment Tunnel', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentCurrencyKent' => array( 'title' => __( 'Payment Currency', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannel1' => array( 'title' => __( 'Payment Channel Name (1)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( 'Debit / Atm Cards', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannelKent' => array( 'title' => __( 'Payment Channel Code (1)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannel2' => array( 'title' => __( 'Payment Channel Name (2)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( 'Credit Cards', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannelVisa' => array( 'title' => __( 'Payment Channel Code (2)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannel3' => array( 'title' => __( 'Payment Channel Name (3)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannelCode3' => array( 'title' => __( 'Payment Channel Code (3)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannel4' => array( 'title' => __( 'Payment Channel Name (4)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannelCode4' => array( 'title' => __( 'Payment Channel Code (4)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannel5' => array( 'title' => __( 'Payment Channel Name (5)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannelCode5' => array( 'title' => __( 'Payment Channel Code (5)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannel6' => array( 'title' => __( 'Payment Channel Name (6)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), 'paymentChannelCode6' => array( 'title' => __( 'Payment Channel Code (6)', 'woocommerce' ), 'type' => 'text', 'description' => __( '' ), 'default' => __( '', 'woocommerce' ), 'desc_tip' => true, ), );
	}
	public
	function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );
		$names = '';


		foreach ( $order->get_items() as $item_id => $item_data ) {
			if(isset($item_data['name'])){
				$names .= $item_data['name'] . " and ";
			}
		}

		$channelName = '';
		if ( isset( $_POST[ 'payment_type' ] ) && $_POST[ 'payment_type' ] == 'visa' ) {
			$paymentchannel = $this->paymentChannelVisa;
			$channelName = $this->paymentChannel2;
		} elseif ( $_POST[ 'payment_type' ] == 'code3' ) {
			$paymentchannel = $this->paymentChannelCode3;
			$channelName = $this->paymentChannel3;
		} elseif ( $_POST[ 'payment_type' ] == 'code4' ) {
			$paymentchannel = $this->paymentChannelCode4;
			$channelName = $this->paymentChannel4;
		} elseif ( $_POST[ 'payment_type' ] == 'code5' ) {
			$paymentchannel = $this->paymentChannelCode5;
			$channelName = $this->paymentChannel5;
		} elseif ( $_POST[ 'payment_type' ] == 'code6' ) {
			$paymentchannel = $this->paymentChannelCode6;
			$channelName = $this->paymentChannel6;
		} else {
			$channelName = $this->paymentChannel1;
			$paymentchannel = $this->paymentChannelKent;
		}
		$tunnel = $this->paymentTunnelKent;
		$currency = $this->paymentCurrencyKent;
		$isysid = date( "Ymd" ) . rand( 1, 100000 ) . $order_id;
		$amount = $order->get_total();
		$description = "";
		$language = $this->paymentLanguage;
		$country = $this->paymentCountry;
		$merchant_name = $this->paymentMechantName;
		$akey = $this->paymentAuthKey;
		$timestamp = time();
		$rnd = "";
		$original = $this->paymentEncryptedSaltEncript;
		$dataToComputeHash = $paymentchannel . "paymentchannel" . $isysid . "isysid" . $amount . "amount" . $timestamp . "timestamp" . $description . "description" . $rnd . "rnd" . $original . "original";

		$decryptedOriginal = $this->paymentEncryptedSaltDecrypt;
		$hash = strtoupper( hash_hmac( "sha256", $dataToComputeHash, $decryptedOriginal ) );
		add_post_meta( $order_id, 'hash', $hash );
		add_post_meta( $order_id, 'channelName', $channelName );
		add_post_meta( $order_id, 'hash', $hash );
		$merchantResponseUrl = site_url() . "/wp-content/plugins/ogpay/return.php";
		$host = "https://pay-it.mobi/globalpayit/pciglobal/WebForms/Payitcheckoutservice%20.aspx";

		$url = $host . "?country=" . $country . "&paymentchannel=" . $paymentchannel . "&isysid=" . $isysid . "&amount=" . $amount . "&tunnel=" . $tunnel . "&description=" . $description . "&description2=" . $description2 . "&currency=" . $currency . "&Responseurl=" . $merchantResponseUrl . "&merchant_name=" . $merchant_name . "&akey=" . $akey . "&hash=" . $hash . "&original=" . $original . "&timestamp=" . $timestamp . "&rnd=" . $rnd;

		return array( 'result' => 'success', 'redirect' => $url, );
	}
	public
	function get_icon() {
		$icon_html = '';
		$icon_html .= '<img src="' . site_url() . '/wp-content/plugins/ogpay/images/logo.png" style="height:31px!important; width:100px!important;" alt="' . esc_attr__( 'One Global Payment', 'woocommerce' ) . '" />';
		return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
	}
	public
	function thankyou_page() {
		if ( $this->instructions ) {
			echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
		}
	}
	public
	function payment_fields() {
		echo $this->description;
		echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';
		echo '<div class="form-row form-row-wide">
							<label>Choose Method <span class="required">*</span></label>
							<select name="payment_type">
							<option value="kent">' . $this->paymentChannel1 . '</option>
							<option value="visa">' . $this->paymentChannel2 . '</option>
								';
		if ( $this->paymentChannel3 != '' ) {
			echo '<option value="code3">' . $this->paymentChannel3 . '</option>';
		}
		if ( $this->paymentChannel4 != '' ) {
			echo '<option value="code4">' . $this->paymentChannel4 . '</option>';
		}
		if ( $this->paymentChannel5 != '' ) {
			echo '<option value="code5">' . $this->paymentChannel5 . '</option>';
		}
		if ( $this->paymentChannel6 != '' ) {
			echo '<option value="code6">' . $this->paymentChannel6 . '</option>';
		}
		echo '</select>
						</div>';
		echo '<div class="clear"></div></fieldset>';
	}
	public
	function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $this->instructions && !$sent_to_admin && $this->id === $order->get_payment_method() ) {
			echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
		}
	}
}
?>
