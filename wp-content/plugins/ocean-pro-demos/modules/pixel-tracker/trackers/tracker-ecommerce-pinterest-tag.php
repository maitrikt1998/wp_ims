<?php

/**
 * Manages Pinterest Pixel integration for WooCommerce events.
 */
class OPD_Pinterest_eCommerce_PixelTracker {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_Pinterest_eCommerce_PixelTracker |null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_Pinterest_eCommerce_PixelTracker
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Constructor.
	 * Sets up the Pinterest Pixel event tracking for WooCommerce if enabled in the options.
	 */
	public function __construct() {
		if ( opd_get_option( 'enable_pinterest_tag', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			if ( opd_get_option( 'pinterest_tag_track_checkout', 'oec_pixel_tracker', 'off' ) === 'on' ) {
				add_action( 'woocommerce_thankyou', array( $this, 'track_checkout_event' ) );
			}
		}
	}



	/**
	 * Tracks the 'Checkout' event.
	 *
	 * @param int $order_id The ID of the order being processed.
	 */
	public function track_checkout_event( $order_id ) {
		$order    = wc_get_order( $order_id );
		$total    = $order->get_total();
		$items    = $order->get_items();
		$quantity = array_sum(
			array_map(
				function ( $item ) {
					return $item->get_quantity();
				},
				$items
			)
		);
		echo "<script>
            pintrk('track', 'checkout', {
                value: {$total},
                order_quantity: {$quantity},
                currency: '" . get_woocommerce_currency() . "'
            });
        </script>";
	}
}

/**
 * Initializes and returns an instance of OPD_Pinterest_eCommerce_PixelTracker.
 *
 * @return OPD_Pinterest_eCommerce_PixelTracker
 */
function opd_pinterest_pixel_tracker_ecommerce_instance() {
	return OPD_Pinterest_eCommerce_PixelTracker::instance();
}

opd_pinterest_pixel_tracker_ecommerce_instance();
