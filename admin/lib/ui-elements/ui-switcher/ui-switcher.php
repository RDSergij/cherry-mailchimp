<?php
/**
 * Class for the building ui swither elements .
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'UI_Switcher2' ) ) {

	/**
	 * Class UI_Switcher generic ui-element switcher
	 */
	class UI_Switcher2 {

		/**
		 * Switcher settings
		 *
		 * @var array
		 */
		private $settings = array();

		/**
		 * Default settings
		 *
		 * @var array
		 */
		private $defaults_settings = array(
			'id'				=> 'cherry-ui-swither-id',
			'name'				=> 'cherry-ui-swither-name',
			'value'				=> 'true',
			'toggle'			=> array(
				'true_toggle'	=> 'On',
				'false_toggle'	=> 'Off',
				'true_slave'	=> '',
				'false_slave'	=> '',
			),
			'style'				=> 'normal',
			'class'				=> '',
		);

		/**
		 * Constructor method for the UI_Switcher class.
		 *
		 * @since  4.0.0
		 */
		function __construct( $args = array() ) {
			$this->defaults_settings['id'] = 'cherry-ui-swither-' . uniqid();
			$this->settings = wp_parse_args( $args, $this->defaults_settings );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
			self::enqueue_assets();
		}

		/**
		 * Render html UI_Switcher.
		 *
		 * @since  4.0.0
		 */
		public function render() {
			$data_attr_line = ( ! empty( $this->settings['toggle']['true_slave'] ) ) ? 'data-true-slave="' . $this->settings['toggle']['true_slave'] . '"' : '';
			$data_attr_line .= ( ! empty( $this->settings['toggle']['false_slave'] ) ) ? ' data-false-slave="' . $this->settings['toggle']['false_slave'] . '"' : '';

			$html = '';
			$html .= '<div class="cherry-switcher-wrap size-' . $this->settings['style'] . ' ' . $this->settings['class'] . '">';
				$html .= '<label class="sw-enable"><span>' . $this->settings['toggle']['true_toggle'] . '</span></label>';
				$html .= '<label class="sw-disable"><span>' . $this->settings['toggle']['false_toggle']  . '</span></label>';
				$html .= '<input id="' . $this->settings['id'] . '" type="hidden" class="cherry-input-switcher" name="' . $this->settings['name'] . '" ' . checked( 'true', $this->settings['value'], false ) . ' value="' . esc_html( $this->settings['value'] ) . '" ' . $data_attr_line . '>';
			$html .= '</div>';

			return $html;
		}

		/**
		 * Get current file URL
		 *
		 * @since  4.0.0
		 */
		public static function get_current_file_url() {
			$assets_url = dirname( __FILE__ );
			$site_url = site_url();
			$assets_url = str_replace( untrailingslashit( ABSPATH ), $site_url, $assets_url );
			$assets_url = str_replace( '\\', '/', $assets_url );

			return $assets_url;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Switcher.
		 *
		 * @since  4.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_script(
				'ui-switcher-min',
				self::get_current_file_url() . '/assets/ui-switcher.js',
				array( 'jquery' ),
				'1.0.0',
				true
			);
			wp_enqueue_style(
				'ui-switcher-min',
				self::get_current_file_url() . '/assets/min/ui-switcher.min.css',
				array(),
				'1.0.0',
				'all'
			);
		}
	}
}
