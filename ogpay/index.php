<?php
/* @wordpress-plugin
 * Plugin Name:       Og Pay for Woocommerce
 * Description:       Supercharge your business with Og Pay
 * Version:           2.1.0
 * WC requires at least: 2.6
 * WC tested up to: 3.6
 * Author:            Og Business by One Global
 * Author URI:        http://oneglobal.co/
 * Text Domain:       woocommerce-other-payment-gateway
 */
$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
if ( new_payment_is_woocommerce_active() ) {
  add_filter( 'woocommerce_payment_gateways', 'new_payment_gateway' );

  function new_payment_gateway( $gateways ) {
    $gateways[] = 'WC_Gateway_OneGlobal';
    return $gateways;
  }
  add_action( 'plugins_loaded', 'new_other_payment_gateway' );

  function new_other_payment_gateway() {
    include 'class-wc-gateway-oneglobal.php';
  }
}

function new_payment_is_woocommerce_active() {
  $active_plugins = ( array )get_option( 'active_plugins', array() );
  if ( is_multisite() ) {
    $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
  }
  return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'salcode_add_plugin_page_settings_link' );

function salcode_add_plugin_page_settings_link( $links ) {
  $links[] = '<a href="' .
  admin_url( 'admin.php?page=wc-settings&tab=checkout&section=oneglobal' ) .
  '">' . __( 'Settings' ) . '</a>';
  return $links;
}

add_action( 'woocommerce_thankyou', 'bbloomer_add_content_thankyou' );

function bbloomer_add_content_thankyou( $order_id ) {

  echo '
	<style>
	#payment-id, #payment-status{
		background-color: #f5f5f5;
		padding: 18px;
		margin-bottom: 1rem;
	}
	</style>
	<div class="payment-details"><h2>Payment Details</h2>
	<div id="payment-id" style=""><strong>Payment ID : </strong> '
    . get_post_meta( $order_id, 'payment_order_id', true ) . '</div>
	  <div id="payment-status"><strong>Payment Status : </strong> '
  . get_post_meta( $order_id, 'payment_result', true ) . '</div></div><br/>';
}

?>
