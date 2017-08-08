$("#radio_id").click(function() {
    $("#project_id").attr("disabled", false);
    $("#project_key").attr("disabled", true);
});

$("#radio_key").click(function() {
    $("#project_id").attr("disabled", true);
    $("#project_key").attr("disabled", false);
});