$(function () {
    'use strict';
    
    //bug and uat bug
    var datetime = [];
    $('.cost_graph_efficiency').each(function($key,$value){
        datetime.push($(this).html());
    });

    var name_cost_efficiency          = get_data_chart(".name_cost_efficiency");
    var name_fix_cost                 = get_data_chart(".name_fix_cost");
    var name_leakage                  = get_data_chart(".name_leakage");
    var name_number_UAT_bug           = get_data_chart(".name_UAT_bug_number");
    var name_weight_UAT_bug           = get_data_chart(".name_UAT_bug_weight");
    var name_customer_survey          = get_data_chart(".name_customer_survey");
    var name_defect_remove_efficiency = get_data_chart(".name_defect_remove_efficiency");
    var name_defect_rate              = get_data_chart(".name_defect_rate");
    var name_code_productivity        = get_data_chart(".name_code_productivity");
    var name_create_test_productivity = get_data_chart(".name_create_test_productivity");
    var name_tested_productivity      = get_data_chart(".name_tested_productivity");
    var get_analysis                  = get_description(".name_cost_efficiency");
    
    //call Number defect report by status chart
    if($('#cost_efficiency').length != ''){
        project_kpi_chart(datetime,'cost_efficiency',name_cost_efficiency,'line','Cost efficiency', '%', get_analysis);
    }
    
    if($('#fix_cost').length != ''){
        project_kpi_chart(datetime,'fix_cost',name_fix_cost,'line','Fixing cost','%', get_analysis);
    }
    
    if($('#leakage').length != ''){
        project_kpi_chart(datetime,'leakage',name_leakage,'line','Leakage','Wdef/mm', get_analysis);
    }
    
    if($('#customer_survey').length != ''){
        project_kpi_chart(datetime,'customer_survey',name_customer_survey,'line','Customer Survey','Point', get_analysis);
    }
    
    if($('#UAT_bug_number').length != ''){
        project_kpi_chart(datetime,'UAT_bug_number',name_number_UAT_bug,'line','Number UAT bug','Number', get_analysis);
    }
    
    if($('#UAT_bug_weight').length != ''){
        project_kpi_chart(datetime,'UAT_bug_weight',name_weight_UAT_bug,'line','Weight of UAT bug','Weight', get_analysis);
    }
    
    if($('#defect_remove_efficiency').length != ''){
        project_kpi_chart(datetime,'defect_remove_efficiency',name_defect_remove_efficiency,'line','Defect remove efficiency','%', get_analysis);
    }
    
    if($('#defect_rate').length != ''){
        project_kpi_chart(datetime,'defect_rate',name_defect_rate,'line','Defect rate','Wdef/mm', get_analysis);
    }
    
    if($('#code_productivity').length != ''){
        project_kpi_chart(datetime,'code_productivity',name_code_productivity,'line','Code productivity','LOC/mm', get_analysis);
    }
    
    if($('#testcase_productivity').length != ''){
        project_kpi_chart(datetime,'testcase_productivity',name_create_test_productivity,'line','Create test case productivity','TC/mm', get_analysis);
    }
    
    if($('#tested_productivity').length != ''){
        project_kpi_chart(datetime,'tested_productivity',name_tested_productivity,'line','Tested productivity','Tested/mm', get_analysis);
    }
});

function project_kpi_chart(xAxisData,container,seriesData,chartType,title, unit, analysis){
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
                text: unit
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
            formatter: function() {
                return '<b>'+ xAxisData[this.point.x] + '</b><br/>' +this.series.name + ':' + this.point.y + '<br/>Analysis: '+ analysis[this.point.x] ;
             }
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
function get_data_chart(parentEle)
{
    var name = [];
    var title = ['Target','Actual'];
    var k = 0;
    $(parentEle).each(function(key,value){
       var data = new Array();
       $(this).find('.data-plan').each(function(key,value){
           data.push(parseFloat($(this).html()));
       });
       name.push({
           'name':title[k++],
           'data':data,
       });
    });
    $(parentEle).each(function(key,value){
        var data = new Array();
        $(this).find('.data').each(function(key,value){
            data.push(parseFloat($(this).html()));
        });
        name.push({
            'name':title[k++],
            'data':data,
        });
     });
    return name;
}
function get_description(parentEle)
{
    var data = new Array();
    $(parentEle).each(function(key,value){
       $(this).find('.description').each(function(key,value){
           data.push($(this).html().trim());
       });
    });
    return data;
}