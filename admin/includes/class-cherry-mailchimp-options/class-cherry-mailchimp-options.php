<?php
/**
 * MailChimp Configuration class.
 *
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

// If class 'MailChimp_Options' not exists.
if ( !class_exists('Mailchimp_Options') ) {

	class Mailchimp_Options {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Prefix name
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public static $name = 'mailchimp';

		static $options = array();

		private function __construct() {
			// Cherry option filter.
			add_filter( 'cherry_defaults_settings',      array( $this, 'cherry_mailchimp_settings' ) );
			//add_filter( 'cherry_get_single_post_layout', array( $this, 'get_single_option' ),  11, 2 );
		}

		/**
		 * Add menu item in Cherry FrameWork
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function cherry_mailchimp_settings( $result_array ) {
			$mailchimp_options = array();

			$mailchimp_options[ self::$name . '_apikey' ] = array(
				'type'			=> 'text',
				'title'			=> __( 'Api Key', 'cherry-mailchimp' ),
				'description'	=> __( 'Set your Api Key', 'cherry-mailchimp' ),
				'value'			=> __( '', 'cherry-mailchimp' ),
			);

			$mailchimp_options[ self::$name . '_list' ] = array(
				'type'			=> 'text',
				'title'			=> __( 'List', 'cherry-mailchimp' ),
				'description'	=> __( 'Subscribe list id', 'cherry-mailchimp' ),
				'value'			=> __( '', 'cherry-mailchimp' ),
			);

			$mailchimp_options[ self::$name . '_confirm' ] = array(
				'type'			=> 'switcher',
				'title'			=> __( 'Confirmation', 'cherry-mailchimp' ),
				'description'	=> __( 'Email confirmation', 'cherry-mailchimp' ),
				'value'			=> __( 'true', 'cherry-mailchimp' ),
			);

			$mailchimp_options[ self::$name . '_placeholder' ] = array(
				'type'			=> 'text',
				'title'			=> __( 'Placeholder', 'cherry-mailchimp' ),
				'description'	=> __( 'Default placeholder for email input', 'cherry-mailchimp' ),
				'value'			=> __( 'Enter your email', 'cherry-mailchimp' ),
			);

			$mailchimp_options[ self::$name . '_button_text' ] = array(
				'type'			=> 'text',
				'title'			=> __( 'Button', 'cherry-mailchimp' ),
				'description'	=> __( 'Default submit button text', 'cherry-mailchimp' ),
				'value'			=> __( 'Subscribe', 'cherry-mailchimp' ),
			);

			$mailchimp_options[ self::$name . '_success_message' ] = array(
				'type'			=> 'text',
				'title'			=> __( 'Success message', 'cherry-mailchimp' ),
				'description'	=> __( 'Default success message', 'cherry-mailchimp' ),
				'value'			=> __( 'Subscribed successfully', 'cherry-mailchimp' ),
			);

			$mailchimp_options[ self::$name . '_fail_message' ] = array(
				'type'			=> 'text',
				'title'			=> __( 'Fail message', 'cherry-mailchimp' ),
				'description'	=> __( 'Default fail message', 'cherry-mailchimp' ),
				'value'			=> __( 'Subscribed failed', 'cherry-mailchimp' ),
			);

			$mailchimp_options[ self::$name . '_warning_message' ] = array(
				'type'			=> 'text',
				'title'			=> __( 'Warning message', 'cherry-mailchimp' ),
				'description'	=> __( 'Default warning message', 'cherry-mailchimp' ),
				'value'			=> __( 'Email is incorect', 'cherry-mailchimp' ),
			);

			$mailchimp_options = apply_filters( 'cherry_mailchimp_default_settings', $mailchimp_options );
			$result_array['mailchimp-options-section'] = array(
				'name'			=> __('Cherry MailChimp', 'cherry-mailchimp'),
				'icon' 			=> 'dashicons dashicons-format-gallery',
				'priority'		=> 120,
				'options-list'	=> $mailchimp_options
			);
			return $result_array;
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

	}//end class

	Mailchimp_Options::get_instance();
}