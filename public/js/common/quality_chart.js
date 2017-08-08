$(function () {
  //====================== Productivity (KLOC/mm) chart ===============
    if($('#productivity_kloc').length != ''){
         getChart('.projectReportTable','.record','1','10','#productivity_kloc','column','Productivity (KLOC/mm)');
    }
  //====================== Productivity (TC/ mm)chart ===============
    if($('#productivity_tc_mt').length != ''){
        getChart('.projectReportTable','.record','1','11','#productivity_tc_mt','column','Productivity (TC/ mm) chart');
    }
  //====================== Quality (Bug/ KLOC) chart ===============
    if($('#quality_bug_kloc').length != ''){
        getChart('.projectReportTable','.record','1','12','#quality_bug_kloc','column','Quality (Bug/ KLOC)');
    }
  //====================== Quality (UAT Bug/Kloc) chart ===============
    if($('#quality_uat').length != ''){
        getChart('.projectReportTable','.record','1','13','#quality_uat','column','Quality (UAT Bug/Kloc)');
    }
  //====================== Quality (Bug/TC) chart ===============
    if($('#quality_bug_tc').length != ''){
        getChart('.projectReportTable','.record','1','14','#quality_bug_tc','column','Quality (Bug/TC)');
    }
  //====================== Quality (% Bug before release) chart ===============
    if($('#quality_bug_before_release').length != ''){
        getChart('.projectReportTable','.record','1','15','#quality_bug_before_release','column','Quality (% Bug before release)');
    }
  //====================== Quality (Bug / mm) chart ===============
    if($('#quality_bug_mm').length != ''){
        getChart('.projectReportTable','.record','1','16','#quality_bug_mm','column','Quality (Bug / mm)');
    }
});

function getChart(tableData,parentTrEle,xDataPosition,yDataPosition,container,chartType,chartTitle){
    var series = [],
    xAxys = [],
    yAxys = [],
    kloc_data = [];
    
    $(tableData).find(parentTrEle).each(function(i,e){
        xAxys.push($(this).find('td:eq('+xDataPosition+')').html());
        yAxys.push($(this).find('td:eq('+yDataPosition+')').html());
    });
    
    for(var i = 0;i < xAxys.length; i++){
        kloc_data.push(
           [
               xAxys[i],parseFloat(yAxys[i])
           ]
        );
    }
    
    series.push({
        name: 'Population',
        data: kloc_data
    });
    
    $(container).highcharts({
        chart: {
            type: chartType
        },
        title: {
            text: chartTitle
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Kloc: <b>{point.y}</b>'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    rotation: -90,
                    color: '#FFFFFF',
                    align: 'right',
                    format: '{point.y}',// one decimal
                    y: 10, // 10 pixels down from the top
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                },
                color:'#00b050'
            }
        },
        series: series
    });
}