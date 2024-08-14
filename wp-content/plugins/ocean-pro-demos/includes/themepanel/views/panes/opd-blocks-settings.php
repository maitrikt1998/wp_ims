<?php
$opd_popup_builder_enable = get_option( 'opd_popup_builder_enable', 'no' );
$opd_cpt_manager_enable   = get_option( 'opd_cpt_manager_enable', 'no' );

?>

<div class="oceanwp-tp-pane-box" id="oceanwp-tp-opd-settings">

	<?php require_once oceanwp_theme_panel()->panel_top_header(); ?>

	<div class="oceanwp-tp-wide-block">

		<div class="oceanwp-tp-block-outer">
			<img class="oceanwp-tp-wide-block-image" src="<?php echo esc_url( OPD_URL . 'includes/themepanel/assets/images/misc-pro-features.png' ); ?>" />
			<h2 class="oceanwp-tp-block-title"><?php esc_html_e( 'Misc Pro Features', 'oceanwp' ); ?></h2>
		</div>

		<div class="oceanwp-tp-wide-block">
			<div class="oceanwp-tp-block-outer">
				<img class="oceanwp-tp-wide-block-image" src="<?php echo esc_url( OPD_URL . 'includes/themepanel/assets/images/popup-builder.png' ); ?>" />
				<h2 class="oceanwp-tp-block-title"><?php esc_html_e( 'Popup Builder', 'oceanwp' ); ?></h2>
			</div>
			<h3 class="oceanwp-tp-block-description"><?php echo esc_html__( 'Enable Popup Builder', 'ocean-pro-demos' ); ?></h3>
			<div id="ocean-popup-builder-enable" class="oceanwp-tp-switcher column-wrap">
				<label for="oceanwp-switch-popup-builder-enable" class="column-name">
					<input type="checkbox" role="checkbox" name="popup-builder-enable" value="true" id="oceanwp-switch-popup-builder-enable" <?php checked( $opd_popup_builder_enable === 'yes' ); ?> />
					<span class="slider round"></span>
				</label>
			</div>

			<?php if ( is_plugin_active( 'ocean-ecommerce/ocean-ecommerce.php' ) ) : ?>
				<div class="ocean-ecommerce-text-block" data-setting-id="opd-popup-builder-enable" style="
				<?php
				if ( $opd_popup_builder_enable == false ) :
					?>
							display: none;<?php endif; ?>">
					<div class="ocean-ecommerce-widget-description">
						<p class="ocean-ecommerce-tp-block-description warning"><?php echo esc_html__( 'Hi there! We noticed you have the Ocean eComm Treasure Box installed and active. To simplify things for you and keep your user-experience at the highest level, we are encouraging you to access this feature through the Ocean eComm Treasure Box dashboard. Find more information about ', 'ocean-pro-demos' ); ?><a href="https://docs.oceanwp.org/article/884-how-to-use-the-popup-builder" target="_blank"><?php echo esc_html__( 'how to use the Popup Builder within our documentation.', 'ocean-pro-demos' ); ?></a></p>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div class="oceanwp-tp-wide-block">
			<div class="oceanwp-tp-block-outer">
				<img class="oceanwp-tp-wide-block-image" src="<?php echo esc_url( OPD_URL . 'includes/themepanel/assets/images/cpt-manager.png' ); ?>" />
				<h2 class="oceanwp-tp-block-title"><?php esc_html_e( 'CPT Manager', 'oceanwp' ); ?></h2>
			</div>
			<h3 class="oceanwp-tp-block-description"><?php echo esc_html__( 'Enable CPT Manager', 'ocean-pro-demos' ); ?></h3>
			<div id="ocean-cpt-manager-enable" class="oceanwp-tp-switcher column-wrap">
				<label for="oceanwp-switch-cpt-manager-enable" class="column-name">
					<input type="checkbox" role="checkbox" name="cpt-manager-enable" value="true" id="oceanwp-switch-cpt-manager-enable" <?php checked( $opd_cpt_manager_enable === 'yes' ); ?> />
					<span class="slider round"></span>
				</label>
			</div>
		</div>


	</div>
</div>
