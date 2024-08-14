<?php
use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || die();

class Ocean_Elementor_Popup_Settings {

	public static function init() {
		add_action( 'elementor/documents/register_controls', array( __CLASS__, 'add_controls_section' ), 1, 1 );
		add_action( 'elementor/document/after_save', array( __CLASS__, 'after_save' ) );
	}

	public static function add_controls_section( $element ) {
		$post_id = get_the_ID();

		// Check if the current post type being edited is opb-popup.
		if ( 'opb-popup' !== get_post_type( $post_id ) ) {
			return;
		}

		$popup_delay              = get_post_meta( $post_id, 'popup_delay', true );
		$popup_inactivity_time    = get_post_meta( $post_id, 'popup_inactivity_time', true );
		$popup_autoclose_delay    = get_post_meta( $post_id, 'popup_autoclose_delay', true );
		$popup_pages              = get_post_meta( $post_id, 'popup_pages', true );
		$exclude_pages            = get_post_meta( $post_id, 'popup_exclude_pages', true );
		$popup_trigger            = get_post_meta( $post_id, 'popup_trigger', true );
		$popup_display_mode       = get_post_meta( $post_id, 'popup_display_mode', true );
		$popup_size               = get_post_meta( $post_id, 'popup_size', true );
		$popup_width              = get_post_meta( $post_id, 'popup_width', true );
		$popup_height             = get_post_meta( $post_id, 'popup_height', true );
		$popup_position           = get_post_meta( $post_id, 'popup_position', true );
		$popup_close_button       = get_post_meta( $post_id, 'popup_close_button', true );
		$disable_mobile           = get_post_meta( $post_id, 'disable_mobile', true );
		$popup_overlay_enabled    = get_post_meta( $post_id, 'popup_overlay_enabled', true );
		$popup_show_on_whole_site = get_post_meta( $post_id, 'popup_show_on_whole_site', true );
		$popup_overlay_color      = get_post_meta( $post_id, 'popup_overlay_color', true );

		$element->start_controls_section(
			'_section_popup_settings',
			array(
				'label' => __( 'Popup Settings', 'ocean-ecommerce' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$element->add_control(
			'_el_popup_show_on_whole_site',
			array(
				'label'        => __( 'Show on Whole Site', 'ocean-ecommerce' ),
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'label_on'     => __( 'Yes', 'ocean-ecommerce' ),
				'label_off'    => __( 'No', 'ocean-ecommerce' ),
				'return_value' => '1',
				'default'      => $popup_show_on_whole_site,
			)
		);

		$element->add_control(
			'_el_popup_pages',
			array(
				'label'     => __( 'Popup Pages', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'options'   => wp_list_pluck( get_pages(), 'post_title', 'ID' ),
				'default'   => $popup_pages,
				'condition' => array(
					'_el_popup_show_on_whole_site' => '',
				),
			)
		);

		$element->add_control(
			'_el_exclude_pages',
			array(
				'label'     => __( 'Exclude Pages', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'options'   => wp_list_pluck( get_pages(), 'post_title', 'ID' ),
				'default'   => $exclude_pages,
				'condition' => array(
					'_el_popup_show_on_whole_site!' => '',
				),
			)
		);

		$element->add_control(
			'_el_popup_delay',
			array(
				'label'     => __( 'Popup Delay (in seconds)', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'step'      => 1,
				'default'   => $popup_delay,
				'condition' => array(
					'_el_popup_trigger!' => 'inactivity',
				),
			)
		);

		$element->add_control(
			'_el_popup_autoclose_delay',
			array(
				'label'   => __( 'Popup Autoclose Delay (in seconds)', 'ocean-ecommerce' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'step'    => 1,
				'default' => $popup_autoclose_delay,
			)
		);

		$element->add_control(
			'_el_popup_trigger',
			array(
				'label'       => __( 'Popup Trigger', 'ocean-ecommerce' ),
				'description' => __( 'Select the popup trigger (use .popup-trigger-POPUPID class)', 'ocean-ecommerce' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'load'        => __( 'On Load', 'ocean-ecommerce' ),
					'click'       => __( 'On Click', 'ocean-ecommerce' ),
					'hover'       => __( 'On Hover', 'ocean-ecommerce' ),
					'exit_intent' => __( 'Exit Intent', 'ocean-ecommerce' ),
					'inactivity'  => __( 'Inactivity', 'ocean-ecommerce' ),
				),
				'default'     => $popup_trigger,
			)
		);

		$element->add_control(
			'_el_popup_inactivity_time',
			array(
				'label'     => __( 'Inactivity Time (in seconds)', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'step'      => 1,
				'default'   => $popup_inactivity_time,
				'condition' => array(
					'_el_popup_trigger' => 'inactivity',
				),
			)
		);
		$element->add_control(
			'_el_popup_display_mode',
			array(
				'label'   => __( 'Popup Display Mode', 'ocean-ecommerce' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'every_time'       => __( 'Every time', 'ocean-ecommerce' ),
					'once_per_session' => __( 'One time per session', 'ocean-ecommerce' ),
					'once'             => __( 'One time', 'ocean-ecommerce' ),
				),
				'default' => $popup_display_mode,
			)
		);

		$element->add_control(
			'_el_popup_size',
			array(
				'label'       => __( 'Popup Size', 'ocean-ecommerce' ),
				'description' => __( 'Select the popup size (in % or use Custom to set the px.)', 'ocean-ecommerce' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '50%',
				'options'     => array(
					'10%'    => '10%',
					'20%'    => '20%',
					'30%'    => '30%',
					'40%'    => '40%',
					'50%'    => '50%',
					'60%'    => '60%',
					'70%'    => '70%',
					'80%'    => '80%',
					'90%'    => '90%',
					'100%'   => '100%',
					'custom' => 'Custom',
				),
				'default'     => $popup_size,
				'selectors'   => array(
					// '.e-con > .e-con-inner' => 'max-width: {{VALUE}} !important;',
					'.elementor-section-wrap' => 'max-width: {{VALUE}} !important; margin: 0 auto;',
				),
			)
		);

		$element->add_control(
			'_el_popup_width',
			array(
				'label'     => __( 'Popup Width', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => $popup_width,
				'condition' => array(
					'_el_popup_size' => 'custom',
				),
				'selectors' => array(
					// '.e-con > .e-con-inner'   => 'max-width: {{VALUE}}px !important;',
					// '.elementor-section > .elementor-container'  => 'max-width: {{VALUE}}px !important; margin: 0 auto;',
					'.elementor-section-wrap' => 'max-width: {{VALUE}}px !important; margin: 0 auto;',
				),
			)
		);

		$element->add_control(
			'_el_popup_height',
			array(
				'label'     => __( 'Popup Height', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => $popup_height,
				'condition' => array(
					'_el_popup_size' => 'custom',
				),
				'selectors' => array(
					// '.e-con > .e-con-inner'   => 'height: {{VALUE}}px !important;',
					// '.elementor-section > .elementor-container'  => 'height: {{VALUE}}px !important; margin: 0 auto;',
					'.elementor-section-wrap' => 'height: {{VALUE}}px !important; margin: 0 auto;',
				),
			)
		);

		$element->add_control(
			'_el_popup_position',
			array(
				'label'   => __( 'Popup Position', 'ocean-ecommerce' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'center'        => __( 'Center', 'ocean-ecommerce' ),
					'left-center'   => __( 'Left Center', 'ocean-ecommerce' ),
					'right-center'  => __( 'Right Center', 'ocean-ecommerce' ),
					'top-left'      => __( 'Top Left', 'ocean-ecommerce' ),
					'top-center'    => __( 'Top Center', 'ocean-ecommerce' ),
					'top-right'     => __( 'Top Right', 'ocean-ecommerce' ),
					'bottom-left'   => __( 'Bottom Left', 'ocean-ecommerce' ),
					'bottom-center' => __( 'Bottom Center', 'ocean-ecommerce' ),
					'bottom-right'  => __( 'Bottom Right', 'ocean-ecommerce' ),
				),
				'default' => $popup_position,
			)
		);

		$element->add_control(
			'_el_popup_close_button',
			array(
				'label'   => __( 'Popup Close Button', 'ocean-ecommerce' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'escape' => __( 'Escape', 'ocean-ecommerce' ),
					'close'  => __( 'Close Button', 'ocean-ecommerce' ),
				),
				'default' => $popup_close_button,
			)
		);

		$element->add_control(
			'_el_disable_mobile',
			array(
				'label'        => __( 'Disable on Mobile', 'ocean-ecommerce' ),
				'description'  => __( 'Set to disable popup on mobile devices.', 'ocean-ecommerce' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'ocean-ecommerce' ),
				'label_off'    => __( 'No', 'ocean-ecommerce' ),
				'return_value' => '1',
				'default'      => $disable_mobile,
			)
		);

		$element->add_control(
			'_show_popup_layout',
			array(
				'label'     => __( 'Show Popup Layout', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => '-1',
				'options'   => array(
					'-1'    => __( 'Hide', 'ocean-ecommerce' ),
					'99999' => __( 'Show', 'ocean-ecommerce' ),
				),
			)
		);

		$element->add_control(
			'_el_popup_styles_percent',
			array(
				'label'     => __( 'Popup Styles', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::HIDDEN,
				'default'   => 'custom-popup-styles',
				'selectors' => array(
					'html.elementor-html::before' => '
						content: "";
						pointer-events: none;
						display: block;
						position: fixed; 
						top: 0; 
						left: 50%; 
						transform: translateX(-50%); 
						z-index: {{_show_popup_layout.VALUE}};
						width: {{_el_popup_size.VALUE}}; 
						height: {{_el_popup_size.VALUE}};
						border: 3px dashed #8f9193;',
					'#elementor-preview #top-bar-sticky-wrapper, #elementor-preview #site-header-sticky-wrapper, #elementor-preview #footer' => 'display: none !important;',                        // '.e-con > .e-con-inner'  => '
						// max-width: {{_el_popup_size.VALUE}} !important;
						// ',
				),
				'condition' => array(
					'_el_popup_size!' => 'custom',
				),
			)
		);

		$element->add_control(
			'_el_popup_styles_custom',
			array(
				'label'     => __( 'Popup Styles', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::HIDDEN,
				'default'   => 'custom-popup-styles',
				'selectors' => array(
					'html.elementor-html::before' => '
						content: "";
						pointer-events: none;
						display: block;
						position: fixed; 
						top: 0; 
						left: 50%; 
						transform: translateX(-50%);
						z-index: {{_show_popup_layout.VALUE}};
						width: {{_el_popup_width.VALUE}}px; 
						height: {{_el_popup_height.VALUE}}px; 
						border: 3px dashed #8f9193;',
					'#elementor-preview #top-bar-sticky-wrapper, #elementor-preview #site-header-sticky-wrapper, #elementor-preview #footer' => 'display: none !important;',
					// '.e-con > .e-con-inner'  => '
					// max-width: {{_el_popup_width.VALUE}} !important;
					// ',
				),
				'condition' => array(
					'_el_popup_size' => 'custom',
				),
			)
		);

		$element->add_control(
			'_el_popup_overlay_enabled',
			array(
				'label'        => __( 'Enable Popup Overlay', 'ocean-ecommerce' ),
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'label_on'     => __( 'Yes', 'ocean-ecommerce' ),
				'label_off'    => __( 'No', 'ocean-ecommerce' ),
				'return_value' => '1',
				'default'      => $popup_overlay_enabled,
			)
		);

		$element->add_control(
			'_el_popup_overlay_color',
			array(
				'label'     => __( 'Popup Overlay Color', 'ocean-ecommerce' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $popup_overlay_color,
				'selectors' => array(
					'.popup-overlay' => 'background-color: {{VALUE}};',
					// 'html.elementor-html::after' => 'pointer-events: none; content: ""; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: {{VALUE}}; z-index: 99998;'
				),
				'condition' => array(
					'_el_popup_overlay_enabled' => '1',
				),
			)
		);

		$element->add_control(
			'_popup_settings_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Here you can modify Ocean Popup Builder settings.', 'ocean-ecommerce' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$element->end_controls_section();
	}

	public static function after_save( $obj ) {
		// Retrieve the page settings manager.
		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

		$page_id = $obj->get_id();

		// Retrieve the settings model for the current page.
		$page_settings_model = $page_settings_manager->get_model( $page_id );

		// Retrieve data from a custom control.
		$_el_popup_delay              = $page_settings_model->get_settings( '_el_popup_delay' );
		$_el_popup_inactivity_time    = $page_settings_model->get_settings( '_el_popup_inactivity_time' );
		$_el_popup_autoclose_delay    = $page_settings_model->get_settings( '_el_popup_autoclose_delay' );
		$_el_popup_pages              = $page_settings_model->get_settings( '_el_popup_pages' );
		$_el_exclude_pages            = $page_settings_model->get_settings( '_el_exclude_pages' );
		$_el_popup_trigger            = $page_settings_model->get_settings( '_el_popup_trigger' );
		$_el_popup_display_mode       = $page_settings_model->get_settings( '_el_popup_display_mode' );
		$_el_popup_size               = $page_settings_model->get_settings( '_el_popup_size' );
		$_el_popup_width              = $page_settings_model->get_settings( '_el_popup_width' );
		$_el_popup_height             = $page_settings_model->get_settings( '_el_popup_height' );
		$_el_popup_position           = $page_settings_model->get_settings( '_el_popup_position' );
		$_el_popup_close_button       = $page_settings_model->get_settings( '_el_popup_close_button' );
		$_el_disable_mobile           = $page_settings_model->get_settings( '_el_disable_mobile' );
		$_el_popup_overlay_enabled    = $page_settings_model->get_settings( '_el_popup_overlay_enabled' );
		$_el_popup_show_on_whole_site = $page_settings_model->get_settings( '_el_popup_show_on_whole_site' );
		$_el_popup_overlay_color      = $page_settings_model->get_settings( '_el_popup_overlay_color' );

		update_post_meta( $page_id, 'popup_delay', absint( $_el_popup_delay ) );
		update_post_meta( $page_id, 'popup_inactivity_time', absint( $_el_popup_inactivity_time ) );
		update_post_meta( $page_id, 'popup_autoclose_delay', absint( $_el_popup_autoclose_delay ) );
		update_post_meta( $page_id, 'popup_pages', ! is_array( $_el_popup_pages ) ? array( $_el_popup_pages ) : $_el_popup_pages );
		update_post_meta( $page_id, 'popup_exclude_pages', ! is_array( $_el_exclude_pages ) ? array( $_el_exclude_pages ) : $_el_exclude_pages );
		update_post_meta( $page_id, 'popup_trigger', $_el_popup_trigger );
		update_post_meta( $page_id, 'popup_display_mode', $_el_popup_display_mode );
		update_post_meta( $page_id, 'popup_size', $_el_popup_size );
		update_post_meta( $page_id, 'popup_width', $_el_popup_width );
		update_post_meta( $page_id, 'popup_height', $_el_popup_height );
		update_post_meta( $page_id, 'popup_position', $_el_popup_position );
		update_post_meta( $page_id, 'popup_close_button', $_el_popup_close_button );
		update_post_meta( $page_id, 'disable_mobile', $_el_disable_mobile === '1' );
		update_post_meta( $page_id, 'popup_close_button', $_el_popup_close_button );
		update_post_meta( $page_id, 'popup_overlay_enabled', $_el_popup_overlay_enabled === '1' );
		update_post_meta( $page_id, 'popup_show_on_whole_site', $_el_popup_show_on_whole_site === '1' );
		update_post_meta( $page_id, 'popup_overlay_color', $_el_popup_overlay_color );
		delete_transient( 'ocean_popup_builder_core_style_' . $page_id );
	}
}

Ocean_Elementor_Popup_Settings::init();
