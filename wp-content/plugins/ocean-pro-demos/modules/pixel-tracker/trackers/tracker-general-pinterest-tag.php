<?php

/**
 * Manages Pinterest Pixel integration for WooCommerce events.
 */
class OPD_Pinterest_PixelTracker {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_Pinterest_PixelTracker |null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_Pinterest_PixelTracker
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Sets up hooks for Pinterest Pixel integration.
	 */
	public function __construct() {
		if ( opd_get_option( 'enable_pinterest_tag', 'oec_pixel_tracker', 'off' ) === 'on' ) {
						add_action( 'wp_head', array( $this, 'output_pinterest_pixel' ) );
			$this->add_pinterest_tag_events();
		}
	}

	/**
	 * Adds WooCommerce hooks for Pinterest Pixel event tracking.
	 */
	private function add_pinterest_tag_events() {
		if ( opd_get_option( 'pinterest_tag_track_page_visit', 'oec_pixel_tracker', 'off' ) === 'on' ) {
						add_action( 'wp_head', array( $this, 'track_page_visit_event' ) );
		}
		if ( opd_get_option( 'pinterest_tag_track_search', 'oec_pixel_tracker', 'off' ) === 'on' ) {
						add_action( 'wp_footer', array( $this, 'track_search_event' ) );
		}
	}


	/**
	 * Outputs the Pinterest Pixel code.
	 */
	public function output_pinterest_pixel() {
		$pinterest_tag_id = opd_get_option( 'pinterest_tag_id', 'oec_pixel_tracker', '' );
		if ( ! empty( $pinterest_tag_id ) ) {
			echo "<!-- Pinterest Tag -->
            <script>
            !function(e){if(!window.pintrk){window.pintrk = function () {
            window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
            n=window.pintrk;n.queue=[],n.version=\"3.0\";var
            t=document.createElement(\"script\");t.async=!0,t.src=e;var
            r=document.getElementsByTagName(\"script\")[0];
            r.parentNode.insertBefore(t,r)}}(\"https://s.pinimg.com/ct/core.js\");
            pintrk('load', '{$pinterest_tag_id}');
            pintrk('page');
            </script>
            <noscript>
            <img height=\"1\" width=\"1\" style=\"display:none;\" alt=\"\"
            src=\"https://ct.pinterest.com/v3/?event=init&tid={$pinterest_tag_id}&noscript=1\" />
            </noscript>
            <!-- end Pinterest Tag -->\n";
		}
	}


	/**
	 * Outputs JavaScript to track page views for Pinterest Pixel.
	 */
	public function track_page_visit_event() {
		echo "<script>
            pintrk('track', 'pagevisit');
        </script>";
	}

	/**
	 * Outputs JavaScript to track search events for Pinterest Pixel.
	 */
	public function track_search_event() {
		echo "<script>
        document.querySelectorAll('form input[type=\"search\"]').forEach(function(searchInput) {
            var form = searchInput.closest('form');
            if(form) {
                form.addEventListener('submit', function() {
                    var searchValue = searchInput.value.trim();
                    if(searchValue) {
                        pintrk('track', 'search', {
                            search_query: searchValue
                        });
                    }
                });
            }
        });
        </script>";
	}
}

/**
 * Initializes and returns an instance of OPD_Pinterest_PixelTracker.
 *
 * @return OPD_Pinterest_PixelTracker
 */
function opd_pinterest_pixel_tracker_general_instance() {
	return OPD_Pinterest_PixelTracker::instance();
}

opd_pinterest_pixel_tracker_general_instance();
