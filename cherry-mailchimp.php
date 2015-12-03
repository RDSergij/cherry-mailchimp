<?php
/**
 * Plugin Name:  Cherry MailChimp
 * Plugin URI:
 * Description: ShortCode for MailChimp
 * Version: 0.2
 * Author: Cherry
 * Author URI:
 *
 * @package Cherry_Mailchimp
 *
 * @since 1.0.0
 */


if ( ! class_exists( 'Cherry_Mailchimp_Shortcode' ) ) {
	// simple api class for MailChimp from https://github.com/drewm/mailchimp-api/blob/master/src/Drewm/MailChimp.php
	require_once( 'includes/mailchimp-api.php' );

	// shortcode frontend generation
	require_once( 'includes/cherry-mailchimp-data.php' );

	/**
	 * Set constant path to the plugin directory.
	 *
	 * @since 1.0.0
	 */
	define( 'CHERRY_MAILCHIMP_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

	/**
	 * Set constant path to the plugin URI.
	 *
	 * @since 1.0.0
	 */
	define( 'CHERRY_MAILCHIMP_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


	/**
	 * Define plugin
	 *
	 * @package Cherry_Mailchimp
	 * @since  1.0.0
	 */
	class Cherry_Mailchimp_Shortcode {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance = null;

		/**
		 * Prefix name
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public static $name = 'mailchimp';

		/**
		 * Options list of plugin
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $options = array(
				'apikey'            => '',
				'list'              => '',
				'confirm'           => '',
				'placeholder'       => '',
				'button_text'       => '',
				'success_message'   => '',
				'fail_message'      => '',
				'warning_message'   => '',
		);

		/**
		 * Sets up our actions/filters.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Register shortcode on 'init'.
			add_action( 'init', array( $this, 'register_shortcode' ) );

			// Register shortcode and add it to the dialog.
			add_filter( 'cherry_shortcodes/data/shortcodes', array( $this, 'shortcodes' ) );
			add_filter( 'cherry_templater/data/shortcodes',  array( $this, 'shortcodes' ) );

			add_filter( 'cherry_templater_target_dirs', array( $this, 'add_target_dir' ), 11 );
			add_filter( 'cherry_templater_macros_buttons', array( $this, 'add_macros_buttons' ), 11, 2 );

			// Modify mailchimp shortcode to aloow it process team
			add_filter( 'cherry_shortcodes_add_mailchimp_macros', array( $this, 'extend_mailchimp_macros' ) );
			add_filter( 'cherry-shortcode-swiper-mailchimp-postdata', array( $this, 'add_mailchimp_data' ), 10, 3 );

			$this->data = Cherry_MailChimp_Data::get_instance();

			// Create menu item
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

			// Need for submit frontend form
			add_action( 'wp_ajax_mailchimpsubscribe', array( &$this, 'subscriber_add' ) );
			add_action( 'wp_ajax_nopriv_mailchimpsubscribe', array( &$this, 'subscriber_add' ) );

			// Style to filter for Cherry Framework
			add_filter( 'cherry_compiler_static_css', array( $this, 'add_style_to_compiler' ) );

			// Language include
			add_action( 'plugins_loaded', array( $this, 'include_languages' ) );

			// Get options
			$this->get_options();
		}

		/**
		 * Load languages
		 *
		 * @since 1.0.0
		 */
		public function include_languages() {
			load_plugin_textdomain( 'cherry-mailchimp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Adds team template directory to shortcodes templater
		 *
		 * @since  1.0.0
		 * @param  array $target_dirs existing target dirs.
		 * @return array
		 */
		public function add_target_dir( $target_dirs ) {
			array_push( $target_dirs, plugin_dir_path( __FILE__ ).'/' );
			return $target_dirs;
		}

		/**
		 * Registers the [$this->name] shortcode.
		 *
		 * @since 1.0.0
		 */
		public function register_shortcode() {
			/**
			 * Filters a shortcode name.
			 *
			 * @since 1.0.0
			 * @param string $this->name Shortcode name.
			 */
			$tag = apply_filters( self::$name . '_shortcode_name', self::$name );
			add_shortcode( $tag, array( $this, 'do_shortcode' ) );
		}

		/**
		 * Filter to modify original shortcodes data and add [$this->name] shortcode.
		 *
		 * @since  1.0.0
		 * @param  array $shortcodes Original plugin shortcodes.
		 * @return array             Modified array.
		 */
		public function shortcodes( $shortcodes ) {

			$shortcodes[ self::$name ] = array(
					'name'  => __( 'MailChimp', 'cherry-mailchimp' ), // Shortcode name.
					'desc'  => __( 'MailChimp shortcode', 'cherry-mailchimp' ),
					'type'  => 'single', // Can be 'wrap' or 'single'. Example: [b]this is wrapped[/b], [this_is_single]
					'group' => 'content', // Can be 'content', 'box', 'media' or 'other'. Groups can be mixed
					'atts'  => array( // List of shortcode params (attributes).
							'button_text' => array(
									'default' => '',
									'name'    => __( 'Button', 'cherry-mailchimp' ),
									'desc'    => __( 'Enter button title', 'cherry-mailchimp' ),
							),
							'placeholder' => array(
									'default' => '',
									'name'    => __( 'Placeholder', 'cherry-mailchimp' ),
									'desc'    => __( 'Enter placeholder for email input', 'cherry-mailchimp' ),
							),
							'success_message' => array(
									'default' => '',
									'name'    => __( 'Success message', 'cherry-mailchimp' ),
									'desc'    => __( 'Enter success message', 'cherry-mailchimp' ),
							),
							'fail_message' => array(
									'default' => '',
									'name'    => __( 'Fail message', 'cherry-mailchimp' ),
									'desc'    => __( 'Enter fail message', 'cherry-mailchimp' ),
							),
							'warning_message' => array(
									'default' => '',
									'name'    => __( 'Warning message', 'cherry-mailchimp' ),
									'desc'    => __( 'Enter warning message', 'cherry-mailchimp' ),
							),
							'template' => array(
									'type'   => 'select',
									'values' => array(
											'default.tmpl' => 'default.tmpl',
									),
									'default' => 'default.tmpl',
									'name'    => __( 'Template', 'cherry-team' ),
									'desc'    => __( 'Shortcode template', 'cherry-team' ),
							),
					),
					'icon'     => 'envelope', // Custom icon (font-awesome).
					'function' => array( $this, 'do_shortcode' ), // Name of shortcode function.
			);
			return $shortcodes;
		}

		/**
		 * Pass style handle to CSS compiler.
		 *
		 * @since 1.0.0
		 * @param array $handles CSS handles to compile.
		 * @return array $handles
		 */
		function add_style_to_compiler( $handles ) {
			$handles = array_merge(
					array( 'cherry-team' => plugins_url( 'assets/css/style.css', __FILE__ ) ),
					$handles
			);
			return $handles;
		}

		/**
		 * The shortcode function.
		 *
		 * @since  1.0.0
		 * @param  array  $atts      The user-inputted arguments.
		 * @param  string $content   The enclosed content (if the shortcode is used in its enclosing form).
		 * @param  string $shortcode The shortcode tag, useful for shared callback functions.
		 * @return string
		 */
		public function do_shortcode( $atts, $content = null, $shortcode = 'mailchimp' ) {

			// Custom styles
			wp_register_style( 'simple-subscribe-style', plugins_url( 'assets/css/style.css', __FILE__ ) );
			wp_enqueue_style( 'simple-subscribe-style' );

			// Magnific popup styles
			wp_register_style( 'magnific-popup', plugins_url( 'assets/css/magnific-popup.css', __FILE__ ) );
			wp_enqueue_style( 'magnific-popup' );

			// Magnific popup scripts
			wp_register_script( 'magnific-popup', plugins_url( 'assets/js/jquery.magnific-popup.min.js', __FILE__ ) );
			wp_enqueue_script( 'magnific-popup' );

			// Custom scripts
			wp_register_script( 'mailchimp-script', plugins_url( 'assets/js/script.min.js', __FILE__ ) );
			wp_localize_script( 'mailchimp-script', 'cherryMailchimpParam', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_script( 'mailchimp-script' );

			// Set up the default arguments.
			$defaults = array(
					'button_text'       => __( 'Subscribe', 'cherry-mailchimp' ),
					'placeholder'    	=> __( 'enter your email', 'cherry-mailchimp' ),
					'success_message'   => __( 'Successfully', 'cherry-mailchimp' ),
					'fail_message'     	=> __( 'Failed', 'cherry-mailchimp' ),
					'warning_message'   => __( 'Warning!', 'cherry-mailchimp' ),
					'template'       	=> 'default.tmpl',
					'col_xs'         	=> '12',
					'col_sm'         	=> '6',
					'col_md'         	=> '3',
					'col_lg'         	=> 'none',
					'class'          	=> '',
			);
			/**
			 * Parse the arguments.
			 *
			 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
			 */
			$atts = shortcode_atts( $defaults, $atts, $shortcode );

			return $this->data->the_mailchimp( $atts );
		}

		/**
		 * Add team shortcode macros buttons to templater
		 *
		 * @since  1.0.0
		 *
		 * @param  array  $macros_buttons current buttons array.
		 * @param  string $shortcode      shortcode name.
		 * @return array
		 */
		public function add_macros_buttons( $macros_buttons, $shortcode ) {
			if ( self::$name != $shortcode ) {
				return $macros_buttons;
			}
			$macros_buttons = array(
					'placeholder' => array(
							'id'    => 'cherry_placeholder',
							'value' => __( 'Placeholder', 'cherry-mailchimp' ),
							'open'  => '%%PLACEHOLDER%%',
							'close' => '',
					),
					'button_text' => array(
							'id'    => 'cherry_button_text',
							'value' => __( 'Button text', 'cherry-mailchimp' ),
							'open'  => '%%BUTTON_TEXT%%',
							'close' => '',
					),
					'success_message' => array(
							'id'    => 'cherry_success_message',
							'value' => __( 'Success message', 'cherry-mailchimp' ),
							'open'  => '%%SUCCESS_MESSAGE%%',
							'close' => '',
					),
					'fail_message' => array(
							'id'    => 'cherry_fail_message',
							'value' => __( 'Fail message', 'cherry-mailchimp' ),
							'open'  => '%%FAIL_MESSAGE%%',
							'close' => '',
					),
					'warning_message' => array(
							'id'    => 'cherry_warning_message',
							'value' => __( 'Warning message', 'cherry-mailchimp' ),
							'open'  => '%%WARNING_MESSAGE%%',
							'close' => '',
					),
			);
			return $macros_buttons;
		}

		/**
		 * Add team macros data to process it in mailchimp shortcode
		 *
		 * @since 1.0.0
		 *
		 * @param  array $postdata default data.
		 * @param  array $post_id  processed post ID.
		 * @param  array $atts     shortcode attributes.
		 * @return array
		 */
		public function add_mailchimp_data( $postdata, $post_id, $atts ) {
			require_once( '/includes/class-cherry-mailchimp-template-callbacks.php' );
			$callbacks = new Cherry_Mailchimp_Template_Callbacks( $atts );
			$postdata['placeholder']   		= $callbacks->get_placeholder();
			$postdata['button_text']  		= $callbacks->get_button_text();
			$postdata['success_message'] 	= $callbacks->get_success_message();
			$postdata['fail_message']   	= $callbacks->get_fail_message();
			$postdata['warning_message']    = $callbacks->get_warning_message();
			return $postdata;
		}


		/**
		 * Process save
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function save_options() {
			if ( empty( $_POST['action'] ) || 'Save' != $_POST['action'] ) {
				return;
			}

			foreach ( $this->options as $option_key => $option_value ) {
				$option_value = ! empty( $_POST[ $option_key ] ) ? $_POST[ $option_key ] : '';
				update_option( self::$name . $option_key, $option_value );
			}

			$this->get_options();
		}

		/**
		 * Get plugin options
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function get_options() {
			if ( ! empty( $this->options ) && is_array( $this->options ) && count( $this->options ) > 0 ) {
				foreach ( $this->options as $option_key => $option_value ) {
					$this->options[ $option_key ] = get_option( self::$name . $option_key );
				}
			}
		}

		/**
		 * Create admin menu item
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function admin_menu() {
			add_menu_page( 'Cherry MailChimp Options', 'Cherry MailChimp', 'manage_options', 'cherry-mailchimp-options', array( &$this, 'options_page' ), null, 10 );
		}

		/**
		 * Admin options page
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function options_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Access denied.' ) );
			}

			if ( ! isset( $_GET['page'] ) || 'cherry-mailchimp-options' !== $_GET['page'] ) {
				return;
			}

			// Custom styles
			wp_register_style( 'simple-subscribe-admin', plugins_url( 'assets/css/admin.css', __FILE__ ) );
			wp_enqueue_style( 'simple-subscribe-admin' );

			// Custom scripts
			wp_register_script( 'mailchimp-script-api', plugins_url( 'assets/js/cherry-api.min.js', __FILE__ ) );
			wp_localize_script( 'mailchimp-script-api', 'cherry_ajax', wp_create_nonce( 'cherry_ajax_nonce' ) );
			wp_localize_script( 'mailchimp-script-api', 'wp_load_style', null );
			wp_localize_script( 'mailchimp-script-api', 'wp_load_script', null );
			wp_enqueue_script( 'mailchimp-script-api' );

			// Shortcode generator
			$base_url = trailingslashit( CHERRY_MAILCHIMP_URI ) . 'admin/includes/class-cherry-shortcode-generator/';
			$base_dir = trailingslashit( CHERRY_MAILCHIMP_DIR ) . 'admin/includes/class-cherry-shortcode-generator/';
			require_once( $base_dir . 'class-cherry-shortcode-generator.php' );

			add_filter( 'cherry_shortcode_generator_register', array( $this, 'add_shortcode_to_generator' ), 10, 2 );

			new Cherry_Shortcode_Generator( $base_dir, $base_url, 'cherry-mailchimp' );

			wp_enqueue_style( 'bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css' );

			$this->save_options();
			$this->get_options();

			// Include ui-elements
			include trailingslashit( CHERRY_MAILCHIMP_DIR ) . '/admin/lib/ui-elements/ui-text/ui-text.php';
			include trailingslashit( CHERRY_MAILCHIMP_DIR ) . '/admin/lib/ui-elements/ui-switcher/ui-switcher.php';
			include trailingslashit( CHERRY_MAILCHIMP_DIR ) . '/admin/lib/ui-elements/ui-textarea/ui-textarea.php';

			// Return html of options page
			return include_once 'views/options-page.php';
		}

		/**
		 * Add MailChimp shortcode to generator
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function add_shortcode_to_generator() {
			$shortcodes = array(
					'team' => array(
						'name' => __( 'MailChimp', 'cherry-mailchimp' ),
						'slug' => 'cherry_mailchimp',
						'desc' => __( 'Cherry MailChimp shortcode', 'cherry-mailchimp' ),
						'type' => 'single',
						'atts' => array(
							array(
								'name'  => 'placeholder',
								'id'    => 'placeholder',
								'type'  => 'text',
								'value' => __( 'enter your email', 'cherry-mailchimp' ),
								'label' => __( 'Placeholder', 'cherry-team' ),
								'desc'  => __( 'Placeholder for email input', 'cherry-mailchimp' ),
							),
							array(
								'name'  => 'button_text',
								'id'    => 'button_text',
								'type'  => 'text',
								'value' => __( 'Subscribe', 'cherry-mailchimp' ),
								'label' => __( 'Button', 'cherry-team' ),
								'desc'  => __( 'Enter button title', 'cherry-mailchimp' ),
							),
							array(
								'name'  => 'success_message',
								'id'    => 'success_message',
								'type'  => 'text',
								'value' => __( 'Subscribed successfully', 'cherry-mailchimp' ),
								'label' => __( 'Success message', 'cherry-team' ),
								'desc'  => __( 'Enter success message', 'cherry-mailchimp' ),
							),
							array(
								'name' => 'fail_message',
								'id' => 'fail_message',
								'type' => 'text',
								'value' => __('Subscribed failed', 'cherry-mailchimp'),
								'label' => __('Fail message', 'cherry-team'),
								'desc' => __('Enter fail message', 'cherry-mailchimp'),
							),
							array(
								'name'  => 'warning_message',
								'id'    => 'warning_message',
								'type'  => 'text',
								'value' => __( 'Email is incorect', 'cherry-mailchimp' ),
								'label' => __( 'Warning message', 'cherry-team' ),
								'desc'  => __( 'Enter warning message', 'cherry-mailchimp' ),
							),
						),
						'icon'      => 'envelope',
						'function'  => array( $this, 'do_shortcode' ) // Name of shortcode function.
					),
				);

	        return $shortcodes;
		}

		/**
		 * Check MailChimp account
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		private function check_apikey() {
			if ( empty( $this->options['apikey'] ) ) {
				return false;
			}

			$mailChimpAPI_obj = new MailChimp( $this->options['apikey'] );
			$result = $mailChimpAPI_obj->call( '/helper/ping', array(
					'apikey'    => $this->options['apikey'],
			), 20);

			if ( ! empty( $result['error'] ) || empty( $result['msg'] ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Add email to subscriber list
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function subscriber_add() {

			$this->get_options();

			/**
			 * Default fail response
			 */

			$return = array(
						'status'	=> 'failed',
					);

			$email = sanitize_email( $_POST['email'] );

			if ( is_email( $email ) && ! empty( $this->options['list'] ) && $this->check_apikey() ) {

				/**
				 * Call api
				 */

				$mailChimpAPI_obj = new MailChimp( $this->options['apikey'] );
				$result = $mailChimpAPI_obj->call( '/lists/subscribe', array(
								'id'	=> $this->options['list'],
								'email'	=> array(
											'email'    => $email,
											'euid'     => time() . rand( 1, 1000 ),
											'leid'     => time() . rand( 1, 1000 ),
										),
								'double_optin'	=> $this->options['confirm'],
							), 20);

				if ( ! empty( $result['leid'] ) ) {

					/**
					 * Success response
					 */

					$return = array(
							'status' => 'success',
					);
				}

				$return['result'] = $result;

			}

			// Send answer
			wp_send_json( $return );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
	Cherry_Mailchimp_Shortcode::get_instance();
}
