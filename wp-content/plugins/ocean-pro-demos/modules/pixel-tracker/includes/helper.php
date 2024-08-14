<?php

/**
 * Handles the setup and integration of pixel tracking for e-commerce events.
 */
class OPD_PixelHelper {

	/**
	 * Singleton instance of the class.
	 *
	 * @var OPD_PixelHelper|null
	 */
	private static $instance = null;

	/**
	 * Tracking options configuration.
	 *
	 * @var array
	 */
	public $track_options = array();

	/**
	 * Ensures a single instance of the class.
	 *
	 * @return OPD_PixelHelper The instance of this class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Initializes tracking options and sets up e-commerce event hooks.
	 */
	public function __construct() {
		$this->set_options();

		$this->add_to_cart_ecommerce_events();
	}

	/**
	 * Registers e-commerce event hooks based on tracking options.
	 */
	protected function add_to_cart_ecommerce_events() {
		if ( opd_get_option( 'gtm_track_add_to_cart_event', 'oec_pixel_tracker', 'off' ) === 'on'
		|| opd_get_option( 'fb_track_add_to_cart_event', 'oec_pixel_tracker', 'off' ) === 'on'
		|| opd_get_option( 'tiktok_pixel_track_add_to_cart_event', 'oec_pixel_tracker', 'off' ) === 'on'
		|| opd_get_option( 'pinterest_tag_track_addtocart', 'oec_pixel_tracker', 'off' ) === 'on'
		) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'woocommerce_after_single_product', array( $this, 'product_detail_view' ) );
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'single_add_to_cart' ) );
		}
	}

	/**
	 * Initializes tracking options from settings.
	 */
	protected function set_options() {
		$this->track_options = array(
			'currency'                  => esc_js( get_woocommerce_currency() ),

			'fb_pixel_active'           => opd_get_option( 'enable_facebook_pixel', 'oec_pixel_tracker', 'off' ) === 'on',
			'gtm_active'                => opd_get_option( 'enable_google_tag_manager', 'oec_pixel_tracker', 'off' ) === 'on',
			'tiktok_pixel_active'       => opd_get_option( 'enable_tiktok_pixel', 'oec_pixel_tracker', 'off' ) === 'on',
			'pinterest_tag_active'      => opd_get_option( 'enable_pinterest_tag', 'oec_pixel_tracker', 'off' ) === 'on',

			'fb_pixel_add_to_cart'      => opd_get_option( 'gtm_track_add_to_cart_event', 'oec_pixel_tracker', 'off' ) === 'on',
			'gtm_add_to_cart'           => opd_get_option( 'fb_track_add_to_cart_event', 'oec_pixel_tracker', 'off' ) === 'on',
			'tiktok_pixel_add_to_cart'  => opd_get_option( 'tiktok_pixel_track_add_to_cart_event', 'oec_pixel_tracker', 'off' ) === 'on',
			'pinterest_tag_add_to_cart' => opd_get_option( 'pinterest_tag_track_addtocart', 'oec_pixel_tracker', 'off' ) === 'on',
		);
	}

	/**
	 * Tracks product detail views for analytics.
	 */
	public function product_detail_view() {
		$product    = wc_get_product();
		$category   = get_the_terms( $product->get_id(), 'product_cat' );
		$categories = '';
		if ( $category ) {
			foreach ( $category as $term ) {
				$categories .= $term->name . ',';
			}
		}
		// remove last comma(,) if multiple categories are there.
		$categories = rtrim( $categories, ',' );
		// product detail view json.
		$prodpage_detail_json = array(
			'ocean_track_id'       => esc_js( $product->get_id() ),
			'ocean_track_identify' => $product->get_sku() ? esc_js( $product->get_sku() ) : esc_js( $product->get_id() ),
			'ocean_track_name'     => esc_js( $product->get_title() ),
			'ocean_track_price'    => esc_js( $product->get_price() ),
		);

		// prod page detail view json.
		wc_enqueue_js( 'ocean_track_pdetails=' . json_encode( $prodpage_detail_json ) . ';' );
	}

	/**
	 * Tracks single product add to cart events for analytics.
	 */
	public function single_add_to_cart() {
		// return if not product page.
		if ( ! is_single() || ! is_product() ) {
			return;
		}
		global $product;
		$variations_data = array();
		if ( $product && $product->is_type( 'variable' ) ) {
			$variations_data['default_attributes']   = $product->get_default_attributes();
			$variations_data['available_variations'] = $product->get_available_variations();
			$variations_data['available_attributes'] = $product->get_variation_attributes();
		}
		?>
		<script data-cfasync="false" data-no-optimize="1" data-pagespeed-no-defer>
			window.addEventListener('load', call_ocean_pixel_helper, true);

			function call_ocean_pixel_helper() {
				const ocen_pixel_js = new Ocean_Pixel_Helper(<?php echo json_encode( $this->track_options ); ?>);
				ocen_pixel_js.singleProductAddToCartEventBind(<?php echo json_encode( $variations_data ); ?>);
			}
		</script>
		<?php
	}

	/**
	 * Enqueues necessary scripts for tracking.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'ocean-pixel-helper', plugins_url( '../assets/js/ocean-pixel-helper.js', __FILE__ ), array( 'jquery' ), DEMO_API_IMAGES_ASSETS_VERSION, true );
	}
}

/**
 * Initializes and returns an instance of OPD_PixelHelper.
 *
 * @return OPD_PixelHelper The singleton instance of the pixel helper.
 */
function oec_pixel_helper_instance() {
	return OPD_PixelHelper::instance();
}



// Conditionally instantiate the pixel helper based on tracking settings.
if ( opd_get_option( 'enable_facebook_pixel', 'oec_pixel_tracker', 'off' ) === 'on'
	|| opd_get_option( 'enable_google_tag_manager', 'oec_pixel_tracker', 'off' ) === 'on'
	|| opd_get_option( 'enable_tiktok_pixel', 'oec_pixel_tracker', 'off' ) === 'on'
	|| opd_get_option( 'enable_pinterest_tag', 'oec_pixel_tracker', 'off' ) === 'on'
	) {
	oec_pixel_helper_instance();
}
