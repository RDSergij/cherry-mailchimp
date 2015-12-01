<?php

/**
 * Created by PhpStorm.
 * User: serhiiosadchyi
 * Date: 30.11.15
 * Time: 10:30
 */
class Cherry_Mailchimp_Template_Callbacks {
    /**
     * Shortcode attributes array
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
     * Get data
     *
     * @since 1.0.3
     */

    public function get_apikey() {
        if (!empty($this->atts['apikey'])) {
            return $this->atts['apikey'];
        } else {
            return get_option('mailchimpapikey');
        }

    }

    public function get_list() {
        if (!empty($this->atts['list'])) {
            return $this->atts['list'];
        } else {
            return get_option('mailchimplist');
        }

    }

    public function get_placeholder() {
        if (!empty($this->atts['content'])) {
            return $this->atts['placeholder'];
        } else {
            return get_option('mailchimpplaceholder');
        }

    }

    public function get_content() {
        if (!empty($this->atts['content'])) {
            return $this->atts['content'];
        } else {
            return get_option('mailchimpcontent');
        }
    }

    public function get_button_text() {
        if (!empty($this->atts['button_text'])) {
            return $this->atts['button_text'];
        } else {
            return get_option('mailchimpbutton_text');
        }
    }

    public function get_success_message() {
        if (!empty($this->atts['success_message'])) {
            return $this->atts['success_message'];
        } else {
            return get_option('mailchimpsuccess_message');
        }
    }

    public function get_fail_message() {
        if (!empty($this->atts['fail_message'])) {
            return $this->atts['fail_message'];
        } else {
            return get_option('mailchimpfail_message');
        }
    }

    public function get_warning_message() {
        if (!empty($this->atts['warning_message'])) {
            return $this->atts['warning_message'];
        } else {
            return get_option('mailchimpwarning_message');
        }
    }

    /**
     * Wrap single team item into HTML wrapper with custom class
     *
     * @since  1.0.0
     * @param  string $value meta value.
     * @param  string $class custom CSS class.
     * @return string
     */
    public function meta_wrap( $value = null, $class = null ) {
        if ( ! $value ) {
            return;
        }
        $css_class = 'team-meta_item';
        if ( $class ) {
            $css_class .= ' ' . sanitize_html_class( $class );
        }
        return sprintf( '<span class="%s">%s</span>', $css_class, $value );
    }

}