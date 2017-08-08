<?php

use Illuminate\Database\Seeder;
use App\Models\Priority;

/**
 *
 * Nov 3, 20164:07:56 PM
 * @author tampt6722
 *
 */
class PriorityTableSeeder extends Seeder
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
                        'name' =>'Low',
                        'source_id' => 0,
                        'integrated_priority_id' => 0,
                        'related_id' => 1,
                    ],
                    [
                        'key' => 2,
                        'name' =>'Normal',
                        'source_id' => 0,
                        'integrated_priority_id' => 0,
                        'related_id' => 2,
                    ],
                    [
                        'key' => 3,
                        'name' =>'High',
                        'source_id' => 0,
                        'integrated_priority_id' => 0,
                        'related_id' => 3,
                    ],
                    [
                        'key' => 4,
                        'name' =>'Urgent',
                        'source_id' => 0,
                        'integrated_priority_id' => 0,
                        'related_id' => 4,
                    ],
                    [
                        'key' => 5,
                        'name' =>'Immediately',
                        'source_id' => 0,
                        'integrated_priority_id' => 0,
                        'related_id' => 5,
                    ]
        ];
        foreach ($data as $item)
            Priority::create($item);
    }
}
