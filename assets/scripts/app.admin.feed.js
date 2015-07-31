google.load('feeds', '1');
var feedList = new Array();
function helpMeToFindFeeds(keyword){
	var elLoader = jQuery('#loader_feed');

	if (keyword == '') {
		alert('Keyword required!');
		return false;
	}

	elLoader.show();
	elLoader.html('Searching feeds with keyword <strong><i>' + keyword + '...</i></strong>');
	
	google.feeds.findFeeds(keyword, function(result) {
		if (!result.error) {
			elLoader.html('Feeds for keyword <strong><i>' + keyword + '</i></strong>');
			var html = '';
			for (var i = 0; i < result.entries.length; i++) {
				var entry = result.entries[i];
				if ( entry.url != '' ) {
					feedList[i] = entry.url;
					var count = i+1;
					html += '<span class="grabthisfeed" feed="' + entry.url + '" title="Pick this feed" style="cursor:pointer;">';
					html += '	<em><img src="//s2.googleusercontent.com/s2/favicons?domain=' + entry.link + '"/> <strong>' + removeHTMLTags(entry.title) + '</strong></em>';
					html += '	<p>' + removeHTMLTags(entry.contentSnippet) + '</p>';
					html += '</span>';
				}
			}
			jQuery('#feed_keyword').prop('disabled', false);
			jQuery("#display_feeds").empty();
			jQuery("#display_feeds").append(html);
			jQuery('#feed_keyword').val('');
			jQuery('#feed_keyword').focus();
		}
		elLoader.hide();   
	});
	return false;
}

function removeHTMLTags(html){
	var cleanHTML = html.replace(/&(lt|gt);/g, 
		function (strMatch, p1){
			return (p1 == "lt")? "<" : ">";
		}
	);

	return cleanHTML.replace(/<\/?[^>]+(>|$)/g, "");
}

function showTemporaryMessage(message, type, timeout) {
	Messenger().hideAll();
	Messenger().post({
		message: message,
		type: type,
		hideAfter: timeout
	});
}