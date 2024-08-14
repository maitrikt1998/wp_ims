<?php

function enqueue_gutenberg_sidebar_script() {
	global $post;

	if ( 'opb-popup-php' !== get_post_type( $post ) ) {
		return;
	}

	wp_enqueue_script(
		'popup-php-sidebar',
		OPD_URL . 'modules/popup-builder/includes/popup-php-templates/assets/js/editor/editor.js',
		array( 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-element', 'wp-api-fetch', 'wp-compose', 'wp-blocks', 'wp-editor' ),
		'1',
		true
	);

	// Get the list of pages.
	$args  = array(
		'post_type'      => 'page',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids', // This will only get IDs to make it faster.
	);
	$pages = get_posts( $args );

	$formatted_pages = array();
	foreach ( $pages as $page_id ) {
		$formatted_pages[] = array(
			'id'   => $page_id,
			'text' => get_the_title( $page_id ),
		);
	}

	// Localize script.
	wp_localize_script( 'popup-php-sidebar', 'localizedPages', $formatted_pages );
}
add_action( 'enqueue_block_editor_assets', 'enqueue_gutenberg_sidebar_script' );


function enqueue_my_react_script() {
	$popup_ids = Popup_PHP_Frontend()->get_available_popup_ids();
	if ( ! empty( $popup_ids ) ) {
		$popup_id = reset( $popup_ids );
	} else {
		return;
	}

	wp_enqueue_script( 'popup-php-frontend', OPD_URL . 'modules/popup-builder/includes/popup-php-templates/assets/js/frontend/frontend.js', array( 'wp-element' ), OPD_VERSION, true );

	$popup_data_array = array();

	foreach ( $popup_ids as $popup_id ) {
		$popup_type        = get_post_meta( $popup_id, 'opb_selected_popup_type_meta', true );
		$button_text       = get_post_meta( $popup_id, 'opb_button_text_meta', true );
		$button_link       = get_post_meta( $popup_id, 'opb_button_link_meta', true );
		$popup_image_id    = get_post_meta( $popup_id, 'opb_popup_image', true );
		$popup_image       = wp_get_attachment_url( $popup_image_id );
		$popup_content     = get_post_meta( $popup_id, 'opb_popup_content', true );
		$heading_text_meta = get_post_meta( $popup_id, 'opb_heading_text_meta', true );
		$processed_content = do_shortcode( $popup_content );
		// colors.
		$background_color_meta  = get_post_meta( $popup_id, 'opb_background_color_meta', true );
		$button_color           = get_post_meta( $popup_id, 'opb_button_color_meta', true );
		$button_hover_color     = get_post_meta( $popup_id, 'opb_button_hover_color_meta', true );
		$title_color_meta       = get_post_meta( $popup_id, 'opb_title_color_meta', true );
		$content_color_meta     = get_post_meta( $popup_id, 'opb_content_color_meta', true );
		$button_text_color_meta = get_post_meta( $popup_id, 'opb_button_text_color_meta', true );

		$button_border_width_meta = get_post_meta( $popup_id, 'opb_button_border_width_meta', true );
		$button_border_color_meta = get_post_meta( $popup_id, 'opb_button_border_color_meta', true );
		$button_border_style_meta = get_post_meta( $popup_id, 'opb_button_border_style_meta', true );

		$popup_border_width_meta = get_post_meta( $popup_id, 'opb_popup_border_width_meta', true );
		$popup_border_color_meta = get_post_meta( $popup_id, 'opb_popup_border_color_meta', true );
		$popup_border_style_meta = get_post_meta( $popup_id, 'opb_popup_border_style_meta', true );

		$title_align_meta   = get_post_meta( $popup_id, 'opb_title_align_meta', true );
		$content_align_meta = get_post_meta( $popup_id, 'opb_content_align_meta', true );
		$button_align_meta  = get_post_meta( $popup_id, 'opb_button_align_meta', true );
		$image_align_meta   = get_post_meta( $popup_id, 'opb_image_align_meta', true );

		$popup_border_radius_meta_json = get_post_meta( $popup_id, 'opb_popup_border_radius_meta', true );
		$popup_border_radius_meta      = $popup_border_radius_meta_json ? json_decode( $popup_border_radius_meta_json, true ) : array(
			'top'    => '0px',
			'right'  => '0px',
			'bottom' => '0px',
			'left'   => '0px',
		);

		$image_border_radius_meta_json = get_post_meta( $popup_id, 'opb_image_border_radius_meta', true );
		$image_border_radius_meta      = $image_border_radius_meta_json ? json_decode( $image_border_radius_meta_json, true ) : array(
			'top'    => '0px',
			'right'  => '0px',
			'bottom' => '0px',
			'left'   => '0px',
		);

		$button_border_radius_meta_json = get_post_meta( $popup_id, 'opb_button_border_radius_meta', true );
		$button_border_radius_meta      = $button_border_radius_meta_json ? json_decode( $button_border_radius_meta_json, true ) : array(
			'top'    => '0px',
			'right'  => '0px',
			'bottom' => '0px',
			'left'   => '0px',
		);

		$title_typography_meta_json = get_post_meta( $popup_id, 'opb_title_typography_meta', true );
		$title_typography_meta      = $title_typography_meta_json ? json_decode( $title_typography_meta_json, true ) : array();

		$content_typography_meta_json = get_post_meta( $popup_id, 'opb_content_typography_meta', true );
		$content_typography_meta      = $content_typography_meta_json ? json_decode( $content_typography_meta_json, true ) : array();

		$button_typography_meta_json = get_post_meta( $popup_id, 'opb_button_typography_meta', true );
		$button_typography_meta      = $button_typography_meta_json ? json_decode( $button_typography_meta_json, true ) : array();

		$popup_data_array[ $popup_id ] = array(
			'popupType'                 => $popup_type,
			'buttonColor'               => $button_color,
			'buttonText'                => $button_text,
			'buttonLink'                => $button_link,
			'imageUrl'                  => $popup_image,
			'content'                   => $processed_content,
			'popupBackgroundColor'      => $background_color_meta,
			'popupHeadingText'          => $heading_text_meta,
			'buttonHoverColor'          => $button_hover_color,
			'titleColor'                => $title_color_meta,
			'contentColor'              => $content_color_meta,
			'buttonTextColor'           => $button_text_color_meta,
			'buttonBorderWidth'         => $button_border_width_meta,
			'buttonBorderColor'         => $button_border_color_meta,
			'buttonBorderStyle'         => $button_border_style_meta,
			'popupBorderWidth'          => $popup_border_width_meta,
			'popupBorderColor'          => $popup_border_color_meta,
			'popupBorderStyle'          => $popup_border_style_meta,
			'titleAlign'                => $title_align_meta,
			'contentAlign'              => $content_align_meta,
			'buttonAlign'               => $button_align_meta,
			'imageAlign'                => $image_align_meta,
			'popupBorderRadius'         => $popup_border_radius_meta,
			'imageBorderRadius'         => $image_border_radius_meta,
			'buttonBorderRadius'        => $button_border_radius_meta,
			'titleTypographySettings'   => $title_typography_meta,
			'contentTypographySettings' => $content_typography_meta,
			'buttonTypographySettings'  => $button_typography_meta,
		);
	}
	wp_localize_script( 'popup-php-frontend', 'oceanPopupDataArray', $popup_data_array );
}
add_action( 'wp_enqueue_scripts', 'enqueue_my_react_script' );


function enqueue_custom_styles() {
	wp_enqueue_style( 'ocean-popup-styles', OPD_URL . 'modules/popup-builder/includes/popup-php-templates/assets/css/frontend/styles.css', array(), OPD_VERSION );
}
add_action( 'wp_enqueue_scripts', 'enqueue_custom_styles' );

function enqueue_admin_custom_styles() {
	global $post;
	if ( 'opb-popup-php' === get_post_type( $post ) ) {
		wp_enqueue_style( 'ocean-popup-styles', OPD_URL . 'modules/popup-builder/includes/popup-php-templates/assets/css/admin/admin-styles.css', array(), OPD_VERSION );
	}
}
add_action( 'enqueue_block_editor_assets', 'enqueue_admin_custom_styles' );


function hide_gutenberg_editor() {
	global $post;
	if ( 'opb-popup-php' === get_post_type( $post ) ) {
		echo '<style>.is-root-container {display: none;}</style>';
		echo '<style>.edit-post-visual-editor {max-height: 200px;}</style>';
	}
}
add_action( 'admin_head', 'hide_gutenberg_editor' );


function enqueue_select2_for_opb_popup_php_post_type() {
	global $post;

	if ( class_exists( 'WooCommerce' ) && is_object( $post ) && 'opb-popup-php' === get_post_type( $post ) ) {
		wp_enqueue_script( 'select2' );
		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );

	}
}
add_action( 'admin_enqueue_scripts', 'enqueue_select2_for_opb_popup_php_post_type' );



function sanitize_selected_pages_array( $data ) {
	if ( ! is_array( $data ) ) {
		return array();
	}

	// Flatten the array.
	$data = array_reduce(
		$data,
		function ( $carry, $item ) {
			return array_merge( $carry, (array) $item );
		},
		array()
	);

	return array_map( 'intval', $data );
}


function sanitize_meta_number( $number, $meta_key, $object_type ) {
	if ( ! is_numeric( $number ) ) {
		return 0; // return default value.
	}
	return intval( $number );
}


function shortcode_processor( WP_REST_Request $request ) {
	return do_shortcode( $request->get_param( 'content' ) );
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'shortcode-processor/v1',
			'/process',
			array(
				'methods'             => 'POST',
				'callback'            => 'shortcode_processor',
				'permission_callback' => '__return_true',
			)
		);
	}
);

function load_google_font_ajax() {

	if ( isset( $_POST['font_names'] ) && is_array( $_POST['font_names'] ) ) {
		$font_names = array_map( 'sanitize_text_field', $_POST['font_names'] );
	} else {
		wp_send_json_error( 'Invalid font names', 400 );
	}

	$font_url = 'https://fonts.googleapis.com/css?family=';

	foreach ( $font_names as $index => $font_name ) {
		if ( $index > 0 ) {
			$font_url .= '|';
		}
		$font_url .= urlencode( $font_name );
	}

	// Return the font URL.
	echo $font_url;
	wp_die();
}
add_action( 'wp_ajax_load_google_font', 'load_google_font_ajax' );
