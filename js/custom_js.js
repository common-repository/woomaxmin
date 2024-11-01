jQuery(document).ready(function() {
  jQuery('#womaxmin-option').submit(function() {
    jQuery(".sbmt_op").attr("disabled",true);
    jQuery(this).ajaxSubmit({
      error:function(){
        jQuery(".sbmt_op").attr("disabled",false);
        jQuery('#error_msg').html("<div id='massage'></div>");
        jQuery('#massage').append('<div role="alert" class="alert alert-danger alert-dismissible fade in"> <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button> <strong>Please enter a valid data!</strong></div>').show();
      },
       success: function(){
          jQuery('#error_msg').html("<div id='massage'></div>");
          jQuery('#massage').append('<div role="alert" class="alert alert-success alert-dismissible fade in"> <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button> <strong>Settings Saved Successfully</strong></div>').show();
          jQuery(".sbmt_op").attr("disabled",false);
       }
    });
    return false; 
  });
});