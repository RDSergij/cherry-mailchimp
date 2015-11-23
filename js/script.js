jQuery('.simpleFormStyleClass').submit(
	function(e) {
		e.preventDefault();
		$form = jQuery(this);
		var data = jQuery(this).serialize();
		$form.find('.message').hide();
		$form.find('input[type=email]').attr('disabled', 'disabled');
		jQuery.post(param.ajaxurl, data, 
			function(response){
				if (response.status=='success')
					$form.find('.success_message').show('slow').delay(5000).fadeOut();
				else 
					$form.find('.fail_message').show('slow').delay(5000).fadeOut();					
				
				$form.find('input[type=email]').removeAttr('disabled');
				$form.trigger('reset');
			}
		);
		return true;
	}
);