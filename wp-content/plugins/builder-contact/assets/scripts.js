(function ($) {

	if (Themify.is_builder_active || $('form.builder-contact').length > 0) {
		var $body = $('body');

		function send_form(form) {
			var data = $(form).serialize();

			data += '&action=builder_contact_send';
			data += '&contact-settings=' + $('.builder-contact-form-data', form).html();
			if (form.find('[name="g-recaptcha-response"]').length > 0) {
				data += '&contact-recaptcha=' + form.find('[name="g-recaptcha-response"]').val();
			}
			$.ajax({
				url: form.prop('action'),
				method: 'POST',
				data: data,
				dataType: 'json',
				success: function (data) {
					if (data && data.themify_message) {
						form.find('.contact-message').html(data.themify_message).fadeIn();
						form.removeClass('sending');
						$('html').stop().animate({ scrollTop: form.offset().top - 100 }, 500, 'swing');
						if (data.themify_success) {
							$body.trigger('builder_contact_message_sent', [form, data.themify_message]);
							form[0].reset();
						} else {
							$body.trigger('builder_contact_message_failed', [form, data.themify_message]);
						}
						if (typeof grecaptcha === 'object') {
							grecaptcha.reset();
						}
					}
				}
			});
		}

		function callback() {
			if (!Themify.is_builder_active) {
				$body.on('submit', '.builder-contact', function (e) {
					e.preventDefault();
					if ($(this).hasClass('sending')) {
						return false;
					}
					$(this).addClass('sending').find('.contact-message').fadeOut();
					send_form($(this));
				});
			}
		}

		function captcha(el) {

			if ($('.builder-contact-field-captcha', el).length > 0) {
				if (typeof grecaptcha === 'undefined') {
					Themify.LoadAsync('//www.google.com/recaptcha/api.js', callback, '', true, function () {
						return typeof grecaptcha !== 'undefined';
					});
				}
				else {
					callback();
				}
			}
			else {
				callback();
			}

		}

		$body.on('focus', '.module-contact.contact-animated-label input, .module-contact.contact-animated-label textarea', function () {
			var label = $("label[for='" + $(this).attr('id') + "']"); //.addClass( 'inside' );
			if (label.length === 0) {
				label = $(this).closest(".builder-contact-field").find("label");
			}
			label.css({'top': 0, 'left': 0});
		}).on('blur', '.module-contact.contact-animated-label input, .module-contact.contact-animated-label textarea', function () {
			if ($(this).val() == "") {
				var label = $("label[for='" + $(this).attr('id') + "']"); //.addClass( 'inside' );
				if (label.length == 0) label = $(this).closest(".builder-contact-field").find("label");
				var inputEl = label.next('.control-input').find('input,textarea');
				if (inputEl.prop('tagName') === 'TEXTAREA') {
					// Label displacement for textarea should be calculated with it's row count in mind
					label.css('top', (label.outerHeight() / 2 + inputEl.outerHeight() / parseInt(inputEl.prop('rows')) ) + 'px');
				} else {
					label.css('top', (label.outerHeight() / 2 + inputEl.outerHeight() / 2 ) + 'px');
				}
				label.css('left', '10px');
			}
		}).on('change', '.builder-contact-field .control-input input[type="checkbox"]', function(){
			var $group = $(this).closest('.control-input').find('input[type="checkbox"]');
			$group.prop('required', true);
			if($group.is(":checked")){
				$group.prop('required', false);
			}
		}).on('reset', 'form.builder-contact', function () {
			$(this).find('.builder-contact-field .control-input input[type="checkbox"]').prop('required', true);
		});

		function animated_labels(el, type) {
			var items = $('.module-contact.contact-animated-label', el);
			if (el && el.hasClass('contact-animated-label')) {
				items = items.add(el);
			}
			if (items.length > 0) {
				items.find('input,textarea').prop('placeholder', '').trigger('blur');
				setTimeout(function () {
					items.find('label').css({
						'-webkit-transition-property': 'top, left',
						'-webkit-transition-duration': '0.3s',
						'transition-property': 'top, left',
						'transition-duration': '0.3s',
						'visibility': 'visible'
					});
				}, 50);
			}
		};

		var ordering_fields = function () {

			$('.builder-contact-fields').each(function () {

				var mylist = $(this);
				var listitems = mylist.children('div').get();

				listitems.sort(function (a, b) {
					var compA = $(a).attr('data-order') ? parseInt( $(a).attr('data-order') ) : $(a).index();
					var compB = $(b).attr('data-order') ? parseInt( $(b).attr('data-order') ) : $(b).index();
					return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
				});
				$.each(listitems, function (idx, itm) {
					mylist.append(itm);
				});

			})

		};

		function contact_load(e,el,type){
			animated_labels(el, type);
			ordering_fields();
			captcha(el);
		}

		if (Themify.is_builder_active) {
			$body.on('builder_load_module_partial', contact_load);
			if(Themify.is_builder_loaded){
				contact_load();
			}
		}else{
			contact_load();
		}

	}
}(jQuery));