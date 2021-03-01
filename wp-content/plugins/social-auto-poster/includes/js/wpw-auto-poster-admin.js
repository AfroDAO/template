jQuery(document).ready(function ($) {

    $(document).on( 'click', '.clear-date',function(){
    	$('#_wpweb_select_hour').val('');
    });

    if( $('#_wpweb_select_hour').length ){

    	$('#_wpweb_select_hour').datetimepicker({
        	dateFormat: WpwAutoPosterAdmin.date_format,
        	minDate: new Date(WpwAutoPosterAdmin.current_date),
        	timeFormat: WpwAutoPosterAdmin.time_format,
        	showMinute : false,
        	ampm: false,
        	stepMinute:60,
        	showOn : 'focus',
        	stepHour: 1,
        	currentText: '',
	    }).attr('readonly','readonly');	    	
    }
});