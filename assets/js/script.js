/**
 * Submit event
 */

jQuery( document ).ready( function() {

	jQuery( '#cherry-mailchimp-form' ).submit(
		function( e ) {
			var form = jQuery( this );
			var data = jQuery( this ).serialize();
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

			e.preventDefault();

			form.find( '.message' ).hide();

			// Valid email
			if ( false === regex.test( form.find( 'input[type=email]' ).val() ) ) {

				// Show warning message
				form.find( '.message-warning' ).show( 'slow' ).delay( 5000 ).fadeOut();
				return true;
			}

			// Disable form element
			form.find( 'input[type=email]' ).attr( 'disabled', 'disabled' );
			form.find( 'button' ).attr( 'disabled', 'disabled' );

			// Send data
			jQuery.post( window.cherryMailchimpParam.ajaxurl, data,
				function( response ) {

					// Show message
					if ( 'success' === response.status ) {
						form.find( '.message-success' ).show( 'slow' ).delay( 5000 ).fadeOut();
					} else {
						form.find( '.message-fail' ).show( 'slow' ).delay( 5000 ).fadeOut();
					}

					// Enable form element
					form.find( 'input[type=email]' ).removeAttr( 'disabled' );
					form.find( 'button' ).removeAttr( 'disabled' );

					// Clear form element
					form.trigger( 'reset' );
				}
			);
			return true;
		}
	);

	// Popup window init
	jQuery( '.subscribe-popup-link' ).magnificPopup( {
		type: 'inline',
		preloader: false,
		focus: '#cherry-mailchimp-form input[type=email]'
	});
});
