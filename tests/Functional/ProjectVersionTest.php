<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class ProjectVersionTest extends TestCase
{
    use DatabaseTransactions;
    use LoginTrait;
    use PageTrait;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function setUp()
    {
        parent::setUp();
        $this->login([
                'email' => 'admin@example.com',
                'password' => 'admin@example.com'
        ]);
    }

    public function testIndex()
    {
        $projectId = DB::table('projects')->first()->id;
        $response = $this->call('GET', '/projects/'.$projectId.'/version');
        $this->assertEquals(200, $response->status());
    }

    protected function createAndSave($projectId, $description)
    {
        return $this->visit('projects/'. $projectId .'/version/create')

        ->type('New action for first test', 'name')
        ->type($description, 'description')
        ->type('25-10-2016', 'start_date')
        ->type('26-10-2016', 'end_date')
        ->seePageIs('/projects/'. $projectId . '/version')
        ->see($description);
    }

    protected function read($projectId, $description)
    {
        return $this->visit('/projects/'. $projectId . '/version')
        ->see($description);
    }

    protected function update($projectId, $newDes)
    {
        $version_id = DB::table('project_versions')->orderBy('id','DESC')->first()->id;
        return $this->visit('projects/'. $projectId .'/version/edit/'. $version_id)
        ->type($newDes, 'description')
        ->press('save')
        ->seeInDatabase('project_versions',
                [
                        'id' => $version_id,
                        'content' => $newDes
                ])
                ->seePageIs('/projects/'. $projectId . '/version')
                ->see($newDes);
    }

}
