$('.closeMessage').click(function(){
    $('#errorMessage').removeClass('show').addClass('hide');
});

$(function () {
    'use strict';
    $("#importFileButton").click(function (){
        $.ajaxPrefilter(function(options, originalOptions, xhr) { 
            var token = $('meta[name="csrf-token"]').attr('content');
            if (token) {
                return xhr.setRequestHeader('X-CSRF-TOKEN', token); 
            }
        });
        $.ajax({
            method: "POST",  
            url: "/defect-report/check_file_name",
            dataType: "json",
            data:{
                _name: $("#upload-demo").val()
            },
            success: function(result) {
                if (result['data'] == '1') {
                    $('#confirmUpdate .modal-body').html("This file named " + result['fileName'] + " exists in database. Do you want to import this file?");
                    $('#confirmUpdate').modal({
                        backdrop: 'static',
                        keyboard: false
                    }).show();
                } else if (result['data'] == 'x'){
                    $('#errorMessage').removeClass('hide').addClass('show');
                    $('#errorMessage #message').text('File is required!');
                } else {
                    var uploadfile = new FormData($("#formImport")[0]);
                    $.ajax({
                        url: '/defect-report/import_bug',
                        type: 'POST',
                        dataType: "json",
                        data: uploadfile,
                        beforeSend: function(){
                            $('.ajax-loader').show();
                        },
                        complete: function(){
                            $('.ajax-loader').hide();
                        },
                        success: function(message) {
                            switch(message['switch']) {
                                case '1':
                                    $('#errorMessage').removeClass('hide').addClass('show');
                                    $('#errorMessage #message').text('The import file must be a file of type: xls, xlsx.');
                                    break;
                                case '2':
                                    $('#errorMessage').removeClass('hide').addClass('show');
                                    $('#errorMessage #message').text('Import max size file is 2MB!');
                                    break;
                                case '3':
                                    $('#errorMessage').removeClass('hide').addClass('show');
                                    $('#errorMessage #message').text(message['content']);
                                    break;
                                case '4':
                                    $('#errorMessage').removeClass('hide').addClass('show');
                                    $('#errorMessage #message').text(message['content']);
                                    break;
                                case '5':
                                    $('#errorMessage').removeClass('hide').addClass('show');
                                    var msgHtml = '';
                                    var eachMessage = message['content'];
                                    for(var i = 0 ; i < eachMessage.length ; i++){
                                        msgHtml += eachMessage[i] + '<br>';
                                    }
                                    $('#errorMessage #message').html(msgHtml);
                                    break;
                                case '6':
                                    $('#errorMessage').removeClass('hide').addClass('show');
                                    $('#errorMessage #message').text('Error when import file!');
                                    break;
                                case '7':
                                    $('#ticketID').val(message['importExcel']);
                                    $('#exportFileAfterImport').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    }).show();
                                    break;
                                case '8':
                                    $('#errorMessage').removeClass('hide').addClass('show');
                                    $('#errorMessage #message').text('Please select a specific Project before importing!');
                                    break;  
                            }
                        },
                        contentType: false,
                        processData: false,
                        mimeType:"multipart/form-data"
                    });
                }
            },
        });
        $('#listImport').modal('hide');
    });
    
    $("#confirmButton").click(function (){
        $.ajaxPrefilter(function(options, originalOptions, xhr) { 
            var token = $('meta[name="csrf-token"]').attr('content');
            if (token) {
                return xhr.setRequestHeader('X-CSRF-TOKEN', token); 
            }
        });
        var uploadfile = new FormData($("#formImport")[0]);
        $.ajax({
            url: '/defect-report/import_bug_after_confirm',
            type: 'POST',
            data: uploadfile,
            dataType: "json",
            beforeSend: function(){
                $('.ajax-loader').show();
            },
            complete: function(){
                $('.ajax-loader').hide();
            },
            success: function(message) {
                console.log(message);
                switch(message['switch']) {
                    case '1':
                        $('#errorMessage').removeClass('hide').addClass('show');
                        $('#errorMessage #message').text('The import file must be a file of type: xls, xlsx.');
                        break;
                    case '2':
                        $('#errorMessage').removeClass('hide').addClass('show');
                        $('#errorMessage #message').text('Import max size file is 2MB!');
                        break;
                    case '3':
                        $('#errorMessage').removeClass('hide').addClass('show');
                        $('#errorMessage #message').text(message['content']);
                        break;
                    case '4':
                        $('#errorMessage').removeClass('hide').addClass('show');
                        $('#errorMessage #message').text(message['content']);
                        break;
                    case '5':
                        $('#errorMessage').removeClass('hide').addClass('show');
                        var msgHtml = '';
                        var eachMessage = message['content'];
                        for(var i = 0 ; i < eachMessage.length ; i++){
                            msgHtml += eachMessage[i] + '<br>';
                        }
                        $('#errorMessage #message').html(msgHtml);
                        break;
                    case '6':
                        $('#errorMessage').removeClass('hide').addClass('show');
                        $('#errorMessage #message').text('Error when import file!');
                        break;
                    case '7':
                        $('#ticketID').val(message['importExcel']);
                        $('#exportFileAfterImport').modal({
                            backdrop: 'static',
                            keyboard: false
                        }).show();
                        break;
                    case '8':
                        $('#errorMessage').removeClass('hide').addClass('show');
                        $('#errorMessage #message').text('Please select a specific Project before importing!');
                        break;             
                }
            },
            contentType: false,
            processData: false,
            mimeType:"multipart/form-data"
        });
    });
    
    $("#confirmExport").click(function (){
       $('#exportFileAfterImport').modal('hide');
    });
});