$(document).ready(function(){
    var email = $("#main_email_hidden").val();
    var data = jQuery.parseJSON(email);
    $( "#main_email" ).autocomplete({
          source: data,
          appendTo: "#emailMappingModal"
        });

    $('.update').click(function(){
        $('#related_email').val($(this).attr('mappingEmail'));
        $('#main_email').val($(this).attr('mainEmail'));
        $('#user_id').val($(this).attr('userId'));
        $('#emailMappingModal').modal({'show': true});        
    });    
});