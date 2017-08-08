$(document).ready(function(){
    $('.delete').click(function(){
        $('#data-id').val($(this).attr('dataId'));
        $('#deleteModal').modal({'show': true});
    });
    $("#start_date").keypress(function(event) {event.preventDefault();});
    $("#end_date").keypress(function(event) {event.preventDefault();});
    $("#actual_start_date").keypress(function(event) {event.preventDefault();});
    $("#actual_end_date").keypress(function(event) {event.preventDefault();});
});