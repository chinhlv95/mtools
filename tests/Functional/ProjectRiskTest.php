<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class ProjectRiskTest extends TestCase
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
        $project_id = DB::table('projects')->first()->id;
        $response = $this->call('GET', 'projects/' . $project_id.'/risk/create');
        $this->assertEquals(200, $response->status());
    }

    //---------------------------
    //
    // Test unit function
    //
    //---------------------------

    /**
     * Run first test CRUD for risk page.
     * CRUD - Create, Read, Update, Delete
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

    /**
     * First test with create new risk function when create success.
     * Include: test input data -> test save to database -> show message
     *
     * @author: thangdv8182
     * @param int $projectId
     * @param string $description
     */
    protected function testGetCreate($projectId)
    {
        return $this->visit('projects/' . $projectId.'/risk/create')
                    ->select('1', 'category_id')
                    ->select('1', 'impact')
                    ->select('1', 'status')
                    ->type('Risk_title', 'risk_title')
                    ->type('30', 'propability')
                    ->type('ceo.com', 'guideline_link')
                    ->type('Test first', 'mitigration_plan')
                    ->type('123', 'task_id')
                    ->press('Save')
                     ->seeInDatabase('project_risk',['risk_title' => 'Risk_title'])
                     ->seePageIs('projects/' . $projectId.'/risk/list');
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

    protected function testDelete($projectId)
    {
         $risk_id = DB::table('project_risk')->orderBy('id','DESC')->first()->id;
         $this->visit('projects/' . $projectId.'/risk/list')
              ->click('delete'.$risk_id)
              ->see('Do you want delete?')
              ->press('Delete');
//               ->seeInDatabase('project_risk',['id' => $risk_id])
//               ->notSeeInDatabase('project_risk',['id' => $risk_id, 'deleted_at'=>null]);
    }

    protected function read($project_id, $description)
    {
        return $this->visit('projects/' .$project_id.'/risk/list')
                    ->see($description);
    }

    //---------------------------
    //
    // Test validate
    //
    //---------------------------

    /**
     * Run first test with validate
     *
     * @author thangdv8182
     */
    public function testValidate()
    {
        // Get Project Id and Sprint name from database.
        $project_id = DB::table('projects')->first()->id;
        $this->testValidateCreate($project_id);
    }

    /**
     * First test validate with create new risk function.
     * Include: test input data -> test validate with message
     *
     * @author: thangdv8182
     * @param int $projectId
     */
    protected function testValidateCreate($projectId)
    {
        return $this->visit('projects/' .$projectId.'/risk/create')
                    ->press('Save')
                    ->see('The risk title field is required.')
                    ->see('The probability field is required.')
                    ->see('The mitigration plan field is required.')
                    ->press('Save')
                    ->see('The risk title field is required.')
                    ->see('The probability field is required.')
                    ->see('The mitigration plan field is required.');
    }
}
