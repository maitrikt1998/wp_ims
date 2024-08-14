<?php

/**
 * Scripts Panel
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class Ocean_PD_Theme_Panel {

	/**
	 * Start things up
	 */
	public function __construct() {



		// Add custom scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_filter( 'oceanwp_theme_panel_pane_ocean_images_settings', array( $this, 'ocean_images_part' ) );

		add_filter( 'oceanwp_theme_panel_pane_opd_blocks_settings', array( $this, 'opd_blocks_settings' ) );

		add_filter( 'oceanwp_theme_panel_pane_opd_pixel_tracker_settings', array( $this, 'opd_pixel_tracker_settings' ) );

		add_filter( 'oceanwp_theme_panel_sections', array( $this, 'opd_oceanwp_theme_panel_section' ) );
	}

	/**
	 * Admin Scripts.
	 */
	public static function admin_scripts( $hook ) {
		$current_screen = get_current_screen();
		// Only load scripts when needed.
		if ( 'toplevel_page_oceanwp' != $current_screen->id ) {
			return;
		}
		wp_enqueue_style( 'font-awesome');
		// JS.
		wp_enqueue_script( 'ocean-pd-scripts-themepanel', plugins_url( '/assets/js/theme-panel.min.js', __FILE__ ),array(), DEMO_API_IMAGES_ASSETS_VERSION, true );
		wp_enqueue_script( 'ocean-pd-scripts-themepanel-tabs', plugins_url( '/assets/js/theme-panel-tabs.min.js', __FILE__ ),array(), DEMO_API_IMAGES_ASSETS_VERSION, true );
		wp_enqueue_style( 'oceanwp-theme-panel-opd', plugins_url( '/assets/css/theme-panel-opd.min.css', __FILE__ ), array(), DEMO_API_IMAGES_ASSETS_VERSION  );
	}

	function ocean_images_part() {
		return DEMO_API_IMAGES_PATH . 'includes/themepanel/views/panes/ocean-images-settings.php';
	}

	function opd_blocks_settings() {
		return OPD_PATH . 'includes/themepanel/views/panes/opd-blocks-settings.php';
	}

	function opd_pixel_tracker_settings() {
		return OPD_PATH . 'includes/themepanel/views/panes/opd-pixel-tracker-settings.php';
	}

	public static function get_ocean_images_settings() {
		$settings = array();

		return apply_filters( 'ocean_integrations_settings', $settings );
	}

	public function opd_oceanwp_theme_panel_section( $sections ) {
		$opd_blocks_settings_section = array(
			'opd-blocks-settings' => array(
				'title' => __( 'Misc Pro Features', 'ocean-pro-demos' ),
				'href'  => 'opd-blocks-settings',
				'order' => 100,
			),
		);
		$opd_pixel_tracker_settings_section = array(
			'opd-pixel-tracker-settings' => array(
				'title' => __( 'Pixel Tracker', 'ocean-pro-demos' ),
				'href'  => 'opd-pixel-tracker-settings',
				'order' => 101,
			),
		);

		$sections = array_merge( $sections, $opd_blocks_settings_section, $opd_pixel_tracker_settings_section );

		return $sections;
	}
}

new Ocean_PD_Theme_Panel();
