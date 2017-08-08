$(document).ready(function() {
    $('.open-startdate').click(function(event){
        event.preventDefault();
        $('#start_date').focus();
    });
    $('.open-enddate').click(function(event){
        event.preventDefault();
        $('#end_date').focus();
    });
});