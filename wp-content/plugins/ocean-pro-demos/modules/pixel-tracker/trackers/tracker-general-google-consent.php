<?php

/**
 * Class OPD_GTM_Consent
 * 
 */

class OPD_GTM_Consent {

	/**
	 * Holds the singleton instance.
	 *
	 * @var OPD_GTM_Consent|null
	 */
	private static $instance = null;

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @return OPD_GTM_Consent
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * OPD_GTM_Consent constructor.
	 * Sets up hooks for GTM integration.
	 */
	public function __construct() {
		if ( opd_get_option( 'enable_google_tag_manager', 'oec_pixel_tracker', 'off' ) === 'on' ) {
			add_action( 'wp_head', array( $this, 'output_google_consent_mode_script' ), 20 );
			add_action( 'wp_head', array( $this, 'inject_custom_styles' ) );
			add_action( 'wp_footer', array( $this, 'inject_consent_ui' ) );
		}
	}

		/**
		 * Injects custom styles for the consent banner.
		 */
	public function inject_custom_styles() {
		$enable_google_consent = opd_get_option( 'enable_google_consent', 'oec_pixel_tracker', 'off' );
		if ( $enable_google_consent !== 'on' ) {
			return;
		}
		echo '<style>
            .consent-banner {
				left: 0;
				border-radius: 0 4px 0 0;
				padding: 30px;
				width: 350px;
				max-width: 100%;
				box-shadow: 0 20px 150px rgba(0,0,0,.1);
				padding: 30px 20px;
				position: fixed;
				bottom: 0;
				background-color: #2e3243;
				color: #fff;
				font-size: 14px;
				z-index: 999999;
			}
			.consent-button-wrapper {
				display: flex;
				justify-content: space-around;
			}
            .consent-button-wrapper button.button {
                margin: 5px;
                padding: 10px 20px;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
				min-width: 150px;
            }
            .consent-button-wrapper button.deny {
                background-color: #dc3545;
            }
        </style>';
	}

	 /**
     * Outputs the consent banner HTML.
     */
    public function render_consent_banner() {
        echo "<div class='consent-banner'>
            <p>" . __('This site uses cookies and other tracking technologies to assist with navigation, analyze your use of our services, and provide content from third parties. ', 'ocean-ecommerce') . "</p>
            <p>" . __('By clicking "Accept" you agree to our use of cookies and other tracking technologies in accordance with our Privacy Policy.', 'ocean-ecommerce') . "</p>
            <div class='consent-button-wrapper'>
                <button id='acceptConsentButton' class='button allow'>" . __('Accept', 'ocean-ecommerce') . "</button>
                <button id='denyConsentButton' class='button deny'>" . __('Deny', 'ocean-ecommerce') . "</button>
            </div>
        </div>";
    }

	/**
	 * Outputs the consent banner HTML and JavaScript for handling interactions.
	 */
	public function inject_consent_ui() {
		$enable_google_consent = opd_get_option( 'enable_google_consent', 'oec_pixel_tracker', 'off' );
		if ( $enable_google_consent !== 'on' ) {
			return;
		}
		// Check if consent has already been given or denied to decide if the banner should be shown.
		$consent_given = isset( $_COOKIE['consent_given'] ) ? $_COOKIE['consent_given'] : 'false';

		if ( $consent_given === 'false' ) {
			$this->render_consent_banner();
			echo "
			<script>
				document.addEventListener('DOMContentLoaded', function() {
					function setConsent(consentState) {
						localStorage.setItem('google_adsConsent', consentState);
						localStorage.setItem('google_analyticsConsent', consentState);
						localStorage.setItem('google_ad_user_data', consentState);
						localStorage.setItem('google_ad_personalization', consentState);
						localStorage.setItem('google_functionality_storage', consentState);
						localStorage.setItem('google_personalization_storage', consentState);
						localStorage.setItem('google_security_storage', consentState);
						document.cookie = 'consent_given=' + consentState + '; path=/';
						location.reload();
					}

					document.getElementById('acceptConsentButton').addEventListener('click', function() {
						setConsent('granted');
					});

					document.getElementById('denyConsentButton').addEventListener('click', function() {
						setConsent('denied');
					});
				});
			</script>";
		}
	}

	/**
	 * Outputs the Google Consent Mode script in the head of the document.
	 */

	public function output_google_consent_mode_script() {
		$enable_google_consent = opd_get_option( 'enable_google_consent', 'oec_pixel_tracker', 'off' );
		if ( $enable_google_consent !== 'on' ) {
			return;
		}

		echo "<script>
			window.dataLayer = window.dataLayer || [];
			function gtag() { dataLayer.push(arguments); }

			// Read consent settings from localStorage and log each consent state
			function getConsentState(consentName, defaultValue) {
				var consentValue = localStorage.getItem(consentName);
				var state = consentValue === 'granted' ? 'granted' : (consentValue === 'denied' ? 'denied' : defaultValue);
				return state;
			}

			gtag('consent', 'default', {
				'ad_storage': getConsentState('google_adsConsent', 'default'),
				'analytics_storage': getConsentState('google_analyticsConsent', 'default'),
				'ad_user_data': getConsentState('google_ad_user_data', 'default'),
				'ad_personalization': getConsentState('google_ad_personalization', 'default'),
				'functionality_storage': getConsentState('google_functionality_storage', 'default'),
				'personalization_storage': getConsentState('google_personalization_storage', 'default'),
				'security_storage': getConsentState('google_security_storage', 'default'),
			});
		</script>";
	}
}

/**
 * Initializes and returns an instance of OPD_GTM_Consent.
 *
 * @return OPD_GTM_Consent
 */
function oec_gtm_consent_instance() {
	return OPD_GTM_Consent::instance();
}


oec_gtm_consent_instance();
