<?php

use Illuminate\Database\Seeder;
use App\Models\Status;

/**
 *
 * Nov 3, 20164:09:59 PM
 * @author tampt6722
 *
 */
class StatusTableSeeder extends Seeder
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
                'key'                  => 1,
                'name'                 => 'New',
                'source_id'            => 0,
                'integrated_status_id' => 0,
                'related_id' => 1
            ],
            [
                'key'                  => 2,
                'name'                 => 'In Progress',
                'source_id'            => 0,
                'integrated_status_id' => 0,
                'related_id' => 2
            ],
            [
                'key'                   => 3,
                'name'                 => 'Resolved',
                'source_id'            => 0,
                'integrated_status_id' => 0,
                'related_id' => 3
            ],
            [
                'key'                  => 4,
                'name'                 => 'Feedback',
                'source_id'            => 0,
                'integrated_status_id' => 0,
                'related_id' => 4
            ],
            [
                'key'                  => 5,
                'name'                 => 'Closed',
                'source_id'            => 0,
                'integrated_status_id' => 0,
                'related_id' => 5
            ],
            [
                'key'                  => 6,
                'name'                 => 'Rejected',
                'source_id'            => 0,
                'integrated_status_id' => 0,
                'related_id' => 6
            ]
        ];
        foreach ($data as $item){
            Status::create($item);
        }
    }
}
