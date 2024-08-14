<?php

class Ocean_Taxonomy_Creator {

	/**
	 * Constructor for the class.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'init', array( $this, 'save_taxonomy' ), 9 );
		add_action( 'init', array( $this, 'register_custom_taxonomies' ) );
	}

	/**
	 * Creates a plugin settings page in the WordPress admin.
	 */
	public function add_plugin_page() {
		add_submenu_page(
			'create_cpt',
			__( 'Taxonomy Create', 'ocean-cpt' ),
			__( 'Taxonomy Create', 'ocean-cpt' ),
			'manage_options',
			'create_taxonomy',
			array( $this, 'create_taxonomy_page_content' )
		);
	}

	public function save_taxonomy() {
		if (
			isset( $_POST['update_settings'] ) && $_POST['update_settings_branch'] === 'taxonomy' && current_user_can( 'manage_options' ) && check_admin_referer( 'taxonomy_update_settings' ) ) {
			$new_options = Ocean_Taxonomy_Helper::sanitize_settings( $_POST );
			// Retrieve existing Taxonomy configurations.
			$existing_taxonomies = get_option( 'custom_taxonomy_manager_options', array() );

			// Define a unique identifier for the new Taxonomy.
			$taxonomy_id = sanitize_title( $new_options['custom_taxonomy_slug'] );

			// Prevent overwriting an existing Taxonomy with the same slug.
			if ( isset( $existing_taxonomies[ $taxonomy_id ] ) ) {
				add_action( 'admin_notices', array( $this, 'error_notice' ) );
				return;
			}

			// Add the new Taxonomy to the array of existing Taxonomies.
			$existing_taxonomies[ $taxonomy_id ] = $new_options;

			// Save the updated array of Taxonomies.
			update_option( 'custom_taxonomy_manager_options', $existing_taxonomies );

			// Optional: Add a message to show that options have been saved.
			add_action( 'admin_notices', array( $this, 'success_notice' ) );
		}
	}

	public function success_notice() {
		echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__( 'Taxonomy created successfully.', 'ocean-cpt' ) . '</p></div>';
	}

	public function error_notice() {
		echo '<div id="message" class="error notice is-dismissible"><p>' . esc_html__( 'A taxonomy with this slug already exists. Please choose a different slug.', 'ocean-cpt' ) . '</p></div>';
	}

	/**
	 * Renders the content of the plugin settings page.
	 */
	public function create_taxonomy_page_content() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'ocean-cpt' ) );
		}

		// Render the settings page.
		?>
			<div class="wrap" id="ocean-taxonomy">
				<h2 class="page-title"><?php echo esc_html__( 'Custom Taxonomy Creator', 'ocean-cpt' ); ?></h2>
				<form method="post" action="">
					<div class="postbox-container">
						<div id="poststuff">
						<?php
						wp_nonce_field( 'taxonomy_update_settings' );
						// Render form fields from Ocean_Taxonomy_Helper.
						Ocean_Taxonomy_Helper::render_form_fields( array() );
						?>
							<input type="hidden" name="update_settings_branch" value="taxonomy"/>
							<input type="submit" name="update_settings" value="<?php esc_html_e( 'Create Taxonomy', 'ocean-cpt' ); ?>" class="button-primary"/>
						</div>
					</div>
				</form>
			</div>
			<?php
	}

	/**
	 * Registers custom taxonomies.
	 */
	public function register_custom_taxonomies() {
		$taxonomies      = get_option( 'custom_taxonomy_manager_options', array() );
		$taxonomy_config = Ocean_Taxonomy_Helper::get_taxonomy_config();

		foreach ( $taxonomies as $taxonomy_id => $options ) {
			if ( is_array( $options ) && ! empty( $options['custom_taxonomy_slug'] ) && ! taxonomy_exists( $options['custom_taxonomy_slug'] ) ) {
				$taxonomy = sanitize_title( $options['custom_taxonomy_slug'] );
				$labels   = array();

				// Basic Settings Labels.
				$labels['name']          = $options['plural_label'] ?? 'Custom Taxonomies';
				$labels['singular_name'] = $options['singular_label'] ?? 'Custom Taxonomy';

				// Additional Labels from config.
				foreach ( $taxonomy_config['additional_labels']['fields'] as $field ) {
					$key            = strtolower( $field['slug'] );
					$labels[ $key ] = $options[ $field['slug'] ] ?? $field['default'];
				}

				// Default args with overridable options.
				$args = array(
					'labels'                => $labels,
					'public'                => $options['public'] ?? true,
					'publicly_queryable'    => $options['publicly_queryable'] ?? true,
					'hierarchical'          => $options['hierarchical'] ?? false,
					'show_ui'               => $options['show_ui'] ?? true,
					'show_in_menu'          => $options['show_in_menu'] ?? true,
					'show_in_nav_menus'     => $options['show_in_nav_menus'] ?? true,
					'query_var'             => $options['query_var'] ?? $taxonomy,
					'rewrite'               => array(
						'slug'         => $options['rewrite_slug'] ?? $taxonomy,
						'with_front'   => $options['rewrite_with_front'] ?? true,
						'hierarchical' => $options['rewrite_hierarchical'] ?? false,
					),
					'show_admin_column'     => $options['show_admin_column'] ?? false,
					'show_in_rest'          => $options['show_in_rest'] ?? false,
					'rest_base'             => $options['rest_base'] ?? $taxonomy,
					'show_tagcloud'         => $options['show_tagcloud'] ?? true,
					'rest_controller_class' => $options['rest_controller_class'] ?? 'WP_REST_Terms_Controller',
					'sort'                  => $options['sort'] ?? null,
					'meta_box_cb'           => $options['meta_box_cb'] ?? null,
					'show_in_quick_edit'    => $options['show_in_quick_edit'] ?? true,
					'capabilities'          => $options['capabilities'] ?? array(),
					'meta_box_cb'           => $options['meta_box_cb'] ?? null,
					'default_term'          => $options['default_term'] ?? null,
				);

				// Register the post type.
				register_taxonomy( $taxonomy, isset( $options['object_type'] ) ? $options['object_type'] : null, $args );

			}
		}
	}

	/**
	 * Add custom taxonomies to the OceanWP metabox support.
	 *
	 * @param array $taxonomies Existing post types from the filter.
	 * @return array Updated array of post types.
	 */
	public function add_custom_taxonomies_to_oe_metabox( $taxonomies ) {
		// Retrieve custom taxonomies from saved options.
		$saved_taxonomies  = get_option( 'custom_taxonomy_manager_options', array() );
		$custom_taxonomies = array();
		foreach ( $saved_taxonomies as $taxonomy_id => $taxonomy_options ) {
			if ( ! empty( $taxonomy_options['custom_taxonomy_slug'] ) ) {
				$custom_taxonomies[] = sanitize_title( $taxonomy_options['custom_taxonomy_slug'] );
			}
		}

		// Merge with existing post types.
		$taxonomies = array_unique( array_merge( $taxonomies, $custom_taxonomies ) );
		return $taxonomies;
	}
}

// Initialize the classes
new Ocean_Taxonomy_Creator();
