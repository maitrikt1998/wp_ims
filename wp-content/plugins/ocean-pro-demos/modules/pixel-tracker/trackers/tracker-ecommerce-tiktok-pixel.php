<?php
namespace owpEcommerce\Modules\PixelTracker;

/**
 * Manages TikTok Pixel integration for WooCommerce events.
 */
class OPD_TikTok_eCommerce_PixelTracker {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_TikTok_eCommerce_PixelTracker|null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_TikTok_eCommerce_PixelTracker
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Sets up hooks for TikTok Pixel integration.
	 */
	public function __construct() {
		if ( opd_get_option( 'enable_tiktok_pixel', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			// add_action( 'wp_footer', array( $this, 'maybe_output_order_completion_script' ) );
			$this->add_tiktok_pixel_events();
		}
	}


	/**
	 * Adds WooCommerce hooks for TikTok Pixel event tracking.
	 */
	private function add_tiktok_pixel_events() {

		if ( opd_get_option( 'tiktok_pixel_track_product_views_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'woocommerce_after_single_product', array( $this, 'track_tiktok_product_view' ) );
		}

		if ( opd_get_option( 'tiktok_pixel_track_checkout_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'woocommerce_before_checkout_form', array( $this, 'track_tiktok_checkout' ) );
		}
		if ( opd_get_option( 'tiktok_pixel_track_order_completion_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'woocommerce_thankyou', array( $this, 'track_tiktok_order_completion' ) );
		}
	}

	/**
	 * Tracks product views for TikTok Pixel.
	 */
	public function track_tiktok_product_view() {
		global $product;
		$product_id    = $product->get_id();
		$product_price = $product->get_price();
		echo "<script>
		ttq.track('ViewContent', {
			contents: [{
				content_id: '" . $product_id . "',
				content_name: '" . $product->get_name() . "',
				quantity: 1,
				price: " . $product_price . "
			}],
			content_type: 'product',
			value: " . $product_price . ",
			currency: '" . get_woocommerce_currency() . "'
		});
		</script>";
	}

	/**
	 * Tracks checkout initiation for TikTok Pixel.
	 */
	public function track_tiktok_checkout() {
		$cart       = WC()->cart->get_cart();
		$contents   = array();
		$totalValue = 0;

		foreach ( $cart as $item ) {
			$product     = wc_get_product( $item['product_id'] );
			$contents[]  = array(
				'content_id'   => $item['product_id'],
				'content_name' => $product->get_name(),
				'quantity'     => $item['quantity'],
				'price'        => $item['line_total'] / $item['quantity'],
			);
			$totalValue += $item['line_total'];
		}

		echo "<script>
		ttq.track('InitiateCheckout', {
			contents: " . json_encode( $contents ) . ",
			content_type: 'product',
			value: " . $totalValue . ",
			currency: '" . get_woocommerce_currency() . "'
		});
		</script>";
	}


	/**
	 * Tracks order completion for TikTok Pixel.
	 *
	 * @param int $order_id The ID of the completed order.
	 */

	public function track_tiktok_order_completion( $order_id ) {
		$order       = wc_get_order( $order_id );
		$order_total = $order->get_total();
		$currency    = get_woocommerce_currency();

		$customer_email = $order->get_billing_email();
		$customer_phone = $order->get_billing_phone();

		$contents = array();

		foreach ( $order->get_items() as $item_id => $item ) {
			$product    = $item->get_product();
			$contents[] = array(
				'content_id'   => $item->get_product_id(),
				'content_name' => $item->get_name(),
				'quantity'     => $item->get_quantity(),
				'price'        => $item->get_total() / $item->get_quantity(),
			);
		}

		$json_contents = json_encode( $contents );

		?>
	<script>
		// Identify the user with email and phone number
		ttq.identify({
			email: '<?php echo $customer_email; ?>',
			phone_number: '<?php echo $customer_phone; ?>',
		});

		ttq.track('PlaceAnOrder', {
			contents: <?php echo $json_contents; ?>,
			value: <?php echo $order_total; ?>,
			currency: '<?php echo $currency; ?>',
		});
		
		ttq.track('CompletePayment', {
			contents: <?php echo $json_contents; ?>,
			value: <?php echo $order_total; ?>,
			currency: '<?php echo $currency; ?>',
		});
	</script>
		<?php
	}
}

/**
 * Initializes and returns an instance of OPD_TikTok_eCommerce_PixelTracker.
 *
 * @return OPD_TikTok_eCommerce_PixelTracker
 */
function opd_tiktok_pixel_tracker_instance() {
	return OPD_TikTok_eCommerce_PixelTracker::instance();
}

opd_tiktok_pixel_tracker_instance();
