<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;
    use LoginTrait;
    use PageTrait;

    /**
     * Set up login for test.
     *
     * @author thangdv8182
     * @return void
     */

    public function setUp()
    {
        parent::setUp();
        $this->login([
                'email' => 'admin@example.com',
                'password' => 'admin@example.com'
            ]);
    }

    /**
     * Test link Url
     *
     * @author thangdv8182
     * @return void
     */

    public function testIndex()
    {
        $response = $this->call('GET', 'user-management/');
        $this->assertEquals(200, $response->status());
    }

    //---------------------------
    //
    // Test unit function
    //
    //---------------------------

    /**
     * Run first test CRUD for user management page.
     *
     * @author: thangdv8182
     */
    public function testCRUD()
    {
        // Get Project Id and Sprint name from database.
        $project_id = DB::table('projects')->first()->id;
        // $description as a data for test
        $description = "Project management - List Risk";

        $this->testGetCreate($project_id)
             ->read($project_id, $description)
             ->testGetEdit($project_id)
             ->read($project_id, $description)
             ->testDelete($project_id);
    }


    protected function testGetEdit($projectId)
    {
        $risk_id = DB::table('project_risk')->orderBy('id','DESC')->first()->id;
        return $this->visit('projects/' . $projectId.'/risk/list')
                    ->click('edit'.$risk_id)
                     ->seePageIs('projects/' . $projectId.'/risk/edit/'.$risk_id."?page=1")
                      ->type('40', 'propability')
                      ->press('save')
                     ->seePageIs('projects/' . $projectId.'/risk/list?page=1');
    }

    protected function read($project_id, $description)
    {
        return $this->visit('projects/' .$project_id.'/risk/list')
                    ->see($description);
    }
}
