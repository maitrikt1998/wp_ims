<?php

class Ocean_Taxonomy_Manager {

	/**
	 * Constructor for the class.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_management_page' ) );
	}

	/**
	 * Creates a plugin settings page in the WordPress admin.
	 */

	public function add_management_page() {
		add_submenu_page(
			'create_cpt',
			__( 'Taxonomy Manage', 'ocean-cpt' ),
			__( 'Taxonomy Manage', 'ocean-cpt' ),
			'manage_options',
			'manage_taxonomies',
			array( $this, 'manage_taxonomies_page_content' )
		);
	}

	/**
	 * Renders the content of the custom post type management page.
	 * Handles the creation, deletion, and updating of custom taxonomies.
	 */
	public function manage_taxonomies_page_content() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'ocean-cpt' ) );
		}

		// Retrieve existing Taxonomy configurations.
		$taxonomies = get_option( 'custom_taxonomy_manager_options', array() );

		// Handle deletion.
		if ( isset( $_POST['delete_taxonomy'] ) && check_admin_referer( 'manage_taxonomy_nonce_action', 'manage_taxonomy_nonce_field' ) ) {
			$taxonomy_id = sanitize_text_field( $_POST['selected_taxonomy'] );
			if ( isset( $taxonomies[ $taxonomy_id ] ) ) {
				unset( $taxonomies[ $taxonomy_id ] );
				update_option( 'custom_taxonomy_manager_options', $taxonomies );
				echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__( 'Custom Taxonomy deleted.', 'ocean-cpt' ) . '</p></div>';
				$redirect_url = esc_url_raw( $_SERVER['REQUEST_URI'] );
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}

		// Handle editing.
		if ( isset( $_POST['save_changes'] ) && check_admin_referer( 'manage_taxonomy_nonce_action', 'manage_taxonomy_nonce_field' ) ) {
			$taxonomy_id = sanitize_text_field( $_POST['selected_taxonomy'] );
			if ( isset( $taxonomies[ $taxonomy_id ] ) ) {
				// Iterate over the $_POST array and update the Taxonomy details.
				$post_inputs = Ocean_Taxonomy_Helper::sanitize_settings( $_POST );
				foreach ( $post_inputs as $key => $value ) {
					// Ensure we're only capturing relevant $_POST data.
					if ( in_array( $key, array( 'save_changes', 'selected_taxonomy', '_wpnonce', '_wp_http_referer' ), true ) ) {
						continue;
					}
					$taxonomies[ $taxonomy_id ][ $key ] = $value;
				}
				update_option( 'custom_taxonomy_manager_options', $taxonomies );
				echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__( 'Custom Taxonomy updated.', 'ocean-cpt' ) . '</p></div>';
			}
		}

		// Get the first Taxonomy's ID as a default value.
		$default_taxonomy_id = ! empty( $taxonomies ) ? key( $taxonomies ) : null;

		// Check if a specific Taxonomy is selected for editing, otherwise use the default.
		$taxonomy_id = isset( $_POST['selected_taxonomy'] ) && array_key_exists( $_POST['selected_taxonomy'], $taxonomies ) ? sanitize_text_field( $_POST['selected_taxonomy'] ) : $default_taxonomy_id;

		echo '<div class="wrap" id="ocean-taxonomy">';
		echo '<h2 class="page-title">' . esc_html__( 'Manage Custom Taxonomies', 'ocean-cpt' ) . '</h2>';

		if ( ! empty( $taxonomies ) ) {
			// Start form.
			echo '<form method="post" action="">';
			echo '<div class="postbox-container">';
			echo '<div id="poststuff">';

			// Create a nonce field for security.
			wp_nonce_field( 'manage_taxonomy_nonce_action', 'manage_taxonomy_nonce_field' );

			// Dropdown for selecting Taxonomy to edit.
			echo '<label class="taxonomy-select" for="selected_taxonomy">' . esc_html__( 'Select Taxonomy:', 'ocean-cpt' ) . '</label>';
			echo '<select name="selected_taxonomy" id="selected_taxonomy" onchange="this.form.submit()">';
			foreach ( $taxonomies as $id => $options ) {
				if ( is_array( $options ) && array_key_exists( 'custom_taxonomy_slug', $options ) ) {
					$selected      = ( $id === $taxonomy_id ) ? 'selected' : '';
					$taxonomy_name = $options['singular_label'] ?? $options['custom_taxonomy_slug'];
					echo "<option value='{$id}' {$selected}>" . esc_html( $taxonomy_name ) . '</option>';
				}
			}
			echo '</select>';

			// Delete button.
			submit_button( 'Delete Taxonomy', 'secondary', 'delete_taxonomy', false, array( 'id' => 'delete-taxonomy-button' ) );

			// Determine which CPT to show for editing.
			$taxonomy_id = isset( $_POST['selected_taxonomy'] ) ? sanitize_text_field( $_POST['selected_taxonomy'] ) : $default_taxonomy_id;

			// Always show the editing fields for the currently selected (or default) Taxonomy.
			if ( $taxonomy_id && isset( $taxonomies[ $taxonomy_id ] ) && is_array( $taxonomies[ $taxonomy_id ] ) && array_key_exists( 'custom_taxonomy_slug', $taxonomies[ $taxonomy_id ] ) ) {
				Ocean_Taxonomy_Helper::render_form_fields( $taxonomies[ $taxonomy_id ] );
				submit_button( __( 'Save Taxonomy', 'ocean-cpt' ), 'primary', 'save_changes' );
			} else {
				echo '<p>' . esc_html__( 'No Custom Taxonomy selected for editing.', 'ocean-cpt' ) . '</p>';
			}
			// End form.
			echo '</div>';
			echo '</div>';
			echo '</form>';

			echo '</div>';
		} else {
			echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__( 'No Custom Taxonomies registered yet.', 'ocean-cpt' ) . '</p></div>';
		}
	}
}

// Initialize the classes
new Ocean_Taxonomy_Manager();
