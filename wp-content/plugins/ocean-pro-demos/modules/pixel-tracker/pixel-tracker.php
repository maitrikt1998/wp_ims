<?php

/**
 * CUstom Post Type Manager
 */
class OPD_Pixel_Tracker {


	private static $instance = null;

	private static $CHECKER_URL = 'https://demos.oceanwp.org/elementor-library/check-license.php';

	/**
	 * [instance]
	 *
	 * @return [OPD_Pixel_Tracker]
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function __construct() {
		add_action( 'wp_ajax_oceanwp_cp_save_pro_demos_pixel_settings', array( $this, 'save_pro_demos_pixel_settings' ) );
		$opd_pixel_tracker_enable = get_option( 'opd_pixel_tracker_enable', 'no' );
		if ( 'yes' === $opd_pixel_tracker_enable ) {
			if ( $this->is_valid_license() ) {
				if ( ! Ocean_Pro_Demos::is_woocommerce_active() ) {
					return;
				} else {
					require_once OPD_PATH . 'modules/pixel-tracker/includes/helper.php';

				}
				require_once OPD_PATH . 'modules/pixel-tracker/trackers/tracker-general-facebook-pixel.php';
				require_once OPD_PATH . 'modules/pixel-tracker/trackers/tracker-general-tiktok-pixel.php';
				require_once OPD_PATH . 'modules/pixel-tracker/trackers/tracker-general-linkedin-insight-tag.php';
				require_once OPD_PATH . 'modules/pixel-tracker/trackers/tracker-general-pinterest-tag.php';
				require_once OPD_PATH . 'modules/pixel-tracker/trackers/tracker-general-google-consent.php';

				require_once OPD_PATH . 'modules/pixel-tracker/trackers/tracker-ecommerce-facebook-pixel.php';
				require_once OPD_PATH . 'modules/pixel-tracker/trackers/tracker-ecommerce-google-tag-manager.php';
				require_once OPD_PATH . 'modules/pixel-tracker/trackers/tracker-ecommerce-tiktok-pixel.php';
				require_once OPD_PATH . 'modules/pixel-tracker/trackers/tracker-ecommerce-pinterest-tag.php';
			}
		}
	}

	public function save_pro_demos_pixel_settings() {
		if ( empty( $_POST['form_fields'] ) ) {
			wp_send_json_error( array( 'message' => 'Something went wrong' ) );
		}

		$params = array();
		parse_str( $_POST['form_fields'], $params );

		$fields_mapping      = self::get_mapping_for_pixel_settings();
		$stored_option_value = get_option( 'oec_pixel_tracker', array() );
		foreach ( $fields_mapping as $form_field_name => $option_field_key ) {
			if ( isset( $params[ $form_field_name ] ) ) {
				$value = $params[ $form_field_name ];
			} else {
				$value = false;
			}
			$stored_option_value[ $option_field_key ] = $value;
		}
		update_option( 'oec_pixel_tracker', $stored_option_value );

		wp_send_json_success( array( 'message' => 'Settings saved' ) );
		wp_die();
	}

	public static function get_mapping_for_pixel_settings() {
		return array(
			'enable-facebook-pixel'                   => 'enable_facebook_pixel',
			'facebook-pixel-id'                       => 'facebook_pixel_id',
			'enable-fb-product-views-tracking'        => 'fb_track_product_views_event',
			'enable-fb-add-to-cart-tracking'          => 'fb_track_add_to_cart_event',
			'enable-fb-checkout-tracking'             => 'fb_track_checkout_event',
			'enable-fb-order-completion-tracking'     => 'fb_track_order_completion_event',
			'enable-fb-content-views-tracking'        => 'fb_track_view_content_event',
			'enable-fb-search-tracking'               => 'fb_track_search_event',
			'enable-fb-contact-tracking'              => 'fb_track_contact_event',

			'enable-google-tag-manager'               => 'enable_google_tag_manager',
			'google-tag-manager-id'                   => 'google_tag_manager_id',
			'enable-gtm-product-views-tracking'       => 'gtm_track_product_views_event',
			'enable-gtm-add-to-cart-tracking'         => 'gtm_track_add_to_cart_event',
			'enable-gtm-checkout-tracking'            => 'gtm_track_checkout_event',
			'enable-order-completion-tracking-gtm'    => 'gtm_track_order_completion_event',

			'enable-tiktok-pixel'                     => 'enable_tiktok_pixel',
			'tiktok-pixel-id'                         => 'tiktok_pixel_id',
			'enable-tiktok-product-views-tracking'    => 'tiktok_pixel_track_product_views_event',
			'enable-tiktok-add-to-cart-tracking'      => 'tiktok_pixel_track_add_to_cart_event',
			'enable-tiktok-checkout-tracking'         => 'tiktok_pixel_track_checkout_event',
			'enable-tiktok-order-completion-tracking' => 'tiktok_pixel_track_order_completion_event',
			'enable-tiktok-content-views-tracking'    => 'tiktok_pixel_track_view_content_event',
			'enable-tiktok-search-tracking'           => 'tiktok_pixel_track_search_event',
			'enable-tiktok-contact-tracking'          => 'tiktok_pixel_track_contact_event',

			'enable-linkedin-insight-tag'             => 'enable_linkedin_insight_tag',
			'linkedin-partner-id'                     => 'linkedin_partner_id',
			'linkedin-contact-conversion-id'          => 'linkedin_contact_conversion_id',
			'linkedin-search-conversion-id'           => 'linkedin_search_conversion_id',
			'linkedin-viewcontent-conversion-id'      => 'linkedin_viewcontent_conversion_id',
			'linkedin-add-to-cart-conversion-id'      => 'linkedin_add_to_cart_conversion_id',
			'linkedin-start-checkout-conversion-id'   => 'linkedin_start_checkout_conversion_id',
			'linkedin-purchase-conversion-id'         => 'linkedin_purchase_conversion_id',

			'enable-pinterest-tag'                    => 'enable_pinterest_tag',
			'pinterest-tag-id'                        => 'pinterest_tag_id',
			'pinterest-tag-track-page-visit'          => 'pinterest_tag_track_page_visit',
			'pinterest-tag-track-search'              => 'pinterest_tag_track_search',
			'pinterest-tag-track-addtocart'           => 'pinterest_tag_track_addtocart',
			'pinterest-tag-track-checkout'            => 'pinterest_tag_track_checkout',

			'enable-google-consent-gtm'               => 'enable_google_consent',
		);
	}

	public function is_valid_license() {
		$validate = $this->check_license();
		if ( $validate ) {
			return true;
		} else {
			return false;

		}
	}

	private function check_license() {

		global $ocean_pro_demos_fs;
		if ( ! empty( $ocean_pro_demos_fs ) ) {
			$plugin_id           = $ocean_pro_demos_fs->get_site()->id;
			$_license            = $ocean_pro_demos_fs->_get_license();
			$license_id          = empty( $_license->parent_license_id ) ? '' : $_license->parent_license_id;
			$is_registered       = $ocean_pro_demos_fs->is_registered();
			$is_tracking_allowed = $ocean_pro_demos_fs->is_tracking_allowed();
			if ( empty( $plugin_id ) || empty( $license_id ) || empty( $is_registered ) || empty( $is_tracking_allowed ) ) {
				return false;
			}
		} else {
			return false;
		}

		$data['plugin_id']           = $plugin_id;
		$data['wp_site_url']         = get_site_url();
		$data['license_id']          = $license_id;
		$data['is_registered']       = $is_registered;
		$data['is_tracking_allowed'] = $is_tracking_allowed;

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, self::$CHECKER_URL );

		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		$response_data = curl_exec( $ch );
		curl_close( $ch );

		$response_data = json_decode( $response_data, true );

		return $response_data['success'];
	}
}


function opd_pixel_tracker_instance() {
	return OPD_Pixel_Tracker::instance();
}

opd_pixel_tracker_instance();
