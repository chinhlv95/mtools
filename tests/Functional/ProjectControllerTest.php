<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class ProjectControllerTest extends TestCase
{
    use DatabaseTransactions;
    use LoginTrait;
    use PageTrait;

    public function setUp()
    {
        parent::setUp();
        $this->login([
                'email' => 'admin@example.com',
                'password' => 'admin@example.com'
            ]);
    }

    protected function create($name)
    {
        return $this->visit('/projects/create')
                    ->select('4', 'department_id')
                    ->type($name, 'name')
                    ->select('3', 'brse')
                    ->type('07/09/2016','plant_start_date')
                    ->type('07/09/2016','plant_end_date')
                    ->select('0', 'language_id')
                    ->type('0','project_id')
                    ->select('1','type_id')
                    ->select('2', 'status')
                    ->press('createProject')
                    ->seeInDatabase('projects',['name' => $name])
                    ->seePageIs('/projects')
                    ->see($name);
    }

    public function testCRUD()
    {
        $name = 'Chauchau' . time();
        $this->create($name);
    }
}
