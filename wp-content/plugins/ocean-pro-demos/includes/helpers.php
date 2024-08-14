<?php

/**
 * Options return
 */
function opd_get_option( $option, $section, $default = '' ) {
	$options = get_option( $section );
	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}
	return $default;
}