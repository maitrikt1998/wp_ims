<?php
/**
 * Class Popup_Frontend
 */
class Popup_Frontend {

	/**
	 * The single instance of the class.
	 *
	 * @var Popup_Frontend|null
	 */
	private static $_instance = null;

	/**
	 * Get the single instance of the class.
	 *
	 * @return Popup_Frontend
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Popup_Frontend constructor.
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'wp_footer', array( $this, 'render_popup_content' ) );
		add_action( 'wp_ajax_save_popup_content', array( $this, 'save_popup_content_ajax_handler' ) );
		add_action( 'wp_ajax_nopriv_save_popup_content', array( $this, 'save_popup_content_ajax_handler' ) );
	}

	/**
	 * Retrieves IDs of available popups based on current post context.
	 *
	 * This method checks all popups of type 'opb-popup' and determines which ones are available to be displayed
	 * on the current page. A popup is considered available if:
	 * 1. It is set to show on the whole site.
	 * 2. It has no specific pages assigned (and thus should appear everywhere).
	 * 3. It is assigned to the current page.
	 *
	 * The popups are fetched using a WP_Query and their availability is determined by checking
	 * post meta values. Once the available popup IDs are determined, they are returned as an array.
	 *
	 * @global WP_Post $post The global post object, representing the current post/page.
	 *
	 * @return array An array of IDs of available popups.
	 */
	private function get_available_popup_ids() {
		global $post;
		$current_post_id = ! empty( $post ) ? (int) $post->ID : false;

		$args = array(
			'post_type'      => 'opb-popup',
			'posts_per_page' => -1,
		);

		$available_popup_ids = array();

		$popup_query = new WP_Query( $args );

		if ( $popup_query->have_posts() ) {
			while ( $popup_query->have_posts() ) {
				$popup_query->the_post();
				$popup_id             = get_the_ID();
				$popup_for_whole_site = get_post_meta( $popup_id, 'popup_show_on_whole_site', true );
				$popup_for_pages      = get_post_meta( $popup_id, 'popup_pages', true );
				$popup_exclude_pages  = get_post_meta( $popup_id, 'popup_exclude_pages', true );

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

		return $available_popup_ids;
	}


	/**
	 * Enqueues scripts and styles for the frontend.
	 */
	public function enqueue_scripts_styles() {

		// Pass PHP variables to JavaScript using wp_localize_script().
		$current_post_id = get_the_ID();
		$popup_ids       = $this->get_available_popup_ids();

		$popups_data = array();
		if ( ! empty( $popup_ids ) ) {
			foreach ( $popup_ids as $avail_popup_id ) {
				$popup_delay           = get_post_meta( $avail_popup_id, 'popup_delay', true );
				$popup_trigger         = get_post_meta( $avail_popup_id, 'popup_trigger', true );
				$popup_display_mode    = get_post_meta( $avail_popup_id, 'popup_display_mode', true );
				$popup_position        = get_post_meta( $avail_popup_id, 'popup_position', true );
				$popup_close_button    = get_post_meta( $avail_popup_id, 'popup_close_button', true );
				$disable_mobile        = get_post_meta( $avail_popup_id, 'disable_mobile', true );
				$popup_size            = get_post_meta( $avail_popup_id, 'popup_size', true );
				$popup_width           = get_post_meta( $avail_popup_id, 'popup_width', true );
				$popup_height          = get_post_meta( $avail_popup_id, 'popup_height', true );
				$popup_autoclose_delay = get_post_meta( $avail_popup_id, 'popup_autoclose_delay', true );
				$popup_overlay_enabled = get_post_meta( $avail_popup_id, 'popup_overlay_enabled', true );
				$popup_overlay_color   = get_post_meta( $avail_popup_id, 'popup_overlay_color', true );
				$popup_inactivity_time = get_post_meta( $avail_popup_id, 'popup_inactivity_time', true );

				$popups_data[ $avail_popup_id ] = array(
					'popup_delay'           => absint( $popup_delay ),
					'popup_trigger'         => $popup_trigger,
					'popup_display_mode'    => $popup_display_mode,
					'popup_position'        => $popup_position,
					'popup_close_button'    => $popup_close_button,
					'disable_mobile_editor' => ! empty( $disable_mobile ) ? 1 : 0,
					'popup_size'            => $popup_size,
					'popup_width'           => $popup_width,
					'popup_height'          => $popup_height,
					'popup_autoclose_delay' => absint( $popup_autoclose_delay ),
					'popup_overlay_enabled' => ! empty( $popup_overlay_enabled ) ? 1 : 0,
					'popup_overlay_color'   => $popup_overlay_color,
					'popup_inactivity_time' => $popup_inactivity_time,
				);
			}

			wp_enqueue_style( 'popup-frontend-style', OPD_URL . 'modules/popup-builder/assets/css/frontend/popup-frontend.css', array(), OPD_VERSION );
			wp_enqueue_script( 'popup-editors-frontend-script', OPD_URL . 'modules/popup-builder/assets/js/frontend/popup-editors-frontend.js', array( 'jquery' ), OPD_VERSION, true );

			$localized_data = array(
				'current_post_id' => $current_post_id,
				'popupIDs'        => $popup_ids,
				'popups_data'     => $popups_data,
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'popup-ajax-nonce' ),
			);

			wp_localize_script( 'popup-editors-frontend-script', 'popupData', $localized_data );

		}
	}

	/**
	 * AJAX handler for saving popup content.
	 *
	 * This function handles the AJAX request for saving the content of a popup.
	 * It verifies the nonce to ensure the request is valid and checks if all required
	 * data is set in the $_POST global variable. The popup content is then sanitized and saved.
	 *
	 * @uses wp_send_json_error() Sends a JSON response back to an Ajax request, indicating failure.
	 * @uses wp_send_json_success() Sends a JSON response back to an Ajax request, indicating success.
	 * @uses absint() Convert a value to non-negative integer.
	 * @uses wp_verify_nonce() Verify that a correct security nonce was used with time limit.
	 *
	 * @return void
	 */
	public function save_popup_content_ajax_handler() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'popup-ajax-nonce' ) ) {
			wp_send_json_error( 'Nonce verification failed' );
			return;
		}

		if ( ! isset( $_POST['post_id'] ) ) {
			wp_send_json_error( 'Post ID not set' );
			return;
		}

		if ( ! isset( $_POST['content'] ) ) {
			wp_send_json_error( 'Content not set' );
			return;
		}
		$post_id = absint( $_POST['post_id'] );
		$content = $_POST['content'];
		$content = str_replace( 'core-block-supports-inline-css', '', $content );
		$this->save_popup_transient_content( $post_id, $content );
		wp_send_json_success();
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
			if ( count( $popup_ids ) > 1 ) {
				// get random availbale popup.
				$popup_key = array_rand( $popup_ids, 1 );
				$popup_id  = $popup_ids[ $popup_key ];
			} else {
				$popup_id = reset( $popup_ids );
			}

			echo '<div class="popup-overlay popup-overlay-' . esc_attr( $popup_id ) . '"></div>';

			foreach ( $popup_ids as $popup_id ) {
				$popup_content      = '';
				$popup_built_with   = '';
				$popup_close_button = get_post_meta( $popup_id, 'popup_close_button', true );
				$popup_size         = get_post_meta( $popup_id, 'popup_size', true );
				$popup_width        = get_post_meta( $popup_id, 'popup_width', true );
				$popup_height       = get_post_meta( $popup_id, 'popup_height', true );

				// Check if Elementor is active and content exists.
				$document = class_exists( '\Elementor\Plugin' ) ? \Elementor\Plugin::instance()->documents->get( $popup_id ) : false;
				if ( $document && $document->is_built_with_elementor() ) {
					$popup_built_with  = 'elementor';
					$elementor_content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $popup_id );
					if ( ! empty( $elementor_content ) ) {
						$popup_content = $elementor_content;
					} else {
						$popup_content = $this->get_popup_content( $popup_id );
					}
				} else {
					$popup_built_with = 'gutenberg';
					// Fallback to Gutenberg content.
					$popup_content = $this->get_popup_transient_content( $popup_id );
				}

				// Output the popup content.
				echo '<div class="popup popup-' . esc_attr( $popup_id ) . ' ' . esc_attr( $popup_built_with ) . '" data-built-with="' . esc_attr( $popup_built_with ) . '" data-need-memory-update="' . ( empty( $popup_content ) ? get_the_permalink( $popup_id ) : 0 ) . '" >';
				echo '<div class="popup-content">';
				if ( $popup_close_button == 'close' ) {
					echo '<button class="popup-close-button">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
												<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
											</svg>
										</button>';
				}
					echo $popup_content;
					echo '</div>';
				echo '</div>';
				?>

				<style>
					.e-con > .e-con-inner {
						<?php if ( ! empty( $popup_size ) && is_numeric( $popup_size ) ) : ?>
							width: <?php echo esc_html( $popup_size ); ?>%;
						<?php endif; ?>

						<?php if ( ! empty( $popup_width ) && is_numeric( $popup_width ) ) : ?>
							max-width: <?php echo esc_html( $popup_width ); ?>px;
						<?php endif; ?>

						<?php if ( ! empty( $popup_height ) && is_numeric( $popup_height ) ) : ?>
							height: <?php echo esc_html( $popup_height ); ?>px;
						<?php endif; ?>
					}
				</style>

				<?php
			}
			?>
				<?php
				// }
		}
	}

	/**
	 * Get the popup content stored in a transient.
	 *
	 * @param int $popup_id The ID of the popup post.
	 * @return string The popup content.
	 */
	protected function get_popup_transient_content( $popup_id ) {
		$transient_key = 'ocean_popup_builder_core_style_' . $popup_id;
		return wp_kses_stripslashes( get_transient( $transient_key ) );
	}

	/**
	 * Save the popup content in a transient.
	 *
	 * @param int    $popup_id The ID of the popup post.
	 * @param string $content The content to be saved.
	 * @return bool True if the value was set, false otherwise.
	 */
	protected function save_popup_transient_content( $popup_id, $content ) {
		$transient_key = 'ocean_popup_builder_core_style_' . $popup_id;
		return set_transient( $transient_key, $content, DAY_IN_SECONDS );
	}

	/**
	 * Get and prepare the popup content.
	 *
	 * @global object $wp_embed Handles media embedding.
	 * @param int $popup_id The ID of the popup post.
	 * @return string The processed popup content.
	 */
	private function get_popup_content( $popup_id ) {
		global $wp_embed;
		$post                   = get_post( $popup_id );
		$post_content           = do_blocks( $post->post_content );
		$post_content           = $wp_embed->run_shortcode( $post_content );
		$post_content           = do_shortcode( $post_content );
		$post_content           = $wp_embed->autoembed( $post_content );
		$post_content           = wptexturize( $post_content );
		$post_content           = wpautop( $post_content );
		$post_content           = shortcode_unautop( $post_content );
		$post_content           = prepend_attachment( $post_content );
		$wp_filter_content_tags = function_exists( 'wp_filter_content_tags' ) ? 'wp_filter_content_tags' : 'wp_make_content_images_responsive';
		$post_content           = $wp_filter_content_tags( $post_content );
		$post_content           = convert_smilies( $post_content );

		$echo_content = '';
		if ( strpos( $post_content, '\<\!\-\-more\-\-\>' ) !== false ) {
			list( $first_content, $second_content ) = explode( '<!--more-->', $post_content, 2 );
			if ( $second_content = preg_replace( '#^\s*\<\/p\>#', '', $second_content ) ) {
				$echo_content .= preg_replace( '#\<p\>\s*$#', '', $first_content );
				$echo_content .= '<a href="#" class="oec_more_content_button" >' . __( 'Read More...', 'ocean-ecommerce' ) . '</a>';
				$echo_content .= '<div style="display:none;">' . $second_content . '</div>';
				$echo_content .= '
					<script>
						jQuery(document).on( "click", ".oec_more_content_button", function (e) {
							e.preventDefault();
							jQuery(this).next().show(0);
							jQuery(this).hide(0);
						});
					</script>';
			} else {
				$echo_content .= $post_content;
			}
		} else {
			$echo_content .= $post_content;
		}

		return $echo_content;
	}
}

// Get the instance of Popup_Frontend class.
Popup_Frontend::instance();
