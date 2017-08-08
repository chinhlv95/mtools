$(function () {
    'use strict';
    
    //bug and uat bug
    var colors = ['#5BBD2B','#205AA7','#F9F400','#EC870E','#EE0000'];
    var colorsBug = ['#205AA7','#5BBD2B'];
    var datetime = [];
    $('.uat_bug').each(function($key,$value){
        datetime.push($(this).html());
    });
    var name = [];
    var k = 0;
    $('.name_bug').each(function(key,value){
       var data = new Array();
       $(this).find('.data').each(function(key,value){
           data.push(parseFloat($(this).html()));
       });
       name.push({
           'name':$(this).find('.name').html(),
           'data':data,
           'color': colorsBug[k++]
       });
    });
    var data_uat_bug = get_data_chart(".uat_bug",colors);
  //close and found
    var time_found_close = [];
    $('.found_close_bug').each(function($key,$value){
        time_found_close.push($(this).html());
    });
    var data_found_close = [];
    var k = 0;
    var typeShow = ['column','column','spline'];
    $('.name_found_close').each(function(key,value){
       var data = new Array();
       $(this).find('.data').each(function(key,value){
           data.push(parseFloat($(this).html()));
       });
       data_found_close.push({
           'type':typeShow[k],
           'name':$(this).find('.name').html(),
           'data':data,
           'color': colors[k++]
       });
    });
  //who make bug
    var title_status_bug = get_title_status('.by_status');
    var data_status_bug = get_data_chart_status('.by_status',colors);
    //who make bug
    var title_make_bug = get_title_status('.who_make');
    var data_make_bug = get_data_chart_status('.who_make',colors);
    //who found bug
    var title_found_bug = get_title_status('.who_found');
    var data_found_bug = get_data_chart_status('.who_found',colors);
//    
//    //who fix bug
    var title_fix_bug = get_title_status('.who_fix');
    var data_fix_bug = get_data_chart_status('.who_fix',colors);
//    //who fix bug

    var title_cause = get_title_status('.root_cause');
    var data_root_cause = get_data_chart_status('.root_cause',colors);
    //call Number defect report by status chart
    if($('#collumn_chart').length != ''){
        defect_by_status_chart(title_status_bug,'collumn_chart',data_status_bug,'column','By bug status');
    }
    if($('#line_chart_uat').length != ''){
        defect_by_line_chart(datetime,'line_chart_uat',name,'line','By bug and bug after release');
    }
    if($('#line_chart_open_close').length != ''){
        console.log(data_found_close);
        chartColumAndLine(time_found_close,'line_chart_open_close',data_found_close,'By KPI bug');
    }
    if($('#col_chart_make').length != ''){
        defect_by_status_chart(title_make_bug,'col_chart_make',data_make_bug,'column','By who made defect');        
    }
    if($('#col_chart_found').length != ''){
        defect_by_status_chart(title_found_bug,'col_chart_found',data_found_bug,'column','By who found defect');
    }
    if($('#col_chart_fix').length != ''){
        defect_by_status_chart(title_fix_bug,'col_chart_fix',data_fix_bug,'column','By who fix defect');
    }
    if($('#col_root_casue').length != ''){
        defect_by_status_chart(title_cause,'col_root_casue',data_root_cause,'column','By root cause');
    }
    
});

function defect_by_status_chart(xAxisData,container,seriesData,chartType,title){
    var myChart = Highcharts.chart(container, {
        chart: {
            type: chartType
        },
        title: {
            text: title,
        },
        xAxis: {
            categories: xAxisData
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total Bugs'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                }
            }
        },
        series: seriesData
    });
}
function chartColumAndLine(xAxisData,container,seriesData,title)
{
    var myChart = Highcharts.chart(container, {
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: title,
        },
        subtitle: {
            text: title,
        },
        xAxis: [{
            categories: xAxisData,
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Temperature',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'Rainfall',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            x: 120,
            verticalAlign: 'top',
            y: 100,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        series: seriesData,
    });
}
function defect_by_line_chart(xAxisData,container,seriesData,chartType,title){
    var myChart = Highcharts.chart(container, {
        chart: {
            type: chartType
        },
        title: {
            text: title,
        },
        xAxis: {
            categories: xAxisData
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total Bugs'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                }
            }
        },
        series: seriesData
    });
}
function get_data_chart(parentEle,colors)
{
    var name = [];
    var k = 0;
    $(parentEle).each(function(key,value){
       var data = new Array();
       $(this).find('.data').each(function(key,value){
           data.push(parseFloat($(this).html()));
       });
       name.push({
           'name':$(this).find('.name').html(),
           'data':data,
           'color': colors[k++]
       });
    });
    return name;
}

function get_title_status(table_name)
{
    var title_cause = [];
    $(table_name +' .data .name').each(function(key,value){
        title_cause.push($(this).html());
    });
    return title_cause;
}
function get_data_chart_status(table_name,colors)
{
    var name=[];
    var k = 0;
    $(table_name +' .title').each(function(key,value){
        var tharr=[];
        $(table_name).find(".data").each(function(){
            tharr.push(parseFloat($(this).find("td:eq("+(key+1)+")").text()));
        });
        name.push({
            name:$(this).html(),
            data:tharr,
            color:colors[k++]
        });
    });
    return name;
}

