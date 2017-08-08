<?php

use Illuminate\Database\Seeder;
use App\Models\TicketType;

/**
 *
 * Nov 3, 20164:10:15 PM
 * @author tampt6722
 *
 */
class TicketTypeTableSeeder extends Seeder
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
                'key'                       => 1,
                'name'                      => 'User Story',
                'source_id'                 => 0,
                'related_id'                => 1,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 2,
                'name'                      => 'Request',
                'source_id'                 => 0,
                'related_id'                => 2,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 3,
                'name'                      => 'Q&A',
                'source_id'                 => 0,
                'related_id'                => 3,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 4,
                'name'                      => 'Requirement',
                'source_id'                 => 0,
                'related_id'                => 4,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 5,
                'name'                      => 'Design',
                'source_id'                 => 0,
                'related_id'                => 5,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 6,
                'name'                      => 'Code',
                'source_id'                 => 0,
                'related_id'                => 6,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 7,
                'name'                      => 'Test',
                'source_id'                 => 0,
                'related_id'                => 7,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 8,
                'name'                      => 'Deployment',
                'source_id'                 => 0,
                'related_id'                => 8,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 9,
                'name'                      => 'Bug',
                'source_id'                 => 0,
                'related_id'                => 9,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 10,
                'name'                      => 'Bug after release',
                'source_id'                 => 0,
                'related_id'                => 10,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 11,
                'name'                      => 'Task',
                'source_id'                 => 0,
                'related_id'                => 11,
                'integrated_ticket_type_id' => 0
            ],
            [
                'key'                       => 12,
                'name'                      => 'Other',
                'source_id'                 => 0,
                'related_id'                => 12,
                'integrated_ticket_type_id' => 0
            ],
        ];

        foreach ($data as $item){
            TicketType::create($item);
        }
    }
}
