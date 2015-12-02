<?php
/**
 * Class for frontend
 *
 * @package Cherry_MailChimp_Data
 * @since 1.0.0
 */
class Cherry_MailChimp_Data {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * return array
	 */
	private static $instance = null;

	/**
	 *
	 * The array of arguments for template file.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	private $post_data = array();
	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		/**
		 * Fires when you need to display team.
		 *
		 * @since 1.0.0
		 */
		add_action( 'cherry_get_mailchimp', array( $this, 'the_mailchimp' ) );
	}
	/**
	 * Display or return HTML-formatted team.
	 *
	 * @since  1.0.0
	 * @param  string|array $args Arguments.
	 * @return string
	 */
	public function the_mailchimp( $args = '' ) {
		/**
		 * Filter the array of default arguments.
		 *
		 * @since 1.0.0
		 * @param array Default arguments.
		 * @param array The 'the_team' function argument.
		 */

		$defaults = apply_filters( 'cherry_the_team_default_args', array(
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
		), $args );
		$args = wp_parse_args( $args, $defaults );

		$output = '';

		// The Display.
		$output .= '<a class="subscribe-popup-link" href="#cherry-mailchimp-form">';
        $output .= $args['button_text'];
        $output .= '</a>';

        $output .= '<div class="cherry-mailchimp-container">';
		$output .= '<form id="cherry-mailchimp-form">';
        $output .= '<input type="hidden" name="action" value="mailchimpsubscribe">';

		$output .= $this->get_mailchimp_loop( $args );

		$output .= '</form>';
        $output .= '</div>';

		return $output;
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
	 * Get team items.
	 *
	 * @since  1.0.0
	 * @param  array $args  The array of arguments.
	 * @return string
	 */
	public function get_mailchimp_loop( $args ) {

		// Item template.
		$template = $this->get_template_by_name( $args['template'], Cherry_Mailchimp_Shortcode::$name );

		$macros    = '/%%([a-zA-Z_]+[^%]{2})(=[\'\"]([a-zA-Z0-9-_\s]+)[\'\"])?%%/';
		$this->setup_template_data( $args );

		$tpl = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $template );

		return $tpl;
	}
	/**
	 * Prepare template data to replace
	 *
	 * @since  1.0.0
	 * @param  array $atts output attributes.
	 * @return array
	 */
	function setup_template_data( $atts ) {
		require_once( 'class-cherry-mailchimp-template-callbacks.php' );
		$callbacks = new Cherry_Mailchimp_Template_Callbacks( $atts );
		$data = array(
			'placeholder' 		=> array( $callbacks, 'get_placeholder' ),
			'button_text'  		=> array( $callbacks, 'get_button_text' ),
			'success_message' 	=> array( $callbacks, 'get_success_message' ),
			'fail_message'    	=> array( $callbacks, 'get_fail_message' ),
			'warning_message'   => array( $callbacks, 'get_warning_message' ),
		);
		$this->post_data = apply_filters( 'cherry_team_data_callbacks', $data, $atts );
		return $callbacks;
	}


	/**
	 * Read template (static).
	 *
	 * @since  1.0.0
	 * @return bool|WP_Error|string - false on failure, stored text on success.
	 */
	public static function get_contents( $template ) {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/file.php' );
		}
		WP_Filesystem();
		global $wp_filesystem;
		if ( ! $wp_filesystem->exists( $template ) ) { // Check for existence.
			return false;
		}
		// Read the file.
		$content = $wp_filesystem->get_contents( $template );
		if ( ! $content ) {
			return new WP_Error( 'reading_error', 'Error when reading file' ); // Return error object.
		}
		return $content;
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
		$default    = plugin_dir_path( __FILE__ ).'templates/shortcodes/mailchimp/default.tmpl';
		$upload_dir = wp_upload_dir();
		$basedir    = $upload_dir['basedir'];
		$content = '';

		if ( file_exists( trailingslashit( $basedir ) . $subdir ) ) {
			$file = trailingslashit( $basedir ) . $subdir;
		} elseif ( file_exists( plugin_dir_path( __FILE__ ) . '/../' . $subdir ) ) {
			$file = plugin_dir_path( __FILE__ ) . '/../' . $subdir;
		} else {
			$file = $default;
		}
		if ( ! empty( $file ) ) {
			$content = self::get_contents( $file );
		}
		return $content;
	}
	/**
	 * Get CSS class name for shortcode by template name
	 *
	 * @since  1.0.5
	 * @param  string $template template name.
	 * @return string|bool false
	 */
	public function get_template_class( $template ) {
		if ( ! $template ) {
			return false;
		}
		// Use the same filter for all cherry-related shortcodes
		$prefix = apply_filters( 'cherry_shortcodes_template_class_prefix', 'template' );
		$class  = sprintf( '%s-%s', esc_attr( $prefix ), esc_attr( str_replace( '.tmpl', '', $template ) ) );
		return $class;
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