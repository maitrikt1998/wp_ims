<?php

class Ocean_CPT_Manager {

	/**
	 * Constructor for the Custom Post Type Manager.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_cpt_management_page' ) );
	}

	/**
	 * Creates a submenu page for managing custom post types.
	 */
	public function create_cpt_management_page() {
		add_submenu_page(
			'create_cpt',
			__( 'Manage Custom Post Types', 'ocean-cpt' ),
			__( 'CPT Manage', 'ocean-cpt' ),
			'manage_options',
			'manage_cpt',
			array( $this, 'cpt_management_page_content' )
		);
	}

	/**
	 * Renders the content of the custom post type management page.
	 * Handles the creation, deletion, and updating of custom post types.
	 */
	public function cpt_management_page_content() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'ocean-cpt' ) );
		}

		// Retrieve existing CPT configurations.
		$cpts = get_option( 'cpt_manager_options', array() );

		// Handle deletion.
		if ( isset( $_POST['delete_cpt'] ) && check_admin_referer( 'manage_cpt_nonce_action', 'manage_cpt_nonce_field' ) ) {
			$cpt_id = sanitize_text_field( $_POST['selected_cpt'] );
			if ( isset( $cpts[ $cpt_id ] ) ) {
				unset( $cpts[ $cpt_id ] );
				update_option( 'cpt_manager_options', $cpts );
				echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__( 'Custom Post Type deleted.', 'ocean-cpt' ) . '</p></div>';
				$redirect_url = esc_url_raw( $_SERVER['REQUEST_URI'] );
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}

		// Handle editing.
		if ( isset( $_POST['save_changes'] ) && check_admin_referer( 'manage_cpt_nonce_action', 'manage_cpt_nonce_field' ) ) {
			$cpt_id = sanitize_text_field( $_POST['selected_cpt'] );
			if ( isset( $cpts[ $cpt_id ] ) ) {
				// Iterate over the $_POST array and update the CPT details.
				$post_inputs = Ocean_CPT_Helper::sanitize_settings( $_POST );
				foreach ( $post_inputs as $key => $value ) {
					// Ensure we're only capturing relevant $_POST data.
					if ( in_array( $key, array( 'save_changes', 'selected_cpt', '_wpnonce', '_wp_http_referer' ), true ) ) {
						continue;
					}
					$cpts[ $cpt_id ][ $key ] = $value;
				}
				update_option( 'cpt_manager_options', $cpts );
				echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__( 'Custom Post Type updated.', 'ocean-cpt' ) . '</p></div>';
			}
		}

		// Get the first CPT's ID as a default value.
		$default_cpt_id = ! empty( $cpts ) ? key( $cpts ) : null;

		// Check if a specific CPT is selected for editing, otherwise use the default.
		$cpt_id = isset( $_POST['selected_cpt'] ) && array_key_exists( $_POST['selected_cpt'], $cpts ) ? sanitize_text_field( $_POST['selected_cpt'] ) : $default_cpt_id;

		echo '<div class="wrap" id="ocean-cpt">';
		echo '<h2 class="page-title">' . esc_html__( 'Manage Custom Post Types', 'ocean-cpt' ) . '</h2>';

		if ( ! empty( $cpts ) ) {
			// Start form.
			echo '<form method="post" action="">';
			echo '<div class="postbox-container">';
			echo '<div id="poststuff">';

			// Create a nonce field for security.
			wp_nonce_field( 'manage_cpt_nonce_action', 'manage_cpt_nonce_field' );

			// Dropdown for selecting CPT to edit.
			echo '<label class="cpt-select" for="selected_cpt">' . esc_html__( 'Select CPT:', 'ocean-cpt' ) . '</label>';
			echo '<select name="selected_cpt" id="selected_cpt" onchange="this.form.submit()">';
			foreach ( $cpts as $id => $options ) {
				if ( is_array( $options ) && array_key_exists( 'post_type_slug', $options ) ) {
					$selected = ( $id === $cpt_id ) ? 'selected' : '';
					$cpt_name = $options['singular_label'] ?? $options['post_type_slug'];
					echo "<option value='{$id}' {$selected}>" . esc_html( $cpt_name ) . '</option>';
				}
			}
			echo '</select>';

			// Delete button.

			submit_button('Delete Post Type', 'secondary', 'delete_cpt', false, array('id' => 'delete-cpt-button'));


			// Determine which CPT to show for editing.
			$cpt_id = isset( $_POST['selected_cpt'] ) ? sanitize_text_field( $_POST['selected_cpt'] ) : $default_cpt_id;

			// Always show the editing fields for the currently selected (or default) CPT.
			if ( $cpt_id && isset( $cpts[ $cpt_id ] ) && is_array( $cpts[ $cpt_id ] ) && array_key_exists( 'post_type_slug', $cpts[ $cpt_id ] ) ) {
				Ocean_CPT_Helper::render_form_fields( $cpts[ $cpt_id ] );
				submit_button( __( 'Save Post Type', 'ocean-cpt' ), 'primary', 'save_changes' );
			} else {
				echo '<p>' . esc_html__( 'No Custom Post Type selected for editing.', 'ocean-cpt' ) . '</p>';
			}
			// End form.
			echo '</div>';
			echo '</div>';
			echo '</form>';

			echo '</div>';
		} else {
			echo '<div id="message" class="updated notice is-dismissible"><p>' . esc_html__( 'No Custom Post Types registered yet.', 'ocean-cpt' ) . '</p></div>';
		}

	}
}

new Ocean_CPT_Manager();
