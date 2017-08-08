$(document).ready(function() { 

    $('#devResponsiveTable, #qaResponsiveTable').DataTable({
        paging: true,
        searching: false,
        "lengthMenu": [ 10, 20, 30, 50 ],
        "columnDefs": [
                       { "orderable": false, "targets": [1,2]}
                     ],
        "language": {
            "lengthMenu": "Item display on page:   _MENU_",
            "info": "Total number of records: _TOTAL_",
            "paginate": {
                "previous":     "<<",
                "next":         ">>"
            }
        },
        "dom": '<"top"li>rt<"bottom"fp><"clear">',
        "fnDrawCallback": function (oSettings) {
            var pgr = $(oSettings.nTableWrapper).find('.dataTables_paginate')
            if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
              pgr.hide();
            } else {
              pgr.show()
            }
          }                  
    });
    
    $('#responsiveTable').DataTable({
        paging: true,
        searching: false,
        "lengthMenu": [ 10, 20, 30, 50 ],
        "columnDefs": [
                       { "orderable": false, "targets": 1,}
                     ],
        "language": {
            "lengthMenu": "Item display on page:   _MENU_",
            "info": "Total number of records: _TOTAL_",
            "paginate": {
                "previous":     "<<",
                "next":         ">>"
            }
        },
      
        "dom": '<"top"li>rt<"bottom"fp><"clear">',
        "fnDrawCallback": function (oSettings) {
            var pgr = $(oSettings.nTableWrapper).find('.dataTables_paginate')
            if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
              pgr.hide();
            } else {
              pgr.show()
            }
          }                  
    });
   
});
