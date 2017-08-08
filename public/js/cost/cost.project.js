$(document).ready(function () {
    $('select[name=reportType]').change(function() {
        var type = $('select[name=reportType]').val();
        if (type == 'personal_detail_report') {
            $('select[name=status]').attr('disabled', 'disabled');
            $('select[name=project]').attr('disabled', 'disabled');
            $('.type_time_pd').hide();
            $('.type_time_my').show();
            $('#month_time').prop('checked', true);
            $("#select_defalt_time").attr("disabled", true);
            $("#month").attr("disabled", false);
            $("#year").attr("disabled", false);
            $("#start_date").attr("disabled", true);
            $("#end_date").attr("disabled", true);
        } else {
            $('#default_time').prop('checked', true);
            $("#select_defalt_time").attr("disabled", false);
            $("#start_date").attr("disabled", true);
            $("#end_date").attr("disabled", true);
            $("#month").attr("disabled", true);
            $("#year").attr("disabled", true);
            $('.type_time_pd').show();
            $('.type_time_my').hide();
        }
    });
    
    $('#search_button_1').click(function() {
        var type = $('select[name=reportType]').val();
        var team = $('select[name=team]').val();
        if (type == "personal_detail_report" && team == "-1") {
            $('#errorMessage').removeClass('hide').addClass('show');
            $('#errorMessage #message').text("Personal Detail Report must search with team!");
        } else {
            $('form#search_form').submit();
        }
    });
    
    $('#configreset').click(function() {
        $('#default_time').prop('checked', true);
        $("#select_defalt_time").attr("disabled", false);
        $("#start_date").attr("disabled", true);
        $("#end_date").attr("disabled", true);
        $("#month").attr("disabled", true);
        $("#year").attr("disabled", true);
        $('.type_time_pd').show();
        $('.type_time_my').hide();
    });
    
    $('#confirmButton').click(function() {
        $('#confirmUpdate').modal('hide');
    });
    
    $('#importFileButton').click(function() {
        $('#import').modal('hide');
    });
    $('#exportFileMonth').click(function() {
        $('#export').modal('hide');
    });
    
    $("#exportFile").click(function (){
        $('#export').modal('hide');
    });
    
    $('.closeMessage').click(function(){
        $('#errorMessage').removeClass('show').addClass('hide');
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

    $('#chk-all').click(function() {
        if($(this).is(':checked')) {
            $('.importTable').find('.chk-row').each(function()  {
                $(this).prop('checked', true);
                $(this).parent().parent().parent().addClass('selected');
                $(".nextStep").removeClass('disabled');
            });
        } else {
            $('.importTable').find('.chk-row').each(function() {
                $(this).prop('checked' , false);
                $(this).parent().parent().parent().removeClass('selected');
                $(".nextStep").addClass('disabled');
            });
        }
    });

    $('.chk-row').click(function() {
        if($('.chk-row').is(':checked')) {
            $(".nextStep").removeClass('disabled');
        } else {
            $(".nextStep").addClass('disabled');
        }
    });
    
    $(".nextStep").click(function() {
        $(".prevStep").removeClass('disabled');
        $(".nextStep").addClass('disabled');
        
        $('#importStep1').removeClass('active');
        $('#importStep1').addClass('disabled');
        
        $('#importStep2').removeClass('disabled');
        $('#importStep2').addClass('active');
        
        $('#importStep2').find('a').attr("data-toggle","tab");
        $('#importStep1').find('a').removeAttr("data-toggle");
        
        $('#tableListProject').removeClass('in active');
        $('#importFileData').addClass('in active');
        
        $('#importFileButton').removeClass('hidden');
    });
    
    $(".prevStep").click(function() {
        $(".nextStep").removeClass('disabled');
        $(".prevStep").addClass('disabled');
        
        $('#importStep1').removeClass('disabled');
        $('#importStep1').addClass('active');
        
        $('#importStep2').removeClass('active');
        $('#importStep2').addClass('disabled');
        
        $('#importStep1').find('a').attr("data-toggle","tab");
        $('#importStep2').find('a').removeAttr("data-toggle");
        
        $('#tableListProject').addClass('in active');
        $('#importFileData').removeClass('in active');
        
        $('#importFileButton').addClass('hidden');
    });
});