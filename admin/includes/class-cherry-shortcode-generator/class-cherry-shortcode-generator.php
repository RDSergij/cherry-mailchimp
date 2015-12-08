<?php
/**
 * Independente shortcode generator class.
 *
 * Instructions:
 * 1. This class MUST be included only on page where you wanna to add 'generate shortcode' button
 * 2. You need to pass 3 required variables - $base_dir, $base_url and $plugin - path and URL to class-cherry-shortcode-generator in your plugin and current plugin slug, without it this class useless
 * 3. Recommended to include and initalize this class on 'admin_init' hook with current page checking
 * 4. On page, where you need to show generate shortcode button, add next code <?php do_action( 'cherry_shortcode_generator_buttons' ); ?>
 * 5. Register shortcode for generator with 'cherry_shortcode_generator_register' filter
 *
 * @package   Cherry Shortcode Generator
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Shortcode_Generator' ) ) {

	/**
	 * Define shortcode generator class
	 */
	class Cherry_Shortcode_Generator {

		/**
		 * Path to 'class-cherry-shortcode-generator' folder
		 *
		 * @var string
		 */
		private $base_dir = null;

		/**
		 * URL to 'class-cherry-shortcode-generator' folder
		 *
		 * @var string
		 */
		private $base_url = null;

		/**
		 * Current plugin name
		 *
		 * @var string
		 */
		public $plugin = null;

		/**
		 * Define avaliable shortocde controls array
		 * @var array
		 */
		private $controls = array();

		/**
		 * Constructor for the class
		 *
		 * @param  string $base_dir path to 'class-cherry-shortcode-generator' folder.
		 * @param  string $base_url URL to 'class-cherry-shortcode-generator' folder.
		 * @param  string $plugin   current pluin name to pass registering context.
		 * @return void|null
		 */
		function __construct( $base_dir = null, $base_url = null, $plugin = null ) {

			if ( ! $base_dir || ! $base_url || ! $plugin ) {
				add_action( 'admin_notices', array( $this, 'show_usage_error_notice' ) );
				return;
			}

			$this->base_dir = $base_dir;
			$this->base_url = $base_url;
			$this->plugin   = $plugin;

			add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
			add_action( 'cherry_shortcode_generator_buttons', array( $this, 'print_buttons' ) );

		}

		/**
		 * Show class usage error in admin area
		 *
		 * @return void
		 */
		public function show_usage_error_notice() {

			// Service notice for developers, no need to translate it.
			printf(
				'<div class="error"><p>%s</p></div>',
				'Please, provide $base_url, $base_path and $plugin parameters to use Cherry_Shortcode_Generator class'
			);
		}

		/**
		 * Get path inside of class-cherry-shortcode-generator
		 *
		 * @param  string $path path to file/dir inside of 'class-cherry-shortcode-generator'.
		 * @return string
		 */
		private function get_path( $path = null ) {

			if ( null !== $path ) {
				return trailingslashit( $this->base_dir ) . $path;
			}

			return trailingslashit( $this->base_dir );

		}

		/**
		 * Get url to file/dir inside of class-cherry-shortcode-generator
		 *
		 * @param  string $path path to file/dir inside of 'class-cherry-shortcode-generator'.
		 * @return string
		 */
		private function get_url( $path = null ) {

			if ( null !== $path ) {
				return esc_url( trailingslashit( $this->base_url ) . $path );
			}

			return esc_url( trailingslashit( $this->base_url ) );

		}

		/**
		 * Register required assets
		 *
		 * @return void
		 */
		public function assets() {
			// Enqueue style allways, when class called
			wp_enqueue_style(
				'cherry-shortcode-generator',
				$this->get_url( 'assets/css/shortode-generator.css' ),
				array(),
				'1.0.0'
			);

			// Scripts only registered and called, when insert shortcode button is printed
			wp_register_script(
				'cherry-shortcode-generator',
				$this->get_url( 'assets/js/min/shortode-generator.min.js' ),
				array( 'cherry-magnific-popup' ),
				'1.0.0',
				true
			);

			wp_register_script(
				'cherry-magnific-popup',
				$this->get_url( 'assets/js/min/jquery.magnific-popup.min.js' ),
				array( 'jquery' ),
				'1.0.0',
				true
			);

			wp_dequeue_style( 'yit-plugin-style' );
			wp_dequeue_style( 'woocommerce_admin_styles' );

		}

		/**
		 * Print generate shortcode buttons and popups for registered shortcodes.
		 *
		 * @return void|null
		 */
		public function print_buttons() {

			/**
			 * Register shortcodes for generator
			 *
			 * @var   array  registered shortcodes
			 * @param string $this->plugin current plugin context.
			 */
			$shortcodes = apply_filters( 'cherry_shortcode_generator_register', array(), $this->plugin );

			if ( empty( $shortcodes ) ) {
				return;
			}

			foreach ( $shortcodes as $shortcode ) {
				$this->print_shortcode_button( $shortcode );
			}

			//do_action( 'admin_enqueue_scripts' );
			$this->assets();
			wp_enqueue_script( 'cherry-shortcode-generator' );
		}

		/**
		 * Print button and generator popup for passed shortcode
		 *
		 * @param  array $shortcode registered shortcode params.
		 * @return void
		 */
		public function print_shortcode_button( $shortcode ) {

			$shortcode = wp_parse_args( $shortcode, array(
				'slug' => 'shortcode',
				'name' => __( 'Shortcode', $this->plugin ),
				'desc' => __( 'Shortcode sescription', $this->plugin ),
				'type' => 'single',
				'atts' => array(),
			) );

			$label  = sprintf( __( 'Generate %s shortcode', $this->plugin ), '<b>' . $shortcode['name'] . '</b>' );
			$button = sprintf(
				'<a href="#sg-%2$s" class="button button-primary_ cherry-sg-open" >%1$s</a>',
				$label, $shortcode['slug']
			);

			$popup = $this->get_shortcode_popup( $shortcode );

			printf( '<div class="cherry-sg-box">%1$s%2$s</div>', $button, $popup );

		}

		/**
		 * Get shortcode popup
		 *
		 * @param  array $shortcode shortcode data.
		 * @return void
		 */
		public function get_shortcode_popup( $shortcode ) {

			$fields    = $this->get_shortcode_fields( $shortcode['atts'] );
			$input     = $this->get_shortcode_input();
			$data_atts = array(
				'input_mask' => $this->plugin,
				'shortcode'  => $shortcode['slug'],
				'type'       => $shortcode['type'],
			);

			$data_atts = $this->prepare_data_atts_string( $data_atts );

			printf(
				'<div class="cherry-sg-popup white-popup-block mfp-hide" id="sg-%3$s" %4$s><form class="cherry-sg-popup_fields">%1$s</form>%2$s</div>',
				$fields, $input, $shortcode['slug'], $data_atts
			);

		}

		/**
		 * Preapre data attributes string from attributes array
		 *
		 * @param  array $atts attributes array.
		 * @return string
		 */
		public function prepare_data_atts_string( $atts = array() ) {

			$result = '';
			foreach ( $atts as $key => $value ) {
				$result .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}

			return $result;

		}

		/**
		 * Get shortcode input HTML
		 *
		 * @return string|null
		 */
		public function get_shortcode_input() {

			if ( ! file_exists( $this->get_path( 'ui/ui-textarea/ui-textarea.php' ) ) ) {
				return null;
			}

			require_once $this->get_path( 'ui/ui-textarea/ui-textarea.php' );
			$ui_control = new UI_Textarea( array(
				'id'    => 'generated-shortcode',
				'name'  => 'generated-shortcode',
				'label' => __( 'Generated shortcode:', $this->plugin ),
				'rows'  => 4,
			) );

			return sprintf( '<div class="cherry-sg-shortcode">%s</div>', $ui_control->render() );

		}

		/**
		 * Get current shortcode control fields
		 *
		 * @param  array $attributes shortode attributes array.
		 * @return sting|null
		 */
		public function get_shortcode_fields( $attributes ) {

			if ( empty( $attributes ) ) {
				return null;
			}

			$result      = '';
			$item_format = '<div class="cherry-sg-item">%s</div>';
			$defaults    = array(
				'type'        => 'text',
				'id'          => 'cherry-ui-input-id',
				'name'        => 'cherry-ui-input-name',
				'value'       => '',
				'placeholder' => '',
				'label'       => '',
				'class'       => '',
			);

			foreach ( $attributes as $attr ) {

				$attr = wp_parse_args( $attr, $defaults );

				$attr['id'] = $this->plugin . '_' . $attr['id'];

				$dir   = 'ui-' . str_replace( '_', '-', $attr['type'] );
				$file  = $dir . '.php';
				$class = 'UI_' . str_replace( ' ', '-', ucwords( str_replace( '_', ' ', $attr['type'] ) ) );

				if ( ! file_exists( $this->get_path( 'ui/' . $dir . '/' . $file ) ) ) {
					$result .= sprintf( $item_format, 'Can`t find file with UI control for ' . $attr['name'] );
				}

				require_once $this->get_path( 'ui/' . $dir . '/' . $file );
				$ui_control = new $class( $attr );

				$result .= sprintf( $item_format, $ui_control->render() );

			}

			return $result;

		}
	}

}
