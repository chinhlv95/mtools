// Auto select check_time when load page
$(document).ready(function() {
    if(getUrlParameter('check_time') == null){
        $('input:radio[name="check_time"][value="1"]').prop('checked', true);
        $("#month").attr("disabled", true);
        $("#year").attr("disabled", true);
    }else if(getUrlParameter('check_time') == 1){
        $("#select_defalt_time").attr("disabled", false);
        $("#month").attr("disabled", true);
        $("#year").attr("disabled", true);
    }else if(getUrlParameter('check_time') == 3){
        $('.type_time_pd').hide();
        $('.type_time_my').show();
        $("#month").attr("disabled", false);
        $("#year").attr("disabled", false);
        $("#select_defalt_time").attr("disabled", false);
    }else{
        $("#select_defalt_time").attr("disabled", true);
        $("#month").attr("disabled", true);
        $("#year").attr("disabled", true);
    }
});

$("#default_time").click(function() {
    $("#select_defalt_time").attr("disabled", false);
    $("#start_date").attr("disabled", true);
    $("#end_date").attr("disabled", true);
    $("#month").attr("disabled", true);
    $("#year").attr("disabled", true);
});

$("#choose_time").click(function() {
    $("#select_defalt_time").attr("disabled", true);
    $("#start_date").attr("disabled", false);
    $("#end_date").attr("disabled", false);
    $("#month").attr("disabled", true);
    $("#year").attr("disabled", true);
});

function dateConfig(datepicker){
    //Datepicker
    var date = $(datepicker).datepicker()
        .on('changeDate',function(ev){
            $('.datepicker.dropdown-menu').hide();
    });
}

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}; 
    


