<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        //disable foreign key check for this connection before running seeders
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call(UsersTableSeeder::class);
        $this->call(CrawlerTypesTableSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(ActivityTableSeeder::class);
        $this->call(TicketTypeTableSeeder::class);
        $this->call(StatusTableSeeder::class);
        $this->call(BugsWeightSeeder::class);
        $this->call(BugsTypeTableSeeder::class);
        $this->call(PriorityTableSeeder::class);
        $this->call(RootCauseTableSeeder::class);
        $this->call(FileImportTableSeeder::class);
        $this->call(NewDataFileImportTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
