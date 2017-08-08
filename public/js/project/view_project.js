$(document).ready(function(){
         $('.delete_risk').click(function(){
             $('#risk-id').val($(this).attr('dataId_risk'));
             $('#deleteModal_risk').modal({'show': true});
         });
     });
     $(document).ready(function(){
         $('.delete_kpt').click(function(){
             $('#kpt-id').val($(this).attr('dataId_kpt'));
             $('#deleteModal_kpt').modal({'show': true});
         });
     });
     $(document).ready(function(){
         $('[data-toggle="tooltip"]').tooltip();
     });