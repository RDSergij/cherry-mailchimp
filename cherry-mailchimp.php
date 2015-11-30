<?php
/*
Plugin Name:  Cherry MailChimp
Plugin URI:
Description: ShortCode for MailChimp
Version: 0.2
Author: Cherry Team
Author URI:
*/

		
if (!class_exists('Cherry_MailChimp')) {
	
	require_once('includes/MailChimp.php'); //simple api class from https://github.com/drewm/mailchimp-api/blob/master/src/Drewm/MailChimp.php
	require_once('includes/Cherry_MailChimp_Data.php'); //simple api class from https://github.com/drewm/mailchimp-api/blob/master/src/Drewm/MailChimp.php

	/**
	 * Define plugin
	 */

	class Cherry_Mailchimp_Shortcode {

		private static $instance = null;
		public static $name = 'mailchimp';
		private $ApiKey;
		public $data = null;
		public $options = array (
				'apikey'            =>'',
				'list'              =>'',
				'placeholder'       =>'',
				'button_text'       =>'',
				'success_message'   =>'',
				'fail_message'      =>'',
				'warning_message'   =>'',
		);

		/**
		 * Init plugin
		 *
		 * @param empty
		 * @return void
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

			$this->data = Cherry_MailChimp_Data::get_instance(); // сам клас відредагую пізніше

			/**
			 * Create menu item
			 */

			add_action( 'admin_menu', array(&$this, 'admin_menu') );

			/**
			 * Create shortcode
			 */
			
			//add_shortcode( 'simplesubscribe', array(&$this, 'subscribeView') );

			/**
			 * Need for submit frontend form
			 */
			
			add_action( 'wp_ajax_simplesubscribe', array(&$this, 'subscriberAdd') );
			add_action( 'wp_ajax_nopriv_simplesubscribe', array(&$this, 'subscriberAdd') );
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
			//var_dump($target_dirs);
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
					'name'  => __( 'MailChimp', 'cherry-team' ), // Shortcode name.
					'desc'  => __( 'MailChimp shortcode', 'cherry-team' ),
					'type'  => 'single', // Can be 'wrap' or 'single'. Example: [b]this is wrapped[/b], [this_is_single]
					'group' => 'content', // Can be 'content', 'box', 'media' or 'other'. Groups can be mixed
					'atts'  => array( // List of shortcode params (attributes).
							'apikey' => array(
									'default' => 0,
									'name'    => __( 'API key', 'cherry-team' ),
									'desc'    => __( 'Enter api key of mailchimp account', 'cherry-team' ),
							),
							'list' => array(
									'default' => '',
									'name'    => __( 'List id', 'cherry-team' ),
									'desc'    => __( 'Enter list id', 'cherry-team' ),
							),
							'class' => array(
									'default' => '',
									'name'    => __( 'Class', 'cherry-team' ),
									'desc'    => __( 'Extra CSS class', 'cherry-team' ),
							),
							'button_text' => array(
									'default' => '',
									'name'    => __( 'Button', 'cherry-team' ),
									'desc'    => __( 'Enter button title', 'cherry-team' ),
							),
							'placeholder' => array(
									'default' => '',
									'name'    => __( 'Placeholder', 'cherry-team' ),
									'desc'    => __( 'Enter placeholder of email input', 'cherry-team' ),
							),
							'success_message' => array(
									'default' => '',
									'name'    => __( 'Success message', 'cherry-team' ),
									'desc'    => __( 'Enter success message', 'cherry-team' ),
							),
							'fail_message' => array(
									'default' => '',
									'name'    => __( 'Fail message', 'cherry-team' ),
									'desc'    => __( 'Enter faile message', 'cherry-team' ),
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
					'icon'     => 'users', // Custom icon (font-awesome).
					'function' => array( $this, 'do_shortcode' ), // Name of shortcode function.
			);
			return $shortcodes;
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

			// Set up the default arguments.
			$defaults = array(
					'apikey'         	=> 0,
					'list'        	 	=> 'testList',
					'button'         	=> __('Subscribe'),
					'placeholder'    	=> __('enter your email'),
					'success_message'   => __('Successfully'),
					'fail_message'     	=> __('Failed'),
					'warning_message'   => __('Warning!'),
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
							'value' => __( 'Placeholder', 'cherry-team' ),
							'open'  => '%%PLACEHOLDER%%',
							'close' => '',
					),
					'button_text' => array(
							'id'    => 'cherry_button',
							'value' => __( 'Button text', 'cherry-team' ),
							'open'  => '%%BUTTON_TEXT%%',
							'close' => '',
					),
					'content' => array(
							'id'    => 'cherry_content',
							'value' => __( 'Short description', 'cherry-team' ),
							'open'  => '%%CONTENT%%',
							'close' => '%%/CONTENT%%',
					),
					'success_message' => array(
							'id'    => 'cherry_success',
							'value' => __( 'Success message', 'cherry-team' ),
							'open'  => '%%SUCCESS_MESSAGE%%',
							'close' => '',
					),
					'fail_message' => array(
							'id'    => 'cherry_fail',
							'value' => __( 'Fail message', 'cherry-team' ),
							'open'  => '%%FAIL_MESSAGE%%',
							'close' => '',
					),
					'warning_message' => array(
							'id'    => 'cherry_warning',
							'value' => __( 'Warning message', 'cherry-team' ),
							'open'  => '%%WARNING_MESSAGE%%',
							'close' => '',
					),
			);
			return $macros_buttons;
		}


		public function get_team_loop( $query, $args ) {
			global $post, $more;
			// Item template.
			$template = $this->get_template_by_name( $args['template'], Cherry_Team_Shortcode::$name );
			/**
			 * Filters template for team item.
			 *
			 * @since 1.0.0
			 * @param string.
			 * @param array   Arguments.
			 */
			$template = apply_filters( 'cherry_team_item_template', $template, $args );
			$count  = 1;
			$output = '';
			if ( ! is_object( $query ) || ! is_array( $query->posts ) ) {
				return false;
			}
			$macros    = '/%%([a-zA-Z]+[^%]{2})(=[\'\"]([a-zA-Z0-9-_\s]+)[\'\"])?%%/';
			$callbacks = $this->setup_template_data( $args );
			foreach ( $query->posts as $post ) {
				// Sets up global post data.
				setup_postdata( $post );
				$tpl       = $template;
				$post_id   = $post->ID;
				$link      = get_permalink( $post_id );
				$this->replace_args['link'] = $link;
				$tpl = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $tpl );
				$item_classes   = array( $args['item_class'], 'item-' . $count, 'clearfix' );
				$item_classes[] = ( $count % 2 ) ? 'odd' : 'even';
				foreach ( array( 'col_xs', 'col_sm', 'col_md', 'col_lg' ) as $col ) {
					if ( ! $args[ $col ] || 'none' == $args[ $col ] ) {
						continue;
					}
					$cols = absint( $args[ $col ] );
					if ( 12 < $cols ) {
						$cols = 12;
					}
					if ( 0 === $cols ) {
						$cols = 1;
					}
					$item_classes[] = str_replace( '_', '-', $col ) . '-' . absint( $args[ $col ] );
					$item_classes[] = ( ( $count - 1 ) % floor( 12 / $cols ) ) ? '' : 'clear-' . str_replace( '_', '-', $col );
				}
				$count++;
				$item_class = implode( ' ', array_filter( $item_classes ) );
				$output .= '<div id="team-' . $post_id . '" class="' . $item_class . '">';
				/**
				 * Filters team items.
				 *
				 * @since 1.0.0
				 * @param string.
				 * @param array  A post meta.
				 */
				$tpl = apply_filters( 'cherry_get_team_loop', $tpl );
				$output .= $tpl;
				$output .= '</div><!--/.team-item-->';
				$callbacks->clear_data();
			}
			// Restore the global $post variable.
			wp_reset_postdata();
			return $output;
		}

		/**
		 * Prepare template data to replace
		 *
		 * @since  1.0.0
		 * @param  array $atts output attributes.
		 * @return array
		 */
		function setup_template_data( $atts ) {

			//var_dump($atts);
			//wp_die();
			require_once( '/includes/class-cherry-mailchimp-template-callbacks.php' );
			$callbacks = new Cherry_Team_Template_Callbacks( $atts );
			$data = array(
					'placeholder'    	=> array( $callbacks, 'get_placeholder' ),
					'button_text'     	=> array( $callbacks, 'get_button_text' ),
					'content'  			=> array( $callbacks, 'get_content' ),
					'success_message'  	=> array( $callbacks, 'get_success_message' ),
					'fail_message'  	=> array( $callbacks, 'get_fail_message' ),
					'warning_message'  	=> array( $callbacks, 'get_warning_message' ),
			);
			$this->post_data = apply_filters( 'cherry_mailchimp_data_callbacks', $data, $atts );
			return $callbacks;
		}

		/**
		 * Callback to replace macros with data
		 *
		 * @since  1.0.0
		 *
		 * @param  array $matches found macros.
		 * @return mixed
		 */
		public function replace_callback( $matches ) {
			if ( ! is_array( $matches ) ) {
				return '';
			}
			if ( empty( $matches ) ) {
				return '';
			}
			$key = strtolower( $matches[1] );
			// if key not found in data -return nothing
			if ( ! isset( $this->post_data[ $key ] ) ) {
				return '';
			}
			$callback = $this->post_data[ $key ];
			if ( ! is_callable( $callback ) ) {
				return;
			}
			// if found parameters and has correct callback - process it
			if ( isset( $matches[3] ) ) {
				return call_user_func( $callback, $matches[3] );
			}
			return call_user_func( $callback );
		}


		/**
		 * Get template file by name
		 *
		 * @since  1.0.0
		 *
		 * @param  string $template  template name.
		 * @param  string $shortcode shortcode name.
		 * @return string
		 */
		public function get_template_by_name( $template, $shortcode ) {
			$file       = '';
			$subdir     = 'templates/shortcodes/' . $shortcode . '/' . $template;
			$default    = CHERRY_TEAM_DIR . 'templates/shortcodes/' . $shortcode . '/default.tmpl';
			$upload_dir = wp_upload_dir();
			$basedir    = $upload_dir['basedir'];
			$content = apply_filters(
					'cherry_team_fallback_template',
					'%%photo%%<div>%%name%%</div><div>%%position%%</div><div>%%content%%</div>'
			);
			if ( file_exists( trailingslashit( $basedir ) . $subdir ) ) {
				$file = trailingslashit( $basedir ) . $subdir;
			} elseif ( file_exists( CHERRY_TEAM_DIR . $subdir ) ) {
				$file = CHERRY_TEAM_DIR . $subdir;
			} else {
				$file = $default;
			}
			if ( ! empty( $file ) ) {
				$content = self::get_contents( $file );
			}
			return $content;
		}

		/**
		 * Add team specific macros buttons into mailchimp shortcode
		 *
		 * @since  1.0.0
		 * @param  array $macros_buttons default macros buttons.
		 * @return array
		 */
		public function extend_mailchimp_macros( $macros_buttons ) {
			$macros_buttons['placeholder'] = array(
					'id'    => 'cherry_placeholder',
					'value' => __( 'Placeholder', 'cherry-team' ),
					'open'  => '%%PLACEHOLDER%%',
					'close' => '',
			);
			$macros_buttons['button_text'] = array(
					'id'    => 'cherry_button_text',
					'value' => __( 'Button text', 'cherry-team' ),
					'open'  => '%%BUTTON_TEXT%%',
					'close' => '',
			);
			$macros_buttons['content'] = array(
					'id'    => 'cherry_phone',
					'value' => __( 'Telephone (Team only)', 'cherry-team' ),
					'open'  => '%%PHONE%%',
					'close' => '',
			);
			$macros_buttons['success_message'] = array(
					'id'    => 'cherry_success_message',
					'value' => __( 'Success message', 'cherry-team' ),
					'open'  => '%%SUCCESS_MESSAGE%%',
					'close' => '',
			);
			$macros_buttons['fail_message'] = array(
					'id'    => 'cherry_fail_message',
					'value' => __( 'Fail message', 'cherry-team' ),
					'open'  => '%%FAIL_MESSAGE%%',
					'close' => '',
			);
			$macros_buttons['warning_message'] = array(
					'id'    => 'cherry_warning_message',
					'value' => __( 'Warning message', 'cherry-team' ),
					'open'  => '%%WARNING_MESSAGE%%',
					'close' => '',
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
			require_once(  '/includes/class-cherry-mailchimp-template-callbacks.php' );
			$callbacks = new Cherry_Mailchimp_Template_Callbacks( $atts );
			$postdata['placeholder']   		= $callbacks->get_placeholder();
			$postdata['button_text']  		= $callbacks->get_button_text();
			$postdata['content']  			= $callbacks->get_content();
			$postdata['success_message'] 	= $callbacks->get_success_message();
			$postdata['fail_message']   	= $callbacks->get_fail_message();
			$postdata['warning_message']    = $callbacks->get_warning_message();
			return $postdata;
		}


		/**
		 * Process save
		 *
		 * @param empty
		 * @return void
		 */
		
		private function save_options() {
			if ( empty($_POST[ 'action' ]) || 'Save' != $_POST[ 'action' ] ) {
				return;
			}

			foreach ($this->options as $option_key=>$option_value) {
				$option_value = !empty($_POST[$option_key]) ? $_POST[$option_key] : '';
				update_option( self::$name . $option_key, $option_value );
			}

			$this->get_options();
		}

		/**
		 * get plugin options
		 *
		 * @param empty
		 * @return void
		 */

		private function get_options() {
			if ( ! empty( $this->options ) && is_array( $this->options ) && count( $this->options ) > 0 ) {
				foreach ( $this->options as $option_key => &$option_value ) {
					$this->options[$option_key] = get_option( self::$name . $option_key );
				}
			}
		}

		/**
		 * Create admin menu item
		 *
		 * @param empty
		 * @return void
		 */
		
		public function admin_menu() {
			add_menu_page( 'Cherry MailChimp Options', 'Cherry MailChimp', 'manage_options', 'cherry-mailchimp-options', array(&$this,
					'options_page'
			), null, 10 );
		}

		/**
		 * Admin options page
		 *
		 * @param empty
		 * @return void
		 */
		
		public function options_page() {
			if ( !current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Access denied.' ) );
			}

			$this->get_options();
			$this->save_options();
			
			$shortcode = $this->generation_shortcode();

			/**
			 * Include ui-elements
			 */

			include (plugin_dir_path( __FILE__ ).'/admin/lib/ui-elements/ui-text/ui-text.php');

			/**
			 * Return html of options page
			 */

			return include ( 'views/options_page.php' );
		}

		/**
		 * Generation default shortcode on option page
		 * @return string
		 */

		private function generation_shortcode() {
			$shortcode = '[cherry_'.self::$name;
			if ( ! empty( $this->options ) && is_array( $this->options ) && count( $this->options ) > 0 ) {
				foreach ( $this->options as $option_key => $option_value ) {
					$shortcode.= ' '.$option_key.'="'.$option_value.'" ';
				}
			}
			$shortcode.= ']';
			return $shortcode;
		}



		/**
		 * Shortcode view
		 *
		 * @param array
		 * @return string
		 */
		
		public function subscribeView( $atts ) {
			 $args = shortcode_atts( array(
				'apikey'			=> $this->ApiKey,
				'button' 			=> 'Subscribe',
				'list' 				=> '0', //list id
				'placeholder' 		=> 'Please, input your email',
				'class' 			=> '',
				'id'				=> 'simple_subscribe_form',
				'success_message'	=> 'Subscribe successful',
				'fail_message'		=> 'Subscribe failed',
			), $atts );

			wp_register_style( 'simple-subscribe-style', plugins_url('assets/css/style.css', __FILE__) );
			wp_enqueue_style( 'simple-subscribe-style' );
			
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js');
			wp_enqueue_script( 'jquery' );
			
			wp_register_script( 'simple-subscribe-script', plugins_url('assets/js/script.js', __FILE__) );
			wp_localize_script( 'simple-subscribe-script', 'param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  
			wp_enqueue_script( 'simple-subscribe-script' );

			/**
			 * Return shortcode html
			 */

			return include ( 'views/shortcode.php' );
		}

		/**
		 * Add email to subscriber list
		 *
		 * @param empty
		 * @return void
		 */
		
		public function subscriberAdd() {

			/**
			 * Default fail response
			 */

			$return = array(
						'status'=>'failed',
					);
			$email = $_POST[ 'email' ];
			$list = $_POST[ 'list' ];
			if ( is_email($email) && !empty($list) ) {

				$apikey = !empty($_POST[ 'apikey' ]) ? $_POST[ 'apikey' ] : $this->options['apikey'];

				/**
				 * Call api
				 */

				$mailChimpAPI_obj = new Drewm\MailChimp($apikey);
				$result = $mailChimpAPI_obj->call('/lists/subscribe', array(
								'id'=>$list,
								'email'=>array(
											'email'    =>$email,
											'euid'     =>time().rand(1,1000),
											'leid'     =>time().rand(1,1000),
										),
							), 20);
				if (!empty($result[ 'leid' ])) {

					/**
					 * Success response
					 */

					$return = array(
							'status' => 'success'
					);
				}

			}
			wp_send_json($return);
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance )
				self::$instance = new self;

			return self::$instance;
		}
		
	}

	Cherry_Mailchimp_Shortcode::get_instance();
	
}
