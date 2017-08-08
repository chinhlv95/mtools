$(document).ready(function() {
    var start_date = $("#start_date").val(),
    project_id = $("#project_id").val(),
    url = "/projects/id/kpi/end_datepicker";
    var string_date = start_date.split("/");
    var day = string_date[0];
    var month = string_date[1];
    var year = string_date[2];
    $("#kpi_end_date").datepicker({ 
        minDate: new Date(year, month - 1, day),
        dateFormat: "dd/mm/yy"
    });
    $("#kpi_end_date").on('change',function(e){
        var end_date = $("#kpi_end_date").val();
        putDataFrom(url,end_date,project_id,start_date);
    });
});
 
 function putDataFrom(url,end_date,project_id,start_date){
     var  CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
     $.ajax({
       method: "Post",
       url: url,
       data: {end_date:end_date,project_id:project_id,start_date:start_date,_token: CSRF_TOKEN},
       dataType: 'json',
       success: function(data)
       {
           $("#actual_cost_efficiency").val(data['costEfficiency']);
           $("#actual_fix_code").val(data['fixingBugCost']);
           $("#actual_leakage").val(data['leakage']);
           $("#actual_bug_after_release_number").val(data['numberUATBug']);
           $("#actual_bug_after_release_weight").val(data['weightUATBug']);
           $("#actual_defect_remove_efficiency").val(data['defectRemove']);
           $("#actual_defect_rate").val(data['defectRate']);
           $("#actual_code_productivity").val(data['codeProductivity']);
           $("#actual_test_case_productivity").val(data['testCaseProductivity']);
           $("#actual_tested_productivity").val(data['testedProductivity']);
       }
      });  
 }
