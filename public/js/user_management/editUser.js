$(document).ready(function() {
    $('#sub_user').select2();
    $('button.closeMessage').click(function() {
        $('div.alert').hide();
    });
});