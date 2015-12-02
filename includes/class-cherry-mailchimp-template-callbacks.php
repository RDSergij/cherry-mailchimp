<?php
/**
 * Define callback functions for templater
 *
 * @package   Cherry_MailChimp
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Callbcks for MailChimp shortcode templater
 *
 * @since  1.0.0
 */
class Cherry_Mailchimp_Template_Callbacks {
	/**
	 * Shortcode attributes
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $atts = array();

	/**
	 * Constructor for the class
	 *
	 * @since 1.0.0
	 * @param array $atts input attributes array.
	 */
	function __construct( $atts ) {
		$this->atts = $atts;
	}

	/**
	 * Get placeholder
	 *
	 * @since 1.0.0
	 */
	public function get_placeholder() {
		if ( ! empty( $this->atts['placeholder'] ) ) {
			return $this->atts['placeholder'];
		} else {
			return get_option( 'mailchimpplaceholder' );
		}
	}

	/**
	 * Get button text
	 *
	 * @since 1.0.0
	 */
	public function get_button_text() {
		if ( ! empty( $this->atts['button_text'] ) ) {
			return $this->atts['button_text'];
		} else {
			return get_option( 'mailchimpbutton_text' );
		}
	}

	/**
	 * Get success message
	 *
	 * @since 1.0.0
	 */
	public function get_success_message() {
		if ( ! empty( $this->atts['success_message'] ) ) {
			return $this->atts['success_message'];
		} else {
			return get_option( 'mailchimpsuccess_message' );
		}
	}

	/**
	 * Get faul message
	 *
	 * @since 1.0.0
	 */
	public function get_fail_message() {
		if ( ! empty( $this->atts['fail_message'] ) ) {
			return $this->atts['fail_message'];
		} else {
			return get_option( 'mailchimpfail_message' );
		}
	}

	/**
	 * Get warning message
	 *
	 * @since 1.0.0
	 */
	public function get_warning_message() {
		if ( ! empty( $this->atts['warning_message'] ) ) {
			return $this->atts['warning_message'];
		} else {
			return get_option( 'mailchimpwarning_message' );
		}
	}
}
