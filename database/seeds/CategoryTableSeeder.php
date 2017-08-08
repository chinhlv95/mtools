<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoryTableSeeder extends Seeder
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
                'value' =>'Communication',
            ],
            [
                'value' =>'Technology',
            ],
            [
                'value' =>'Quality',
            ],
            [
                'value' =>'Requirement',
            ],
            [
                'value' =>'Customer',
            ],
            [
                'value' =>'Resources',
            ],
            [
                'value' =>'Funding',
            ],
            [
                'value' =>'Plan',
            ],
            [
                'value' =>'Estimation',
            ],
            [
                'value' =>'Control',
            ],
        ];
        foreach ($data as $item)
            Category::create($item);
    }
}
