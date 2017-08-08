<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Message Reminder Language Lines
    |--------------------------------------------------------------------------
    |
    | Messages displayed after manipulation to create, delete, edit, search data
    |
    */
    'create_project_success' => 'Create project success!',
    'update_project_success' => 'Update project success!',
    'sync_data_success' => 'Synchronizing data project!',
    'create_projectversion_success' => 'Create project version success!',
    'update_projectversion_success' => 'Update project version success!',
    'delete_projectversion_success' => 'Delete project version success!',
    'create_ticket_type_success'    => 'Create ticket type success!',
    'create_project_kpi_success' => 'Create project KPI success!',
    'update_project_kpi_success' => 'Update project KPI success!',
    'delete_project_kpi_success' => 'Delete project KPI success!',
    'create_department_success' => 'Create department success!',
    'update_department_success' => 'Update department success!',
    'delete_department_success' => 'Delete department success!',
    'kpi_tooltip' => [
        'cost_efficiency' => [
            'obj' => 'Objective: measure efficiency of project effort usage',
            'formula' => 'Formula: total plan cost *100/ total actual cost',
        ],
        'fixing_bug_cost' => [
                        'obj' => 'Objective: measure cost of correcting a defective <br> product and the associated rework costs',
                        'formula' => 'Formula: total actual cost with Fix bug activity *100/ total cost',
        ],
        'leakage' => [
                        'obj' => 'Objective: measure quality of the delivered product for acceptance test. ',
                        'formula' => 'Formula: total weighted defect found by customer after releasing /Total project effort (men month)',
        ],
        'bug_after_release_num' => [
                        'obj' => 'Objective: measure quality of the delivered product for acceptance test',
                        'formula' => 'Formula:  count UAT bug (User Acceptance) by number',
        ],
        'bug_after_release_wei' => [
                        'obj' => 'Objective: measure quality of the delivered product for acceptance test',
                        'formula' => 'Formula:  count UAT bug (User Acceptance) by weighted',
        ],
        'customer_survey' => [
                        'obj' => 'Objective: measurement customer satisfaction based on survey',
                        'formula' => "Formula: get project's survey from Customer",
        ],
        'defect_remove_efficiency' => [
                        'obj' => 'Objective: measure of the development team ability to remove defects prior to release',
                        'formula' => 'Formula:  total weighted of Bug / (total weighted of Bug +total weighted of UAT Bug)',
        ],
        'defect_rate' => [
                        'obj' => 'Objective: measure the efficiency of the testing',
                        'formula' => 'Formula: total weighted of all Bugs/ actual cost (men month)',
        ],
        'code_productivity' => [
                        'obj' => 'Objective: measure programming productivity/ development productivity',
                        'formula' => 'Formula: lines of source code / programmer-month',
        ],
        'created_test_case_productivity' => [
                        'obj' => 'Objective: measure writting test case productivity',
                        'formula' => "Formula: total written test case / tester-month with 'Make test case' activity ",
        ],
        'tested_producactivity' => [
                        'obj' => 'Objective: measure running test case productivity',
                        'formula' => "Formula: total running test case / tester-month with 'Test' activity ",
        ],
    ],
    'delete_department_fail' => 'Can not delete this department/team because this department/team has a child team',
    'get_department_seccess' => 'Sync data department success!'
];