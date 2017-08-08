$(function () {
    'use strict';
    
    var pickerSelector = ['start_date','end_date'],
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
