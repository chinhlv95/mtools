<?php

use Illuminate\Database\Seeder;
use App\Models\RootCause;

/**
 *
 * Nov 3, 20164:09:41 PM
 * @author tampt6722
 *
 */
class RootCauseTableSeeder extends Seeder
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
                        'name' =>'Requirements',
                        'source_id' => 0,
                        'integrated_root_id' => 0,
                        'related_id' => 1,
                     ],
                     [
                         'key' => 2,
                         'name' =>'Design Error',
                         'source_id' => 0,
                         'integrated_root_id' => 0,
                         'related_id' => 2,
                     ],
                     [
                         'key' => 3,
                         'name' =>'Code Error',
                         'source_id' => 0,
                         'integrated_root_id' => 0,
                         'related_id' => 3,
                     ],
                     [
                         'key' => 4,
                         'name' =>'Test Error',
                         'source_id' => 0,
                         'integrated_root_id' => 0,
                         'related_id' => 4,
                     ],
                     [
                         'key' => 5,
                         'name' =>'Configuration',
                         'source_id' => 0,
                         'integrated_root_id' => 0,
                         'related_id' => 5,
                     ],
                     [
                         'key' => 6,
                         'name' =>'Existing Bug',
                         'source_id' => 0,
                         'integrated_root_id' => 0,
                         'related_id' => 6,
                     ]
        ];
        foreach ($data as $item)
            RootCause::create($item);
    }
}
