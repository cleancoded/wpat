jQuery(function ($) {

	/*****************************************************************/
	/* ADD WP COLOR PICKER */
	/*****************************************************************/
	
    $( '.cpa-color-picker' ).wpColorPicker(); // Add Color Picker to all inputs that have 'cpa-color-picker' class
	
	
	/*****************************************************************/
	/* CALL WP MEDIA UPLOADER */
	/*****************************************************************/
	
	$(document).ready(function() {
		
		/* LOGO UPLOADER */
		
		var file_frame; // variable for the wp.media file_frame
		
		$( '#logo_upload_button' ).on( 'click', function( event ) {
			event.preventDefault();
			
			if ( file_frame ) {
				file_frame.open();
				return;
			}
			
			file_frame = wp.media.frames.file_frame = wp.media({
				title: $(this).val(),
				button: {
					text: $(this).val(),
				},
				multiple: false
			});

			file_frame.on( 'select', function() {
				var attachment = file_frame.state().get('selection').first().toJSON();
				$('#logo_upload').val(attachment.url);
				$('#logo_upload ~ .img-upload-container').css("background-image", "url(" + attachment.url + ")");
			});

			file_frame.open();
		});
		
		/* BACKGROUND IMAGE UPLOADER */
		
		var file_frame2; // variable for the wp.media file_frame
		
		$( '#login_bg_upload_button' ).on( 'click', function( event ) {
			event.preventDefault();
			
			if ( file_frame2 ) {
				file_frame2.open();
				return;
			}
			
			file_frame2 = wp.media.frames.file_frame = wp.media({
				title: $(this).val(),
				button: {
					text: $(this).val(),
				},
				multiple: false
			});

			file_frame2.on( 'select', function() {
				var attachment = file_frame2.state().get('selection').first().toJSON();
				$('#login_bg').val(attachment.url);
				$('#login_bg ~ .img-upload-container').css("background-image", "url(" + attachment.url + ")");
			});

			file_frame2.open();
		});
		
		/* TOOLBAR ICON UPLOADER */
		
		var file_frame3; // variable for the wp.media file_frame
		
		$( '#toolbar_icon_upload_button' ).on( 'click', function( event ) {
			event.preventDefault();
			
			if ( file_frame3 ) {
				file_frame3.open();
				return;
			}
			
			file_frame3 = wp.media.frames.file_frame = wp.media({
				title: $(this).val(),
				button: {
					text: $(this).val(),
				},
				multiple: false
			});

			file_frame3.on( 'select', function() {
				var attachment = file_frame3.state().get('selection').first().toJSON();
				$('#toolbar_icon').val(attachment.url);
				$('#toolbar_icon ~ .img-upload-container').css("background-image", "url(" + attachment.url + ")");
			});

			file_frame3.open();
		});
		
		/* LEFT MENU COMPANY LOGO UPLOADER */
		
		var file_frame4; // variable for the wp.media file_frame
		
		$( '#company_box_logo_upload_button' ).on( 'click', function( event ) {
			event.preventDefault();
			
			if ( file_frame4 ) {
				file_frame4.open();
				return;
			}
			
			file_frame4 = wp.media.frames.file_frame = wp.media({
				title: $(this).val(),
				button: {
					text: $(this).val(),
				},
				multiple: false
			});

			file_frame4.on( 'select', function() {
				var attachment = file_frame4.state().get('selection').first().toJSON();
				$('#company_box_logo').val(attachment.url);
				$('#company_box_logo ~ .img-upload-container').css("background-image", "url(" + attachment.url + ")");
			});

			file_frame4.open();
		});
		
	});
		
});
