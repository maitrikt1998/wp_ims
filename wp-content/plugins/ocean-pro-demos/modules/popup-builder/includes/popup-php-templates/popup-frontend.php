<?php
/**
 * Class Popup_PHP_Frontend
 */
class Popup_PHP_Frontend {

	private $available_popup_ids = false;

	/**
	 * The single instance of the class.
	 *
	 * @var Popup_PHP_Frontend|null
	 */
	private static $_instance = null;

	/**
	 * Get the single instance of the class.
	 *
	 * @return Popup_PHP_Frontend
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Popup_PHP_Frontend constructor.
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'wp_footer', array( $this, 'render_popup_content' ) );
		add_action( 'wp_ajax_save_popup_content', array( $this, 'save_popup_content_ajax_handler' ) );
		add_action( 'wp_ajax_nopriv_save_popup_content', array( $this, 'save_popup_content_ajax_handler' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_fonts' ) );
	}

	/**
	 * Retrieves IDs of available popups.
	 *
	 * This method checks all popups of type 'opb-popup-php' and determines which ones are available to be displayed
	 * on the current page. A popup is considered available if:
	 * 1. It is set to show on the whole site.
	 * 2. It has no specific pages assigned (and thus should appear everywhere).
	 * 3. It is assigned to the current page.
	 *
	 * @global WP_Post $post The global post object.
	 *
	 * @return array An array of available popup IDs.
	 */
	public function get_available_popup_ids() {
		if ( $this->available_popup_ids !== false ) {
			return $this->available_popup_ids;
		}

		global $post;
		$current_post_id = ! empty( $post ) ? $post->ID : false;
		$args            = array(
			'post_type'      => 'opb-popup-php',
			'posts_per_page' => -1,
		);

		$available_popup_ids = array();

		$popup_query = new WP_Query( $args );

		if ( $popup_query->have_posts() ) {
			while ( $popup_query->have_posts() ) {
				$popup_query->the_post();
				$popup_id             = get_the_ID();
				$popup_for_whole_site = get_post_meta( $popup_id, 'opb_show_on_whole_site_meta', true );
				$popup_for_pages      = get_post_meta( $popup_id, 'opb_selected_page_meta', true );
				$popup_exclude_pages  = get_post_meta( $popup_id, 'opb_exclude_pages_meta', true );

				// Ensure $popup_exclude_pages is an array.
				if ( ! is_array( $popup_exclude_pages ) ) {
					$popup_exclude_pages = array();
				}

				// Convert all values in $popup_exclude_pages to integers.
				$popup_exclude_pages = array_map( 'intval', $popup_exclude_pages );


				// Check if the popup should be excluded for the current page.
				if ( in_array( $current_post_id, $popup_exclude_pages, true ) ) {
					continue;
				}

				// Handle popup display logic based on whole site setting and specific pages.
				if ( $popup_for_whole_site || ( ! empty( $popup_for_pages ) && is_array( $popup_for_pages ) && in_array( $current_post_id, $popup_for_pages ) ) ) {
					$available_popup_ids[] = $popup_id;
				}
			}
			wp_reset_postdata();
		}
		$this->available_popup_ids = $available_popup_ids;
		return $available_popup_ids;
	}

	/**
	 * Enqueues scripts and styles for the frontend.
	 */
	public function enqueue_scripts_styles() {

		if ( is_singular( 'opb-popup-php' ) ) {
			return;
		}

		// Pass PHP variables to JavaScript using wp_localize_script().
		$current_post_id = get_the_ID();
		$popup_ids       = $this->get_available_popup_ids();

		$popups_php_data = array();
		if ( ! empty( $popup_ids ) ) {
			foreach ( $popup_ids as $avail_popup_id ) {
				$popup_delay           = get_post_meta( $avail_popup_id, 'opb_popup_delay_meta', true );
				$popup_trigger         = get_post_meta( $avail_popup_id, 'opb_popup_trigger_meta', true );
				$popup_display_mode    = get_post_meta( $avail_popup_id, 'opb_popup_display_mode_meta', true );
				$popup_position        = get_post_meta( $avail_popup_id, 'opb_popup_position_meta', true );
				$popup_close_button    = get_post_meta( $avail_popup_id, 'opb_popup_close_button_meta', true );
				$disable_mobile_php    = get_post_meta( $avail_popup_id, 'opb_disable_mobile_meta', true );
				$popup_autoclose_delay = get_post_meta( $avail_popup_id, 'opb_autoclose_delay_meta', true );
				$popup_overlay_enabled = get_post_meta( $avail_popup_id, 'opb_popup_overlay_enabled_meta', true );
				$popup_overlay_color   = get_post_meta( $avail_popup_id, 'opb_overlay_color_meta', true );
				$popup_inactivity_time = get_post_meta( $avail_popup_id, 'opb_popup_inactivity_time_meta', true );


				$popups_php_data[ $avail_popup_id ] = array(
					'popup_delay'           => absint( $popup_delay ),
					'popup_trigger'         => $popup_trigger,
					'popup_display_mode'    => $popup_display_mode,
					'popup_position'        => $popup_position,
					'popup_close_button'    => $popup_close_button,
					'disable_mobile_php'    => ! empty( $disable_mobile_php ) ? 1 : 0,
					'popup_autoclose_delay' => absint( $popup_autoclose_delay ),
					'popup_overlay_enabled' => ! empty( $popup_overlay_enabled ) ? 1 : 0,
					'popup_overlay_color'   => $popup_overlay_color,
					'popup_inactivity_time' => $popup_inactivity_time,

				);
			}

			wp_enqueue_style( 'popup-frontend-style', OPD_URL . 'modules/popup-builder/assets/css/frontend/popup-frontend.css', array(), OPD_VERSION );
			wp_enqueue_script( 'popup-php-frontend-script', OPD_URL . 'modules/popup-builder/assets/js/frontend/popup-php-frontend.js', array( 'jquery' ), OPD_VERSION, true );

			$localized_data = array(
				'current_post_id' => $current_post_id,
				'popupIDs'        => $popup_ids,
				'popups_php_data' => $popups_php_data,
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'popup-ajax-nonce' ),
			);

			wp_localize_script( 'popup-php-frontend-script', 'popupPhpData', $localized_data );

		}
	}

	/**
	 * Renders the popup content on the frontend.
	 */
	public function render_popup_content() {
		if ( isset( $_REQUEST['elementor-preview'] ) && isset( $_REQUEST['ver'] ) ) {
			return;
		}

		$popup_ids = $this->get_available_popup_ids();

		if ( ! empty( $popup_ids ) ) {
			foreach ( $popup_ids as $popup_id ) {

				$popup_id           = absint( $popup_id );
				$popup_close_button = get_post_meta( $popup_id, 'opb_popup_close_button_meta', true );
				echo '<div class="popup-overlay popup-overlay-' . esc_attr( $popup_id ) . '"></div>';
				echo '<div class="popup popup-' . esc_attr( $popup_id ) . '" style="width: auto;">';
				echo '<div class="popup-content">';
				if ( $popup_close_button == 'close' ) {
					echo '<button class="popup-close-button" onclick="hidePopupPHP()">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
					<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
					</svg>
					</button>';
				}
				echo '<div id="ocean-popup-root-' . esc_attr( $popup_id ) . '"></div>';
				echo '</div>';
				echo '</div>';
			}
		}
	}



	/**
	 * Load google fonts.
	 */
	public function load_fonts() {
		$popup_ids = $this->get_available_popup_ids();

		if ( ! empty( $popup_ids ) ) {
			foreach ( $popup_ids as $popup_id ) {
				$popup_id = absint( $popup_id );

				// Retrieve font family for title, content, and button.
				$title_font   = $this->get_typography_font_family( $popup_id, 'opb_title_typography_meta' );
				$content_font = $this->get_typography_font_family( $popup_id, 'opb_content_typography_meta' );
				$button_font  = $this->get_typography_font_family( $popup_id, 'opb_button_typography_meta' );

				// Prepare an array of unique fonts to enqueue.
				$fonts_to_enqueue = array_unique( array_filter( array( $title_font, $content_font, $button_font ) ) );

				// Loop through and enqueue fonts.
				foreach ( $fonts_to_enqueue as $font ) {
					if ( function_exists( 'oceanwp_enqueue_google_font' ) ) {
						oceanwp_enqueue_google_font( $font );
					}
				}
			}
		}
	}

	private function get_typography_font_family( $popup_id, $meta_key ) {
		$typography_json     = get_post_meta( $popup_id, $meta_key, true );
		$typography_settings = $typography_json ? json_decode( $typography_json, true ) : array();
		return isset( $typography_settings['fontFamily'] ) ? $typography_settings['fontFamily'] : '';
	}

}

// Get the instance of Popup_PHP_Frontend class.
function Popup_PHP_Frontend() {
	return Popup_PHP_Frontend::instance();
}

Popup_PHP_Frontend();
