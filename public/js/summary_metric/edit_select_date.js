$(document).ready(function() {
    $("#start_date").on('change',function(e){
            var start_date = $(this).val(),
            project_id = $("#project_id").val();
            end_date = $("#end_date").val(),
            url = "/projects/id/kpi/end_datepicker";
            putDataFrom(url,end_date,project_id,start_date);
    });
    $("#end_date").on('change',function(e){
          var end_date = $(this).val(),
          project_id = $("#project_id").val();
          start_date = $("#start_date").val(),
          url = "/projects/id/kpi/end_datepicker";
          putDataFrom(url,end_date,project_id,start_date);
    });
    $("#start_datepicker").keypress(function(event) {event.preventDefault();});
    $("#end_datepicker").keypress(function(event) {event.preventDefault();});
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