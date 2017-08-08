<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_users')->truncate();

        $roles = [
                    [
                        'name' => 'Admin',
                        'slug' => 'admin',
                        'permissions' => [
                                            'user.view_list_project' => true,
                                            'user.active_inactive_project' => true,
                                            'user.create_project' => true,
                                            'user.change_status_sync' => true,
                                            'user.view_project_info' => true,
                                            'user.update_project_info' => true,
                                            'user.view_version' => true,
                                            'user.view_kpt' => true,
                                            'user.view_list_risk' => true,
                                            'user.view_member' => true,
                                            'user.create_version' => true,
                                            'user.delete_version' => true,
                                            'user.update_version' => true,
                                            'user.create_kpt' => true,
                                            'user.update_kpt' => true,
                                            'user.delete_kpt' => true,
                                            'user.create_risk' => true,
                                            'user.update_risk' => true,
                                            'user.delete_risk' => true,
                                            'user.assign_member' => true,
                                            'user.delete_member' => true,
                                            'user.edit_member' => true,
                                            'user.view_personal_cost' => true,
                                            'user.view_project_cost' => true,
                                            'user.import_cost' => true,
                                            'user.export_cost' => true,
                                            'user.view_defect' => true,
                                            'user.export_defect' => true,
                                            'user.import_defect' => true,
                                            'user.view_quality_report_by_project' => true,
                                            'user.view_quality_report_by_member' => true,
                                            'user.view_quality_report_by_project_member' => true,
                                            'user.admin_setting' => true,
                                            'user.view_roles' => true,
                                            'user.add_new_role' => true,
                                            'user.delete_role' => true,
                                            'user.edit_role' => true,
                                        ]
                    ],
                    [
                        'name' => 'Member',
                        'slug' => 'member'
                    ],
                    [
                        'name' => 'Dev',
                        'slug' => 'dev'
                    ],
                    [
                        'name' => 'DevL',
                        'slug' => 'devl'
                    ],
                    [
                        'name' => 'QA',
                        'slug' => 'qa'
                    ],
                    [
                        'name' => 'QAL',
                        'slug' => 'qal'
                    ],
                    [
                        'name' => 'BSE/VN',
                        'slug' => 'bse/vn'
                    ],
                    [
                        'name' => 'BSE/JP',
                        'slug' => 'bse/jp'
                    ],
                    [
                        'name' => 'Comtor',
                        'slug' => 'comtor'
                    ],
                    [
                        'name' => 'JP Supporter',
                        'slug' => 'jp supporter'
                    ],
                    [
                        'name' => 'Sub BSE',
                        'slug' => 'sub bse'
                    ],
                    [
                        'name' => 'PM',
                        'slug' => 'pm'
                    ],
                    [
                        'name' => 'General Director',
                        'slug' => 'general director'
                    ],
                    [
                        'name' => 'Department Manager',
                        'slug' => 'department manager'
                    ],
                    [
                        'name' => 'Division Manager',
                        'slug' => 'division manager'
                    ],
                    [
                        'name' => 'Team Leader',
                        'slug' => 'team leader'
                    ]
        ];

        foreach ($roles as $role) {
            Sentinel::getRoleRepository()->createModel()->fill($role)->save();
        }

        // Create user with admin-role
        $admin_data = [
                'email'       => 'admin@co-well.com.vn',
                'user_name' => 'admin',
                'password'    => 'cowell@123',
                'first_name'  => 'Admin',
                'last_name'   => 'System',
                'related_id' => 1,
                'permissions' => [
                        'admin' => true,
                ]
        ];

        $admin = Sentinel::registerAndActivate($admin_data);
        $role = Sentinel::findRoleBySlug('admin');
        $role->users()->attach($admin);
    }
}
