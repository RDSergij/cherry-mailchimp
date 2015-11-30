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
     * Clear post data after loop iteration
     *
     * @since  1.0.3
     * @return void
     */
    public function clear_data() {
        $this->post_meta = null;
        $this->post_data = array();
    }
    /**
     * Get data
     *
     * @since 1.0.3
     */

    public function get_apikey() {
        return $this->atts['apikey'];
    }

    public function get_list() {
        return $this->atts['list'];
    }

    public function get_placeholder() {
        return $this->atts['placeholder'];
    }

    public function get_content() {
        if (!empty($this->atts['content'])) {
            return $this->atts['content'];
        } else return ' ';
    }

    public function get_button_text() {
        return $this->atts['button_text'];
    }

    public function get_success_message() {
        return $this->atts['success_message'];
    }

    public function get_fail_message() {
        return $this->atts['fail_message'];
    }

    public function get_warning_message() {
        return $this->atts['warning_message'];
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
    /**
     * Wrap person email into link with mailto:
     *
     * @since  1.0.0
     * @param  string $email Person email.
     * @return string
     */
    public function mail_wrap( $email ) {
        if ( ! is_email( $email ) ) {
            return;
        }
        return sprintf( '<a href="mailto:%1$s" class="team-email-link">%1$s</a>', $email );
    }
    /**
     * Get user website HTML
     *
     * @since  1.0.0
     *
     * @param  string $url  personal wesite URL.
     * @param  string $name person name.
     * @return string
     */
    public function get_website_html( $url = null, $name = null ) {
        $format = apply_filters(
            'cherry_team_personal_website_format',
            '<a href="%s" class="team-website" rel="nofollow">%s</a>'
        );
        $url   = esc_url( $url );
        $label = __( 'Personal website', 'cherry-team' );
        return sprintf( $format, $url, $label );
    }
}