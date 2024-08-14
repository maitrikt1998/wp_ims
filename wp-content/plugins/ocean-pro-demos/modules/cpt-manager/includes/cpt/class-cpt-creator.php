<?php


class Ocean_CPT_Creator {

	/**
	 * Constructor for the class.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'init_menu' ) );
		add_action( 'init', array( $this, 'register_custom_post_types' ) );
		add_action( 'init', array( $this, 'save_cpt' ), 9 );
		add_filter( 'ocean_main_metaboxes_post_types', array( $this, 'add_custom_post_types_to_oe_metabox' ) );
	}

	/**
	 * Creates a plugin settings page in the WordPress admin.
	 */
	public function init_menu() {
		add_menu_page(
			__( 'My O-CPT', 'ocean-cpt' ),
			__( 'My O-CPT', 'ocean-cpt' ),
			'manage_options',
			'create_cpt',
			array( $this, 'create_cpt_page_content' ),
			'dashicons-image-filter',
			5
		);

		add_submenu_page(
			'create_cpt',
			__( 'CPT Create', 'ocean-cpt' ),
			__( 'CPT Create', 'ocean-cpt' ),
			'manage_options',
			'create_cpt',
			array( $this, 'create_cpt_page_content' )
		);
	}

	public function save_cpt() {
		// Check if the user has posted some information and check nonce.
		if ( isset( $_POST['update_settings'] ) && $_POST['update_settings_branch'] === 'cpt' && check_admin_referer( 'cpt_update_settings' ) ) {
			$new_options = Ocean_CPT_Helper::sanitize_settings( $_POST );
			// Retrieve existing CPT configurations.
			$existing_cpts = get_option( 'cpt_manager_options', array() );

			// Define a unique identifier for the new CPT.
			$cpt_id = sanitize_title( $new_options['post_type_slug'] );

			// Prevent overwriting an existing CPT with the same slug.
			if ( isset( $existing_cpts[ $cpt_id ] ) ) {
				add_action( 'admin_notices', array( $this, 'error_notice' ) );
				return;
			}

			// Add the new CPT to the array of existing CPTs.
			$existing_cpts[ $cpt_id ] = $new_options;

			// Save the updated array of CPTs.
			update_option( 'cpt_manager_options', $existing_cpts );

			// Optional: Add a message to show that options have been saved.
			add_action( 'admin_notices', array( $this, 'success_notice' ) );

		}
	}

	public function success_notice() {
		echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__( 'CPT created successfully.', 'ocean-cpt' ) . '</p></div>';
	}

	public function error_notice() {
		echo '<div id="message" class="error notice is-dismissible"><p>' . esc_html__( 'A CPT with this slug already exists. Please choose a different slug.', 'ocean-cpt' ) . '</p></div>';
	}

	/**
	 * Renders the content of the plugin settings page.
	 */
	public function create_cpt_page_content() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'ocean-cpt' ) );
		}

		// Render the settings page.
		?>
			<div class="wrap" id="ocean-cpt">
				<h2 class="page-title"><?php echo esc_html__( 'Custom Post Type Creator', 'ocean-cpt' ); ?></h2>
				<form method="post" action="">
					<div class="postbox-container">
						<div id="poststuff">
						<?php
						wp_nonce_field( 'cpt_update_settings' );
						// Render form fields from Ocean_CPT_Helper.
						Ocean_CPT_Helper::render_form_fields( array() );
						?>
		
							<input type="hidden" name="update_settings_branch" value="cpt"/>
							<input type="submit" name="update_settings" value="<?php esc_html_e( 'Create CPT', 'ocean-cpt' ); ?>" class="button-primary"/>
						</div>
					</div>
				</form>
			</div>
			<?php
	}

	/**
	 * Registers custom post types.
	 */
	public function register_custom_post_types() {
		$cpts       = get_option( 'cpt_manager_options', array() );
		$cpt_config = Ocean_CPT_Helper::get_cpt_config();

		foreach ( $cpts as $cpt_id => $options ) {
			if ( is_array( $options ) && ! empty( $options['post_type_slug'] ) && ! post_type_exists( $options['post_type_slug'] ) ) {
				$post_type = sanitize_title( $options['post_type_slug'] );
				$labels    = array();

				// Basic Settings Labels.
				$labels['name']          = $options['plural_label'] ?? 'Custom Posts';
				$labels['singular_name'] = $options['singular_label'] ?? 'Custom Post';

				// Additional Labels from config.
				foreach ( $cpt_config['additional_labels']['fields'] as $field ) {
					$key            = strtolower( $field['slug'] );
					$labels[ $key ] = $options[ $field['slug'] ] ?? $field['default'];
				}

				// Check and Set Supports
				$default_supports = array( 'title', 'editor', 'thumbnail' ); 
				$supports         = isset( $options['supports'] ) && is_array( $options['supports'] ) ? $options['supports'] : $default_supports;

				// Default args with overridable options.
				$args               = array(
					'labels'                => $labels,
					'description'           => $options['description'] ?? '',
					'public'                => $options['public'] ?? true,
					'publicly_queryable'    => $options['publicly_queryable'] ?? true,
					'exclude_from_search'   => $options['exclude_from_search'] ?? false,
					'show_ui'               => $options['show_ui'] ?? true,
					'show_in_nav_menus'     => $options['show_in_nav_menus'] ?? true,
					'show_in_menu'          => $options['show_in_menu'] ?? true,
					'show_in_admin_bar'     => $options['show_in_admin_bar'] ?? true,
					'menu_position'         => $options['menu_position'] ?? 5,
					'menu_icon'             => $options['menu_icon'] ?? null,
					'capability_type'       => $options['capability_type'] ?? 'post',
					'capabilities'          => $options['capabilities'] ?? array(),
					'map_meta_cap'          => $options['map_meta_cap'] ?? true,
					'hierarchical'          => $options['hierarchical'] ?? false,
					'supports'              => $supports,
					'register_meta_box_cb'  => $options['register_meta_box_cb'] ?? '',
					'taxonomies'            => $options['taxonomies'] ?? array(),
					'has_archive'           => $options['has_archive'] ?? true,
					'rewrite'               => array(
						'slug'       => $options['rewrite_slug'] ?? $post_type,
						'with_front' => $options['rewrite_with_front'] ?? true,
						'feeds'      => $options['rewrite_feeds'] ?? true,
						'pages'      => $options['rewrite_pages'] ?? true,
					),
					'query_var'             => $options['query_var'] ?? true,
					'can_export'            => $options['can_export'] ?? true,
					'delete_with_user'      => $options['delete_with_user'] ?? null,
					'show_in_rest'          => $options['show_in_rest'] ?? true,
					'rest_base'             => $options['rest_base'] ?? $post_type,
					'rest_controller_class' => $options['rest_controller_class'] ?? 'WP_REST_Posts_Controller',
				);
				$args['taxonomies'] = isset( $options['taxonomies'] ) && is_array( $options['taxonomies'] ) ? $options['taxonomies'] : array();

				// Register the post type.
				register_post_type( $post_type, $args );

			}
		}
	}

	/**
	 * Add custom post types to the OceanWP metabox support.
	 *
	 * @param array $post_types Existing post types from the filter.
	 * @return array Updated array of post types.
	 */
	public function add_custom_post_types_to_oe_metabox( $post_types ) {
		// Retrieve custom post types from saved options.
		$saved_cpts        = get_option( 'cpt_manager_options', array() );
		$custom_post_types = array();
		foreach ( $saved_cpts as $cpt_id => $cpt_options ) {
			if ( ! empty( $cpt_options['post_type_slug'] ) ) {
				$custom_post_types[] = sanitize_title( $cpt_options['post_type_slug'] );
			}
		}

		// Merge with existing post types.
		$post_types = array_unique( array_merge( $post_types, $custom_post_types ) );
		return $post_types;
	}
}

new Ocean_CPT_Creator();
