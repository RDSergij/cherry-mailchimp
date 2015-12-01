/**
 * Submit event
 */

jQuery(document).ready(function() {

	jQuery('#cherry-mailchimp-form').submit(
		function(e) {
			e.preventDefault();
			$_form = jQuery(this);
			var data = jQuery(this).serialize();
			$_form.find('.message').hide();

			// Valid email
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if ( !regex.test( $_form.find('input[type=email]').val() ) ) {
				// Show warning message
				$_form.find('.message-warning').show('slow').delay(5000).fadeOut();
				return true;
			}

			// Disable form element
			$_form.find('input[type=email]').attr('disabled', 'disabled');
			$_form.find('button').attr('disabled', 'disabled');

			// Send data
			jQuery.post(param.ajaxurl, data,
				function(response) {

					// Show message
					if (response.status == 'success') {
						$_form.find('.message-success').show('slow').delay(5000).fadeOut();
					} else {
						$_form.find('.message-fail').show('slow').delay(5000).fadeOut();
					}

					// Enable form element
					$_form.find('input[type=email]').removeAttr('disabled');
					$_form.find('button').removeAttr('disabled');

					// Clear form element
					$_form.trigger('reset');
				}
			);
			return true;
		}
	);

	// Popup window init
	jQuery('.subscribe-popup-link').magnificPopup({
		type: 'inline',
		preloader: false,
		focus: '#cherry-mailchimp-form input[type=email]'
	});

});