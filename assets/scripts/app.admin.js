jQuery(document).ready(function($){
	/**
	 * Define default styling for jQuery Messenger
	 * More info at http://github.hubspot.com/messenger/docs/welcome/
	 */
	Messenger.options = {
    extraClasses: 'messenger-fixed messenger-on-top messenger-on-right',
    theme: 'flat'
	}

	$('.vcs_chosen_multiple').chosen({
		no_results_text: "Oops, nothing found!",
		placeholder_text_multiple : "Select",
		width: "100%"
	});

	$('.vcs_chosen_single').chosen();

	/**
	 * Redirect to correct page based on the selected dropdown viral type
	 */
	$(document).on('click', '#viralcontentslider_new', function(e){
		var type = $('#viralcontentslider_type').val();
		var button = $(this);

		button.prop('disabled', true);
		window.location.href = viralcontentslider_params.viralcontentslider_admin_url + '&tab=viral&node=' + type;
	});

	/**
	 * Display thickbox when the feed finder button is clicked 
	 * Feed finder using Google Js API & Google Feed API
	 */
	$(document).on('click', '#feed_finder', function(e){
		tb_show( 'Viral Traffic Boost - We help you to find available feeds', '#TB_inline?width=500&height=550&inlineId=viralcontentslider_feed_modal');
		$('#feed_keyword').focus();
	});

	/**
	 * Action feed finder
	 */
	$(document).on('submit', '#form_find_feed', function(e){
		var keyword = $('#feed_keyword').val();
		if(keyword == '') {
			showTemporaryMessage('Keyword is empty!', 'error', 5);
		}else{
			$('#feed_keyword').prop('disabled', true);
			helpMeToFindFeeds(keyword);
		}
		return false;
	});

	/**
	 * Put the selected feed to the form
	 */
	$(document).on('click', '.grabthisfeed', function(e){
		var feedUrl = $(this).attr('feed');
		$('#feed_url').val(feedUrl);
		$('.tb-close-icon').trigger('click');
	});

	/**
	 * Validation when user create new custom link
	 */
	$(document).on('submit', '#form_custom_link', function(e){
		if(this.custom_link_name.value == ''){
			showTemporaryMessage('Name is empty!', 'error', 5);
			this.custom_link_name.focus();
			return false;
		}
	});

	/**
	 * Validation when user create new custom
	 */
	$(document).on('submit', '#form_custom', function(e){
		if(this.custom_name.value == ''){
			showTemporaryMessage('Name is empty!', 'error', 5);
			this.custom_name.focus();
			return false;
		}

		if(this.custom_display.value == ''){
			showTemporaryMessage('Display is empty!', 'error', 5);
			this.custom_display.focus();
			return false;
		}
	});

	/**
	 * Validation when user create new video
	 */
	$(document).on('submit', '#form_video', function(e){
		if(this.video_name.value == ''){
			showTemporaryMessage('Name is empty!', 'error', 5);
			this.video_name.focus();
			return false;
		}
	});

	/**
	 * Validation when user create new tag
	 */
	$(document).on('submit', '#form_tag', function(e){
		if(this.tag_name.value == ''){
			showTemporaryMessage('Name is empty!', 'error', 5);
			this.tag_name.focus();
			return false;
		}
		if(this.tag_display.value == ''){
			showTemporaryMessage('Display is empty!', 'error', 5);
			this.tag_display.focus();
			return false;
		}
	});

	/**
	 * Validation when user create new link
	 */
	$(document).on('submit', '#form_link', function(e){
		if(this.link_name.value == ''){
			showTemporaryMessage('Name is empty!', 'error', 5);
			this.link_name.focus();
			return false;
		}
		if(this.link_links.value == ''){
			showTemporaryMessage('Link is empty!', 'error', 5);
			this.link_links.focus();
			return false;
		}
	});

	/**
	 * Validation when user create new category
	 */
	$(document).on('submit', '#form_category', function(e){
		if(this.category_name.value == ''){
			showTemporaryMessage('Name is empty!', 'error', 5);
			this.category_name.focus();
			return false;
		}
		if($('#category_categories').val() == null){
			showTemporaryMessage('Category is empty!', 'error', 5);
			return false;
		}
		if(this.category_display.value == ''){
			showTemporaryMessage('Display is empty!', 'error', 5);
			this.category_display.focus();
			return false;
		}
	});

	/**
	 * Validation when user create new author
	 */
	$(document).on('submit', '#form_author', function(e){
		if(this.author_name.value == ''){
			showTemporaryMessage('Name is empty!', 'error', 5);
			this.author_name.focus();
			return false;
		}
		if($('#author_authors').val() == null){
			showTemporaryMessage('Author is empty!', 'error', 5);
			return false;
		}
		if(this.author_display.value == ''){
			showTemporaryMessage('Display is empty!', 'error', 5);
			this.author_display.focus();
			return false;
		}
	});

	/**
	 * Validation when user create new article
	 */
	$(document).on('submit', '#form_article', function(e){
		if(this.article_name.value == ''){
			showTemporaryMessage('Name is empty!', 'error', 5);
			this.article_name.focus();
			return false;
		}

		if($('#article_posts').val() == null && $('#article_pages').val() == null){
			showTemporaryMessage('Article is empty!', 'error', 5);
			return false;
		}
	});

	/**
	 * Validation when user create new feed
	 */
	$(document).on('submit', '#form_feed', function(e){
		if(this.feed_name.value == ''){
			showTemporaryMessage('Name is empty!', 'error', 5);
			this.feed_name.focus();
			return false;
		}
		if(this.feed_url.value == ''){
			showTemporaryMessage('Feed url is empty!', 'error', 5);
			this.feed_url.focus();
			return false;
		}
		if(this.feed_display.value == ''){
			showTemporaryMessage('Display is empty!', 'error', 5);
			this.feed_display.focus();
			return false;
		}
	});
	
	/**
	 * Trash feed content
	 */
	$(document).on('click', '.viralcontentslider_trash_content_video', function(e){
		e.preventDefault();
		var title = $(this).data('title');
		var trashUrl = $(this).attr('href');

		msgTrashVideoContent = Messenger().post({
			message: 'Trash <strong><i>'+title+'</i></strong>?',
			type: 'info',
			actions: {
				deactivate: {
					label: 'Trash',
					action: function(){
						$.ajax({
							url : trashUrl,
							type: 'GET',
							dataType: 'HTML',
							beforeSend: function(){
								showTemporaryMessage('Processing...', 'info', 50);
							},
							complete: function(){
							},
							success: function(res){
								if(res == 'VIDEO_CONTENT_TRASHED'){
									var tr = $(e.target).closest("tr");
									$(tr).hide('slow', function(){$(tr).remove();});
									Messenger().hideAll();
								}else{
									Messenger().hideAll();
									showTemporaryMessage('Error while deleting video, please try again.', 'error', 5);
									return false;
								}
							}
						});
					}
				},
				cancel: {
					action: function(){
						msgTrashVideoContent.hide()
					}
				}
			}
		});
		return false;
	});

	/**
	 * Trash feed content
	 */
	$(document).on('click', '.viralcontentslider_trash_content_feed', function(e){
		e.preventDefault();
		var title = $(this).data('title');
		var link = $(this).data('link');
		var trashUrl = $(this).attr('href');

		msgTrashFeedContent = Messenger().post({
			message: 'Trash <strong><i>'+title+' ('+link+')</i></strong>?',
			type: 'info',
			actions: {
				deactivate: {
					label: 'Trash',
					action: function(){
						$.ajax({
							url : trashUrl,
							type: 'GET',
							dataType: 'HTML',
							beforeSend: function(){
								showTemporaryMessage('Processing...', 'info', 50);
							},
							complete: function(){
							},
							success: function(res){
								if(res == 'FEED_CONTENT_TRASHED'){
									var tr = $(e.target).closest("tr");
									$(tr).hide('slow', function(){$(tr).remove();});
									Messenger().hideAll();
								}else{
									Messenger().hideAll();
									showTemporaryMessage('Error while deleting content feed, please try again.', 'error', 5);
									return false;
								}
							}
						});
					}
				},
				cancel: {
					action: function(){
						msgTrashFeedContent.hide()
					}
				}
			}
		});
		return false;
	});

	/**
	 * Fetch feed
	 */
	$(document).on('click', '#vcs_fetch_feed', function(e){
		e.preventDefault();
		var feed = $(this).data('feed');
		var fetchUrl = $(this).attr('href');

		msgFetchFeed = Messenger().post({
			message: 'Continue fetch feed with url <strong><i>'+feed+'</i></strong>?',
			type: 'info',
			actions: {
				deactivate: {
					label: 'Fetch',
					action: function(){
						$.ajax({
							url : fetchUrl,
							type: 'GET',
							dataType: 'JSON',
							beforeSend: function(){
								showTemporaryMessage('Please wait, it may take a while...', 'info', 2000);
							},
							complete: function(){
							},
							success: function(res){
								if(res.code == 'success'){
									Messenger().hideAll();
									showTemporaryMessage('Success. Total new content : '+res.total, 'success', 5);
									return false;
								}else{
									Messenger().hideAll();
									showTemporaryMessage('Error while fetching feed, please try again. '+res.message, 'error', 5);
									return false;
								}
							}
						});
					}
				},
				cancel: {
					action: function(){
						msgFetchFeed.hide()
					}
				}
			}
		});
		return false;
	});

	/**
	 * Trash link
	 */
	$(document).on('click', '.vcs_custom_link_trash_link', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var name = $(this).attr('title');
		var trashUrl = $(this).attr('href');

		msgTrashCustomLink = Messenger().post({
			message: name + '?',
			type: 'info',
			actions: {
				deactivate: {
					label: 'Trash',
					action: function(){
						$.ajax({
							url : trashUrl,
							type: 'GET',
							dataType: 'HTML',
							beforeSend: function(){
								showTemporaryMessage('Processing...', 'info', 50);
							},
							complete: function(){
							},
							success: function(res){
								if(res == 'CUSTOM_LINK_TRASHED'){
									var el = $('#customlink_table_'+id);
									$(el).hide('slow', function(){$(el).remove();});
									Messenger().hideAll();
								}else{
									Messenger().hideAll();
									showTemporaryMessage('Error while deleting link, please try again.', 'error', 5);
									return false;
								}
							}
						});
					}
				},
				cancel: {
					action: function(){
						msgTrashCustomLink.hide()
					}
				}
			}
		});
		return false;
	});

	/**
	 * Trash created Viral Traffic Boost
	 */
	$(document).on('click', '.viralcontentslider_trash_viral', function(e){
		e.preventDefault();
		var name = $(this).data('name');
		var trashUrl = $(this).attr('href');

		msgTrashViral = Messenger().post({
			message: 'Trash <strong><i>' + name + '</i></strong>?',
			type: 'info',
			actions: {
				deactivate: {
					label: 'Trash',
					action: function(){
						$.ajax({
							url : trashUrl,
							type: 'GET',
							dataType: 'HTML',
							beforeSend: function(){
								showTemporaryMessage('Processing...', 'info', 50);
							},
							complete: function(){
							},
							success: function(res){
								if(res == 'VIRAL_TRASHED'){
									var tr = $(e.target).closest("tr");
									$(tr).hide('slow', function(){$(tr).remove();});
									Messenger().hideAll();
								}else{
									Messenger().hideAll();
									showTemporaryMessage('Error while deleting viral, please try again.', 'error', 5);
									return false;
								}
							}
						});
					}
				},
				cancel: {
					action: function(){
						msgTrashViral.hide()
					}
				}
			}
		});
		return false;
	});
});