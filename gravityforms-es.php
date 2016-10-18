<?php
/*
Plugin Name: Gravity Forms (Spanish)
Plugin URI: http://www.closemarketing.es/servicios/wordpress-plugins/gravity-forms-es/
Description: Extends the Gravity Forms plugin and add-ons with the Spanish language

Version: 2.0
Requires at least: 3.0

Author: Closemarketing
Author URI: http://www.closemarketing.es/

Text Domain: gravityforms-es
Domain Path: /languages/

License: GPL
*/

class GravityFormsESPlugin {
	/**
	 * The plugin file
	 *
	 * @var string
	 */
	private $file;

	////////////////////////////////////////////////////////////

	/**
	 * The current langauge
	 *
	 * @var string
	 */
	private $language;

	/**
	 * Flag for the dutch langauge, true if current langauge is dutch, false otherwise
	 *
	 * @var boolean
	 */
	private $is_spanish;

	////////////////////////////////////////////////////////////

	/**
	 * Construct and intialize
	 */
	public function __construct( $file ) {
		$this->file = $file;

		// Priority is set to 8, beceasu the Signature Add-On is using priority 9
		add_action( 'init', array( $this, 'init' ), 8 );

		add_filter( 'gform_admin_pre_render',       array( $this, 'gform_admin_pre_render' ) );
		add_filter( 'gform_currencies',             array( $this, 'gform_currencies' ) );
		add_filter( 'gform_address_types',          array( $this, 'gform_address_types' ) );
		add_filter( 'gform_address_display_format', array( $this, 'gform_address_display_format' ) );

		add_action( 'wp_print_scripts', array( $this, 'wp_print_scripts' ) );

		/*
		 * @since Gravity Forms v1.6.12
		 *
		 * Gravity Forms don't execute the load_plugin_textdomain() in the 'init'
		 * action, therefor we have to make sure this plugin will load first
		 *
		 * @see http://stv.whtly.com/2011/09/03/forcing-a-wordpress-plugin-to-be-loaded-before-all-other-plugins/
		 */
		add_action( 'activated_plugin', array( $this, 'activated_plugin' ) );
	}

	////////////////////////////////////////////////////////////

	/**
	 * Activated plugin
	 */
	public function activated_plugin() {
		$path = str_replace( WP_PLUGIN_DIR . '/', '', $this->file );

		if ( $plugins = get_option( 'active_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );

				update_option( 'active_plugins', $plugins );
			}
		}

		if ( $plugins = get_site_option( 'active_sitewide_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );

				update_site_option( 'active_sitewide_plugins', $plugins );
			}
		}
	}

	////////////////////////////////////////////////////////////

	/**
	 * Initialize
	 */
	public function init() {
		$rel_path = dirname( plugin_basename( $this->file ) ) . '/languages/';

		// Determine language
		if ( $this->language == null ) {
			$this->language = get_option( 'WPLANG', WPLANG );
			$this->is_spanish = ( $this->language == 'es' || $this->language == 'es_ES' );
		}

		// The ICL_LANGUAGE_CODE constant is defined from an plugin, so this constant
		// is not always defined in the first 'load_textdomain_mofile' filter call
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$this->is_spanish = ( ICL_LANGUAGE_CODE == 'es' );
		}
		// Load plugin text domain - Gravity Forms (es)
		load_plugin_textdomain( 'gravityforms-es', false, $rel_path );

		// Load plugin text domain - Gravity Forms user registration Add-On
		load_plugin_textdomain( 'gravityformsuserregistration', false, $rel_path );

		// Load plugin text domain - Gravity Forms Aweber
		load_plugin_textdomain( 'gravityformsaweber', false, $rel_path );

		// Load plugin text domain - Gravity Forms Coupons
		load_plugin_textdomain( 'gravityformscoupons', false, $rel_path );

		// Load plugin text domain - Gravity Forms Campaign Monitor
		load_plugin_textdomain( 'gravityformscampaignmonitor', false, $rel_path );

		// Load plugin text domain - Gravity Forms Fresh Books
		load_plugin_textdomain( 'gravityformsfreshbooks', false, $rel_path );

		// Load plugin text domain - Gravity Forms Mail Chimp
		load_plugin_textdomain( 'gravityformsmailchimp', false, $rel_path );

		// Load plugin text domain - Gravity Forms Paypal
		load_plugin_textdomain( 'gravityformspaypal', false, $rel_path );

		// Load plugin text domain - Gravity Forms Polls
		load_plugin_textdomain( 'gravityformspolls', false, $rel_path );

		// Load plugin text domain - Gravity Forms Quiz
		load_plugin_textdomain( 'gravityformsquiz', false, $rel_path );

		// Load plugin text domain - Gravity Forms Signature
		load_plugin_textdomain( 'gravityformssignature', false, $rel_path );

		// Load plugin text domain - Gravity Forms Slack
		load_plugin_textdomain( 'gravityformsslack', false, $rel_path );

		// Load plugin text domain - Gravity Forms Survey
		load_plugin_textdomain( 'gravityformssurvey', false, $rel_path );

		//Load Gravity Forms Strings to Wordpress translate ORG
		require( 'translations/gravityforms.php' );
		require( 'translations/gravityformscoupons.php' );
		require( 'translations/gravityformsquiz.php' );
	}

	////////////////////////////////////////////////////////////

	/**
	 * Gravity Forms translate datepicker
	 */
	public function wp_print_scripts() {
		if ( $this->is_spanish ) {
			/**
			 * gforms_ui_datepicker » @since ?
			 * gforms_datepicker » @since Gravity Forms 1.7.5
			 */
			foreach ( array( 'gforms_ui_datepicker', 'gforms_datepicker' ) as $script_datepicker ) {
				if ( wp_script_is( $script_datepicker ) ) {
					// @see http://code.google.com/p/jquery-ui/source/browse/trunk/ui/i18n/jquery.ui.datepicker-nl.js
					// @see https://github.com/jquery/jquery-ui/blob/master/ui/i18n/jquery.ui.datepicker-nl.js
					$src = plugins_url( 'js/jquery.ui.datepicker-es.js', $this->file );

					wp_enqueue_script( 'gforms_ui_datepicker_es', $src, array( $script_datepicker ), false, true );
				}
			}
		}
	}

	////////////////////////////////////////////////////////////

	/**
	 * Gravity Forms admin pre render
	 */
	public function gform_admin_pre_render( $form ) {
		wp_register_script( 'gravityforms-es-forms', plugins_url( 'js/forms-es.js', $this->file ) );

		wp_localize_script( 'gravityforms-es-forms', 'gravityFormsNlL10n', array(
			'formTitle'           => __( 'Untitled Form', 'gravityforms-es' ) ,
			'formDescription'     => __( 'We would love to hear from you! Please fill out this form and we will get in touch with you shortly.', 'gravityforms-es' ) ,
			'confirmationMessage' => __( 'Thanks for contacting us! We will get in touch with you shortly.', 'gravityforms-es' ) ,
			'buttonText'          => __( 'Submit', 'gravityforms-es' )
		) );

		wp_print_scripts( array( 'gravityforms-es-forms' ) );

		return $form;
	}

	////////////////////////////////////////////////////////////

	/**
	 * Update currency
	 *
	 * @param array $currencies
	 */
	public function gform_currencies( $currencies ) {
		$currencies['EUR'] = array(
			'name'               => __( 'Euro', 'gravityforms-es' ),
			'symbol_left'        => '€',
			'symbol_right'       => '',
			'symbol_padding'     => ' ',
			'thousand_separator' => '.',
			'decimal_separator'  => ',',
			'decimals'           => 2
		);

		return $currencies;
	}

	////////////////////////////////////////////////////////////

	/**
	 * Address types
	 *
	 * @param array $address_types
	 */
	public function gform_address_types( $address_types ) {
		// @see http://www.gravityhelp.com/forums/topic/add-custom-field-to-address-field-set
		$address_types['es'] = array(
			'label'       => __( 'Spain', 'gravityforms-es' ),
			'country'     => __( 'Spain', 'gravityforms-es' ),
			'zip_label'   => __( 'Postal Code', 'gravityforms-es' ),
			'state_label' => __( 'Province', 'gravityforms-es' ),
			'states'      => array_merge( array( '' ), self::get_spanish_provinces() )
		);

		return $address_types;
	}

	////////////////////////////////////////////////////////////

	/**
	 * Get list of Spanish provinces
	 *
	 * @return array
	 */
	public static function get_spanish_provinces() {
		return array(
			__( 'Albacete', 'gravityforms-es' ),
			__( 'Alicante', 'gravityforms-es' ),
			__( 'Almería', 'gravityforms-es' ),
			__( 'Asturias', 'gravityforms-es' ),
			__( 'Ávila', 'gravityforms-es' ),
			__( 'Badajoz', 'gravityforms-es' ),
			__( 'Barcelona', 'gravityforms-es' ),
			__( 'Burgos', 'gravityforms-es' ),
			__( 'Cáceres', 'gravityforms-es' ),
			__( 'Cádiz', 'gravityforms-es' ),
			__( 'Cantabria', 'gravityforms-es' ),
			__( 'Castellón', 'gravityforms-es' ),
			__( 'Ceuta', 'gravityforms-es' ),
			__( 'Ciudad Real', 'gravityforms-es' ),
			__( 'Córdoba', 'gravityforms-es' ),
			__( 'Coruña (La)', 'gravityforms-es' ),
			__( 'Cuenca', 'gravityforms-es' ),
			__( 'Girona', 'gravityforms-es' ),
			__( 'Granada', 'gravityforms-es' ),
			__( 'Guadalajara', 'gravityforms-es' ),
			__( 'Guipuzcoa', 'gravityforms-es' ),
			__( 'Huelva', 'gravityforms-es' ),
			__( 'Huesca', 'gravityforms-es' ),
			__( 'Islas Baleares', 'gravityforms-es' ),
			__( 'Jaén', 'gravityforms-es' ),
			__( 'León', 'gravityforms-es' ),
			__( 'Lleida', 'gravityforms-es' ),
			__( 'Lugo', 'gravityforms-es' ),
			__( 'Madrid', 'gravityforms-es' ),
			__( 'Málaga', 'gravityforms-es' ),
			__( 'Melilla', 'gravityforms-es' ),
			__( 'Murcia', 'gravityforms-es' ),
			__( 'Navarra', 'gravityforms-es' ),
			__( 'Orense', 'gravityforms-es' ),
			__( 'Palencia', 'gravityforms-es' ),
			__( 'Palmas (Las)', 'gravityforms-es' ),
			__( 'Pontevedra', 'gravityforms-es' ),
			__( 'provincia', 'gravityforms-es' ),
			__( 'Rioja (La)', 'gravityforms-es' ),
			__( 'Salamanca', 'gravityforms-es' ),
			__( 'Santa Cruz de Tenerife', 'gravityforms-es' ),
			__( 'Segovia', 'gravityforms-es' ),
			__( 'Sevilla', 'gravityforms-es' ),
			__( 'Soria', 'gravityforms-es' ),
			__( 'Tarragona', 'gravityforms-es' ),
			__( 'Teruel', 'gravityforms-es' ),
			__( 'Toledo', 'gravityforms-es' ),
			__( 'Valencia', 'gravityforms-es' ),
			__( 'Valladolid', 'gravityforms-es' ),
			__( 'Vizcaya', 'gravityforms-es' ),
			__( 'Zamora', 'gravityforms-es' ),
			__( 'Zaragoza', 'gravityforms-es' )
		);
	}

	////////////////////////////////////////////////////////////

	/**
	 * Address display format
	 *
	 * @see http://www.gravityhelp.com/documentation/page/Gform_address_display_format
	 * @param array $address_types
	 */
	public function gform_address_display_format( $format ) {
		if ( $this->is_spanish ) {
			return 'zip_before_city';
		}

		return $format;
	}
}

global $gravityforms_es_plugin;

$gravityforms_es_plugin = new GravityFormsESPlugin( __FILE__ );
