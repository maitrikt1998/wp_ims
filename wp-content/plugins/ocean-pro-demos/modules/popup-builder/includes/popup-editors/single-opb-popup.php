<?php
get_header();
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		echo '<div id="opb-popup-content">';
		the_content();
		echo '</div>';
	}
}
get_footer();
?>

<style>	
	html.elementor-html .page-header,
	html.elementor-html #top-bar-wrap,
	html.elementor-html #top-bar-sticky-wrapper,
	html.elementor-html #site-header-sticky-wrapper,
	html.elementor-html #footer{
		display: none !important
	};
</style>
