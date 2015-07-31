jQuery(document).ready(function($){
	$("#vcs_content").find("[id^='tab']").hide();
  $("#vcs_tabs li:first").attr("id","current");
  $("#vcs_content #tab1").fadeIn();
  
  $('#vcs_tabs a').click(function(e) {
      e.preventDefault();
      if ($(this).closest("li").attr("id") == "current"){
       return;       
      }
      else{             
        $("#vcs_content").find("[id^='tab']").hide();
        $("#vcs_tabs li").attr("id","");
        $(this).parent().attr("id","current");
        $('#' + $(this).attr('name')).fadeIn();
      }
  });
});