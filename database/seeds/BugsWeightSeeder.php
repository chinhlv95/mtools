<?php

use Illuminate\Database\Seeder;
use App\Models\BugWeight;

/**
 *
 * Nov 3, 20164:08:42 PM
 * @author tampt6722
 *
 */
class BugsWeightSeeder extends Seeder
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
                'integrated_bug_weight_id' => 0,
                'related_id' =>  1,
            ],
            [
                'key' => 2,
                'name' =>'Medium',
                'source_id' => 0,
                'integrated_bug_weight_id' => 0,
                'related_id' =>  2,
            ],
            [
                'key' => 3,
                'name' =>'High',
                'source_id' => 0,
                'integrated_bug_weight_id' => 0,
                'related_id' =>  3,
            ],
            [
                'key' => 4,
                'name' =>'Serious',
                'source_id' => 0,
                'integrated_bug_weight_id' => 0,
                'related_id' =>  4,
            ],
            [
                'key' => 5,
                'name' =>'Fatal',
                'source_id' => 0,
                'integrated_bug_weight_id' => 0,
                'related_id' =>  5,
            ],
        ];
        foreach ($data as $item)
            BugWeight::create($item);
    }
}
