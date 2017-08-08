<?php

use Illuminate\Database\Seeder;
use App\Models\BugType;

/**
 *
 * Nov 3, 20164:08:09 PM
 * @author tampt6722
 *
 */
class BugsTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'key' => 1,
                'name' =>'GUI',
                'source_id' => 0,
                'integrated_bug_type_id' => 0,
                'related_id' => 1,
            ],
            [
                 'key' => 2,
                 'name' =>'FUNC',
                 'source_id' => 0,
                 'integrated_bug_type_id' => 0,
                 'related_id' => 2,
            ],
            [
                 'key' => 3,
                 'name' =>'SCEN',
                 'source_id' => 0,
                 'integrated_bug_type_id' => 0,
                 'related_id' => 3,
            ],
            [
                'key' => 4,
                 'name' =>'API',
                 'source_id' => 0,
                 'integrated_bug_type_id' => 0,
                 'related_id' => 4,
            ],
            [
                 'key' => 5,
                 'name' =>'Other',
                 'source_id' => 0,
                 'integrated_bug_type_id' => 0,
                 'related_id' => 5,
            ]
        ];
        foreach ($data as $item)
            BugType::create($item);
    }
}
