$(document).ready(function(){
    $('.delete').click(function(){
        $('#kpt-id').val($(this).attr('dataId'));
        $('#deleteModal').modal({'show': true});
    });
});