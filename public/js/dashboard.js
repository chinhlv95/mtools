(function ($) {

    var dashboardFn = window.dashboardFn || {};

    $.extend(dashboardFn, {
        version: 0.1,
        context: null,
        init: function () {
            dashboardFn.searchHistory();
            dashboardFn.listBusTime();
//            dashboardFn.emulatorCallCenter();
        },
        getParam: function () {
            var params = {};
            if (location.search) {
                var parts = location.search.substring(1).split('&');
                for (var i = 0; i < parts.length; i++) {
                    var nv = parts[i].split('=');
                    if (!nv[0])
                        continue;
                    params[nv[0]] = nv[1] || true;
                }
            }
            return params;
        },
        loadStartTime: function () {
            var e = document.getElementById("day");
            var day = e.options[e.selectedIndex].value;
            var url = '/?start_time=' + day;
            if (dashboardFn.getParam().bus_way_id) {
                url += '&bus_way_id=' + dashboardFn.getParam().bus_way_id;
            }
            if (dashboardFn.getParam().bus_time_id) {
                url += '&bus_time_id=' + dashboardFn.getParam().bus_time_id;
            }
            window.location.href = url;
        },
        loadBusWay: function (id) {
            var url = '/?bus_way_id=' + id;
            if (dashboardFn.getParam().start_time) {
                url += '&start_time=' + dashboardFn.getParam().start_time;
            }
            if (dashboardFn.getParam().bus_time_id) {
                url += '&bus_time_id=' + dashboardFn.getParam().bus_time_id;
            }
            window.location.href = url;
        },
        loadBusTime: function (id) {
            var url = '/?bus_time_id=' + id;
            if (dashboardFn.getParam().start_time) {
                url += '&start_time=' + dashboardFn.getParam().start_time;
            }
            if (dashboardFn.getParam().bus_way_id) {
                url += '&bus_way_id=' + dashboardFn.getParam().bus_way_id;
            }
            window.location.href = url;
        },
        searchHistory: function () {
            var mobilePhone = $('#mobile_phone').val();
            $.ajax({
                type: "GET",
                url: historyUrl,
                data: {mobile:mobilePhone},
                dataType: 'JSON',
            }).done( function (response) {
            	if(response.result === 'ok'){
            		$(".box-history").html(response.data.html);                        
                } else {
                	
                }
            }).fail( function(){
                console.log('Error');
            });
        },
        listBusTime: function () {
            var busWayId = $('#bus-way-id').val();
            var busTimeId = $('#bus-time-id').val();
            var startTime = $('#start-time').val();
            var interval = 20000;   //number of mili seconds between each call
            var refresh = function () {
                $.ajax({
                    type: "GET",
                    url: busTimeListUrl,
                    data: {busWayId: busWayId, busTimeId: busTimeId, startTime: startTime},
                    dataType: 'JSON',
                    success: function (response) {
                        if (response.result === 'ok') {
                            $('#bus-time-list').html(response.data.html);
                            setTimeout(function () {
                                refresh();
                            }, interval);
                        } else {

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                    }
                });
            };
            refresh();
        },
        emulatorCallCenter: function (emulatorCallCenter) {
            $.ajax({
                type: "GET",
                url: emulatorCallCenterUrl,
                data: {emulatorCallCenter:emulatorCallCenter},
                dataType: 'JSON',
                success: function (response) {
                    if(response.result == 'ok'){
                    	$('#sidebar-fly-calling').html(response.data.html);
                    } else {
                        $('#sidebar-fly-calling').html(response.data.html);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }
    });

    window.dashboardFn = dashboardFn;

})(jQuery);

$(document).ready(function () {
    dashboardFn.init();
});