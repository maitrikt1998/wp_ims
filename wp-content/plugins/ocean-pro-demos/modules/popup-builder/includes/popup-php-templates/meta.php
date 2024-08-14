<?php
/**
 * Class OPB_Meta_Fields
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class OPB_Meta_Fields {


	private static $_instance = null;


	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_custom_fields' ) );
		add_action( 'init', array( $this, 'register_custom_meta' ) );
	}

	/**
	 * Add custom fields metabox to the OEC Badge post type.
	 */
	public function add_custom_fields() {
		add_meta_box(
			'opb_popup_container_meta_box',
			__( 'Popup Preview', 'ocean-ecommerce' ),
			array( $this, 'render_badge_custom_fields' ),
			'opb-popup-php',
			'normal',
			'default'
		);
	}

	/**
	 * Renders custom fields for badge in the popup editor.
	 *
	 * This function outputs a container div element with an ID of "popup-previews-container",
	 * which can be used to append or render badge previews dynamically via JavaScript.
	 *
	 * @param WP_Post $post The current post object being edited.
	 */
	public function render_badge_custom_fields( $post ) {
		?>
		<div id="popup-previews-container"></div>
		<?php
	}

	/**
	 * Registers custom meta fields for the custom post type.
	 *
	 * This function is used to register custom post metadata that can be used
	 * to store additional information about posts of the 'opb-popup-php' type.
	 */
	public function register_custom_meta() {

		register_post_meta(
			'opb-popup-php',
			'opb_title_typography_meta',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_content_typography_meta',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_button_typography_meta',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_show_on_whole_site_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'boolean',
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_selected_page_meta',
			array(
				'show_in_rest'      => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'integer',
						),
					),
				),
				'type'              => 'array',
				'single'            => true,
				'sanitize_callback' => 'sanitize_selected_pages_array',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_exclude_pages_meta',
			array(
				'show_in_rest'      => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'integer',
						),
					),
				),
				'type'              => 'array',
				'single'            => true,
				'sanitize_callback' => 'sanitize_selected_pages_array',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_selected_popup_type_meta',
			array(
				'show_in_rest'      => true,
				'type'              => 'string',
				'single'            => true,
				'default'           => 'popup-1',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_button_color_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_button_hover_color_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_title_color_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_content_color_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_button_text_color_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_overlay_color_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_background_color_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_button_text_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'default'           => 'Button Text',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_heading_text_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'default'           => 'Popup Title',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_image',
			array(
				'show_in_rest'      => true,
				'type'              => 'integer',
				'single'            => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_meta(
			'post',
			'opb_popup_content',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'default'       => 'Popup Content',
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_inactivity_time_meta',
			array(
				'show_in_rest'      => true,
				'type'              => 'number',
				'default'           => 0,
				'single'            => true,
				'sanitize_callback' => 'sanitize_meta_number',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_delay_meta',
			array(
				'show_in_rest'      => true,
				'type'              => 'number',
				'default'           => 0,
				'single'            => true,
				'sanitize_callback' => 'sanitize_meta_number',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_autoclose_delay_meta',
			array(
				'show_in_rest'      => true,
				'type'              => 'number',
				'default'           => 0,
				'single'            => true,
				'sanitize_callback' => 'sanitize_meta_number',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_trigger_meta',
			array(
				'show_in_rest'      => true,
				'type'              => 'string',
				'single'            => true,
				'default'           => 'load',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_display_mode_meta',
			array(
				'show_in_rest'      => true,
				'type'              => 'string',
				'single'            => true,
				'default'           => 'every_time',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_position_meta',
			array(
				'show_in_rest'      => true,
				'type'              => 'string',
				'single'            => true,
				'default'           => 'center',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_overlay_enabled_meta',
			array(
				'show_in_rest' => true,
				'type'         => 'boolean',
				'single'       => true,
				'default'      => false,
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_close_button_meta',
			array(
				'show_in_rest' => true,
				'type'         => 'string',
				'single'       => true,
				'default'      => 'close',
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_disable_mobile_meta',
			array(
				'show_in_rest' => true,
				'type'         => 'boolean',
				'single'       => true,
				'default'      => false,
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_button_border_width_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_button_border_color_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_button_border_style_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_border_width_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_popup_border_color_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_popup_border_style_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);

		register_post_meta(
			'opb-popup-php',
			'opb_content_align_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_title_align_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_button_align_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_image_align_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_popup_border_radius_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_image_border_radius_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_button_border_radius_meta',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);
		register_post_meta(
			'opb-popup-php',
			'opb_button_link_meta',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'default'           => 'https://example.com',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);

	}

}


OPB_Meta_Fields::instance();
