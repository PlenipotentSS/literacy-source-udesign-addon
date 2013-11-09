jQuery(document).ready(function($) {
	var formField = null;

	$('#upload_image_button').click(function() {
		$('html').addClass('Image');
		formField = $('#ls_slider_image').attr('name');
		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
		return false;
	});

	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html) {
		var fileURL;
		if (formField != null ) {
			fileURL = $('img',html).attr('src');
			$('#ls_slider_image').val(fileURL);
			$('#ls_slider_img_obj').attr('src', fileURL);
			tb_remove();
			$('html').removeClass('Image');
			formField = null;
		} else {
			window.original_send_to_editor(html);
		}
	};
});