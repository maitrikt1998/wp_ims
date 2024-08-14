<?php
namespace owpEcommerce\Modules\PixelTracker;

/**
 * Manages LinkedIn Insight Tag integration for event tracking.
 */
class OPD_LinkedIn_InsightTag {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_LinkedIn_InsightTag|null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_LinkedIn_InsightTag
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Sets up hooks for LinkedIn Insight Tag integration.
	 */
	public function __construct() {
		if ( opd_get_option( 'enable_linkedin_insight_tag', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_head', array( $this, 'output_linkedin_insight_tag' ) );
			$this->add_linkedin_insight_tag_events();
		}
	}

	/**
	 * Adds WooCommerce hooks for TikTok Pixel event tracking.
	 */
	private function add_linkedin_insight_tag_events() {

		$linkedin_contact_conversion_id = opd_get_option( 'linkedin_contact_conversion_id', 'oec_pixel_tracker', '' );
		if ( ! empty( $linkedin_contact_conversion_id ) ) {
			add_action( 'wp_footer', array( $this, 'track_contact_event' ) );
		}

		// Track Search events.
		$linkedin_search_conversion_id = opd_get_option( 'linkedin_search_conversion_id', 'oec_pixel_tracker', '' );
		if ( ! empty( $linkedin_search_conversion_id ) ) {
			add_action( 'wp_footer', array( $this, 'track_search_event' ) );
		}

		// Track ViewContent events.
		$linkedin_viewcontent_conversion_id = opd_get_option( 'linkedin_viewcontent_conversion_id', 'oec_pixel_tracker', '' );
		if ( ! empty( $linkedin_viewcontent_conversion_id ) ) {
			add_action( 'wp_footer', array( $this, 'track_viewcontent_event' ) );
		}
	}

	/**
	 * Outputs the LinkedIn Insight Tag code.
	 */
	public function output_linkedin_insight_tag() {
		$linkedin_partner_id = opd_get_option( 'linkedin_partner_id', 'oec_pixel_tracker', '0000000' );
		if ( ! empty( $linkedin_partner_id ) ) {
			echo "<!-- LinkedIn Insight Tag Code -->
            <script type='text/javascript'>
            _linkedin_partner_id = '{$linkedin_partner_id}';
            window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
            window._linkedin_data_partner_ids.push(_linkedin_partner_id);
            </script><script type='text/javascript'>
            (function(l) {
            if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
            window.lintrk.q=[]}
            var s = document.getElementsByTagName('script')[0];
            var b = document.createElement('script');
            b.type = 'text/javascript';b.async = true;
            b.src = 'https://snap.licdn.com/li.lms-analytics/insight.min.js';
            s.parentNode.insertBefore(b, s);})(window.lintrk);
            </script>
            <noscript>
            <img height='1' width='1' style='display:none;' alt='' src='https://px.ads.linkedin.com/collect/?pid={$linkedin_partner_id}&fmt=gif' />
            </noscript>
            <!-- End LinkedIn Insight Tag Code -->\n";
		}
	}

	/**
	 * Adds JavaScript to track contact events.
	 */
	public function track_contact_event() {
		$conversion_id = opd_get_option( 'linkedin_contact_conversion_id', 'oec_pixel_tracker', '' );
		?>
		<script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function() {
				// Track clicks on tel: and mailto: links
				document.querySelectorAll('a[href^="tel:"], a[href^="mailto:"]').forEach(function(link) {
					link.addEventListener('click', function() {
						lintrk('track', { conversion_id: <?php echo $conversion_id; ?> });
					});
				});

				// Track form submissions
				document.querySelectorAll('form').forEach(function(form) {
					form.addEventListener('submit', function() {
						lintrk('track', { conversion_id: <?php echo $conversion_id; ?> });
					});
				});
			});
		</script>
		<?php
	}

	/**
	 * Tracks LinkedIn Search events.
	 */
	public function track_search_event() {
		$conversion_id = opd_get_option( 'linkedin_search_conversion_id', 'oec_pixel_tracker', '' );
		?>
		<script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function() {
	
				document.querySelectorAll('form input[type="search"]').forEach(function(searchInput) {
					var form = searchInput.closest('form');
					if(form) {
						form.addEventListener('submit', function(event) {
							var searchValue = searchInput.value.trim();
							if(searchValue) {
								lintrk('track', { conversion_id: <?php echo $conversion_id; ?> });
							}
						});
					}
				});
			});
		</script>
		<?php
	}


	/**
	 * Tracks LinkedIn ViewContent events.
	 */
	public function track_viewcontent_event() {
		$conversion_id = opd_get_option( 'linkedin_viewcontent_conversion_id', 'oec_pixel_tracker', '' );
		?>
		<script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function() {
				lintrk('track', { conversion_id: <?php echo $conversion_id; ?> });
			});
		</script>
		<?php
	}
}

/**
 * Initializes and returns an instance of OPD_LinkedIn_InsightTag.
 *
 * @return OPD_LinkedIn_InsightTag
 */
function opd_linkedin_insight_tag_instance() {
	return OPD_LinkedIn_InsightTag::instance();
}

opd_linkedin_insight_tag_instance();
