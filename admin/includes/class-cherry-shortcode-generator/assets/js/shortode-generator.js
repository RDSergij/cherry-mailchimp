
/**
 * Shortcode generator
 */
( function( $ ) {
	'use strict';

	function genereateShortcode( $target ) {

		var mask      = $target.data( 'input_mask' ),
			shortcode = $target.data( 'shortcode' ),
			sType     = $target.data( 'type' ),
			$attrForm = $( '.cherry-sg-popup_fields', $target ),
			atts      = $attrForm.serializeArray(),
			attName,
			val,
			result;

		result = '[' + shortcode;

		$.each( atts, function( index, val ) {
			result += ' ' + val.name + '="' + val.value + '"';
		});

		result += ']';

		if ( 'single' !== sType ) {
			result += '[/' + shortcode + ']';
		}

		return result;
	}

	function pasteShortcode( $target, $result ) {
		var shortcode = genereateShortcode( $target );
		$result.val( shortcode );
	}

	$( window ).load( function() {
		$( '.cherry-sg-open' ).magnificPopup({
			type: 'inline',
			preloader: false,
			focus: '#name',
			callbacks: {
				open: function() {

					var $resultShortcode = $( '#generated-shortcode', this.content ),
						$target          = this.content;

					// Init UI elements
					$( window ).trigger( 'cherry-ui-elements-init', { 'target': $target } );

					pasteShortcode( $target, $resultShortcode );

					$target.on( 'change blur', function() {
						pasteShortcode( $target, $resultShortcode );
					});

					$target.on( 'click', '.cherry-switcher-wrap', function() {
						pasteShortcode( $target, $resultShortcode );
					});

					$( '.cherry-slider-unit' ).on( 'slidechange', function() {
						pasteShortcode( $target, $resultShortcode );
					} );

				}
			}
		});

	});

}( jQuery ) );
