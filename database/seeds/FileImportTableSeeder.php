<?php

use Illuminate\Database\Seeder;
use App\Models\FileImport;

class FileImportTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $data = [
            [
                'name' => 'Template_Cost_import.xlsx',
                'user_id' => 1,
                'status' => 2,
                'type' => 'Template',
                
            ],
            [
                'name' => 'Template_Bug_import.xlsx',
                'user_id' => 1,
                'status' => 2,
                'type' => 'Template',
            ],
        ];
        foreach ($data as $item)
            FileImport::create($item);
    }

}
