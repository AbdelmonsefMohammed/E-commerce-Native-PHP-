$(function() {
	'use strict';

	//switch bettween login and signup
	$('.login-page h1 span').click(function() {
		$(this).addClass('selected').siblings().removeClass('selected');
		$('.login-page form').hide();
		$('.' + $(this).data('class')).fadeIn(100);
	});
	//Trigger (RUN) the selectBoxIt
	$('select').selectBoxIt({
		autoWidth: false
	});

	//Hide placeholder on form focus

	$('[placeholder]')
		.focus(function() {
			$(this).attr('data-text', $(this).attr('placeholder'));
			$(this).attr('placeholder', '');
		})
		.blur(function() {
			$(this).attr('placeholder', $(this).attr('data-text'));
		});

	//confirmation message on Button
	$('.confirm').click(function() {
		return confirm('Are you sure u want to Delete this ?');
	});

	//live preview for make now ads
	// $('.live-name').keyup(function(){
	// 	$('.live-preview .card-body h5').text($(this).val())
	// });

	// $('.live-desc').keyup(function(){
	// 	$('.live-preview .card-body p').text($(this).val())
	// });

	// $('.live-price').keyup(function(){
	// 	$('.live-preview .price-tag').text($(this).val())
	// });
	$('.live').keyup(function(){
	  $($(this).data('class')).text($(this).val())
	});
});
