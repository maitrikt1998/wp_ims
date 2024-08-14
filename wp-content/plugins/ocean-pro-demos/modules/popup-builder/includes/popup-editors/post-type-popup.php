<?php

/**
 * Class Popup_Post_Type
 */
class OPB_Popup_Post_Type {
	/**
	 * Registers the Popup custom post type.
	 */
	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_popup_post_type' ) );
		add_filter( 'manage_opb-popup_posts_columns', array( __CLASS__, 'add_columns' ) );
		add_action( 'manage_opb-popup_posts_custom_column', array( __CLASS__, 'fill_columns' ), 10, 2 );
		add_filter( 'template_include', array( __CLASS__, 'single_post_template' ), 9999 );
	}

	/**
	 * Filters the single post template for a custom post type.
	 *
	 * When viewing a singular post of the 'opb-popup' custom post type, this function checks
	 * if a specific template file exists and returns its path to override the default template.
	 * If the file does not exist, it returns the original template.
	 *
	 * @param string $template The path of the template to include.
	 * @return string The path of the template to include.
	 */
	public static function single_post_template( $template ) {
		if ( is_singular( 'opb-popup' ) ) {
			$default_file = OPD_PATH . 'modules/popup-builder/includes/popup-editors/single-opb-popup.php';
			if ( file_exists( $default_file ) ) {
				return $default_file;
			}
		}
		return $template;
	}

	/**
	 * Registers the Popup custom post type.
	 */
	public static function register_popup_post_type() {
		$labels = array(
			'name'               => __( 'Custom Popups', 'ocean-ecommerce' ),
			'singular_name'      => __( 'Custom Popup', 'ocean-ecommerce' ),
			'menu_name'          => __( 'Custom Popups', 'ocean-ecommerce' ),
			'name_admin_bar'     => __( 'Custom Popup', 'ocean-ecommerce' ),
			'add_new'            => __( 'Add New', 'ocean-ecommerce' ),
			'add_new_item'       => __( 'Add New Custom Popup', 'ocean-ecommerce' ),
			'new_item'           => __( 'New Custom Popup', 'ocean-ecommerce' ),
			'edit_item'          => __( 'Edit Custom Popup', 'ocean-ecommerce' ),
			'view_item'          => __( 'View Custom Popup', 'ocean-ecommerce' ),
			'all_items'          => __( 'Custom Popups', 'ocean-ecommerce' ),
			'search_items'       => __( 'Search Custom Popups', 'ocean-ecommerce' ),
			'parent_item_colon'  => __( 'Parent Custom Popups:', 'ocean-ecommerce' ),
			'not_found'          => __( 'No Custom Popups found.', 'ocean-ecommerce' ),
			'not_found_in_trash' => __( 'No Custom Popups found in Trash.', 'ocean-ecommerce' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'opb-popup' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'elementor', 'thumbnail' ),
			'show_in_rest'       => true, // Enable Gutenberg editor support.
			'menu_position'      => 4,
		);

		register_post_type( 'opb-popup', $args );

		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Adds custom columns to the post list table in the admin area.
	 *
	 * This function modifies the columns shown in the post list table for a custom post type or any other post type.
	 * It inserts two new columns, 'ID' and 'Assigned Pages', before the 'Date' column.
	 *
	 * @param array $columns An associative array of columns with column ID as the key and column header as the value.
	 * @return array The modified array of columns.
	 */
	public static function add_columns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $title ) {
			// Insert new columns before the date column
			if ( $key == 'date' ) {
				$new_columns['post_id']       = 'ID';
				$new_columns['trigger_type']  = __( 'Trigger Type', 'ocean-ecommerce' );
				$new_columns['trigger_class'] = __( 'Trigger Class', 'ocean-ecommerce' );
				$new_columns['popup_pages']   = __( 'Assigned Pages', 'ocean-ecommerce' );
			}
			$new_columns[ $key ] = $title;
		}

		return $new_columns;
	}

	/**
	 * Provides a mapping of popup trigger type keys to their descriptive texts.
	 *
	 * This method returns an associative array where the keys are the internal
	 * identifiers used for different popup trigger types (e.g., 'load', 'click'),
	 * and the values are the corresponding user-friendly descriptions.
	 *
	 * @return array Associative array mapping trigger type keys to descriptive texts.
	 */
	private static function get_trigger_type_mapping() {
		return array(
			'load'        => __( 'On Load', 'ocean-ecommerce' ),
			'click'       => __( 'On Click', 'ocean-ecommerce' ),
			'hover'       => __( 'On Hover', 'ocean-ecommerce' ),
			'exit_intent' => __( 'Exit Intent', 'ocean-ecommerce' ),
			'inactivity'  => __( 'Inactivity', 'ocean-ecommerce' ),
		);
	}

	/**
	 * Fills the custom columns in the post list table in the admin area.
	 *
	 * This function is responsible for outputting the content for the custom columns added to the post list table.
	 * It handles two specific columns: 'post_id' and 'popup_pages'. For the 'post_id' column, it simply outputs
	 * the ID of the post. For the 'popup_pages' column, it checks whether the popup is set to show on the whole site,
	 * outputs the assigned pages if any, and otherwise shows a placeholder.
	 *
	 * @param string $column The slug of the current column.
	 * @param int    $post_id The ID of the current post being displayed in the list table.
	 */
	public static function fill_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'post_id':
				echo $post_id;
				break;
			case 'popup_pages':
				// Check if "show on the whole site" option is enabled.
				$showOnWholeSite = get_post_meta( $post_id, 'popup_show_on_whole_site', true );

				// If enabled, display the label "Whole Site".
				if ( $showOnWholeSite ) {
					echo 'Whole Site';
					break;
				}

				$assigned_pages = get_post_meta( $post_id, 'popup_pages', true );

				if ( ! is_array( $assigned_pages ) ) {
					// If $assigned_pages is not an array, initialize it as an empty array.
					$assigned_pages = array();
				}

				// Flatten the array if it's nested.
				if ( ! empty( $assigned_pages ) && is_array( $assigned_pages[0] ) ) {
					$assigned_pages = call_user_func_array( 'array_merge', $assigned_pages );
				}

				// Filter out invalid IDs.
				$assigned_pages = array_filter(
					$assigned_pages,
					function ( $id ) {
						return is_numeric( $id ) && $id > 0;
					}
				);

				if ( ! empty( $assigned_pages ) ) {
					$pages = array_map(
						function ( $page_id ) {
							$page_title = get_post_field( 'post_title', $page_id );
							$page_link  = get_permalink( $page_id );
							return "<a href='{$page_link}'>{$page_title}</a>"; // Return the title wrapped in a link.
						},
						$assigned_pages
					);
					echo implode( ', ', $pages );
				} else {
					echo '—'; // Display a dash or any placeholder if no pages are assigned.
				}
				break;
			case 'trigger_type':
				$trigger_type    = get_post_meta( $post_id, 'popup_trigger', true );
				$trigger_mapping = self::get_trigger_type_mapping();
				echo isset( $trigger_mapping[ $trigger_type ] ) ? $trigger_mapping[ $trigger_type ] : ucfirst( $trigger_type );
				break;
			case 'trigger_class':
				$trigger_type = get_post_meta( $post_id, 'popup_trigger', true );
				if ( in_array( $trigger_type, array( 'click', 'hover' ), true ) ) {
					printf( '<code>.popup-trigger-%d</code>', intval( $post_id ) );
				} else {
					echo '—';
				}
				break;
		}
	}

	/**
	 * Flash rewrite rules.
	 *
	 * This function flushes the rewrite rules to ensure the 'opb-popup' post type is accessible.
	 */
	public function flush_rewrite_rules() {
		if ( get_option( 'opb_plugin_permalinks_flushed', true ) !== 'yes' ) {
			flush_rewrite_rules();
			update_option( 'opb_plugin_permalinks_flushed', 'yes' );
		}
	}
}

OPB_Popup_Post_Type::register();
