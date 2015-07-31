<?php
/**
 * Prevent the file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );

if ( $type == 'video' ) {
	$iframe = <<<IFRAME
	<iframe frameborder="0" noresize="noresize" name="preview-frame" src="{$youtubeEmbedPermalink}" class="vcs_full_screen_preview_frame" style="height:369px;"></iframe>
IFRAME;
} else {
	$iframe = <<<IFRAME
	<iframe frameborder="0" noresize="noresize" name="preview-frame" src="{$permalink}" class="vcs_full_screen_preview_frame" style="height:369px;"></iframe>
IFRAME;
}
?>
<html lang="en" class="no-js">
	<head>
	  <meta charset="utf-8">
	  <meta content="chrome=1" http-equiv="X-UA-Compatible">
		<title><?php echo $title; ?></title>
	  <meta content="width=device-width, minimum-scale=1, maximum-scale=1" name="viewport">
	  <!--[if (gt IE 8)]><!-->
	    <link type="text/css" rel="stylesheet" media="all" href="<?php echo VCSEXTENSION_PLUGIN_ASSETS_URL . '/landing/a1.css'; ?>">
	  <!--<![endif]-->
	  <!--[if (lte IE 8)]>
	    <link href="<?php echo VCSEXTENSION_PLUGIN_ASSETS_URL . '/landing/a2.css'; ?>" media="all" rel="stylesheet" type="text/css" />
	  <![endif]-->
	  <link type="text/css" rel="stylesheet" media="all" href="<?php echo VCSEXTENSION_PLUGIN_ASSETS_URL . '/landing/a3.css'; ?>">
	  <script type="text/javascript" src="<?php echo VCSEXTENSION_PLUGIN_ASSETS_URL . '/landing/a1.js'; ?>"></script>
	  <script>
	    var viralCalculateHeight = function() {
	      var headerDimensions = $('.vcs_preview_header').height();
	      $('.vcs_full_screen_preview_frame').height($(window).height() - headerDimensions);
	    }
	    $(document).ready(function() {
	      viralCalculateHeight();
	    });
	    $(window).resize(function() {
	      viralCalculateHeight();
	    }).load(function() {
	      viralCalculateHeight();
	    });
	  </script>
	</head>
	<body class="vcs_full_screen_preview">
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=<?php echo $facebookAppId; ?>=&version=v2.0";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
	  <div class="vcs_preview_header">
		  <div class="vcs_preview_logo">
		    <a href="<?php echo site_url(); ?>"><?php echo get_bloginfo( 'name' ); ?></a>
		    <?php echo get_bloginfo( 'name' ); ?>
		  </div>

	  	<div class="vcs_preview_actions">
	  		<a href="<?php echo $linkBack; ?>" style="text-decoration:none;color:#FFFFFF;font-size:14px;padding-right:10px;">&laquo;&laquo; Back</a>
		    <div class="vcs_preview_action_socialmedias">
		      <div class="fb-share-button" data-href="<?php echo $permalink; ?>" data-layout="button_count"></div>
		      <!--<div class="fb-like" data-href="<?php echo $permalink; ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>-->
		      <a class="twitter-share-button" href="https://twitter.com/share" data-url="<?php echo $permalink; ?>" data-via="<?php echo $twitterUsername; ?>" data-text="<?php echo $title; ?>">Tweet</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
		      <script src="https://apis.google.com/js/platform.js" async defer></script>
		      <div class="g-plus" data-action="share" data-annotation="bubble" data-href="<?php echo $permalink; ?>"></div>
					<!--<div class="g-plusone" data-size="medium" data-href="<?php echo $permalink; ?>"></div>-->
					<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script><script type="IN/Share" data-url="<?php echo $permalink; ?>" data-counter="right"></script>
					<a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark"  data-pin-color="red"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_20.png" /></a>
					<!-- Please call pinit.js only once per page -->
					<script type="text/javascript" async defer src="//assets.pinterest.com/js/pinit.js"></script>
		    </div>

	    	<div class="vcs_preview_action_removeframe">
		      <a href="<?php echo $permalink; ?>">
		        &nbsp;<i class="e-icon -icon-cancel"></i>
					</a>
	    	</div>
	  	</div>
		</div>
		<?php echo $iframe; ?>
	</body>
</html>
