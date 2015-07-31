//console.log('app.admin.media.js');

jQuery(document).ready(function($){
	$(document).on('click', '.vcs_browse_update_video_thumbnail', function(e){
		e.preventDefault();
		wp.media.editor.send.attachment = function(props, attachment){
			jQuery('#video_update_thumbnail').val(attachment.url);
		}
		wp.media.editor.send.link = function(a){
			jQuery('#video_update_thumbnail').val(a.url);
		}
		wp.media.editor.open(this);
		return false;
	});

	$(document).on('click', '.vcs_browse_thumbnail_update_image', function(e){
		e.preventDefault();
		wp.media.editor.send.attachment = function(props, attachment){
			jQuery('#feed_update_image').val(attachment.url);
		}
		wp.media.editor.send.link = function(a){
			jQuery('#feed_update_image').val(a.url);
		}
		wp.media.editor.open(this);
		return false;
	});

	$(document).on('click', '.vcs_browse_thumbnail', function(e){
		e.preventDefault();
		wp.media.editor.send.attachment = function(props, attachment){
			jQuery('#settings_default_thumbnail').val(attachment.url);
		}
		wp.media.editor.send.link = function(a){
			jQuery('#settings_default_thumbnail').val(a.url);
		}
		wp.media.editor.open(this);
		return false;
	});

	$(document).on('click', '.vcs_custom_link_browse_thumbnail', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		wp.media.editor.send.attachment = function(props, attachment){
			if(id == '-1'){
				jQuery('#custom_link_image').val(attachment.url);
			}else{
				jQuery('#custom_link_images_'+id).val(attachment.url);
			}
		}
		wp.media.editor.send.link = function(a){
			if(id == '-1'){
				jQuery('#custom_link_image').val(a.url);
			}else{
				jQuery('#custom_link_images_'+id).val(a.url);
			}
		}
		wp.media.editor.open(this);
		return false;
	});
});
