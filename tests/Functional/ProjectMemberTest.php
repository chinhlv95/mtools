<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class ProjectMemberTest extends TestCase
{
    use DatabaseTransactions;
    use LoginTrait;
    use PageTrait;

    /**
     * Set up login for test.
     *
     * @author tampt6722
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
     * @author tampt6722
     * @return void
     */

    public function testUrl()
    {
        $project_id = DB::table('projects')->first()->id;
        $response = $this->call('GET','/projects/'. $project_id.'/members/list');
        $this->assertEquals(200, $response->status());
    }

    //---------------------------
    //
    // Test unit function
    //
    //---------------------------

    /**
     * Run first test CRUD for kpt page.
     * CRUD - Create, Read, Update, Delete
     *
     * @author: tampt6722
     */
    public function testCRUD()
    {
        // Get Project Id and Sprint name from database.
        $project_id = DB::table('projects')->first()->id;
        // $description as a data for test
        $description = "tuanpq6317@setacinq.com.vn";
        $this->read($project_id, $description);
    }

    /**
     *
     * @author tampt6722
     *
     * @param unknown $projectId
     * @param unknown $description
     * @return ProjectMemberTest
     */
    protected function read($projectId, $description)
    {
        return $this->visit('/projects/'. $projectId.'/members/list')
                    ->see($description);
    }

}
