$(function () {
    'use strict';
    
    /*==================Handler Cost comparision (hour) graph ================*/
    var seriesData = [],
        projectName = [], //get by html of class pj_name
        TotalEffort = [], //get by html of class total_effort
        project_totalEffort = [];
    
        $('.pj_name').each(function(i,e){
            projectName.push($(this).html());
        });
        $('.total_effort').each(function(i,e){
           TotalEffort.push($(this).html()); 
        });
       
        for(var i = 0;i < projectName.length; i++){
            project_totalEffort.push(
               [
                   projectName[i],parseFloat(TotalEffort[i])
               ]
            );
        }
        
        seriesData.push({
                name: 'Total Effort',
                data:project_totalEffort
            });
    /*==================Handler Cost comparision (hour) graph ================*/
        
    
    //call Cost comparision (hour) graph
    if($('#graph_wrap').length != 0){
        cost_comparision_chart('category','cost_comparision_hour',seriesData,'column');
    }
});

//Cost comparision (hour) graph
function cost_comparision_chart(xAxisData,container,seriesData,chartType){
    var myChart = Highcharts.chart(container, {
        chart: {
            type: chartType
        },
        title: {
            text: 'Cost comparision (hour)'
        },
        xAxis: {
            type: xAxisData,
            labels: {
                rotation: -90,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        credits: {
            enabled: false
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total (hour)'
            }
        },
        legend: {
            enabled: true,
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -40,
            y: 20,
            floating: true,
        },
        tooltip: {
            pointFormat: 'Total: <b>{point.y:.1f} hours</b>'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                },
                color:'#00b050'
            }
        },
        series: seriesData
    });
}