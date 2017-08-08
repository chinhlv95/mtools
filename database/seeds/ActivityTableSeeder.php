<?php

use Illuminate\Database\Seeder;
use App\Models\Activity;

/**
 *
 * Nov 3, 2016 4:14:49 PM
 * @author tampt6722
 *
 */
class ActivityTableSeeder extends Seeder
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
                        'integrated_activity_id'   => 0,
                        'name'          => 'Preparation',
                        'source_id'     => 0,
                        'related_id' => 1
                        ],
                        [
                        'key' => 2,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Estimation',
                        'source_id'     => 0,
                        'related_id' => 2
                        ],
                        [
                        'key' => 3,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Make Q&A',
                        'source_id'     => 0,
                        'related_id' => 3
                        ],
                        [
                        'key' => 4,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Make user story',
                        'source_id'     => 0,
                        'related_id' => 4
                        ],
                        [
                        'key' => 5,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Make spec',
                        'source_id'     => 0,
                        'related_id' => 5
                        ],
                        [
                        'key' => 6,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Analysis',
                        'source_id'     => 0,
                        'related_id' => 6
                        ],
                        [
                        'key' => 7,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Understand spec',
                        'source_id'     => 0,
                        'related_id' => 7
                        ],
                        [
                        'key' => 8,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Basic design',
                        'source_id'     => 0,
                        'related_id' => 8
                        ],
                        [
                        'key' => 9,
                        'integrated_activity_id'   => 0,
                        'name'          => 'UI design',
                        'source_id'     => 0,
                        'related_id' => 9
                        ],
                        [
                        'key' => 10,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Detail Design',
                        'source_id'     => 0,
                        'related_id' => 10
                        ],
                        [
                        'key' => 11,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Review',
                        'source_id'     => 0,
                        'related_id' =>11
                        ],
                        [
                        'key' => 12,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Environment construction',
                        'source_id'     => 0,
                        'related_id' => 12
                        ],
                        [
                        'key' => 13,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Coding',
                        'source_id'     => 0,
                        'related_id' => 13
                        ],
                        [
                        'key' => 14,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Code understanding',
                        'source_id'     => 0,
                        'related_id' => 14
                        ],
                        [
                        'key' => 15,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Release check',
                        'source_id'     => 0,
                        'related_id' => 15
                        ],
                        [
                        'key' => 16,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Environment Implementation',
                        'source_id'     => 0,
                        'related_id' => 16
                        ],
                        [
                        'key' => 17,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Deployment',
                        'source_id'     => 0,
                        'related_id' => 17
                        ],
                        [
                        'key' => 18,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Test Plan',
                        'source_id'     => 0,
                        'related_id' => 18
                        ],
                        [
                        'key' => 19,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Make test case',
                        'source_id'     => 0,
                        'related_id' => 19
                        ],
                        [
                        'key' => 20,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Make test data',
                        'source_id'     => 0,
                        'related_id' => 20
                        ],
                        [
                        'key' => 21,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Test',
                        'source_id'     => 0,
                        'related_id' => 21
                        ],
                        [
                        'key' => 22,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Make test script',
                        'source_id'     => 0,
                        'related_id' => 22
                        ],
                        [
                        'key' => 23,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Analysis test result',
                        'source_id'     => 0,
                        'related_id' => 23
                        ],

                        [
                        'key' => 24,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Report test result',
                        'source_id'     => 0,
                        'related_id' => 24
                        ],
                        [
                        'key' => 25,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Confirm bug',
                        'source_id'     => 0,
                        'related_id' => 25
                        ],
                        [
                        'key' => 26,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Investigate',
                        'source_id'     => 0,
                        'related_id' => 26
                        ],
                        [
                        'key' => 27,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Fix bug',
                        'source_id'     => 0,
                        'related_id' => 27
                        ],
                        [
                        'key' => 28,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Management',
                        'source_id'     => 0,
                        'related_id' => 28
                        ],
                        [
                        'key' => 29,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Meeting',
                        'source_id'     => 0,
                        'related_id' => 29
                        ],
                        [
                        'key' => 30,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Support',
                        'source_id'     => 0,
                        'related_id' => 30
                        ],
                        [
                        'key' => 31,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Handover',
                        'source_id'     => 0,
                        'related_id' => 31
                        ],
                        [
                        'key' => 32,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Training/ Learning',
                        'source_id'     => 0,
                        'related_id' => 32
                        ],
                        [
                        'key' => 33,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Japanese learning',
                        'source_id'     => 0,
                        'related_id' => 33
                        ],
                        [
                        'key' => 34,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Translate',
                        'source_id'     => 0,
                        'related_id' => 34
                        ],
                        [
                        'key' => 35,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Support understanding',
                        'source_id'     => 0,
                        'related_id' => 35
                        ],
                        [
                        'key' => 36,
                        'integrated_activity_id'   => 0,
                        'name'          => 'Other',
                        'source_id'     => 0,
                        'related_id' => 36
                        ]
        ];
        foreach ($data as $item){
            Activity::create($item);
        }
    }
}
