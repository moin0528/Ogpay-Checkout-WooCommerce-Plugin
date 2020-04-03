<?php
// error_reporting( 0 );
// ini_set( 'display_errors', 0 );
if ( isset( $_GET[ 'isysid' ] ) ) {
  include_once '../../../wp-config.php';
  include_once '../../../wp-load.php';
  include_once '../../../wp-includes/wp-db.php';
  include_once '../../../wp-includes/pluggable.php';
  include_once '../../../wp-includes/option.php';
  include_once 'class-wc-gateway-oneglobal.php';
  $OneGlobal = new WC_Gateway_OneGlobal();
  global $woocommerce;
  $woo_order_id = substr( $_GET[ 'isysid' ], 13 );
  $chk = get_post_meta( $woo_order_id, 'payment_order_id', true );
  if ( $chk <= 0 ) {
    $order = wc_get_order( $woo_order_id );

    $master_IV = $OneGlobal->paymentMasterIV;

    $dataToComputeHash = $master_IV . "isysid=" . $_GET[ 'isysid' ] . "&result=" . urlencode( $_GET[ 'result' ] ) . $master_IV;
    $computedHash = strtoupper( hash_hmac( "sha256", $dataToComputeHash, $OneGlobal->paymentEncryptedSaltDecrypt ) );

    $note = "";
    if ( $computedHash == $_GET[ 'hash' ] ) {

      if ( strtolower( $_GET[ 'result' ] ) == 'captured' ) {
        $order->update_status( 'processing', 'order_note' );
        $order->payment_complete();
      } elseif ( strtolower( $_GET[ 'result' ] ) == 'cancelled' ) {
        $order->update_status( 'cancelled', 'order_note' );
      } else {
        $order->update_status( 'pending', 'order_note' );
      }

      add_post_meta( $woo_order_id, 'payment_order_id', $_GET[ 'isysid' ] );
      add_post_meta( $woo_order_id, 'payment_result', $_GET[ 'result' ] );

      $channelName = get_post_meta( $woo_order_id, 'channelName', true );
      $note = "Ref No : " . $_GET[ 'isysid' ] . ", Channel Name : " . $channelName . ", Payment Status : " . $_GET[ 'result' ];
    } else {

      $order->update_status( 'pending', 'order_note' );
      $note = "Tampered Data";
    }
    $order->add_order_note( $note );
    $woocommerce->cart->empty_cart();

    echo "<script>window.location.href='" . $order->get_checkout_order_received_url() . "'</script>";
  } else {
    echo "<script>window.location.href='" . site_url() . "/404'</script>";
  }
} else {
  echo "<script>window.location.href='" . site_url() . "'</script>";
}
?>
