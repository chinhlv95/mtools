$(function (){
    'use strict';
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    self = $('.tbl-project #sync_status');
    $(self).each(function(index,item){
        $(self[index]).on('click', function(){
            var id = $(this).val(),
                project_id = $(this).attr('project_id');
            $.ajax({
                method: 'POST',  
                url: "/projects/Inprogress/" + project_id,
                dataType: 'json',
                data:{
                    _token:CSRF_TOKEN
                },
                success: function(data) {
                    if(data == "1"){
                        $(self[index]).html("").html('Stop').addClass('btn-danger');
                    }else if(data == "0"){
                        $(self[index]).html("").html('Start').removeClass('btn-danger').addClass('btn-primary').parent().find('.sync_label').remove();
                    }
                },
            });
         });
    });
    
    var self1 = $('.tbl-project #active');
    $(self1).each(function(index,item){
        $(self1[index]).on('click', function(){
            var project_id = $(this).attr('project_id');
            $.ajax({
                method: 'POST',  
                url: "/projects/active/" + project_id,
                dataType: 'json',
                data:{
                    _token:CSRF_TOKEN
                },
                success: function(data) {
                    if(data == "0"){
                        $(self1[index]).find('i').removeClass('fa-times-circle').addClass('fa-check-circle');
                        $(self1[index]).parent().parent().find('#status_active').html("Inactive");
                        $(self1[index]).parent().parent().find('#sync_status').html("Disable").removeClass().addClass('btn btn-default').prop('disabled', true);
                        $(self1[index]).parent().parent().find('.sync_label').remove();
                    }else if(data == "1"){
                        $(self1[index]).find('i').removeClass('fa-check-circle').addClass('fa-times-circle');
                        $(self1[index]).parent().parent().find('#status_active').html("Active");
                        $(self1[index]).parent().parent().find('#sync_status').html("Start").removeClass().addClass('btn btn-primary').prop('disabled', false);
                    }
                },
            });
         });
    });
});
  