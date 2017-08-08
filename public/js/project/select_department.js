$(function () {
    'use strict';
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content'),
        teamSelect = $('#team_id'),
        divisionSelect = $('#division_id'),
        $files_data = $('#files').val();
    $('#department_id').on('change',function(){
        var id = $(this).val(),
            role = $(this).attr('role');
        $.ajax({
            method: 'POST',
            url: '/projects/fillter/' + id,
            dataType: 'json',
            data: {_token: CSRF_TOKEN}
        })
        .done(function(respone){
            console.log(respone);
            if(JSON.stringify(respone) != '[]'){
                divisionSelect.empty();
                divisionSelect.append($('<option>',{
                    value: -1,
                    text: '-------'
                }));
                $.each(respone, function(i, value) {
                    divisionSelect.append($('<option>', {
                        value: value.id,
                        text : value.name
                    }));
                });
                $('#division_id').trigger('change');
            }else{
                divisionSelect.empty();
                divisionSelect.append($('<option>', {
                    value: -1,
                    text : '-------'
                }));
                teamSelect.empty();
                teamSelect.append($('<option>', {
                    value: -1,
                    text : '-------'
                }));
            }
        })
    });

    $('#division_id').on('change',function(){
        var id = $(this).val(),
            role = $(this).attr('role');
        $.ajax({
            method: 'POST',
            url: '/projects/fillter/' + id,
            dataType: 'json',
            data: {_token: CSRF_TOKEN}
        })
        .done(function(respone){
            if(JSON.stringify(respone) != '[]'){
                teamSelect.html('');
                teamSelect.append($('<option>',{
                    value: -1,
                    text: '-------'
                }));
                $.each(respone, function(i, value) {
                    teamSelect.append($('<option>', {
                        value: value.id,
                        text : value.name
                    }));
                });
            }else{
                teamSelect.html('');
                teamSelect.append($('<option>', {
                    value: -1,
                    text : '-------'
                }));
            }
        });
    });
 });