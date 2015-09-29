(function(){
	var ajaxdata = {
    viralcontentslider_ajax_nonce : viralcontentslider_params.viralcontentslider_ajax_nonce,
		action: "viralcontentslider_tinymce",
	};

	jQuery.post(viralcontentslider_params.viralcontentslider_ajax_url, ajaxdata, function(data){
		jQuery(data).appendTo("body").hide();
		jQuery(document).on('click', '.viralcontentslider_add_shortcode', function(){
      var shortcodeid = jQuery(this).data("id");
			tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[sebar id="' + shortcodeid + '"]');
			tb_remove();
		});
	});

  tinymce.create('tinymce.plugins.ViralContentSlider',{
    init : function(ed, url){
      ed.addButton('viralcontentslider',{
        title : 'Sebar',
        image : url+'/tinymceicon.png',
        onclick : function(){
          tb_show('Sebar - Pick', '#TB_inline?width=750&height=550&inlineId=vcs_shortcodes');
        }
      });
    },
    createControl: function(n, cm){
      return null;
    },
    getInfo: function(){
      return{
        longname : "Sebar",
        author : 'M Teguh A Suandi',
        authorurl : 'https://facebook.com/teguhsuandi',
        infourl : 'http://mtasuandi.com/',
        version : "1.0"
      };
    }
  });
  tinymce.PluginManager.add('viralcontentslider', tinymce.plugins.ViralContentSlider);
})();
