<?php

use Illuminate\Database\Seeder;
use App\Models\Roles;

class CreateUserAdminJp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create user with admin-role
        $admin_data = [
            0 => [
                    'email'       => 'hirose@co-well.jp',
                    'user_name' => 'admin_hirose',
                    'password'    => 'cowell@123',
                    'first_name'  => 'Admin',
                    'last_name'   => 'System',
                    'permissions' => [
                                    'admin' => true,
                    ]
                ],
            1 => [
                    'email'       => 'tamura@co-well.jp',
                    'user_name' => 'admin_tamura',
                    'password'    => 'cowell@123',
                    'first_name'  => 'Admin',
                    'last_name'   => 'System',
                    'permissions' => [
                                    'admin' => true,
                    ],
                ],
            2 => [
                    'email'       => 'muramatsu@co-well.jp',
                    'user_name' => 'admin_muramatsu',
                    'password'    => 'cowell@123',
                    'first_name'  => 'Admin',
                    'last_name'   => 'System',
                    'permissions' => [
                                    'admin' => true,
                    ],
            ],
            3 => [
                    'email'       => 'yoshida@co-well.jp',
                    'user_name' => 'admin_yoshida',
                    'password'    => 'cowell@123',
                    'first_name'  => 'Admin',
                    'last_name'   => 'System',
                    'permissions' => [
                                    'admin' => true,
                    ],
            ],
            4 => [
                    'email'       => 'honma@co-well.jp',
                    'user_name' => 'admin_honma',
                    'password'    => 'cowell@123',
                    'first_name'  => 'Admin',
                    'last_name'   => 'System',
                    'permissions' => [
                                    'admin' => true,
                    ],
            ],
        ];
        foreach($admin_data as $item)
        {
            $role = Roles::find(1);
            $role->users()->attach(Sentinel::registerAndActivate($item));
        }
    }
}
