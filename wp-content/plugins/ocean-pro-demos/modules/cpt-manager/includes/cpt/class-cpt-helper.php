<?php


class Ocean_CPT_Helper {


	/**
	 * Constructor for the Custom Post Type Helper.
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
		if ( 'toplevel_page_create_cpt' === $screen->id || 'my-o-cpt_page_manage_cpt' === $screen->id ) {
			wp_enqueue_style( 'ocean-dashicons-picker', OPD_URL . 'modules/cpt-manager/assets/css/dashicons-picker.css', array( 'dashicons' ), OPD_VERSION );
			wp_enqueue_script( 'ocean-dashicons-picker', OPD_URL . 'modules/cpt-manager/assets/js/dashicons-picker.js', array( 'jquery' ), OPD_VERSION, true );

			wp_enqueue_script( 'ocean-cpt-custom-label', OPD_URL . 'modules/cpt-manager/assets/js/ocean-cpt-manager-script.js', array( 'jquery' ), OPD_VERSION, true );
			wp_enqueue_style( 'ocean-cpt', OPD_URL . 'modules/cpt-manager/assets/css/ocean-cpt-manager.css', array(), OPD_VERSION );
			wp_localize_script(
				'ocean-cpt-custom-label',
				'oceanCPTLabels',
				array(
					'labelsToUpdate' => array(
						'all_items'                => '#all_items',
						'view_item'                => '#view_item',
						'add_new_item'             => '#add_new_item',
						'edit_item'                => '#edit_item',
						'new_item'                 => '#new_item',
						'view_items'               => '#view_items',
						'search_items'             => '#search_items',
						'parent_item_colon'        => '#parent_item_colon',
						'insert_into_item'         => '#insert_into_item',
						'uploaded_to_this_item'    => '#uploaded_to_this_item',
						'filter_items_list'        => '#filter_items_list',
						'items_list'               => '#items_list',
						'item_updated'             => '#item_updated',
						'item_scheduled'           => '#item_scheduled',
						'item_trashed'             => '#item_trashed',
						'item_reverted_to_draft'   => '#item_reverted_to_draft',
						'item_published_privately' => '#item_published_privately',
						'item_published'           => '#item_published',
						'menu_name'                => '#menu_name',
						'items_list_navigation'                => '#items_list_navigation',
					),
				)
			);
		}
	}

	/**
	 * Retrieves the configuration for custom post types.
	 *
	 * @return array Returns an array of configurations for custom post types.
	 */
	public static function get_cpt_config() {
		// Retrieve taxonomies and filter out unwanted ones.
		$args       = array( 'public' => true );
		$taxonomies = get_taxonomies( $args, 'objects' );
		unset( $taxonomies['nav_menu'], $taxonomies['post_format'] );

		// Prepare the taxonomies for display.
		$taxonomy_options = array();
		foreach ( $taxonomies as $taxonomy ) {
			$core_label         = in_array( $taxonomy->name, array( 'category', 'post_tag' ), true ) ? esc_html__( '(WP Core)', 'ocean-cpt' ) : '';
			$taxonomy_options[] = array(
				'value' => $taxonomy->name,
				'label' => $taxonomy->label . ' ' . $core_label,
			);
		}

		return array(
			'basic_settings'    => array(
				'title'  => 'Basic Settings',
				'fields' => array(
					array(
						'slug'     => 'post_type_slug',
						'label'    => 'Post Type Slug',
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
						'slug'        => 'search_items',
						'label'       => 'Search Items',
						'default'     => 'Search Items',
						'placeholder' => 'Search Items',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'not_found',
						'label'       => 'Not Found',
						'default'     => 'Not Found',
						'placeholder' => 'Not Found',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'not_found_in_trash',
						'label'       => 'Not Found in Trash',
						'default'     => 'Not Found in Trash',
						'placeholder' => 'Not Found in Trash',
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
						'slug'        => 'featured_image',
						'label'       => 'Featured Image',
						'default'     => 'Featured Image',
						'placeholder' => 'Featured Image',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'set_featured_image',
						'label'       => 'Set Featured Image',
						'default'     => 'Set Featured Image',
						'placeholder' => 'Set Featured Image',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'remove_featured_image',
						'label'       => 'Remove Featured Image',
						'default'     => 'Remove Featured Image',
						'placeholder' => 'Remove Featured Image',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'use_featured_image',
						'label'       => 'Use Featured Image',
						'default'     => 'Use Featured Image',
						'placeholder' => 'Use Featured Image',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'archives',
						'label'       => 'Archives',
						'default'     => 'Archives',
						'placeholder' => '(e.g. Movie archives)',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'add_new',
						'label'       => 'Add New',
						'default'     => 'Add New',
						'placeholder' => 'Add New',
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
						'slug'        => 'view_items',
						'label'       => 'View Items',
						'default'     => 'View Items',
						'placeholder' => 'View Items',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'filter_items_list',
						'label'       => 'Filter Items List',
						'default'     => 'Filter Items List',
						'placeholder' => 'Filter Items List',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'items_list_navigation',
						'label'       => 'Items List Navigation',
						'default'     => 'Items List Navigation',
						'placeholder' => 'Items List Navigation',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'items_list',
						'label'       => 'Items List',
						'default'     => 'Items List',
						'placeholder' => 'Items List',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'attributes',
						'label'       => 'Attributes',
						'default'     => 'Attributes',
						'placeholder' => 'Attributes',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'name_admin_bar',
						'label'       => 'Name Admin Bar',
						'default'     => 'Name Admin Bar',
						'placeholder' => 'Name Admin Bar',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'insert_into_item',
						'label'       => 'Insert into Item',
						'default'     => 'Insert into Item',
						'placeholder' => 'Insert into Item',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'uploaded_to_this_item',
						'label'       => 'Uploaded to This Item',
						'default'     => 'Uploaded to This Item',
						'placeholder' => 'Uploaded to This Item',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'item_published',
						'label'       => 'Item Published',
						'default'     => 'Item Published',
						'placeholder' => 'Item Published',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'item_published_privately',
						'label'       => 'Item Published Privately',
						'default'     => 'Item Published Privately',
						'placeholder' => 'Item Published Privately',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'item_reverted_to_draft',
						'label'       => 'Item Reverted to Draft',
						'default'     => 'Item Reverted to Draft',
						'placeholder' => 'Item Reverted to Draft',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'item_trashed',
						'label'       => 'Item Trashed',
						'default'     => 'Item Trashed',
						'placeholder' => 'Item Trashed',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'item_scheduled',
						'label'       => 'Item Scheduled',
						'default'     => 'Item Scheduled',
						'placeholder' => 'Item Scheduled',
						'type'        => 'text',
						'required'    => false,
					),
					array(
						'slug'        => 'item_updated',
						'label'       => 'Item Updated',
						'default'     => 'Item Updated',
						'placeholder' => 'Item Updated',
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
						'label'    => 'Publicly Queryable',
						'default'  => true,
						'type'     => 'checkbox',
						'required' => false,
					),
					array(
						'slug'    => 'show_ui',
						'label'   => 'Show UI',
						'type'    => 'checkbox',
						'default' => true,
					),
					array(
						'slug'    => 'show_in_rest',
						'label'   => 'Show in REST',
						'type'    => 'checkbox',
						'default' => true,
					),
					array(
						'slug'    => 'rest_base',
						'label'   => 'REST Base',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'slug'    => 'rest_controller_class',
						'label'   => 'REST Controller Class',
						'type'    => 'text',
						'default' => 'WP_REST_Posts_Controller',
					),
					array(
						'slug'    => 'rest_namespace',
						'label'   => 'REST Namespace',
						'type'    => 'text',
						'default' => 'wp/v2',
					),
					array(
						'slug'    => 'has_archive',
						'label'   => 'Has Archive',
						'type'    => 'checkbox',
						'default' => true,
					),
					array(
						'slug'    => 'show_in_menu',
						'label'   => 'Show in Menu',
						'type'    => 'checkbox',
						'default' => true,
					),
					array(
						'slug'    => 'show_in_nav_menus',
						'label'   => 'Show in Nav Menus',
						'type'    => 'checkbox',
						'default' => false,
					),
					array(
						'slug'    => 'delete_with_user',
						'label'   => 'Delete with User',
						'type'    => 'checkbox',
						'default' => false,
					),
					array(
						'slug'    => 'exclude_from_search',
						'label'   => 'Exclude from Search',
						'type'    => 'checkbox',
						'default' => false,
					),
					array(
						'slug'    => 'capability_type',
						'label'   => 'Capability Type',
						'type'    => 'text',
						'default' => 'post',
					),
					array(
						'slug'    => 'map_meta_cap',
						'label'   => 'Map Meta Capabilities',
						'type'    => 'checkbox',
						'default' => true,
					),
					array(
						'slug'    => 'hierarchical',
						'label'   => 'Hierarchical',
						'type'    => 'checkbox',
						'default' => false,
					),
					array(
						'slug'    => 'can_export',
						'label'   => 'Can Export',
						'type'    => 'checkbox',
						'default' => false,
					),
					array(
						'slug'    => 'rewrite_slug',
						'label'   => 'Rewrite Slug',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'slug'    => 'rewrite_with_front',
						'label'   => 'Rewrite With Front',
						'type'    => 'checkbox',
						'default' => true,
					),
					array(
						'slug'    => 'query_var',
						'label'   => 'Query Var',
						'type'    => 'checkbox',
						'default' => false,
					),
					array(
						'slug'    => 'menu_position',
						'label'   => 'Menu Position',
						'type'    => 'number',
						'default' => 5,
					),
					array(
						'slug'    => 'menu_icon',
						'label'   => 'Menu Icon',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'slug'    => 'menu_icon_button',
						'label'   => 'Choose Dashicon',
						'type'    => 'icon_button',
						'default' => '',
					),
					array(
						'slug'    => 'rewrite_slug_custom',
						'label'   => 'Custom Rewrite Slug',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'slug'    => 'supports_custom',
						'label'   => 'Custom Supports',
						'type'    => 'text',
						'default' => '',
					),

					array(
						'slug'    => 'taxonomies',
						'label'   => 'Taxonomies',
						'type'    => 'checkboxes',
						'options' => $taxonomy_options,
						'default' => '',
					),
					array(
						'slug'     => 'supports',
						'label'    => 'Supports',
						'type'     => 'select',
						'required' => false,
						'default'  => array( 'title', 'editor', 'thumbnail' ),
						'options'  => array(
							'title'           => 'Title',
							'editor'          => 'Editor',
							'thumbnail'       => 'Featured Image',
							'excerpt'         => 'Excerpt',
							'trackbacks'      => 'Trackbacks',
							'custom-fields'   => 'Custom Fields',
							'revisions'       => 'Revisions',
							'author'          => 'Author',
							'page-attributes' => 'Page Attributes',
							'post-formats'    => 'Post Formats',
							'none'            => 'None',
						),
					),
				),
			),
		);
	}

	/**
	 * Sanitizes the settings input for custom post types.
	 *
	 * @param array $input The input array to be sanitized.
	 * @return array Returns the sanitized input array.
	 */
	public static function sanitize_settings( $input ) {
		$new_input = array();
		$config    = self::get_cpt_config();
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
					} elseif ( $field['type'] == 'number' ) {
						// Sanitize the input as an integer for number fields.
						$new_input[ $field['slug'] ] = intval( $input[ $field['slug'] ] );
					} else {
						// Sanitize based on type (e.g., text).
						$new_input[ $field['slug'] ] = sanitize_text_field( $input[ $field['slug'] ] );
					}
				} elseif ( $field['type'] === 'checkbox' ) {
						$new_input[ $field['slug'] ] = false;
				} elseif ( $field['type'] === 'checkboxes' ) {
					$new_input[ $field['slug'] ] = array();
				} elseif ( $field['type'] === 'number' ) {
					// Ensure default value for number field is an integer, even if not provided.
					$new_input[ $field['slug'] ] = isset( $field['default'] ) ? intval( $field['default'] ) : 0;
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
		$config = self::get_cpt_config();
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
				echo '<tr class="' . esc_attr( $field['slug'] ) . '">';
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
				echo '<tr class="' . esc_attr( $field['slug'] ) . '">';
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

			case 'number':
				echo '<tr class="' . esc_attr( $field['slug'] ) . '">';
				echo '<th scope="row"><label for="' . esc_attr( $field['slug'] ) . '">' . esc_attr( $field['label'] ) . '</label></th>';
				echo '<td><input type="number" id="' . esc_attr( $field['slug'] ) . '" name="' . esc_attr( $field['slug'] ) . '" value="' . esc_attr( $option_value ) . '" min="0" ' . ( isset( $field['placeholder'] ) ? 'placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '' ) . '></td>';
				echo '</tr>';
				break;

			case 'checkboxes':
				echo '<tr class="' . esc_attr( $field['slug'] ) . '">';
				echo '<th scope="row">' . esc_attr( $field['label'] ) . '</th>';
				echo '<td>';
				if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
					foreach ( $field['options'] as $option ) {
						$checked = is_array( $option_value ) && in_array( $option['value'], $option_value ) ? 'checked' : '';
						echo '<label>';
						echo '<input type="checkbox" name="' . $field['slug'] . '[]" value="' . esc_attr( $option['value'] ) . '" ' . $checked . '>';
						echo esc_html( $option['label'] );
						echo '</label><br>';
					}
				}
				echo '</td>';
				echo '</tr>';
				break;
			case 'icon_button':
				echo '<tr class="' . esc_attr( $field['slug'] ) . '">';
				echo '<th scope="row">' . esc_html( $field['label'] ) . '</th>';
				echo '<td>';
				echo '<input class="button dashicons-picker" type="button" value="' . esc_attr( $field['label'] ) . '" data-target="#menu_icon" />';
				echo '</td>';
				echo '</tr>';
				break;
			case 'textarea':
				echo '<tr class="' . esc_attr( $field['slug'] ) . '">';
				echo '<th scope="row"><label for="' . esc_attr( $field['slug'] ) . '">' . esc_attr( $field['label'] ) . '</label>' . ( isset( $field['required'] ) && $field['required'] ? '<span class="required">*</span>' : '' ) . '</th>';
				echo '<td><textarea id="' . esc_attr( $field['slug'] ) . '" name="' . esc_attr( $field['slug'] ) . '" rows="5" cols="50" ' . ( isset( $field['required'] ) && $field['required'] ? 'required="required"' : '' ) . '>' . esc_textarea( $option_value ) . '</textarea></td>';
				echo '</tr>';
				break;
			default:
				echo '<tr class="' . esc_attr( $field['slug'] ) . '">';
				echo '<th scope="row">' . esc_attr( $field['label'] ) . '</th>';
				echo '<td>' . ( isset( $field['value'] ) ? esc_attr( $field['value'] ) : '' ) . '</td>';
				echo '</tr>';
		}
	}
}
new Ocean_CPT_Helper();
