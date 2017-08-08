$(function () {
    'use strict';

    $('.btn-create').on('click',function(e){
        $('#source_id').val($('#source').val());
        $('#type_id').val($('#type').val());
        $('#create_form').submit();
    });

    $('.btn-search').on('click',function(e){
    	$('#source_id').val($('#source').val());
        $('#type_id').val($('#type').val());
        $(this).attr('disabled',true);
        $('#search_form').submit();
    });
    
    $('.btn-create-source').on('click',function(e){
        $(this).attr('disabled',true);
        $('#frm_create').submit();
    });
    
    $('.btn-update-source').on('click',function(e){
        $(this).attr('disabled',true);
        $('#frm_update').submit();
    });
});