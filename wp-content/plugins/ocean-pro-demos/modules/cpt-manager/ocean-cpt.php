<?php

/**
 * CUstom Post Type Manager
 */
class OEC_CPT_Manager {


	private static $instance = null;

	/**
	 * [instance]
	 *
	 * @return [OEC_CPT_Manager]
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function __construct() {

		require_once OPD_PATH . 'modules/cpt-manager/includes/cpt/class-cpt-creator.php';
		require_once OPD_PATH . 'modules/cpt-manager/includes/cpt/class-cpt-manager.php';
		require_once OPD_PATH . 'modules/cpt-manager/includes/cpt/class-cpt-helper.php';
		require_once OPD_PATH . 'modules/cpt-manager/includes/taxonomy/class-taxonomy-creator.php';
		require_once OPD_PATH . 'modules/cpt-manager/includes/taxonomy/class-taxonomy-manager.php';
		require_once OPD_PATH . 'modules/cpt-manager/includes/taxonomy/class-taxonomy-helper.php';
	}
}


function opd_cpt_manager_instance() {
	return OEC_CPT_Manager::instance();
}

opd_cpt_manager_instance();
