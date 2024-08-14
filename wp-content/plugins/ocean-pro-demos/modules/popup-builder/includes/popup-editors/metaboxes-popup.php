<?php

/**
 * Class Popup_Meta_Boxes
 */
class OPB_Popup_Meta_Boxes {
	/**
	 * Registers the Popup custom post type.
	 */
	public static function register() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_popup_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_popup_meta' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_popup_assets' ) );
		add_action( 'admin_head', array( __CLASS__, 'oec_gutenwidth_admin_styles' ) );
	}



	/**
	 * Adds meta boxes for the Popup custom post type.
	 */
	public static function add_popup_meta_boxes() {
		add_meta_box(
			'popup_meta_box',
			'Popup Settings',
			array( __CLASS__, 'render_popup_meta_box' ),
			'opb-popup',
			'normal',
			'high'
		);
	}

	/**
	 * Renders the content of the Popup meta box.
	 *
	 * @param WP_Post $post The current post object.
	 */
	public static function render_popup_meta_box( $post ) {
		// Retrieve existing meta values.
		$popup_delay              = get_post_meta( $post->ID, 'popup_delay', true ) ? get_post_meta( $post->ID, 'popup_delay', true ) : '5';
		$popup_pages              = get_post_meta( $post->ID, 'popup_pages', true );
		$popup_trigger            = get_post_meta( $post->ID, 'popup_trigger', true );
		$popup_display_mode       = get_post_meta( $post->ID, 'popup_display_mode', true );
		$popup_size               = get_post_meta( $post->ID, 'popup_size', true ) ? get_post_meta( $post->ID, 'popup_size', true ) : '50%';
		$popup_width              = get_post_meta( $post->ID, 'popup_width', true );
		$popup_height             = get_post_meta( $post->ID, 'popup_height', true );
		$popup_position           = get_post_meta( $post->ID, 'popup_position', true );
		$popup_close_button       = get_post_meta( $post->ID, 'popup_close_button', true );
		$disable_mobile           = get_post_meta( $post->ID, 'disable_mobile', true );
		$popup_autoclose_delay    = get_post_meta( $post->ID, 'popup_autoclose_delay', true ) ? get_post_meta( $post->ID, 'popup_autoclose_delay', true ) : '0';
		$popup_overlay_enabled    = get_post_meta( $post->ID, 'popup_overlay_enabled', true );
		$popup_show_on_whole_site = get_post_meta( $post->ID, 'popup_show_on_whole_site', true );
		$popup_overlay_color      = get_post_meta( $post->ID, 'popup_overlay_color', true ) ? get_post_meta( $post->ID, 'popup_overlay_color', true ) : 'rgba(0, 0, 0, 0.5)';
		$popup_inactivity_time    = get_post_meta( $post->ID, 'popup_inactivity_time', true ) ?: '60';
		$popup_exclude_pages      = get_post_meta( $post->ID, 'popup_exclude_pages', true );

		if ( ! is_array( $popup_exclude_pages ) ) {
			$popup_exclude_pages = array();
		}

		if ( ! is_array( $popup_pages ) ) {
			$popup_pages = array();
		}

		// Retrieve all pages.
		$pages = get_pages();

		// Output the meta box content.
		?>
		<div class="popup-meta-box">
			<div class="vertical-tabs">
				<ul class="vertical-tab-navigation">
					<li aria-selected="true">
						<a href="#popup-general-settings" class="active">
							<i class="dashicons dashicons-admin-generic" aria-hidden="true"></i>
							<span><?php esc_html_e( 'General Settings', 'ocean-ecommerce' ); ?></span>
						</a>
					</li>
					<li aria-selected="false">
						<a href="#popup-advanced-settings">
							<i class="dashicons dashicons-admin-tools" aria-hidden="true"></i>
							<span><?php esc_html_e( 'Advanced Settings', 'ocean-ecommerce' ); ?></span>
						</a>
					</li>
				</ul>

				<div id="popup-general-settings" class="vertical-tab-panel active">

					<div class="field-container has-checkbox-color">
						<div class="oceanwp-mb-desc">
							<label for="popup-show-on-whole-site"><?php esc_html_e( 'Show on Whole Site:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<input type="checkbox" id="popup-show-on-whole-site" name="popup_show_on_whole_site" <?php checked( $popup_show_on_whole_site, '1' ); ?> value="1">
						</div>
					</div>

					<div class="field-container" id="exclude-pages-container" style="<?php echo $popup_show_on_whole_site ? '' : 'display: none;'; ?>">
						<div class="oceanwp-mb-desc">
							<label for="popup-exclude-pages"><?php esc_html_e( 'Exclude Pages:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<select id="popup-exclude-pages" name="popup_exclude_pages[]" multiple>
								<?php foreach ( $pages as $page ) : ?>
									<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( in_array( $page->ID, $popup_exclude_pages ), true ); ?>>
										<?php echo esc_html( $page->post_title ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="field-container" id="select-pages-container">
						<div class="oceanwp-mb-desc">
							<label for="popup-pages"><?php esc_html_e( 'Select Pages:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<select id="popup-pages" name="popup_pages[]" multiple>
								<?php foreach ( $pages as $page ) : ?>
									<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( in_array( $page->ID, $popup_pages ), true ); ?>>
										<?php echo esc_html( $page->post_title ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="field-container popup-trigger-option">
						<div class="oceanwp-mb-desc">
							<label for="popup-trigger"><?php esc_html_e( 'Select Trigger:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<select id="popup-trigger" name="popup_trigger">
								<option value="load" <?php selected( $popup_trigger, 'load' ); ?>><?php esc_html_e( 'On Load', 'ocean-ecommerce' ); ?></option>
								<option value="click" <?php selected( $popup_trigger, 'click' ); ?>><?php esc_html_e( 'On Click', 'ocean-ecommerce' ); ?></option>
								<option value="hover" <?php selected( $popup_trigger, 'hover' ); ?>><?php esc_html_e( 'On Hover', 'ocean-ecommerce' ); ?></option>
								<option value="exit_intent" <?php selected( $popup_trigger, 'exit_intent' ); ?>><?php esc_html_e( 'Exit Intent', 'ocean-ecommerce' ); ?></option>
								<option value="inactivity" <?php selected( $popup_trigger, 'inactivity' ); ?>><?php esc_html_e( 'Inactivity', 'ocean-ecommerce' ); ?></option>
							</select>
						</div>
					</div>

					<div class="field-container inactivity-time">
						<div class="oceanwp-mb-desc">
							<label for="popup-inactivity-time"><?php esc_html_e( 'Inactivity Time (in seconds):', 'ocean-ecommerce' ); ?></label>
							<p class="description"><?php esc_html_e( 'Time of user inactivity after which the popup will be shown.', 'ocean-ecommerce' ); ?></p>
						</div>
						<div class="oceanwp-mb-field">
							<input type="number" id="popup-inactivity-time" name="popup_inactivity_time" value="<?php echo esc_attr( $popup_inactivity_time ); ?>">
						</div>
					</div>

					<div class="field-container popup-delay-option">
						<div class="oceanwp-mb-desc">
							<label for="popup-delay"><?php esc_html_e( 'Popup Delay (in seconds):', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<input type="number" id="popup-delay" name="popup_delay"
							value="<?php echo esc_attr( $popup_delay ); ?>">
						</div>
					</div>

					<div class="field-container">
						<div class="oceanwp-mb-desc">
							<label for="popup-autoclose-delay"><?php esc_html_e( 'Autoclose Delay (in seconds):', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<input type="number" id="popup-autoclose-delay" name="popup_autoclose_delay"
								value="<?php echo esc_attr( $popup_autoclose_delay ); ?>">
						</div>
					</div>

					<div class="field-container">
						<div class="oceanwp-mb-desc">
							<label for="popup-display-mode"><?php esc_html_e( 'Popup Display Mode:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<select id="popup-display-mode" name="popup_display_mode">
								<option value="every_time" <?php selected( $popup_display_mode, 'every_time' ); ?>><?php esc_html_e( 'Every time', 'ocean-ecommerce' ); ?></option>
								<option value="once_per_session" <?php selected( $popup_display_mode, 'once_per_session' ); ?>><?php esc_html_e( 'One time per session', 'ocean-ecommerce' ); ?></option>
								<option value="once" <?php selected( $popup_display_mode, 'once' ); ?>><?php esc_html_e( 'One time', 'ocean-ecommerce' ); ?></option>
							</select>
						</div>
					</div>

					<div class="field-container">
						<div class="oceanwp-mb-desc">
							<label for="popup-size"><?php esc_html_e( 'Popup Size:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<select id="popup-size" name="popup_size">
								<option value="10%" <?php selected( $popup_size, '10%' ); ?>>10%</option>
								<option value="20%" <?php selected( $popup_size, '20%' ); ?>>20%</option>
								<option value="30%" <?php selected( $popup_size, '30%' ); ?>>30%</option>
								<option value="40%" <?php selected( $popup_size, '40%' ); ?>>40%</option>
								<option value="50%" <?php selected( $popup_size, '50%' ); ?>>50%</option>
								<option value="60%" <?php selected( $popup_size, '60%' ); ?>>60%</option>
								<option value="70%" <?php selected( $popup_size, '70%' ); ?>>70%</option>
								<option value="80%" <?php selected( $popup_size, '80%' ); ?>>80%</option>
								<option value="90%" <?php selected( $popup_size, '90%' ); ?>>90%</option>
								<option value="100%" <?php selected( $popup_size, '100%' ); ?>>100%</option>
								<option value="custom" <?php selected( $popup_size, 'custom' ); ?>><?php _e( 'Custom', 'ocean-ecommerce' ); ?></option>
							</select>
						</div>
					</div>

					<div class="custom-size-fields field-container" style="display: none;">
						<div class="oceanwp-mb-desc">
							<label for="popup-width"><?php esc_html_e( 'Width:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
						<input type="text" id="popup-width" name="popup_width" value="<?php echo esc_attr( $popup_width ); ?>" />
						</div>
					</div>

					<div class="custom-size-fields field-container" style="display: none;">
						<div class="oceanwp-mb-desc">
							<label for="popup-height"><?php esc_html_e( 'Height:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<input type="text" id="popup-height" name="popup_height" value="<?php echo esc_attr( $popup_height ); ?>" />
						</div>
					</div>

					<div class="field-container has-checkbox-color">
						<div class="oceanwp-mb-desc">
							<label for="popup-overlay-enabled"><?php esc_html_e( 'Enable Overlay:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<input type="checkbox" id="popup-overlay-enabled" name="popup_overlay_enabled" <?php checked( $popup_overlay_enabled, '1' ); ?> value="1">
						</div>
					</div>

					<div class="field-container has-checkbox-color" id="overlay-color-container">
						<div class="oceanwp-mb-desc">
							<label for="popup-overlay-color"><?php esc_html_e( 'Overlay Color:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<input type="text" id="popup-overlay-color" class="color-picker-alpha-field" data-alpha-enabled="true" name="popup_overlay_color" value="<?php echo esc_attr( $popup_overlay_color ); ?>">
						</div>
					</div>


					<div class="field-container">
						<div class="oceanwp-mb-desc">
							<label for="popup-position"><?php esc_html_e( 'Select Position:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<select id="popup-position" name="popup_position">
								<option value="center" <?php selected( $popup_position, 'center' ); ?>>
									<?php esc_html_e( 'Center', 'ocean-ecommerce' ); ?>
								</option>
								<option value="left-center" <?php selected( $popup_position, 'left-center' ); ?>>
									<?php esc_html_e( 'Left Center', 'ocean-ecommerce' ); ?>
								</option>
								<option value="right-center" <?php selected( $popup_position, 'right-center' ); ?>>
									<?php esc_html_e( 'Right Center', 'ocean-ecommerce' ); ?>
								</option>
								<option value="top-left" <?php selected( $popup_position, 'top-left' ); ?>>
									<?php esc_html_e( 'Top Left', 'ocean-ecommerce' ); ?>
								</option>
								<option value="top-center" <?php selected( $popup_position, 'top-center' ); ?>>
									<?php esc_html_e( 'Top Center', 'ocean-ecommerce' ); ?>
								</option>
								<option value="top-right" <?php selected( $popup_position, 'top-right' ); ?>>
									<?php esc_html_e( 'Top Right', 'ocean-ecommerce' ); ?>
								</option>
								<option value="bottom-left" <?php selected( $popup_position, 'bottom-left' ); ?>>
									<?php esc_html_e( 'Bottom Left', 'ocean-ecommerce' ); ?>
								</option>
								<option value="bottom-center" <?php selected( $popup_position, 'bottom-center' ); ?>>
									<?php esc_html_e( 'Bottom Center', 'ocean-ecommerce' ); ?>
								</option>
								<option value="bottom-right" <?php selected( $popup_position, 'bottom-right' ); ?>>
									<?php esc_html_e( 'Bottom Right', 'ocean-ecommerce' ); ?>
								</option>
							</select>
						</div>
					</div>



				</div>

				<div id="popup-advanced-settings" class="vertical-tab-panel">

					<div class="field-container">
						<div class="oceanwp-mb-desc">
							<label for="popup-close-button"><?php esc_html_e( 'Close Button:', 'ocean-ecommerce' ); ?></label>
						</div>
						<div class="oceanwp-mb-field">
							<select id="popup-close-button" name="popup_close_button">
								<option value="close" <?php selected( $popup_close_button, 'close' ); ?>><?php esc_html_e( 'Close Button', 'ocean-ecommerce' ); ?></option>
								<option value="escape" <?php selected( $popup_close_button, 'escape' ); ?>><?php esc_html_e( 'Escape', 'ocean-ecommerce' ); ?></option>
							</select>
						</div>
					</div>

					<div class="field-container has-checkbox-color">
						<div class="oceanwp-mb-desc">
							<label for="disable-mobile"><?php esc_html_e( 'Disable on Mobile:', 'ocean-ecommerce' ); ?></label>
							<p class="description"><?php esc_html_e( 'Check this box to disable the popup on mobile devices.', 'ocean-ecommerce' ); ?></p>
						</div>
						<div class="oceanwp-mb-field">
							<input type="checkbox" id="disable-mobile" name="disable_mobile" value="1" <?php checked( $disable_mobile, '1' ); ?>>
						</div>
					</div>

				</div>
			</div>
		</div>
		<?php
		wp_nonce_field( 'save_popup_meta_box', 'popup_meta_box_nonce' );
	}

	/**
	 * Add styles to the admin head
	 *
	 *  @return void  */
	public static function oec_gutenwidth_admin_styles() {
		global $post;
		if ( $post && $post->post_type === 'opb-popup' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			$popup_size   = get_post_meta( $post->ID, 'popup_size', true );
			$popup_width  = get_post_meta( $post->ID, 'popup_width', true );
			$popup_height = get_post_meta( $post->ID, 'popup_height', true );
			?>
			<script>
			(function($) {
				$(document).ready(function() {
					jQuery(document).ready(function($){
						$('.color-picker-field').wpColorPicker();
					});
					// Handles the visibility of custom size fields
					function handleCustomSizeFields() {
						var popupSize = $('#popup-size').val();
						$('.custom-size-fields').toggle(popupSize === 'custom');
					}

					// Updates Gutenberg editor width based on popup size
					function updateGutenbergWidth() {
						var popupSize = $('#popup-size').val();
						var popupWidth = $('#popup-width').val();  // Get custom width
						var popupHeight = $('#popup-height').val(); // Get custom height

						// Convert popupHeight to integer for the computation
						var popupHeightValue = parseInt(popupHeight, 10);
						var flexBasisValue = 1.37 * popupHeightValue; // Calculate flex-basis as x1.37 of popupHeight


						// Determine the max size for Gutenberg blocks
						var maxSize = popupSize === 'custom' ? popupWidth + 'px' : popupSize;

						// CSS for Gutenberg blocks
						var css = `
							/* Main column, wide blocks and full-wide blocks width */
							.is-root-container {
								max-width: ${maxSize};
								max-height: ${popupSize === 'custom' ? popupHeight + 'px' : popupSize};
								//display: flex;
								//justify-content: center;
								margin: 0 auto !important;
								z-index: 0;
							}
							.edit-post-visual-editor {
								flex-basis: ${flexBasisValue}px !important;
							}
							:where(.wp-block-group.has-background) {
								padding: 0;
							}

							html :where(.wp-block) {
								margin-bottom: 0;
								margin-top: 0;
							}
							html.wp-toolbar.interface-interface-skeleton__html-container {
								font-size: 62.5%;
							}
						`;

						// Add CSS to the DOM
						var styleTagId = 'popup-gutenberg-width-css';
						var styleTag = document.getElementById(styleTagId);

						if (styleTag) {
							styleTag.parentNode.removeChild(styleTag);
						}

						var styleElement = document.createElement('style');
						styleElement.id = styleTagId;
						styleElement.innerHTML = css;
						document.head.appendChild(styleElement);
					}

					// Initial function calls
					updateGutenbergWidth();
					handleCustomSizeFields();

					// Event listeners
					$('#popup-size').on('change', function() {
						updateGutenbergWidth();
						handleCustomSizeFields();
					});

					$('#popup-width, #popup-height').on('input change', function() {
						updateGutenbergWidth();
					});
				});
			})(jQuery);
			</script>
			<?php
		}
	}


	/**
	 * Saves the meta values for the Popup custom post type.
	 *
	 * @param int $post_id The post ID being saved.
	 */
	public static function save_popup_meta( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( empty( $_POST ) || ( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] === 'elementor_ajax' ) ) {
			return;
		}

		if ( ! isset( $_POST['popup_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['popup_meta_box_nonce'], 'save_popup_meta_box' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( empty( $_POST ) || ( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] === 'elementor_ajax' ) ) {
			return;
		}

		if ( isset( $_POST['popup_delay'] ) ) {
			$popup_delay = absint( $_POST['popup_delay'] );
			update_post_meta( $post_id, 'popup_delay', $popup_delay );
		}

		if ( isset( $_POST['popup_inactivity_time'] ) ) {
			$popup_inactivity_time = absint( $_POST['popup_inactivity_time'] );
			update_post_meta( $post_id, 'popup_inactivity_time', $popup_inactivity_time );
		}

		if ( isset( $_POST['popup_autoclose_delay'] ) ) {
			$popup_autoclose_delay = absint( $_POST['popup_autoclose_delay'] );
			update_post_meta( $post_id, 'popup_autoclose_delay', $popup_autoclose_delay );
		}

		if ( isset( $_POST['popup_trigger'] ) ) {
			$popup_trigger = sanitize_text_field( wp_unslash( $_POST['popup_trigger'] ) );
			update_post_meta( $post_id, 'popup_trigger', $popup_trigger );
		}

		if ( isset( $_POST['popup_display_mode'] ) ) {
			$popup_display_mode = sanitize_text_field( wp_unslash( $_POST['popup_display_mode'] ) );
			update_post_meta( $post_id, 'popup_display_mode', $popup_display_mode );
		}

		if ( isset( $_POST['popup_size'] ) ) {
			$popup_size = sanitize_text_field( wp_unslash( $_POST['popup_size'] ) );
			update_post_meta( $post_id, 'popup_size', $popup_size );
		}

		if ( isset( $_POST['popup_width'] ) ) {
			$popup_width = sanitize_text_field( wp_unslash( $_POST['popup_width'] ) );
			update_post_meta( $post_id, 'popup_width', $popup_width );
		}

		if ( isset( $_POST['popup_height'] ) ) {
			$popup_height = sanitize_text_field( wp_unslash( $_POST['popup_height'] ) );
			update_post_meta( $post_id, 'popup_height', $popup_height );
		}

		if ( isset( $_POST['popup_position'] ) ) {
			$popup_position = sanitize_text_field( wp_unslash( $_POST['popup_position'] ) );
			update_post_meta( $post_id, 'popup_position', $popup_position );
		}

		if ( isset( $_POST['popup_close_button'] ) ) {
			$popup_close_button = sanitize_text_field( wp_unslash( $_POST['popup_close_button'] ) );
			update_post_meta( $post_id, 'popup_close_button', $popup_close_button );
		}

		if ( isset( $_POST['popup_inactivity_time'] ) ) {
			$popup_inactivity_time = absint( $_POST['popup_inactivity_time'] );
			update_post_meta( $post_id, 'popup_inactivity_time', $popup_inactivity_time );
		}

		if ( isset( $_POST['disable_mobile'] ) ) {
			$disable_mobile = '1';
			update_post_meta( $post_id, 'disable_mobile', $disable_mobile );
		} else {
			$disable_mobile = '';
			delete_post_meta( $post_id, 'disable_mobile' );
		}

		if ( isset( $_POST['popup_pages'] ) ) {
			$selected_pages = array_map( 'absint', $_POST['popup_pages'] );
			$selected_pages = array_unique( $selected_pages );
			update_post_meta( $post_id, 'popup_pages', $selected_pages );
		} else {
			$selected_pages = '';
			delete_post_meta( $post_id, 'popup_pages' );
		}

		if ( isset( $_POST['popup_exclude_pages'] ) ) {
			$exclude_pages = array_map( 'absint', (array) $_POST['popup_exclude_pages'] );
			$exclude_pages = array_unique( $exclude_pages );
			update_post_meta( $post_id, 'popup_exclude_pages', $exclude_pages );
		} else {
			delete_post_meta( $post_id, 'popup_exclude_pages' );
		}

		if ( isset( $_POST['popup_overlay_enabled'] ) ) {
			$popup_overlay_enabled = '1';
			update_post_meta( $post_id, 'popup_overlay_enabled', $popup_overlay_enabled );
		} else {
			$popup_overlay_enabled = '';
			delete_post_meta( $post_id, 'popup_overlay_enabled' );
		}

		if ( isset( $_POST['popup_show_on_whole_site'] ) ) {
			$popup_show_on_whole_site = '1';
			update_post_meta( $post_id, 'popup_show_on_whole_site', $popup_show_on_whole_site );
		} else {
			$popup_show_on_whole_site = '';
			delete_post_meta( $post_id, 'popup_show_on_whole_site' );
		}

		if ( isset( $_POST['popup_overlay_color'] ) ) {
			update_post_meta( $post_id, 'popup_overlay_color', sanitize_text_field( wp_unslash( $_POST['popup_overlay_color'] ) ) );
		}

		// Retrieve the page settings manager.
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
			// Retrieve the settings model for the current page.
			$page_settings_model = $page_settings_manager->get_model( $post_id );
			$elementor_settings  = $page_settings_model->get_settings();
			if ( ! empty( $elementor_settings ) ) {
				if ( isset( $popup_delay ) ) {
					$elementor_settings['_el_popup_delay'] = $popup_delay;
				}
				if ( isset( $popup_inactivity_time ) ) {
					$elementor_settings['_el_popup_inactivity_time'] = $popup_inactivity_time;
				}
				if ( isset( $popup_autoclose_delay ) ) {
					$elementor_settings['_el_popup_autoclose_delay'] = $popup_autoclose_delay;
				}
				if ( isset( $selected_pages ) ) {
					$elementor_settings['_el_popup_pages'] = $selected_pages;
				}
				if ( isset( $exclude_pages ) ) {
					$elementor_settings['_el_exclude_pages'] = $exclude_pages;
				}
				if ( isset( $popup_trigger ) ) {
					$elementor_settings['_el_popup_trigger'] = $popup_trigger;
				}
				if ( isset( $popup_size ) ) {
					$elementor_settings['_el_popup_size'] = $popup_size;
				}
				if ( isset( $popup_width ) ) {
					$elementor_settings['_el_popup_width'] = $popup_width;
				}
				if ( isset( $popup_height ) ) {
					$elementor_settings['_el_popup_height'] = $popup_height;
				}
				if ( isset( $popup_position ) ) {
					$elementor_settings['_el_popup_position'] = $popup_position;
				}
				if ( isset( $popup_close_button ) ) {
					$elementor_settings['_el_popup_close_button'] = $popup_close_button;
				}
				if ( isset( $disable_mobile ) ) {
					$elementor_settings['_el_disable_mobile'] = $disable_mobile;
				}
				if ( isset( $popup_overlay_enabled ) ) {
					$elementor_settings['_el_popup_overlay_enabled'] = $popup_overlay_enabled;
				}
				if ( isset( $popup_show_on_whole_site ) ) {
					$elementor_settings['_el_popup_show_on_whole_site'] = $popup_show_on_whole_site;
				}
				if ( isset( $popup_overlay_color ) ) {
					$elementor_settings['_el_popup_overlay_color'] = $popup_overlay_color;
				}
				$page_settings_manager->save_settings( $elementor_settings, $post_id );
			}
			delete_transient( 'ocean_popup_builder_core_style_' . $post_id );
		}
	}

	public static function enqueue_popup_assets() {
		$screen = get_current_screen();

		if ( 'opb-popup' === $screen->post_type && 'post' === $screen->base ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker-alpha', OPD_URL . 'modules/popup-builder/assets/js/admin/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), OPD_VERSION, true );
			wp_enqueue_style( 'popup-style', OPD_URL . 'modules/popup-builder/assets/css/admin/metaboxes/admin-metaboxes.css' );
			wp_enqueue_script( 'popup-script', OPD_URL . 'modules/popup-builder/assets/js/admin/metaboxes/admin-metaboxes.js', array( 'jquery' ), OPD_VERSION, true );
		}
	}
}

// Register the Popup_Meta_Boxes class
OPB_Popup_Meta_Boxes::register();
