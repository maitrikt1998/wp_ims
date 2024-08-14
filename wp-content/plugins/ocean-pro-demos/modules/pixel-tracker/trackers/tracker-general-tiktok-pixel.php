<?php

/**
 * Manages TikTok Pixel integration for WooCommerce events.
 */
class OPD_TikTok_PixelTracker {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_TikTok_PixelTracker |null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_TikTok_PixelTracker
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Sets up hooks for TikTok Pixel integration.
	 */
	public function __construct() {
		if ( opd_get_option( 'enable_tiktok_pixel', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_head', array( $this, 'output_tiktok_pixel' ) );
			$this->add_tiktok_pixel_events();
		}
	}

	/**
	 * Adds WooCommerce hooks for TikTok Pixel event tracking.
	 */
	private function add_tiktok_pixel_events() {
		// View Content events.
		if ( opd_get_option( 'tiktok_pixel_track_view_content_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_footer', array( $this, 'tiktok_pixel_track_view_content' ) );
		}

		if ( opd_get_option( 'tiktok_pixel_track_search_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_footer', array( $this, 'tiktok_pixel_track_search_event' ) );
		}
		if ( opd_get_option( 'tiktok_pixel_track_contact_event', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_footer', array( $this, 'tiktok_pixel_track_contact_event' ) );
		}
	}


	/**
	 * Outputs the TikTok Pixel code.
	 */
	public function output_tiktok_pixel() {
		$tiktok_pixel_id = opd_get_option( 'tiktok_pixel_id', 'oec_pixel_tracker', 'CNKXXXXX00X0XX0XXX00' );
		if ( ! empty( $tiktok_pixel_id ) ) {
			echo "<!-- TikTok Pixel Code -->
            <script>
			
            !function(w,d,t){
                w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page', 'track', 'identify', 'instances', 'debug', 'on', 'off', 'once', 'ready', 'alias', 'group', 'enableCookie', 'disableCookie'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){var e=ttq._i=ttq._i||{};return e[t]=e[t]||[],e[t].ttq=ttq,ttq},ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{},ttq._i[e]=ttq._i[e]||{},ttq._i[e].onload=n;var o=d.createElement('script');o.type='text/javascript',o.async=!0,o.src=i+'?sdkid='+e+'&lib='+t;var a=d.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)};
				ttq.load('{$tiktok_pixel_id}');
                ttq.page();
            }(window, document, 'ttq');
            </script>
            <!-- End TikTok Pixel Code -->\n";
		}
	}


	/**
	 * Outputs JavaScript to track page views for TikTok Pixel.
	 */
	public function tiktok_pixel_track_view_content() {
		if ( is_singular() ) {
			$post_id      = get_the_ID();
			$post_type    = get_post_type( $post_id );
			$content_type = 'product';

			if ( $post_type === 'product' ) {
				$content_type = $post_type;
			}

			$content_ids = json_encode( array( $post_id ) );
		} else {

			$content_ids  = json_encode( array() );
			$content_type = 'product';
		}

		echo <<<EOT
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				ttq.track('ViewContent', {
					content_ids: {$content_ids},
					content_type: '{$content_type}'
				});
			});
		</script>
		EOT;
	}




	public function tiktok_pixel_track_search_event() {
		echo "<script>
            document.querySelectorAll('form input[type=\"search\"]').forEach(function(searchInput) {
                var form = searchInput.closest('form');
                if(form) {
                    form.addEventListener('submit', function() {
                        var searchValue = searchInput.value.trim();
                        if(searchValue) {
                            ttq.track('Search', {
                                contents: [{ query: searchValue }],
                                content_type: 'search_query'
                            });
                        }
                    });
                }
            });
        </script>";
	}

	public function tiktok_pixel_track_contact_event() {
		echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('a[href^=\"tel:\"], a[href^=\"mailto:\"]').forEach(function(link) {
                    link.addEventListener('click', function() {
                        ttq.track('Contact');
                    });
                });

                document.querySelectorAll('form').forEach(function(form) {
                    form.addEventListener('submit', function() {
                        ttq.track('Contact');
                    });
                });
            });
        </script>";
	}
}

/**
 * Initializes and returns an instance of OPD_TikTok_PixelTracker.
 *
 * @return OPD_TikTok_PixelTracker
 */
function opd_tiktok_pixel_tracker_general_instance() {
	return OPD_TikTok_PixelTracker::instance();
}

opd_tiktok_pixel_tracker_general_instance();
