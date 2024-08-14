<?php

/**
 * Popup Builder
 */
class OEC_Popup_Builder {


	private static $instance = null;

	/**
	 * [instance]
	 *
	 * @return [OEC_Popup_Builder]
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function __construct() {

		// Popup Editors.
		require_once OPD_PATH . 'modules/popup-builder/includes/popup-editors/post-type-popup.php';
		require_once OPD_PATH . 'modules/popup-builder/includes/popup-editors/metaboxes-popup.php';
		require_once OPD_PATH . 'modules/popup-builder/includes/popup-editors/popup-frontend.php';
		if ( class_exists( '\Elementor\Plugin' ) ) {
			require_once OPD_PATH . 'modules/popup-builder/includes/popup-editors/elementor-popup-settings.php';
		}

		// Popup PHP Templates.
		require_once OPD_PATH . 'modules/popup-builder/includes/popup-php-templates/post-type-popup.php';
		require_once OPD_PATH . 'modules/popup-builder/includes/popup-php-templates/functions.php';
		require_once OPD_PATH . 'modules/popup-builder/includes/popup-php-templates/meta.php';
		require_once OPD_PATH . 'modules/popup-builder/includes/popup-php-templates/popup-frontend.php';

	}

}


function oec_popup_builder_instance() {
	return OEC_Popup_Builder::instance();
}

oec_popup_builder_instance();
