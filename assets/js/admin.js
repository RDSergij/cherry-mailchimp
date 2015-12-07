/**
 * Submit event
 */

jQuery( document ).ready( function() {
    jQuery( '#cherry-mailchimp-options-save' ).click( function() {
        var form = jQuery( '#cherry-mailchimp-option' );

        jQuery( this ).find( 'div' ).addClass( 'active' );

        var data = form.serialize();
        // Send data
        jQuery.post( window.cherryMailchimpParam.ajaxurl, data,
            function( response ) {
                var message = '';
                var type = '';
                form.find( '#cherry-mail-chimp-message').removeClass( 'cherry-message-success' );
                form.find( '#cherry-mail-chimp-message').removeClass( 'cherry-message-failed' );

                if ( ! response.message ) {
                    message = cherryMailchimpParam.default_error_message;
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
                    jQuery( '#cherry-mail-chimp-connect').addClass( 'text-danger' );
                } else {
                    jQuery( '#cherry-mail-chimp-connect' ).removeClass( 'text-danger' );
                    jQuery( '#cherry-mail-chimp-connect' ).removeClass( 'text-success' );
                    jQuery( '#cherry-mail-chimp-connect').addClass( 'text-' + response.connect_status );
                }

                if ( ! response.connect_message ) {
                    jQuery( '#cherry-mail-chimp-connect' ).html( '(' + cherryMailchimpParam.default_disconnect_message + ')' );
                } else {
                    jQuery( '#cherry-mail-chimp-connect' ).html( '(' + response.connect_message + ')' );
                }


                noticeCreate( type, message );
                //jQuery( this ).find( 'div' ).removeClass( 'spinner-state' );
                jQuery( '#cherry-mailchimp-options-save' ).find( 'div' ).removeClass( 'active' );
            }
        );
    });
});

function noticeCreate( type, message ) {
    var
        notice = jQuery('<div class="notice-box ' + type + '"><span class="dashicons"></span><div class="inner">' + message + '</div></div>')
        ,	rightDelta = 0
        ,	timeoutId
        ;

    jQuery('#cherry-mailchimp-option').append( notice );
    reposition();
    rightDelta = -1*(notice.outerWidth( true ) + 10);
    notice.css({'right' : rightDelta });

    timeoutId = setTimeout( function () { notice.css({'right' : 10 }).addClass('show-state') }, 100 );
    timeoutId = setTimeout( function () {
        rightDelta = -1*(notice.outerWidth( true ) + 10);
        notice.css({ right: rightDelta }).removeClass('show-state');
    }, 4000 );
    //timeoutId = setTimeout( function () { notice.remove(); clearTimeout(timeoutId); }, 4500 );

    function reposition(){
        var
            topDelta = 100
            ;
        jQuery('.notice-box').each(function( index ){
            jQuery( this ).css({ top: topDelta });
            topDelta += jQuery(this).outerHeight(true);
        })
    }
}
