$(function () {
    'use strict';
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content'),
        teamSelect = $('#team_id'),
        divisionSelect = $('#division_id'),
        $files_data = $('#files').val();
    var max_file_size           = 2048576, //allowed file size. (1 MB = 1048576)
        allowed_file_types      = ['doc','docx','xls','xlsx','xlsm','ppt','pptx','pdf','txt','png', 'gif', 'jpeg', 'pjpeg','jpg','PNG','GIF','JPEG','PJPEG','JPG'], //allowed file types
        result_output           = '#output #responsiveTable > tbody tr:nth-child(2)', //ID of an element for response output
        result_deleted_output = '#output #responsiveTable > tbody tr',
        message_area             = '.upload_message', // Class of element response error message
        progress_bar_id         = '#progress-wrp'; //ID of an element for response output
    
    //disable enter date
    $("#plant_start_date").keypress(function(event) {event.preventDefault();});
    $("#plant_end_date").keypress(function(event) {event.preventDefault();});
    $("#actual_start_date").keypress(function(event) {event.preventDefault();});
    $("#actual_end_date").keypress(function(event) {event.preventDefault();});
    
    //Handler upload file
    $('#upload_file').on('click',function(){
        var proceed = true, //set proceed flag
            error  = '', //define store error mesage
            total_files_size = 0, //default total files size
            files = $('#files')[0].files,
            file_type = '';
            //reset progressbar
            $(progress_bar_id +" .progress-bar").css("width", "0%");
            $(progress_bar_id + " .status").text("0%");
            
            if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
                error = "Your browser does not support new File API! Please upgrade."; //push error text
            }else{
                //check iterate files in file input field
                //check not select file
                var total_selected_files = files.length; 
                if(total_selected_files <= 0){
                    error = 'Please choose file before submit upload';
                    proceed = false;
                }
                
              //iterate files in file input field
                $(files).each(function(i, ifile){
                    file_type = ifile.name.split('.').pop();
                    if(ifile.value !== ''){ //continue only if file(s) are selected
                        if(allowed_file_types.indexOf(file_type) === -1){ //check unsupported file
                            error = "<b>"+ ifile.name + "</b> is unsupported file type!"; //push error text
                            proceed = false; //set proceed flag to false
                        }
                        total_files_size = total_files_size + ifile.size; //add file size to total size
                    }
                });
                
                //check total file size is greater than max file size
                if(total_files_size > max_file_size){
                    error = "You have "+total_selected_files+" file(s) with total size "+ Math.round(total_files_size / 1048576)+ "MB, Allowed size is 2MB, Try smaller file!"; //push error text
                    proceed = false; //set proceed flag to false
                }
                
                //if all condition pass and proceed == true
                if(proceed){
                    $.ajaxPrefilter(function(options, originalOptions, xhr) { // this will run before each request
                        var token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using
                        if (token) {
                            return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
                        }
                    });
                    var formData = new FormData($('#frmUpdateProject')[0]);
                    var project_id = $("#project_id").val();
                    $.ajax({
                        url: '/projects/edit/upload/' + project_id,
                        type: 'POST',
                        data: formData,
                        cache: false,
                        processData:false,
                        contentType: false,
                        xhr: function(){
                            var xhr = $.ajaxSettings.xhr();
                            //upload process
                            if(xhr.upload){
                                //reset progressbar
                                $(progress_bar_id +" .progress-bar").css("width", "0%");
                                $(progress_bar_id + " .status").text("0%");
                                xhr.upload.addEventListener('progress', progressHandlingFunction, true);
                            }
                            return xhr;
                        },
                        mimeType:"multipart/form-data"
                    }).done(function(respone){
                        if(respone != ''){
                            $('#files').val('');
                            $(result_output).after(respone);
                        }
                      //update progressbar
                        $(progress_bar_id +" .progress-bar").delay(3000).css("width", "0%");
                        $(progress_bar_id + " .status").delay(3000).text( "0%");
                        window.location.replace(window.location.href);
                    })
                }
            }
            
            $(message_area).html(""); //reset output
            if(error != ''){
                $(message_area).append(error);
            }else{
                $(message_area).html(""); //reset output
            }
    });
    
    //handler progress
    function progressHandlingFunction(e) {
        var position = e.loaded || event.position;
        var total = e.total;
        if (e.lengthComputable) {
           var e = parseInt((e.loaded / total) * 100);
        }
        
        //update progressbar
        $(progress_bar_id +" .progress-bar").css("width", e + "%");
        $(progress_bar_id + " .status").text( e + "%");
    }
    
    $('#responsiveTable').on('click','.delete',function(){
        $('#file-id').val($(this).attr('dataId'));
        $('#file-name').val($(this).attr('name'));
        $('#deleteModal').modal({'show': true});
    });
    
    $('#btn-delete-file').on('click',function(){
        $.ajaxPrefilter(function(options, originalOptions, xhr) { // this will run before each request
            var token = $('meta[name="csrf-token"]').attr('content'); // or _token, whichever you are using
            if (token) {
                return xhr.setRequestHeader('X-CSRF-TOKEN', token); // adds directly to the XmlHttpRequest Object
            }
        });
        var formData = new FormData($('#frmDeleteFile')[0]),
            project_id = $('#project-id').val();
        $.ajax({
            url: '/projects/edit/uploads/delete/' + project_id,
            type: 'POST',
            cache: false,
            processData: false,
            contentType: false,
            data: formData,
            mimeType:"multipart/form-data"
        }).done(function(respone){
            $(result_deleted_output).not('tr:nth-child(1),tr:nth-child(2)').remove();
            $(result_output).after(respone);
            $('#btn_delete_all').attr('disabled','disabled');
            if($('#deleteAll').is(':checked')){
                $('#deleteAll').attr('checked', false);
            }
        });
    });
    
    //handler checked all checkbox
    $('#deleteAll').on('click',function(){
        $('.action .delete_item').prop('checked',this.checked);
    });
    
    $('#btn_delete_all').attr('disabled','disabled');
    var checkedTotal = 0;
    //check listener of checkbox to disable,enable button delete
    $('#responsiveTable').on('change','.delete_item',function(){
        if($('#delete_item:checked').length > 0) {
            $('#btn_delete_all').removeAttr('disabled');
            if($('#delete_item:checked').length == $('.delete_item').length - 1) {
                $('#deleteAll').prop('checked', true);
            } else {
                $('#deleteAll').prop('checked', false);
            }
        } else {
            $('#deleteAll').prop('checked', false);
            $('#btn_delete_all').attr('disabled','disabled');
        }
    });
    
    //handler delete file when click delete all file
    $('#btn_delete_all').on('click',function(){
        $('#deleteAllItemModal').modal({'show': true});
    });
    
    $('#deleteAllItemModal #btn-delete-file').on('click',function(){
        var checkedValues = new Array(),
        project_id = $('#project-id').val();
        $('.tbl-image-list').find('input:checkbox:checked').each(function(i,index){
            if(this.value != ''){
                checkedValues.push({
                    id: this.value,
                    value: $(this).attr('file_name')
                });
            }
         });
        $.ajax({
            url: '/projects/edit/uploads/delete/'+project_id,
            type: 'POST',
            data: {
                _token: CSRF_TOKEN,
                _checkedValue : checkedValues
            }
        }).done(function(respone){
            $(result_deleted_output).not('tr:nth-child(1),tr:nth-child(2)').remove();
            $(result_output).after(respone);
            $('#btn_delete_all').attr('disabled','disabled');
            if($('#deleteAll').is(':checked')){
                $('#deleteAll').attr('checked', false);
            }
        });            
    });
    
    //handler append resource
    var totalResource = '',
        resource_params = new Array(),
        deleteBtn = $('.delete-resource'),
        addBtn = $('.add-resource'),
        resourceItem = $('.resource_items'),
        wrap = $('#resource');
    
        $(resourceItem).each(function(i,index){
            if(i == 0){
                totalResource = $(this).parent().parent().find('.resource_items option').length;
            }
            if(i != 0){
                $(this).parent().parent().find('.delete-resource').removeClass('hide').addClass('show');
            }
        });

        //get resource default data
        resourceItem.each(function(i,index){
            if(i == 0){
                $(this).find('option').each(function(a,b){
                    resource_params.push({
                        key: this.value,
                        value: this.text
                    });                    
                });
             return false;
            }
        });
        
        //handler on click event in add resource button
        addBtn.on('click',function(event){
            totalResource--;
          //call resrouce template
            var html = resrouceTmpl(resource_params);
            $('#resource').append(html);
            if(totalResource == 1){
                $(this).attr('disabled','disabled');
            }
        });
        
        wrap.on('click','.delete-resource',function(e,index){
            e.preventDefault();
            $(this).parent().parent().remove();
            if(index == 0){
                addBtn.on('click');
            }
            totalResource++;
            if(totalResource == 2){
                $(addBtn).removeAttr('disabled');
            }
        });
});

function resrouceTmpl(params){
    var html = "<div class='form-group row' class='row-item'>" +
    "<div class='col-md-3'>" +
        "<select name='resource_items[]' class='form-control input-sm resource-items'>";
  //push data resource in to template html
    for(var i=0;i < params.length; i++){
        html += '<option value="'+ params[i]['key'] +'">'+ params[i]['value'] +'</option>';
    }
    html += "</select>" +
                "</div>" +
                    "<div class='col-md-3'>" +
                        "<input class='form-control input-sm' name='resource_name[]' type='text'>" +
                    "</div>" +
                    "<div class='col-md-3 control-label'>" +
                        "<a href='#' class='delete-resource pull-left'>" +
                            "<i class='fa fa-trash-o' aria-hidden='true'></i></a>" +
                    "</div>" +
                 "</div>";
    return html;
}