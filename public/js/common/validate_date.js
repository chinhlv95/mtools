/**
 * 
 */
$(document).ready(function(){
    $(":input").inputmask();

    $.validator.addMethod(
        "vnDate",
        function(value, element) {
            // put your own logic here, this is just a (crappy) example
            return value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/);
        },
        "Please enter a date in the format dd/mm/yyyy."
    );
    $("#search_form").validate({
      //specify the validation rules
      rules: {
      start_date: {
          required:true,
          vnDate:true,
      },
      end_date: {
          required:true,
          vnDate:true,
      },
      },
      submitHandler: function(form){
          form.submit();
      }
      });
});