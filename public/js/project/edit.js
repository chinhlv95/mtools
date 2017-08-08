$(document).ready(function() {
    $(".number_only").keypress(function (e) {
        if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
    });
    $('.open-startdate').click(function(event){
        event.preventDefault();
        $('#start_date').focus();
    });
    $('.open-enddate').click(function(event){
        event.preventDefault();
        $('#end_date').focus();
    });
    $('.open-actual-startdate').click(function(event){
        event.preventDefault();
        $('#actual_start_date').focus();
    });
    $('.open-actualenddate').click(function(event){
        event.preventDefault();
        $('#actual_end_date').focus();
    });
    $(function () {
        'use strict';
        
        var pickerSelector = ['actual_start_date','actual_end_date'],
            selectedDate = '';
        pickerSelector.map(function(e,i){
            if($('#'+e).length > 0){
                $('#'+e).datepicker({
                    dateFormat: 'dd/mm/yy',
                    onClose:function(date, picker){
                        if(i == 0){
                            selectedDate = date;
                            $( "#"+pickerSelector[1] ).datepicker( "option", "minDate", selectedDate );
                        }
                        if(i == 1){
                            selectedDate = date;
                            $( "#"+pickerSelector[0] ).datepicker( "option", "maxDate", selectedDate );
                        }
                    }
                });
            }
        });
    ;});
});