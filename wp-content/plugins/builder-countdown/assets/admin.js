jQuery(function($){

	$( 'body' ).on( 'editing_module_option', function(e){
		if( ! $('.builder-countdown-datepicker').length > 0 ) return;

		$( '.builder-countdown-datepicker input' ).datetimepicker({
			showOn: 'both',
			showButtonPanel: true,
			closeButton: BuilderCountdownAdmin.closeButton,
			buttonText: BuilderCountdownAdmin.buttonText,
			dateFormat: BuilderCountdownAdmin.dateFormat,
			timeFormat: BuilderCountdownAdmin.timeFormat,
			stepMinute: 5,
			separator: BuilderCountdownAdmin.separator,
			beforeShow: function(input, inst) {
				$('#ui-datepicker-div').addClass( 'themifyDateTimePickerPanel' );
			}
		})
		.next().addClass( 'button' );
	});

});