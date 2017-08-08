$('document').ready(function(){
    $('#choose_time').click(function(){
        $("#units_time").val($("#units_time option:first").val());
        $('#units_time option').each(function(key,value){
            if($(this).val() == 'month' || $(this).val() == 'year')
            {
                $(this).addClass('hidden');
            }
        });
    });
    $('#default_time').click(function(){
        $("#units_time").val($("#units_time option:first").val());
        $('#units_time option').each(function(key,value){
            if($(this).val() == 'month' || $(this).val() == 'year')
            {
                $(this).removeClass('hidden');
            }
        });
    });
    if ($("#choose_time").prop("checked")) {
        $("#units_time").val($("#units_time option:first").val());
        $('#units_time option').each(function(key,value){
            if($(this).val() == 'month' || $(this).val() == 'year')
            {
                $(this).addClass('hidden');
            }
        });
    }
    $('#department_id').on('change',function(){
        if($('#project_id').val() == -1)
        {
            $('#search').attr('disabled','disabled');
            $('#export').attr('disabled','disabled');
        }else{
            $('#search').removeAttr('disabled');
            $('#export').removeAttr('disabled');
        }
    });
    $('#division_id').on('change',function(){
        if($('#project_id').val() == -1)
        {
            $('#search').attr('disabled','disabled');
            $('#export').attr('disabled','disabled');
        }else{
            $('#search').removeAttr('disabled');
            $('#export').removeAttr('disabled');
        }
    });
    $('#team_id').on('change',function(){
        if($('#project_id').val() == -1)
        {
            $('#search').attr('disabled','disabled');
            $('.export_bug').attr('disabled','disabled');
        }else{
            $('#search').removeAttr('disabled');
            $('.export_bug').removeAttr('disabled');
        }
    });
    $('#project_id').on('change',function(){
        if($('#project_id').val() == -1)
        {
            $('#search').attr('disabled','disabled');
            $('.export_bug').attr('disabled','disabled');
        }else{
            $('#search').removeAttr('disabled');
            $('.export_bug').removeAttr('disabled');
        }
    });
    if($('#project_id').val() == -1)
    {
        $('#search').attr('disabled','disabled');
        $('.export_bug').attr('disabled','disabled');
    }
    $('#select_defalt_time').on('change',function(){
        if($(this).val() == 'this_year' || $(this).val() == 'last_year')
        {
            $('#units_time option').eq(1).prop('selected', true);
            $('#units_time option').each(function(key,value){
                if($(this).val() == 'day')
                {
                    $(this).addClass('hidden');
                }
            });
        }else{
            $('#units_time option').eq(0).prop('selected', true);
            $('#units_time option').each(function(key,value){
                if($(this).val() == 'day')
                {
                    $(this).removeClass('hidden');
                }
            });
        }
    });
});
