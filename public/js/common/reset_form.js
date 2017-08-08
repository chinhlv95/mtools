$(document).ready(function(){
    $('#configreset').click(function(){
        $("input.form-control").val('');
        $("textarea").val('');
        $("select").prop('selectedIndex' , 0);
        $(".error-message").remove();
        $("div").removeClass("has-error");
        $('input[name="check_time"]').prop('checked', false);
        $('input:radio[name="check_time"][value="1"]').prop('checked', true);
        $("#start_date").attr("disabled","disabled");
        $("#end_date").attr("disabled","disabled");
        $("#select_defalt_time").removeAttr("disabled","disabled");
        $("#searchResult").empty();
    });
})