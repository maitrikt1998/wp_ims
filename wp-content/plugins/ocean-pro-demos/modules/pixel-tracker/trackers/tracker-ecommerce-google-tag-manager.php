<?php


/**
 * Class OPD_GTM_Tracker
 * Manages Google Tag Manager integration for WooCommerce events.
 */

class OPD_GTM_Tracker {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_GTM_Tracker|null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_GTM_Tracker
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * OPD_GTM_Tracker constructor.
	 * Sets up hooks for GTM integration.
	 */
	public function __construct() {
		if ( opd_get_option( 'enable_google_tag_manager', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_head', array( $this, 'output_gtm_script' ) );
			add_action( 'wp_body_open', array( $this, 'output_gtm_noscript' ) );
			add_action( 'wp_footer', array( $this, 'maybe_output_order_completion_script' ) );
			$this->add_gtm_ecommerce_events();
		}
	}

	/**
	 * Outputs the Google Tag Manager script in the head of the document.
	 */
	public function output_gtm_script() {
		$gtm_id = opd_get_option( 'google_tag_manager_id', 'oec_pixel_tracker', 'GTM-XXXXXXXX' );
		if ( ! empty( $gtm_id ) ) {
			echo "<!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','" . esc_js( $gtm_id ) . "');</script>
            <!-- End Google Tag Manager -->";
		}
	}

	/**
	 * Outputs the Google Tag Manager noscript fallback.
	 */
	public function output_gtm_noscript() {
		$gtm_id = opd_get_option( 'google_tag_manager_id', 'oec_pixel_tracker', 'GTM-XXXXXXXX' );
		if ( ! empty( $gtm_id ) ) {
			echo '<!-- Google Tag Manager (noscript) -->
			<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $gtm_id ) . '"
			height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
			<!-- End Google Tag Manager (noscript) -->';
		}
	}

	/**
	 * Adds WooCommerce hooks for GTM event tracking.
	 */
	private function add_gtm_ecommerce_events() {
		// Using opd_get_option for GTM event tracking settings.
		if ( opd_get_option( 'gtm_track_product_views_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'woocommerce_after_single_product', array( $this, 'track_gtm_product_view' ) );
		}

		if ( opd_get_option( 'gtm_track_checkout_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'woocommerce_before_checkout_form', array( $this, 'track_gtm_checkout' ) );
		}
	}

	/**
	 * Tracks product views for GTM.
	 */
	public function track_gtm_product_view() {
		global $product;
			echo "<script>
			dataLayer.push({
				'event': 'productView',
				'ecommerce': {
					'detail': {
						'products': [{
							'id': '" . esc_js( $product->get_id() ) . "',
							'name': '" . esc_js( $product->get_name() ) . "',
							'price': '" . esc_js( $product->get_price() ) . "'
						}]
					}
				}
			});
		</script>";
	}

	/**
	 * Tracks checkout initiation for GTM.
	 */
	public function track_gtm_checkout() {
			$cart     = WC()->cart->get_cart();
			$products = array();

		foreach ( $cart as $cart_item_key => $cart_item ) {
			$product_id = $cart_item['product_id'];
			$product    = wc_get_product( $product_id );
			$products[] = array(
				'id'       => $product_id,
				'name'     => $product->get_name(),
				'price'    => $product->get_price(),
				'quantity' => $cart_item['quantity'],
			);
		}

			echo "<script>
			dataLayer.push({
				'event': 'checkout',
				'ecommerce': {
					'checkout': {
						'actionField': {'step': 1, 'option': 'Checkout Start'},
						'products': " . wp_json_encode( $products, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ) . '
					}
				}
			});
			</script>';
	}

	/**
	 * Tracks order completion for GTM.
	 *
	 * @param int $order_id The ID of the completed order.
	 */
	public function track_gtm_order_completion( $order_id ) {

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$products = array();
		foreach ( $order->get_items() as $item_id => $item ) {
			$product_id = $item->get_product_id();
			$product    = wc_get_product( $product_id );
			if ( ! $product ) {
				continue;
			}
			$products[] = array(
				'id'       => $product_id,
				'name'     => $product->get_name(),
				'price'    => $item->get_total(),
				'quantity' => $item->get_quantity(),
			);
		}

		if ( empty( $products ) ) {
			return;
		}

		echo "<script>
		dataLayer.push({
			'event': 'purchase',
			'ecommerce': {
				'currencyCode': '" . get_woocommerce_currency() . "',
				'purchase': {
					'actionField': {
						'id': '" . $order_id . "',
						'revenue': '" . $order->get_total() . "'
					},
					'products': " . wp_json_encode( $products, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ) . '
				}
			}
		});
		</script>';
	}


	/**
	 * Conditionally outputs the order completion tracking script.
	 */
	public function maybe_output_order_completion_script() {
		if ( is_wc_endpoint_url( 'order-received' ) ) {
			global $wp;
			$order_id = isset( $wp->query_vars['order-received'] ) ? $wp->query_vars['order-received'] : false;

			if ( $order_id && opd_get_option( 'gtm_track_order_completion_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
				$this->track_gtm_order_completion( $order_id );
			}
		}
	}
}

/**
 * Initializes and returns an instance of OPD_GTM_Tracker.
 *
 * @return OPD_GTM_Tracker
 */
function oec_gtm_tracker_instance() {
	return OPD_GTM_Tracker::instance();
}


oec_gtm_tracker_instance();
