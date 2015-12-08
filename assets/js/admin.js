/**
 * Submit event
 */

jQuery( document ).ready( function() {
    jQuery( '#cherry-mailchimp-options-save' ).click( function() {
        var form = jQuery( '#cherry-mailchimp-option' );
        var data = form.serialize();

        jQuery( this ).find( 'div' ).addClass( 'active' );
        jQuery( this ).addClass( 'right-spiner' );

        // Send data
        jQuery.post( window.cherryMailchimpParam.ajaxurl, data,
            function( response ) {
                var message = '';
                var type = '';
                form.find( '#cherry-mail-chimp-message' ).removeClass( 'cherry-message-success' );
                form.find( '#cherry-mail-chimp-message' ).removeClass( 'cherry-message-failed' );

                if ( ! response.message ) {
                    message = window.cherryMailchimpParam.default_error_message;
                } else {
                    message = response.message;
                }

                if ( ! response.type ) {
                    type = 'error';
                } else {
                    type = response.type;
                }

                if ( ! response.connect_status ) {
                    jQuery( '#cherry-mail-chimp-connect' ).removeClass( 'text-success' );
                    jQuery( '#cherry-mail-chimp-connect' ).addClass( 'text-danger' );
                } else {
                    jQuery( '#cherry-mail-chimp-connect' ).removeClass( 'text-danger' );
                    jQuery( '#cherry-mail-chimp-connect' ).removeClass( 'text-success' );
                    jQuery( '#cherry-mail-chimp-connect' ).addClass( 'text-' + response.connect_status );
                }

                if ( ! response.connect_message ) {
                    jQuery( '#cherry-mail-chimp-connect' ).html( '(' + window.cherryMailchimpParam.default_disconnect_message + ')' );
                } else {
                    jQuery( '#cherry-mail-chimp-connect' ).html( '(' + response.connect_message + ')' );
                }

                noticeCreate( type, message );

                jQuery( '#cherry-mailchimp-options-save' ).find( 'div' ).removeClass( 'active' );
                jQuery( '#cherry-mailchimp-options-save' ).removeClass( 'right-spiner' );

                cherryMailchimpGeneratorView();
            }
        );

    });
});

function genereateShortcode( target ) {

    var mask      = target.data( 'input_mask' ),
        shortcode = target.data( 'shortcode' ),
        sType     = target.data( 'type' ),
        $attrForm = jQuery( '.cherry-sg-popup_fields', target ),
        atts      = $attrForm.serializeArray(),
        attName,
        val,
        result;

    result = '[' + shortcode;

    jQuery.each( atts, function( index, val ) {
        result += ' ' + val.name + '="' + val.value + '"';
    });

    result += ']';

    if ( 'single' !== sType ) {
        result += '[/' + shortcode + ']';
    }

    return result;
}

function pasteShortcode( target, result ) {
    var shortcode = genereateShortcode( target );
    result.val( shortcode );
}

function cherryMailchimpGeneratorView() {
    jQuery.post( window.cherryMailchimpParam.ajaxurl, { action: 'cherry_mailchimp_generator_view' },
        function( response ) {
            jQuery( '#cherry-mailchimp-generate-view' ).html( response );

            jQuery( '.cherry-sg-open' ).magnificPopup({
                type: 'inline',
                preloader: false,
                focus: '#name',
                callbacks: {
                    open: function() {

                        var resultShortcode = jQuery( '#generated-shortcode', this.content ),
                            target          = this.content;

                        // Init UI elements
                        jQuery( window ).trigger( 'cherry-ui-elements-init', { 'target': target } );

                        pasteShortcode( target, resultShortcode );

                        target.on( 'change blur', function() {
                            pasteShortcode( target, resultShortcode );
                        });

                        target.on( 'click', '.cherry-switcher-wrap', function() {
                            pasteShortcode( target, resultShortcode );
                        });

                        jQuery( '.cherry-slider-unit' ).on( 'slidechange', function() {
                            pasteShortcode( target, resultShortcode );
                        } );

                    }
                }
            });

        });
}

function noticeCreate( type, message ) {
    var
        notice = jQuery( '<div class="notice-box ' + type + '"><span class="dashicons"></span><div class="inner">' + message + '</div></div>' ),
        rightDelta = 0,
        timeoutId;

    jQuery( '#cherry-mailchimp-option' ).append( notice );
    reposition();
    rightDelta = -1 * ( notice.outerWidth( true ) + 10 );
    notice.css( { 'right': rightDelta } );

    timeoutId = setTimeout( function() {
        notice.css( { 'right': 10 } ).addClass( 'show-state' );
    }, 100 );

    timeoutId = setTimeout( function() {
        rightDelta = -1 * ( notice.outerWidth( true ) + 10 );
        notice.css( { right: rightDelta } ).removeClass( 'show-state' );
    }, 4000 );

    timeoutId = setTimeout( function() {
        notice.remove();
        clearTimeout( timeoutId );
    }, 4500 );

    function reposition() {
        var topDelta = 100;

        jQuery( jQuery( '.notice-box' ).get().reverse() ).each( function( index ) {
            jQuery( this ).css( { top: topDelta } );
            topDelta += jQuery( this ).outerHeight( true );
        });
    }
}
