<?php


class Ocean_Taxonomy_Helper {

	/**
	 * Constructor for the Custom Taxonomy Helper.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts_and_styles() {
		$screen = get_current_screen();
		if ( 'my-o-cpt_page_create_taxonomy' === $screen->id || 'my-o-cpt_page_manage_taxonomies' === $screen->id ) {
			wp_enqueue_script( 'ocean-cpt-custom-label', OPD_URL . 'modules/cpt-manager/assets/js/ocean-cpt-manager-script.js', array( 'jquery' ), OPD_VERSION, true );
			wp_enqueue_style( 'ocean-cpt', OPD_URL . 'modules/cpt-manager/assets/css/ocean-cpt-manager.css', array(), OPD_VERSION );
			wp_localize_script(
				'ocean-cpt-custom-label',
				'oceanCPTLabels',
				array(
					'labelsToUpdate' => array(
						'all_items'                  => '#all_items',
						'view_item'                  => '#view_item',
						'add_new_item'               => '#add_new_item',
						'edit_item'                  => '#edit_item',
						'new_item'                   => '#new_item',
						'view_items'                 => '#view_items',
						'search_items'               => '#search_items',
						'parent_item'                => '#parent_item',
						'parent_item_colon'          => '#parent_item_colon',
						'insert_into_item'           => '#insert_into_item',
						'uploaded_to_this_item'      => '#uploaded_to_this_item',
						'filter_items_list'          => '#filter_items_list',
						'items_list'                 => '#items_list',
						'item_updated'               => '#item_updated',
						'item_scheduled'             => '#item_scheduled',
						'item_trashed'               => '#item_trashed',
						'item_reverted_to_draft'     => '#item_reverted_to_draft',
						'item_published_privately'   => '#item_published_privately',
						'item_published'             => '#item_published',
						'menu_name'                  => '#menu_name',
						'popular_items'              => '#popular_items',
						'update_item_name'           => '#update_item_name',
						'separate_items_with_commas' => '#separate_items_with_commas',
						'add_or_remove_items'        => '#add_or_remove_items',
						'items_list_navigation'      => '#items_list_navigation',
						'back_to_items'              => '#back_to_items',
						'new_item_name'              => '#new_item_name',
						'update_item'                => '#update_item',
						'filter_by_item'             => '#filter_by_item',
						'item_link'                  => '#item_link',
					),
				)
			);
		}
	}

	/**
	 * Retrieves the configuration for custom taxonomies.
	 *
	 * @return array Returns an array of configurations for custom taxonomies.
	 */
	public static function get_taxonomy_config() {
		$post_types        = get_post_types( array( 'public' => true ), 'objects' );
		$post_type_options = array();

		foreach ( $post_types as $post_type ) {
			// Exclude certain built-in post types if necessary
			if ( ! in_array( $post_type->name, array( 'attachment', 'revision', 'nav_menu_item' ) ) ) {
				$post_type_options[] = array(
					'value' => $post_type->name,
					'label' => $post_type->labels->singular_name,
				);
			}
		}

		return array(
			'basic_settings'    => array(
				'title'  => 'Basic Settings',
				'fields' => array(
					array(
						'slug'     => 'custom_taxonomy_slug',
						'label'    => 'Taxonomy Slug',
						'default'  => '',
						'type'     => 'text',
						'required' => true,
					),
					array(
						'slug'     => 'plural_label',
						'label'    => 'Plural Label',
						'default'  => '',
						'type'     => 'text',
						'required' => true,
					),
					array(
						'slug'     => 'singular_label',
						'label'    => 'Singular Label',
						'default'  => '',
						'type'     => 'text',
						'required' => true,
					),
					array(
						'slug'        => 'object_type',
						'label'       => 'Attach to Post Type',
						'type'        => 'checkboxes',
						'options'     => $post_type_options,
						'description' => 'Select one or more post types to associate with this taxonomy.',
						'required'    => true,
					),
				),
			),
			'additional_labels' => array(
				'title'  => 'Additional Labels',
				'fields' => array(

					array(
						'slug'        => 'description',
						'label'       => 'Description',
						'default'     => 'Description',
						'placeholder' => 'Description',
						'type'        => 'textarea',
						'required'    => false,
					),
					array(
						'slug'        => 'menu_name',
						'label'       => 'Menu Name',
						'default'     => 'Menu Name',
						'placeholder' => 'Menu Name',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'all_items',
						'label'       => 'All Items',
						'default'     => 'All Items',
						'placeholder' => 'All Items',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'edit_item',
						'label'       => 'Edit Item',
						'default'     => 'Edit Item',
						'placeholder' => 'Edit Item',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'view_item',
						'label'       => 'View Item',
						'default'     => 'View Item',
						'placeholder' => 'View Item',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'update_item_name',
						'label'       => 'Update Item Name',
						'default'     => 'Update Item Name',
						'placeholder' => 'Update Item Name',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'filter_by_item',
						'label'       => 'Filter by Item',
						'default'     => 'Filter by Item',
						'placeholder' => 'Filter by Item',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'update_item',
						'label'       => 'Update Item',
						'default'     => 'Update Item',
						'placeholder' => 'Update Item',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'add_new_item',
						'label'       => 'Add New Item',
						'default'     => 'Add New Item',
						'placeholder' => 'Add New Item',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'new_item_name',
						'label'       => 'New Item Name',
						'default'     => 'New Item Name',
						'placeholder' => 'New Item Name',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'parent_item_colon',
						'label'       => 'Parent Item Colon',
						'default'     => 'Parent Item Colon',
						'placeholder' => 'Parent Item Colon',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'parent_item',
						'label'       => 'Parent Item',
						'default'     => 'Parent Item',
						'placeholder' => 'Parent Item',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'search_items',
						'label'       => 'Search Items',
						'default'     => 'Search Items',
						'placeholder' => 'Search Items',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'popular_items',
						'label'       => 'Popular Items',
						'default'     => 'Popular Items',
						'placeholder' => 'Popular Items',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'item_link',
						'label'       => 'Item Link',
						'default'     => 'Item Link',
						'placeholder' => 'Item Link',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'separate_items_with_commas',
						'label'       => 'Separate Items with Commas',
						'default'     => 'Separate Items with Commas',
						'placeholder' => 'Separate Items with Commas',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'add_or_remove_items',
						'label'       => 'Add or Remove Items',
						'default'     => 'Add or Remove Items',
						'placeholder' => 'Add or Remove Items',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'choose_from_most_used',
						'label'       => 'Choose From Most Used',
						'default'     => 'Choose from the most used',
						'placeholder' => 'Choose From Most Used',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'not_found',
						'label'       => 'Not Found',
						'default'     => 'No terms found',
						'placeholder' => 'Not found',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'no_terms',
						'label'       => 'No Terms',
						'default'     => 'No terms',
						'placeholder' => 'No terms',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'name_field_description',
						'label'       => 'Name Field Description',
						'default'     => 'The name is how it appears on your site.',
						'placeholder' => 'The name is how it appears on your site.',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'items_list_navigation',
						'label'       => 'Items List Navigation',
						'default'     => 'Items list navigation',
						'placeholder' => 'Items List Navigation',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'items_list',
						'label'       => 'Items List',
						'default'     => 'Items list',
						'placeholder' => 'Items List',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'back_to_items',
						'label'       => 'Back to Items',
						'default'     => '← Back to Items',
						'placeholder' => 'Back to Items',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'term_name_field_description',
						'label'       => 'Term Name Field Description',
						'default'     => 'Name of the term',
						'placeholder' => 'Term Name Field Description',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'term_parent_field_description',
						'label'       => 'Term Parent Field Description',
						'default'     => 'Parent of the term',
						'placeholder' => 'Term Parent Field Description',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'term_slug_field_description',
						'label'       => 'Term Slug Field Description',
						'default'     => 'Slug of the term',
						'placeholder' => 'Term Slug Field Description',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'term_description_field_description',
						'label'       => 'Term Description Field Description',
						'default'     => 'Description of the term',
						'placeholder' => 'Term Description Field Description',
						'type'        => 'text',
						'required'    => false,
					),
				),
			),
			'args'              => array(
				'title'  => 'Arguments',
				'fields' => array(
					array(
						'slug'     => 'public',
						'label'    => 'Public',
						'default'  => true,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'publicly_queryable',
						'label'    => 'Public Queryable',
						'default'  => true,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'hierarchical',
						'label'    => 'Hierarchical',
						'default'  => false,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'show_ui',
						'label'    => 'Show UI',
						'default'  => true,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'show_in_menu',
						'label'    => 'Show in Menu',
						'default'  => true,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'show_in_nav_menus',
						'label'    => 'Show in Nav Menus',
						'default'  => true,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'query_var',
						'label'    => 'Query Var',
						'default'  => true,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'rewrite',
						'label'    => 'Rewrite',
						'default'  => true,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'rewrite_slug',
						'label'    => 'Custom Rewrite Slug',
						'default'  => '',
						'type'     => 'text',
						'required' => false,
					),
					array(
						'slug'     => 'rewrite_with_front',
						'label'    => 'Rewrite With Front',
						'default'  => true,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'rewrite_hierarchical',
						'label'    => 'Rewrite Hierarchical',
						'default'  => false,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'show_admin_column',
						'label'    => 'Show Admin Column',
						'default'  => false,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'show_in_rest',
						'label'    => 'Show in REST API',
						'default'  => false,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'rest_base',
						'label'    => 'REST API Base Slug',
						'default'  => '',
						'type'     => 'text',
						'required' => false,
					),
					array(
						'slug'     => 'rest_controller_class',
						'label'    => 'REST API Controller Class',
						'default'  => 'WP_REST_Terms_Controller',
						'type'     => 'text',
						'required' => false,
					),
					array(
						'slug'     => 'rest_namespace',
						'label'    => 'REST API Namespace',
						'default'  => 'wp/v2',
						'type'     => 'text',
						'required' => false,
					),
					array(
						'slug'     => 'sort',
						'label'    => 'Sort',
						'default'  => false,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'show_in_quick_edit',
						'label'    => 'Show in Quick Edit',
						'default'  => false,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'show_tagcloud',
						'label'    => 'Show in tag cloud',
						'default'  => false,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'     => 'meta_box_cb',
						'label'    => 'Metabox Callback',
						'default'  => null,
						'type'     => 'text',
						'required' => false,
					),
					array(
						'slug'     => 'default_term',
						'label'    => 'Default Term',
						'default'  => '',
						'type'     => 'text',
						'required' => false,
					),
				),
			),
		);
	}




	/**
	 * Sanitizes the settings input for custom taxonomies.
	 *
	 * @param array $input The input array to be sanitized.
	 * @return array Returns the sanitized input array.
	 */
	public static function sanitize_settings( $input ) {
		$new_input = array();
		$config    = self::get_taxonomy_config();
		foreach ( $config as $section ) {
			foreach ( $section['fields'] as $field ) {
				if ( isset( $input[ $field['slug'] ] ) ) {
					if ( $field['type'] == 'checkbox' ) {
						$new_input[ $field['slug'] ] = isset( $input[ $field['slug'] ] );
					} elseif ( $field['type'] == 'checkboxes' ) {
						// Sanitize each value in the array for checkboxes.
						$new_input[ $field['slug'] ] = array_map( 'sanitize_text_field', $input[ $field['slug'] ] );
					} elseif ( $field['type'] == 'select' && is_array( $input[ $field['slug'] ] ) ) {
						// Sanitize each value in the array for select.
						$new_input[ $field['slug'] ] = array_map( 'sanitize_text_field', $input[ $field['slug'] ] );
					} else {
						// Sanitize based on type (e.g., text).
						$new_input[ $field['slug'] ] = sanitize_text_field( $input[ $field['slug'] ] );
					}
				} elseif ( $field['type'] === 'checkbox' ) {
						$new_input[ $field['slug'] ] = false;
				} elseif ( $field['type'] === 'checkboxes' ) {
					$new_input[ $field['slug'] ] = array();
				}
			}
		}
		return $new_input;
	}

	/**
	 * Renders form fields based on the provided options.
	 *
	 * @param array $options The options array used to render the form fields.
	 */
	public static function render_form_fields( $options ) {
		$config = self::get_taxonomy_config();
		foreach ( $config as $section_id => $section ) {

			echo "<div id='ocpt_panel_" . esc_attr( $section_id ) . "' class='ocpt-section postbox'>"; ?>
			<div class="postbox-header">
				<h2 class="handle ui-sortable-handle">
					<span><?php esc_html_e( $section['title'], 'ocean-cpt' ); ?></span>
				</h2>
			</div>
			<div class="inside">
				<div class="main">
					<table class="form-table ocpt-table">
						<tbody>
							<?php
							foreach ( $section['fields'] as $field ) {
								self::render_field( $field, $options );
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
			</div> 
			<?php
		}
	}

	/**
	 * Renders a single form field based on the provided field configuration and options.
	 *
	 * @param array $field The field configuration array.
	 * @param array $options The options array used for the field's value.
	 */
	public static function render_field( $field, $options ) {
		// Ensure the option exists in the $options array, otherwise default to null.
		$option_value = isset( $options[ $field['slug'] ] ) ? $options[ $field['slug'] ] : ( isset( $field['default'] ) ? $field['default'] : null );

		// Render the field based on its type.
		switch ( $field['type'] ) {
			case 'text':
				echo '<tr class="' . esc_attr( $field['slug'] ) . '">';
				echo '<th scope="row"><label for="' . esc_attr( $field['slug'] ) . '">' . esc_attr( $field['label'] ) . '</label>' . ( isset( $field['required'] ) && $field['required'] ? '<span class="required">*</span>' : '' ) . '</th>';
				echo '<td><input type="text" id="' . esc_attr( $field['slug'] ) . '" name="' . esc_attr( $field['slug'] ) . '" value="' . esc_attr( $option_value ) . '"  ' . ( isset( $field['required'] ) && $field['required'] ? 'required="true"' : '' ) . ' ' . ( isset( $field['placeholder'] ) ? 'placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '' ) . '></td>';
				echo '</tr>';
				break;
			case 'checkbox':
				echo '<tr>';
				echo '<th scope="row"><label for="' . esc_attr( $field['slug'] ) . '">' . esc_attr( $field['label'] ) . '</label></th>';

				// Determine if the checkbox should be checked.
				if ( ! empty( $options ) ) {
					// update action.
					$is_checked = isset( $options[ $field['slug'] ] ) ? (bool) $options[ $field['slug'] ] : false;
				} else {
					// create action.
					$is_checked = isset( $field['default'] ) ? (bool) $field['default'] : false;
				}

				echo '<td><input type="checkbox" id="' . esc_attr( $field['slug'] ) . '" name="' . esc_attr( $field['slug'] ) . '" value="1" ' . checked( $is_checked, true, false ) . '></td>';
				echo '</tr>';
				break;

			case 'select':
				echo '<tr>';
				echo '<th scope="row"><label for="' . esc_attr( $field['slug'] ) . '">' . esc_attr( $field['label'] ) . '</label></th>';
				echo '<td><select name="' . esc_attr( $field['slug'] ) . '[]" multiple style="height: 220px;">';

				// Check if the options are set and is an array, otherwise default to an empty array.

				foreach ( $field['options'] as $option_key => $option_label ) {
					$selected = in_array( $option_key, (array) $option_value, true ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $option_key ) . '" ' . $selected . '>' . esc_html( $option_label ) . '</option>';
				}
				echo '</select></td>';
				echo '</tr>';
				break;

			case 'checkboxes':
				echo '<tr>';
				echo '<th scope="row"><label for="' . esc_attr( $field['slug'] ) . '">' . esc_attr( $field['label'] ) . '</label>' . ( isset( $field['required'] ) && $field['required'] ? '<span class="required">*</span>' : '' ) . '</th>';
				echo '<td>';
				if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
					foreach ( $field['options'] as $option ) {
						$checked = is_array( $option_value ) && in_array( $option['value'], $option_value ) ? 'checked' : '';
						echo '<label>';
						echo '<input type="checkbox" name="' . esc_attr( $field['slug'] ) . '[]" value="' . esc_attr( $option['value'] ) . '" ' . $checked . '>';
						echo esc_html( $option['label'] );
						echo '</label><br>';
					}
				}
				echo '</td>';
				echo '</tr>';
				break;

			case 'textarea':
				echo '<tr>';
				echo '<th scope="row"><label for="' . esc_attr( $field['slug'] ) . '">' . esc_attr( $field['label'] ) . '</label>' . ( isset( $field['required'] ) && $field['required'] ? '<span class="required">*</span>' : '' ) . '</th>';
				echo '<td><textarea id="' . esc_attr( $field['slug'] ) . '" name="' . esc_attr( $field['slug'] ) . '" rows="5" cols="50" ' . ( isset( $field['required'] ) && $field['required'] ? 'required="required"' : '' ) . '>' . esc_textarea( $option_value ) . '</textarea></td>';
				echo '</tr>';
				break;
			default:
				echo '<tr>';
				echo '<th scope="row">' . esc_attr( $field['label'] ) . '</th>';
				echo '<td>' . ( isset( $field['value'] ) ? esc_attr( $field['value'] ) : '' ) . '</td>';
				echo '</tr>';
		}
	}
}
new Ocean_Taxonomy_Helper();
