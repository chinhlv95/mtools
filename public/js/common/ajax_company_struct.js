$(function() {
    'use strict';

    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content'), 
                    departmentSelect = $('#department_id'), 
                    teamSelect = $('#team_id'),
                    divisionSelect = $('#division_id'), 
                    projectSelect = $('#project_id'), 
                    brseSelect = $('#brse_id'), 
                    apiUrl = '/fillcombobox', 
                    removeCharater = [ 'Department' ], 
                    oldData = '';

    // get data from api to fill combobox
    $.getJSON(apiUrl, {
        tagmode : "any",
        format : "json"
    }).done(function(data) {
        oldData = data;
    });
    departmentSelect.on('change', function(e) {
        var selectedId = $(this).val(), params = [], divisions = [];
        var deparmentId = [];
        $('#department_id option').each(function() {
            deparmentId.push($(this).val());
        });
        oldData[0].divisions.map(function(i, e) {
            if ($.inArray(i.parent_id.toString(), deparmentId) != -1) {
                divisions.push({
                    id : i.id,
                    name : i.name,
                    parent_id : i.parent_id
                });
            }
        });

        oldData[0].divisions.map(function(i, e) {
            if (i.parent_id == selectedId) {
                params.push({
                    id : i.id,
                    name : i.name,
                    parent_id : i.parent_id
                });
            }
        });
        if (params.length > 0) {
            divisionSelect.html('');
            divisionSelect.append($('<option>', {
                value : "-1",
                text : ' -- All --'
            }));
            appendSelect(divisionSelect, params);
        } else {
            divisionSelect.html('');
            divisionSelect.append($('<option>', {
                value : "-1",
                text : ' -- All --'
            }));
            appendSelect(divisionSelect, divisions);
        }
        divisionSelect.trigger("change");
    });
    divisionSelect.on('change', function(e) {
        var selectedId = $(this).val(), params = [], teams = [];
        var divisionId = [];
        $('#division_id option').each(function() {
            divisionId.push($(this).val());
        });
        oldData[0].teams.map(function(i, e) {
            if ($.inArray(i.parent_id.toString(), divisionId) != -1) {
                teams.push({
                    id : i.id,
                    name : i.name,
                    parent_id : i.parent_id
                });
            }
        });

        oldData[0].teams.map(function(i, e) {
            if (i.parent_id == selectedId) {
                params.push({
                    id : i.id,
                    name : i.name,
                    parent_id : i.parent_id
                });
            }
        });
        if (params.length > 0) {
            teamSelect.html('');
            
            teamSelect.append($('<option>', {
                value : "-1",
                text : ' -- All --'
            }));
            appendSelect(teamSelect, params);
        } else {
            teamSelect.html('');
            teamSelect.append($('<option>', {
                value : "-1",
                text : ' -- All --'
            }));
            appendSelect(teamSelect, teams);
        }
        teamSelect.trigger("change");
    });
    teamSelect.on('change', function(e) {
        var selectedId = $(this).val(), params = [], projects = [];
        var teamId = [];
        $('#team_id option').each(function() {
            teamId.push($(this).val());
        });
        console.log(teamId);
        oldData[0].projects.map(function(i, e) {
            if ($.inArray(i.department_id.toString(), teamId) != -1) {
                projects.push({
                    id : i.id,
                    name : i.name,
                    parent_id : i.parent_id
                });
            }
        });

        oldData[0].projects.map(function(i, e) {
            if (i.department_id == selectedId) {
                params.push({
                    id : i.id,
                    name : i.name,
                    parent_id : i.parent_id
                });
            }
        });
        if (params.length > 0) {
            projectSelect.html('');
            projectSelect.append($('<option>', {
                value : "-1",
                text : ' -- All --'
            }));
            appendSelect(projectSelect, params);
        } else {
            projectSelect.html('');
            projectSelect.append($('<option>', {
                value : "-1",
                text : ' -- All --'
            }));
            appendSelect(projectSelect, projects);
        }
        projectSelect.trigger("change");
    });
});

function appendSelect(select, params) {
    for (var i = 0; i < params.length; i++) {
        select.append($('<option>', {
            value : params[i].id,
            text : params[i].name,
            parent_id : params[i].parent_id
        }));
    }
}

function removeSelect(select) {
    select.empty();
    select.append($('<option>', {
        value : "-1",
        text : ' -- All --'
    }));
}