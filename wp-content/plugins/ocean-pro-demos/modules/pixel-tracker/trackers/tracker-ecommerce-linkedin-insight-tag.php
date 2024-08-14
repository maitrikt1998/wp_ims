<?php

/**
 * Manages LinkedIn Insight Tag integration for event tracking.
 */
class OPD_LinkedIn_eCommerce_InsightTag {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_LinkedIn_ECommerceEvents|null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_LinkedIn_ECommerceEvents
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Sets up hooks for LinkedIn Insight Tag eCommerce event integration.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'set_csp_header' ) );		
		add_action( 'woocommerce_add_to_cart', array( $this, 'track_add_to_cart_event' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'track_start_checkout_event' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'track_purchase_event' ) );

		// Include the AJAX handler in frontend.
		if ( is_admin() ) {
			return;
		}

		add_action( 'wp_footer', array( $this, 'add_to_cart_ajax_handler' ) );
	}


	/**
	 * Set CSP header early in the template redirect phase.
	 */
	public function set_csp_header() {
		header( 'Permissions-Policy: browsing-topics=()' );
	}
	/**
	 * Tracks Add to Cart events.
	 */
	public function track_add_to_cart_event() {
		$conversion_id = opd_get_option( 'linkedin_add_to_cart_conversion_id', 'oec_pixel_tracker', '' );
		if ( ! empty( $conversion_id ) ) {
			echo "<script>
				lintrk('track', { conversion_id: $conversion_id });
			</script>";
		}
	}

	/**
	 * Outputs JavaScript to handle AJAX Add to Cart event tracking.
	 */
	public function add_to_cart_ajax_handler() {
		$conversion_id = opd_get_option( 'linkedin_add_to_cart_conversion_id', 'oec_pixel_tracker', '' );
		if ( empty( $conversion_id ) ) {
			return;
		}
		?>
	<script type="text/javascript">
		jQuery(function($) {
			$('body').on('added_to_cart', function(event) {
				lintrk('track', { conversion_id: "<?php echo $conversion_id; ?>" });
			});
		});
	</script>
		<?php
	}


	/**
	 * Tracks Start Checkout events.
	 */
	public function track_start_checkout_event() {
		$conversion_id = opd_get_option( 'linkedin_start_checkout_conversion_id', 'oec_pixel_tracker', '' );
		if ( ! empty( $conversion_id ) ) {
			echo "<script>
				lintrk('track', { conversion_id: $conversion_id });
			</script>";
		}
	}

	/**
	 * Tracks Purchase events on the Thank You page.
	 *
	 * @param int $order_id The ID of the completed order.
	 */
	public function track_purchase_event( $order_id ) {
		$conversion_id = opd_get_option( 'linkedin_purchase_conversion_id', 'oec_pixel_tracker', '' );
		if ( ! empty( $conversion_id ) ) {
			echo "<script>
                lintrk('track', { conversion_id: '{$conversion_id}' });
            </script>";
		}
	}
}

/**
 * Initializes and returns an instance of OPD_LinkedIn_eCommerce_InsightTag.
 *
 * @return OPD_LinkedIn_eCommerce_InsightTag
 */
function opd_linkedin_ecommerce_insight_tag_instance() {
	return OPD_LinkedIn_eCommerce_InsightTag::instance();
}

opd_linkedin_ecommerce_insight_tag_instance();
