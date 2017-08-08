<?php
return [
    'stream_types' => [
        0 => 'Mtool',
        1 => 'Idom Backlog',
        2 => 'GDO Redmine',
        3 => 'Redmine 02',
        4 => 'Cowell Redmine',
        5 => 'Portal'
    ],

    'setting_type' => [
        1 => 'Status',
        2 => 'Tracker',
        3 => 'Priority',
        4 => 'Bug Weight',
        5 => 'Bug Type',
        6 => 'Root Cause',
        7 => 'Activity'
    ],
    'process_apply' => [
        1 => 'Scrum',
        2 => 'Waterfall',
        3 => 'Follow customer',
    ],

    'cost_type' =>[
        '1' => 'Share',
        '2' => 'Fix bid',
        '3' => 'Product'
    ],

    'position' =>[
        '1' => 'PM',
        '2' => 'Bse',
        '3' => 'Sub-Bse',
        '4' => 'QAL',
        '5' => 'QA',
        '6' => 'Devl',
        '7' => 'Dev',
        '8' => 'Comtor'
    ],

    'project_type' => [
        1 => 'New',
        2 => 'Maintenance',
        3 => 'New & Maintenance',
    ],

    'kpt_type' => [
        1 => 'Keep',
        2 => 'Problem',
        3 => 'Try',
    ],

    'risk_strategy' => [
        1 => 'Accept',
        2 => 'Reduce',
        3 => 'Avoid',
        4 => 'Tranfer',
    ],

    'status_file' => [
        0 => 'Inactive',
        1 => 'Active',

    ],
    'file_type' => [
        0 => 'Export',
        1 => 'Import',

    ],
    'status' => [
        0 => '-- All --',
        1 => 'Not start',
        2 => 'Inprogress',
        3 => 'Pending',
        4 => 'Closed',
    ],

    'risk_impact' => [
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
    ],

    'Brse' => [
        0 => 'Brse 1',
        1 => 'Brse 2',
        2 => 'Brse 3',
        3 => 'Brse 4',
        4 => 'Brse 5',
    ],

    'project_language' =>[
        1 => 'VB.net',
        2 => 'C#.net',
        3 => 'PHP',
        4 => 'Perl',
        5 => 'Ruby',
        6 => 'Swift',
        7 => 'Objective c',
        8 => 'Java',
    ],

    'paginate_number' =>[
        10 => '10',
        20 => '20',
        30 => '30',
        50 => '50',
    ],

    'role' =>[
        'CEO' => 'General Director',
        'Director' => 'Department Manager',
        'DM' => 'Division Manager',
        'BSE' => 'BSE/VN',
        'PM' => 'PM',
        'QA' => 'QA',
        'Member' =>'Member',
    ],

    'select_date'=> [
        'this_month' => 'This month',
        'last_month' => 'Last month',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'this_week' => 'This week',
        'last_week' => 'Last week',
        'this_year' => 'This year',
    ],

    'report_type' => [
        'summary' => 'By Summary',
        'time' => 'By Time'
    ],

    'units_time' => [
        'day' => 'Day',
        'day' => 'Day',
        'week' => 'Week',
        'month' => 'Month',
        'year' => 'Year',
    ],

    'description_tool_tip'=>[
        'Low' => 'Very minor defects, no way to affect the functionalities of the product.',
        'Medium' => 'Minor function does not work or works incorrectly or is documented incorrectly',
        'High' => 'Major function or system component does not work, work incorrectly or is documented incorrectly. ',
        'Serious' => 'Importance major function work incorrectly Non-functional requirements are not satifed: performance effects, security problem',
        'Fatal' => 'A fatal issue where a large piece of functionality or major system component is completely broken.There is no workaround and testing cannot continue. ',
    ],

    'cost_report_type' => [
        'summary_report' => 'Summary Report',
        'position_report' => 'Position Report',
        'graph_report' => 'Graph Report',
        'personal_report' => 'Personal Report',
        'personal_detail_report' => 'Personal Detail Report',
    ],

    'RECORD_PER_PAGE' => 10,

    'STRUCTURE_PERMISSION' =>[
       [
        'view_list_project' => 'View list project',//root element
            'active_inactive_project' => 'Active/ inactive project',
            'create_project' => 'Create project',
            'change_status_sync' => 'Start/ stop synchronization',
            'view_project_info' => 'View project management', // parent element
                'update_project_info' => 'Update project info',
      ],
      [
            'view_version' => 'View version', //parent element
                'create_version' => 'Create version',
                'delete_version' => 'Delete version',
                'update_version' => 'Update version',
     ],
     [
            'view_kpt' => 'View KPT', //parent element
                'create_kpt' => 'Create KPT',
                'update_kpt' => 'Update KPT',
                'delete_kpt' => 'Delete KPT',
      ],
      [
            'view_list_risk' => 'View list risk',//parent element
                'create_risk' => 'Create Rick',
                'update_risk' => 'Update Rick',
                'delete_risk' => 'Delete Rick',
      ],
      [
            'view_member' => 'View Member',// parent element
                'assign_member' => 'Assign member',
                'delete_member' => 'Delete member',
                'inactive_member' => 'Active/Inactive member',
                'edit_member' => 'Edit member',
        ]
    ],
    'COST_PERMISSION' => [
        ['view_personal_cost' => 'View personal cost',
         'view_project_cost' => 'View project cost'// parent element
        ],
        [
            'import_cost' => 'Import cost',
            'export_cost' => 'Export cost'
        ]

    ],
    'FILE_MANAGEMENT' => [
                    ['view_file_management' => 'View file management',]
    ],
    'DEFECT_PERMISSION' => [
        'view_defect' => 'View defect',// parent element
            'export_defect' => 'Export defect',
            'import_defect' => 'Import defect'
    ],
    'PQ_PERMISSION' => [
        'view_quality_report_by_project' => 'View Quality report by project',
        'view_quality_report_by_member' => 'View Quality report by member',
        'view_quality_report_by_project_member' => 'View Quality report by project member',
    ],
    'ADMINISTRATION_PERMISSION' => [
        ['admin_setting' => 'Admin setting',
         'view_roles' => 'View roles',// parent element
        ],
        [
            'add_new_role' => 'Add new',
            'delete_role' => 'Delete',
            'edit_role' => 'Edit'
        ]
    ],
    'bug_weight'=>[
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '5',
        '5' => '8',
    ],
    'bug_type'=>[
        '1' => 'Number of bug',
        '2' => 'Weight bug',
    ],
    'qp_report_type' => [
        1 => 'Data Table Report',
        2 => 'Graph Report'
    ],
    'status_user' => [
                    1 => 'UnLock',
                    2 => 'Lock'
    ],
    'men_month' => 168,

    'kpi_report_type' => [
        1 => 'Week',
        2 => 'Month',
        3 => 'Baseline',
    ],
    'qp_time_report_type' => [
        1 => 'Weekly',
        2 => 'Monthly'
    ],
    'report_select_date'=> [
                    'this_month' => 'This month',
                    'last_month' => 'Last month',
                    'last_three_month' => 'Last 3 months',
                    'last_six_month' => 'Last 6 months',
                    'this_year' => 'This year',
                    'last_year' => 'Last year',
    ],
    'resource'    => [
            0   => 'No',
            1   => 'yes',
    ],
    'status_project' => [
            1 => 'Not start',
            2 => 'Inprogress',
            3 => 'Pending',
            4 => 'Closed',
    ],

    'list_years_report' => [
                    'this_year' => 'This year',
                    'last_year' => 'Last year',
    ],
   'list_months_report' => [
               0=> 'All months',
               1=> 'January(1)',
               2=> 'February(2)',
               3=> 'March(3)',
               4=> 'April(4)',
               5=> 'May(5)',
               6=> 'June(6)',
               7=> 'July(7)',
               8=> 'August(8)',
               9=> 'September(9)',
               10=> 'October(10)',
               11=>'November(11)',
               12=> 'December(12)'
   ],
    'rank_top' => [
        5 => '5',
        10 => '10',
    ],
];