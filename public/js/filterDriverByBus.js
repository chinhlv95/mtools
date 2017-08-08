$(document).ready(function(){
    $('#bus').on('change', function (e) {
        var optionSelected = $("option:selected", this);
        var bus_id = this.value;
        var start_time =$('#start_time').val();
        $.ajax({
            url: "/bus-times/getDriver",
            type: "GET",
            contentType: "JSON",
            data: {bus_id: bus_id, start_time: start_time},
            success:function(data) {
                $('#driver option').each(function() {
                    if($(this).val() == data) {
                        $(this).prop("selected", true);
                    }
                });
            }
        });
        
        $.ajax({
            url: "/bus-times/getSeats",
            type: "GET",
            contentType: "JSON",
            data: {bus_id: bus_id},
            success:function(data) {
                $('#seats').val(data);
            }
        });
    });
});