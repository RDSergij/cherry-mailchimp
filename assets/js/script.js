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
			$_form.find('input[type=email]').attr('disabled', 'disabled');
			jQuery.post(param.ajaxurl, data,
				function(response) {
					if (response.status == 'success') {
						$_form.find('.success_message').show('slow').delay(5000).fadeOut();
					} else {
						$_form.find('.fail_message').show('slow').delay(5000).fadeOut();
					}
					$_form.find('input[type=email]').removeAttr('disabled');
					$_form.trigger('reset');
				}
			);
			return true;
		}
	);

	jQuery('.subscribe-popup-link').magnificPopup({
		type: 'inline',
		preloader: false,
		focus: '#email',

		// When elemened is focused, some mobile browsers in some cases zoom in
		// It looks not nice, so we disable it:
		callbacks: {
			beforeOpen: function() {
				if(jQuery(window).width() < 700) {
					this.st.focus = false;
				} else {
					this.st.focus = '#name';
				}
			}
		}
	});

});
