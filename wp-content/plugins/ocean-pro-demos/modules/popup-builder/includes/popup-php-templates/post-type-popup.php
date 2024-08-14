<?php

/**
 * Class Popup_Post_Type
 */
class OPB_Popup_Post_Type_PHP {
	/**
	 * Registers the Simple Popup  custom post type.
	 */
	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_popup_post_type' ) );
		add_filter( 'manage_opb-popup-php_posts_columns', array( __CLASS__, 'add_columns' ) );
		add_action( 'manage_opb-popup-php_posts_custom_column', array( __CLASS__, 'fill_columns' ), 10, 2 );
	}

	/**
	 * Registers the Simple Popup  custom post type.
	 */
	public static function register_popup_post_type() {
		$labels = array(
			'name'               => __( 'Simple Popups', 'ocean-ecommerce' ),
			'singular_name'      => __( 'Simple Popup ', 'ocean-ecommerce' ),
			'menu_name'          => __( 'Simple Popups', 'ocean-ecommerce' ),
			'name_admin_bar'     => __( 'Simple Popup ', 'ocean-ecommerce' ),
			'add_new'            => __( 'Add New Simple', 'ocean-ecommerce' ),
			'add_new_item'       => __( 'Add New Simple Popup ', 'ocean-ecommerce' ),
			'new_item'           => __( 'New Simple Popup ', 'ocean-ecommerce' ),
			'edit_item'          => __( 'Edit Simple Popup ', 'ocean-ecommerce' ),
			'view_item'          => __( 'View Simple Popup ', 'ocean-ecommerce' ),
			'all_items'          => __( 'Simple Popups', 'ocean-ecommerce' ),
			'search_items'       => __( 'Search Simple Popups', 'ocean-ecommerce' ),
			'parent_item_colon'  => __( 'Parent Simple Popups:', 'ocean-ecommerce' ),
			'not_found'          => __( 'No Simple popups found.', 'ocean-ecommerce' ),
			'not_found_in_trash' => __( 'No Simple popups found in Trash.', 'ocean-ecommerce' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'opb-popup-php' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'custom-fields' ),
			'show_in_rest'       => true, // Enable Gutenberg editor support.
			'menu_position'      => 5,
		);

		register_post_type( 'opb-popup-php', $args );

		// Flush rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Adds custom columns to the post type list table.
	 *
	 * @param array $columns An associative array of column names and labels.
	 * @return array An updated array of column names and labels.
	 */
	public static function add_columns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $title ) {
			// Insert new columns before the date column.
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
	 * Fills the custom columns in the post type list table.
	 *
	 * @param string $column The name of the column to display.
	 * @param int    $post_id The current post ID.
	 */
	public static function fill_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'post_id':
				echo absint( $post_id );
				break;
			case 'popup_pages':
				// Check if "show on the whole site" option is enabled.
				$showOnWholeSite = get_post_meta( $post_id, 'opb_show_on_whole_site_meta', true );

				// If enabled, display the label "Whole Site".
				if ( $showOnWholeSite ) {
					echo esc_html__( 'Whole Site', 'ocean-ecommerce' );
					break;
				}

				$assigned_pages = get_post_meta( $post_id, 'opb_selected_page_meta', true );

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
							return "<a href='{$page_link}'>{$page_title}</a>";
						},
						$assigned_pages
					);
					echo implode( ', ', $pages );
				} else {
					echo esc_html__( '—', 'ocean-ecommerce' );
				}
				break;
			case 'trigger_type':
				$trigger_type    = get_post_meta( $post_id, 'opb_popup_trigger_meta', true );
				$trigger_mapping = self::get_trigger_type_mapping();
				echo isset( $trigger_mapping[ $trigger_type ] ) ? $trigger_mapping[ $trigger_type ] : ucfirst( $trigger_type );
				break;
			case 'trigger_class':
				$trigger_type = get_post_meta( $post_id, 'opb_popup_trigger_meta', true );
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
	 * This function flushes the rewrite rules to ensure the 'opb-popup-php' post type is accessible.
	 */
	public function flush_rewrite_rules() {
		if ( get_option( 'opb_plugin_permalinks_flushed', true ) !== 'yes' ) {
			flush_rewrite_rules();
			update_option( 'opb_plugin_permalinks_flushed', 'yes' );
		}
	}
}

OPB_Popup_Post_Type_PHP::register();
