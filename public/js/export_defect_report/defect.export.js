$('document').ready(function(){
    $("#report_type").change(function(){
        if($(this).val() == "summary")
        {
            $("#units_time").attr("disabled", 'disabled');
        }else $("#units_time").removeAttr("disabled");
    });
    $('#chk-all-export').click(function() {
        if($(this).is(':checked')) {
            $('.exportTable').find('.chk-row-export').each(function()  {
                $(this).prop('checked', true);
                $(this).parent().parent().parent().addClass('selected');
                $("#exportFile").removeClass('disabled');
            });
        } else {
            $('.exportTable').find('.chk-row-export').each(function() {
                $(this).prop('checked' , false);
                $(this).parent().parent().parent().removeClass('selected');
                $("#exportFile").addClass('disabled');
            });
        }
    });
    $('.chk-row-export').click(function() {
        if($('.chk-row-export').is(':checked')) {
            $("#exportFile").removeClass('disabled');
        } else {
            $("#exportFile").addClass('disabled');
        }
    });
});
