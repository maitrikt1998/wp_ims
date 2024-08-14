<?php

/**
 * Manages Facebook Pixel integration for WooCommerce events.
 */
class OPD_Facebook_PixelTracker_General {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_Facebook_PixelTracker_General|null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_Facebook_PixelTracker_General
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Sets up hooks for Facebook Pixel integration.
	 */
	public function __construct() {
		if ( opd_get_option( 'enable_facebook_pixel', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			$this->add_fb_pixel_events();
		}
	}

	/**
	 * Adds WooCommerce hooks for Facebook Pixel event tracking.
	 */
	private function add_fb_pixel_events() {
		// Track search events.
		if ( opd_get_option( 'fb_track_search_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_footer', array( $this, 'fb_track_search_event' ) );
		}
		// Track view any content events.
		if ( opd_get_option( 'fb_track_view_content_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_footer', array( $this, 'track_fb_view_content' ) );
		}
		// Track view contact events.
		if ( opd_get_option( 'fb_track_contact_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_footer', array( $this, 'track_fb_contact_event' ) );
		}
	}

	/**
	 * Outputs JavaScript to track search form submissions for Facebook Pixel.
	 */
	public function fb_track_search_event() {
		?>
		<script>
			document.querySelectorAll('form input[type="search"]').forEach(function(searchInput) {
				var form = searchInput.closest('form');
				if(form) {
					form.addEventListener('submit', function() {
						// Check if the search input has a value
						var searchValue = searchInput.value.trim();
						if(searchValue) {
							// Track the search event with the search keyword
							fbq('track', 'Search', {
								search_string: searchValue
							});
						}
					});
				}
			});
		</script>
		<?php
	}

	/**
	 * Outputs JavaScript to track page views for Facebook Pixel.
	 */
	public function track_fb_view_content() {
		if ( is_singular() ) {
			$post_id      = get_the_ID();
			$post_type    = get_post_type( $post_id );
			$content_name = get_the_title( $post_id );
			$content_ids  = json_encode( array( $post_id ) );
			$content_type = 'WordPress ' . ucfirst( $post_type );
		} else {
			$content_name = wp_title( '', false );
			$content_ids  = json_encode( array() );
			$content_type = is_home() ? 'Homepage' : 'Archive Page';
		}

		echo <<<EOT
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				fbq('track', 'ViewContent', {
					content_name: '{$content_name}',
					content_ids: {$content_ids},
					content_type: '{$content_type}'
				});
			});
		</script>
		EOT;
	}

	/**
	 * Outputs JavaScript to track form submissions and clicks on tel: and mailto: links as Contact events.
	 */
	public function track_fb_contact_event() {
		?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			function handleLinkContact(event) {
				var href = event.target.href;
				if (href.startsWith('tel:') || href.startsWith('mailto:')) {
					fbq('track', 'Contact');
				}
			}

			document.querySelectorAll('a[href^="tel:"], a[href^="mailto:"]').forEach(function(link) {
				link.addEventListener('click', handleLinkContact);
			});

			function handleFormContact(event) {
				fbq('track', 'Contact');
			}

			document.querySelectorAll('form').forEach(function(form) {
				form.addEventListener('submit', handleFormContact);
			});
		});
	</script>
		<?php
	}
}

/**
 * Initializes and returns an instance of OPD_Facebook_PixelTracker_General.
 *
 * @return OPD_Facebook_PixelTracker_General
 */
function oec_facebook_pixel_tracker_general_instance() {
	return OPD_Facebook_PixelTracker_General::instance();
}

oec_facebook_pixel_tracker_general_instance();
