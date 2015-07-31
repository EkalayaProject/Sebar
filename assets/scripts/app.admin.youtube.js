/**
 * Make sure the script is called from pages.
 */
//console.log('app.admin.youtube.js');

function browseYoutubeVideos() {
	tb_show('Browse Youtube Videos', '#TB_inline?width=1000&height=500&inlineId=viralcontentslider_browse_youtube_videos');
	jQuery('#viralcontentslider_youtube_keyword').focus();
}

jQuery(document).on('click', '#viralcontentslider_pick_selected_videos', function(e){
	e.preventDefault();

	var checkedVideo = jQuery('.viralcontentslider_youtube_select_video:checked');
	var checkedVideoLength = checkedVideo.length;

	if ( checkedVideoLength > 0 ) {
		videosHTML = '';
		checkedVideo.each(function(i){
			var videoId = jQuery(this).data('videoid');
			var published = jQuery(this).data('published');
			var title = jQuery(this).data('title');
			var description = jQuery(this).data('description');
			var trimmedtitle = jQuery(this).data('trimmedtitle');
			var duration = jQuery(this).data('duration');
			var strduration = jQuery(this).data('strduration');
			var thumbnail = jQuery(this).data('thumbnail');
			var linkVideo = jQuery(this).data('link');

			videosHTML += '<div class="theme viralcontentslider_youtube_video_thumbnail_' + videoId + '">';
			videosHTML += '<div class="theme-screenshot">';
			videosHTML += '<img alt="" src="' + thumbnail + '" title="' + title + ' - ' + duration + '">'
			videosHTML += '</div>';
			videosHTML += '<h3 class="theme-name" title="' + title + ' - ' + duration + '">' + trimmedtitle + '</h3>';
			videosHTML += '<div class="theme-actions">'
			videosHTML += '<a class="button button-secondary viralcontentslider_trash_video" title="Trash" data-videoid="' + videoId + '"><span class="dashicons dashicons-trash" style="padding-top:4px;"></span></a>&nbsp;';
			videosHTML += '<a href="' + linkVideo + '?TB_iframe=true&width=700&height=450" class="thickbox button button-primary" title="View"><span class="dashicons dashicons-visibility" style="padding-top:4px;"></span></a>';
			videosHTML += '</div>';
			videosHTML += '<input type="hidden" class="viralcontentslider_yvideos_' + videoId + '" name="viralcontentslider_yvideos[]" value="' + videoId + '_VCS_' + title + '_VCS_' + duration + '_VCS_' + strduration + '_VCS_' + linkVideo + '_VCS_' + thumbnail + '_VCS_' + published + '_VCS_' + description + '">';
			videosHTML += '</div>';
		});
		jQuery('#viralcontentslider_add_new_video').prepend(videosHTML);
		checkedVideo.removeAttr('checked');
		jQuery('#viralcontentslider_youtube_videos').trigger('reset');
		jQuery('.tb-close-icon').trigger('click');
	}else{
		return false;
	}
});

jQuery(document).on('submit', '#viralcontentslider_form_browse_youtube_videos', function(e){
	var elKeyword = jQuery('#viralcontentslider_youtube_keyword');
	var elButton = jQuery('#search_youtube_videos');
	var elLoader = jQuery('#viralcontentslider_loader_youtube');
	var elDisplay = jQuery('#viralcontentslider_display_youtube_thumbnail');

	jQuery.ajax({
		url: viralcontentslider_params.viralcontentslider_ajax_url,
		data: {
			keyword: elKeyword.val(),
			viralcontentslider_ajax_nonce : viralcontentslider_params.viralcontentslider_ajax_nonce,
			ajaxNode : 'browseYoutubeVideos',
			action : 'viralcontentslider_ajax'
		},
		type: 'POST',
		dataType: 'HTML',
		beforeSend: function(){
			elDisplay.html('');
			elKeyword.prop('disabled', true);
			elButton.prop('disabled', true);
			elLoader.html('Searching videos with keyword <strong>' + elKeyword.val() + ' ...</strong>' ).show();
		},
		complete: function(){
			elKeyword.prop('disabled', false);
			elButton.prop('disabled', false);
			elLoader.html('Videos for keyword <strong>' + elKeyword.val() + '</strong>');
			elKeyword.val('');
		},
		success: function(res){
			elKeyword.focus();
			elDisplay.html(res);
		}
	});
	return false;
});

jQuery(document).on('click', '.viralcontentslider_trash_video', function(){
	var videoid = jQuery(this).data('videoid');
	var target = jQuery('.viralcontentslider_youtube_video_thumbnail_' + videoid);
	jQuery('.viralcontentslider_yvideos_' + videoid).removeAttr('name');
	target.hide('slow', function(){ target.remove(); });
});
