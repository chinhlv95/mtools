<?php

use Illuminate\Database\Seeder;
use App\Models\FileImport;
class NewDataFileImportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //created row check file template Macro
        $data = [
            [
                'name' => 'Check_file_template.xlsm',
                'user_id' => 1,
                'status' => 2,
                'type' => 'Template',
                
            ],
            
        ];
        foreach ($data as $item)
            FileImport::create($item);
    }
}
