<?php
$ocean_images_settings = Ocean_PD_Theme_Panel::get_ocean_images_settings();
?>

<div id="ocean-images-control" class="column-wrap clr">
	<form class="integration-settings" data-settings-for="ocean_images">
		<table class="form-table">
				<tbody>
					<tr id="owp_api_images_integration_tr">
						<th scope="row">
							<label for="owp_api_images_integration"><?php esc_html_e( 'Enable Ocean Images Module', 'ocean-pro-demos' ); ?></label>
						</th>
						<td>
							<select name="owp_integrations[api_images_integration]" id="owp_api_images_integration">
								<option <?php selected( $ocean_images_settings['api_images_integration'], '0', true ); ?> value="0">
									<?php esc_html_e( 'Disable', 'ocean-pro-demos' ); ?>
								</option>
								<option <?php selected( $ocean_images_settings['api_images_integration'], '1', true ); ?> value="1">
									<?php esc_html_e( 'Enable', 'ocean-pro-demos' ); ?>
								</option>							
							</select>
						</td>
					</tr>
					<tr id="owp_api_images_integration_server_tr">
						<th scope="row">
							<label for="owp_api_images_integration_server"><?php esc_html_e( 'Server', 'ocean-pro-demos' ); ?></label>
						</th>
						<td>
							<select name="owp_integrations[api_images_integration_server]" id="owp_api_images_integration_server">
								<option <?php selected( $ocean_images_settings['api_images_integration_server'], 'main', true ); ?> value="main">
									<?php esc_html_e( 'Main', 'ocean-pro-demos' ); ?>
								</option>
								<option <?php selected( $ocean_images_settings['api_images_integration_server'], 'alternative', true ); ?> value="alternative">
									<?php esc_html_e( 'Alternative', 'ocean-pro-demos' ); ?>
								</option>							
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="form-table api-ingegrations">
			<tbody>
				<tr id="owp_flaticon_integration_tr">
					<th scope="row">
						<label for="owp_flaticon_integration"><?php esc_html_e( 'Enable Flaticon', 'ocean-pro-demos' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[flaticon_integration]" id="owp_flaticon_integration">
							<option <?php selected( $ocean_images_settings['flaticon_integration'], '0', true ); ?> value="0">
								<?php esc_html_e( 'Disable', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['flaticon_integration'], '1', true ); ?> value="1">
								<?php esc_html_e( 'Enable', 'ocean-pro-demos' ); ?>
							</option>
						</select>
					</td>
				</tr>
				<tr id="owp_flaticon_image_width_tr">
					<th scope="row">
						<label for="owp_flaticon_image_width"><?php esc_html_e( 'Flaticon Image Width', 'ocean-pro-demos' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[flaticon_image_width]" id="owp_flaticon_image_width">
							<option <?php selected( $ocean_images_settings['flaticon_image_width'], '16', true ); ?> value="16">
								<?php esc_html_e( '16px', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['flaticon_image_width'], '24', true ); ?> value="24">
								<?php esc_html_e( '24px', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['flaticon_image_width'], '32', true ); ?> value="32">
								<?php esc_html_e( '32px', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['flaticon_image_width'], '64', true ); ?> value="64">
								<?php esc_html_e( '64px', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['flaticon_image_width'], '128', true ); ?> value="128">
								<?php esc_html_e( '128px', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['flaticon_image_width'], '256', true ); ?> value="256">
								<?php esc_html_e( '256px', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( empty( $ocean_images_settings['flaticon_image_width'] ) || $ocean_images_settings['flaticon_image_width'] == '512' ); ?> value="512">
								<?php esc_html_e( '512px', 'ocean-pro-demos' ); ?>
							</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table api-ingegrations">
			<tbody>
				<tr id="owp_freepik_integration_tr">
					<th scope="row">
						<label for="owp_freepik_integration"><?php esc_html_e( 'Enable Freepik', 'ocean-pro-demos' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[freepik_integration]" id="owp_freepik_integration">
							<option <?php selected( $ocean_images_settings['freepik_integration'], '0', true ); ?> value="0">
								<?php esc_html_e( 'Disable', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_integration'], '1', true ); ?> value="1">
								<?php esc_html_e( 'Enable', 'ocean-pro-demos' ); ?>
							</option>
						</select>
					</td>
				</tr>
				<tr id="owp_freepik_image_width_tr">
					<th scope="row">
						<label for="owp_freepik_image_width"><?php esc_html_e( 'Freepik Image Width', 'ocean-pro-demos' ); ?></label>
					</th>
					<td>
						<select name="owp_integrations[freepik_image_width]" id="owp_freepik_image_width">
							<option <?php selected( $ocean_images_settings['freepik_image_width'], 'normal', true ); ?> value="normal">
								<?php esc_html_e( 'Normal', 'ocean-pro-demos' ); ?>
							</option>
							<option <?php selected( $ocean_images_settings['freepik_image_width'], 'hd', true ); ?> value="hd">
								<?php esc_html_e( 'HD', 'ocean-pro-demos' ); ?>
							</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
