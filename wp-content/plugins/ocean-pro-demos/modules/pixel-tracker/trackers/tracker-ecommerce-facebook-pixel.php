<?php

/**
 * Manages Facebook Pixel integration for WooCommerce events.
 */
class OPD_Facebook_PixelTracker {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_Facebook_PixelTracker|null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_Facebook_PixelTracker
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Sets up hooks for Facebook Pixel integration.
	 */
	public function __construct() {
		if ( opd_get_option( 'enable_facebook_pixel', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_head', array( $this, 'output_facebook_pixel' ) );
			add_action( 'wp_footer', array( $this, 'maybe_output_order_completion_script' ) );
			$this->add_fb_pixel_events();
		}
	}


	/**
	 * Outputs the Facebook Pixel code.
	 */
	public function output_facebook_pixel() {
		$facebook_pixel_id = opd_get_option( 'facebook_pixel_id', 'oec_pixel_tracker', '1400000000000000' );
		if ( ! empty( $facebook_pixel_id ) ) {
			echo "<!-- Facebook Pixel Code -->
            <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{$facebook_pixel_id}'); 
            fbq('track', 'PageView');
            </script>
            <noscript><img height='1' width='1' style='display:none'
            src='https://www.facebook.com/tr?id={$facebook_pixel_id}&ev=PageView&noscript=1'
            /></noscript>
            <!-- End Facebook Pixel Code -->";
		}
	}

	/**
	 * Adds WooCommerce hooks for Facebook Pixel event tracking.
	 */
	private function add_fb_pixel_events() {

		if ( opd_get_option( 'fb_track_product_views_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'woocommerce_after_single_product', array( $this, 'track_fb_product_view' ) );
		}

		if ( opd_get_option( 'fb_track_checkout_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'woocommerce_before_checkout_form', array( $this, 'track_fb_checkout' ) );
		}
	}

	/**
	 * Tracks product views for Facebook Pixel.
	 */
	public function track_fb_product_view() {
		global $product;
		echo "<script>
		fbq('track', 'ViewContent', {
            content_ids: ['{$product->get_id()}'],
            content_type: 'product'
        });</script>";
	}

	/**
	 * Tracks checkout initiation for Facebook Pixel.
	 */
	public function track_fb_checkout() {
		$cart        = WC()->cart->get_cart();
		$product_ids = array();
		$value       = 0;

		foreach ( $cart as $cart_item_key => $cart_item ) {
			$product_ids[] = $cart_item['product_id'];
			$value        += $cart_item['line_total'];
		}

		echo "<script>
		fbq('track', 'InitiateCheckout', {
            content_ids: ['" . implode( "','", $product_ids ) . "'],
            content_type: 'product',
            value: {$value},
            currency: '" . get_woocommerce_currency() . "'
        });</script>";
	}

	/**
	 * Tracks order completion for Facebook Pixel.
	 *
	 * @param int $order_id The ID of the completed order.
	 */
	public function track_fb_order_completion( $order_id ) {
		$order       = wc_get_order( $order_id );
		$product_ids = array();
		$value       = $order->get_total();

		foreach ( $order->get_items() as $item_id => $item ) {
			$product_ids[] = $item->get_product_id();
		}

		echo "<script>

		fbq('track', 'Purchase', {
            content_ids: ['" . implode( "','", $product_ids ) . "'],
            content_type: 'product',
            value: {$value},
            currency: '" . get_woocommerce_currency() . "'
        });</script>";
	}


	/**
	 * Conditionally outputs the order completion tracking script for Facebook Pixel.
	 */
	public function maybe_output_order_completion_script() {
		if ( is_wc_endpoint_url( 'order-received' ) ) {
			global $wp;
			$order_id = isset( $wp->query_vars['order-received'] ) ? $wp->query_vars['order-received'] : false;

			if ( $order_id && opd_get_option( 'fb_track_order_completion_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
				$this->track_fb_order_completion( $order_id );
			}
		}
	}
}

/**
 * Initializes and returns an instance of OPD_Facebook_PixelTracker.
 *
 * @return OPD_Facebook_PixelTracker
 */
function oec_facebook_pixel_tracker_instance() {
	return OPD_Facebook_PixelTracker::instance();
}

oec_facebook_pixel_tracker_instance();
