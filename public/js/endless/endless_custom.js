$(function(){

  $('#carlineslider').owlCarousel({
    loop:false,
    margin:0,
    responsiveClass:true,
    nav:true,
    dots:false,
    autoplay:false,
    autoHeight:false,
    autoplayTimeout:4000,
    autoplayHoverPause:false,
    navText:false,
    responsive:{
      0:{
        items:1
      },
      481:{
        items:2
      },
      768:{
        items:3
      },
      992:{
        items:4
      }
    }
  });
  
  dateConfig('.datepicker');
  AssignDate('#startDate','#endDate');
  searchAssignRangerDate('#plant_start_date','#plant_end_date');
  searchAssignRangerDate('#searchStartDate','#searchEndDate');
  searchAssignRangerDate('#start_date','#end_date');
  searchAssignRangerDate('#actual_start_date','#actual_end_date');
});

function dateConfig(datepicker){
    //Datepicker
    var date = $(datepicker).datepicker()
        .on('changeDate',function(ev){
            $('.datepicker.dropdown-menu').hide();
    });
}

function searchAssignRangerDate(startDatePicker,endDatePicker){
    var checkin = $(startDatePicker).datepicker().on('changeDate', function(ev) {
      if (ev.date.valueOf() > checkout.date.valueOf()) {
        var newDate = new Date(ev.date)
        newDate.setDate(newDate.getDate() + 1);
        checkout.setValue(newDate);
      }
      if(ev.date.valueOf() < checkout.date.valueOf() ){
          var newDate = new Date(ev.date)
          newDate.setDate(newDate.getDate());
          checkout.setValue(newDate);
      }
      checkin.hide();
      $(endDatePicker)[0].focus();
    }).data('datepicker');
    
    var checkout = $(endDatePicker).datepicker({
      onRender: function(date) {
        if( date.valueOf() <= checkin.date.valueOf()){
            return 'disabled';
        }
      }
    }).on('changeDate', function(ev) {
      checkout.hide();
    }).data('datepicker');
}

function AssignDate(startDatePicker,endDatePicker){
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
     
    var checkin = $(startDatePicker).datepicker({
      onRender: function(date) {
        return date.valueOf() < now.valueOf() ? 'disabled' : '';
      }
    }).on('changeDate', function(ev) {
      if (ev.date.valueOf() > checkout.date.valueOf()) {
        var newDate = new Date(ev.date)
        newDate.setDate(newDate.getDate() + 1);
        checkout.setValue(newDate);
      }
      checkin.hide();
      $(endDatePicker)[0].focus();
    }).data('datepicker');
    var checkout = $(endDatePicker).datepicker({
      onRender: function(date) {
        return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
      }
    }).on('changeDate', function(ev) {
      checkout.hide();
    }).data('datepicker');
}