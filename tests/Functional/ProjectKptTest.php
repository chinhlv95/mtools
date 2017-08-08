<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class ProjectKptTest extends TestCase
{
    use DatabaseTransactions;
    use LoginTrait;
    use PageTrait;

    /**
     * Set up login for test.
     *
     * @author thanhnb6719
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
     * @author thanhnb6719
     * @return void
     */

    public function testIndex()
    {
        $projectId = DB::table('projects')->first()->id;
        $response = $this->call('GET', '/projects/'.$projectId.'/kpt/list');
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
     * @author: thanhnb6719
     */
    public function testCRUD()
    {
        // Get Project Id and Sprint name from database.
        $projectId = DB::table('projects')->first()->id;
        $sprintId = DB::table('project_versions')->first()->id;
        $categoryId = DB::table('categories')->first()->id;

        // $description as a data for test
        $description1 = "Description for first test 1 ". time();
        $description2 = "Description for first test 2 ". time();
        $description3 = "Description for first test - update function". time();

        $this->createAndContinue($projectId, $sprintId, $categoryId, $description1)
             ->read($projectId, $description1)
             ->createAndSave($projectId, $sprintId, $categoryId, $description2)
             ->read($projectId, $description2)
             ->update($projectId, $description2, $description3)
             ->destroy($projectId);
//              ->dontSee($description3);
    }

    /**
     * First test with create new kpt function when create success.
     * Include: test input data -> test save to database -> show message
     *
     * @author: thanhnb6719
     * @param int $projectId
     * @param string $sprintName
     * @param string $description
     */
    protected function createAndContinue($projectId, $sprintId, $categoryId, $description)
    {
        return $this->visit('projects/'. $projectId .'/kpt/new_kpt')
                    ->select($sprintId, 'release')
                    ->select($categoryId, 'category')
                    ->select('3', 'type')
                    ->select('1', 'status')
                    ->type($description, 'description')
                    ->type('New action for first test', 'action')
                    ->press('save_and_continue')
                    ->seeInDatabase('project_kpt',
                            [
                             'content' => $description,
                             'action' => 'New action for first test'
                            ])
                    ->seePageIs('projects/'. $projectId .'/kpt/new_kpt')
                    ->see('Add new success!');
    }

    protected function createAndSave($projectId, $sprintId, $categoryId, $description)
    {
        return $this->visit('projects/'. $projectId .'/kpt/new_kpt')
                    ->select($sprintId, 'release')
                    ->select($categoryId, 'category')
                    ->select('3', 'type')
                    ->select('1', 'status')
                    ->type($description, 'description')
                    ->type('New action for first test', 'action')
                    ->press('save')
                    ->seeInDatabase('project_kpt',
                            ['content' => $description,
                             'action' => 'New action for first test'
                            ])
                    ->seePageIs('/projects/'. $projectId . '/kpt/list')
                    ->see($description);
    }

    protected function read($projectId, $description)
    {
        return $this->visit('/projects/'. $projectId . '/kpt/list')
                    ->see($description);
    }

    protected function update($projectId, $oldDes, $newDes)
    {
        $kptId = DB::table('project_kpt')->orderBy('id','DESC')->first()->id;
        return $this->visit('projects/'. $projectId .'/kpt/edit/'. $kptId)
                    ->type($newDes, 'description')
                    ->press('save')
                    ->seeInDatabase('project_kpt',
                            [
                                 'id' => $kptId,
                                 'content' => $newDes
                            ])
                    ->seePageIs('/projects/'. $projectId . '/kpt/list')
                    ->see($newDes);
    }

    protected function destroy($projectId)
    {
        $kptId = DB::table('project_kpt')->orderBy('id','DESC')->first()->id;
        $this->withoutMiddleware();
        $this->visit('/projects/'. $projectId . '/kpt/list')
             ->click($kptId)
             ->see('Do you want delete KPT?')
             ->press('deletey')
             ->seeInDatabase('project_kpt',['id' => $kptId])
             ->notSeeInDatabase('project_kpt',['id' => $kptId,'deleted_at'=>null]);
    }

    //---------------------------
    //
    // Test validate
    //
    //---------------------------

    /**
     * Run first test with validate
     *
     * @author thanhnb6719
     */
    public function testValidate()
    {
        // Get Project Id and Sprint name from database.
        $projectId = DB::table('projects')->first()->id;
        $this->testValidateCreate($projectId);
    }

    /**
     * First test validate with create new kpt function.
     * Include: test input data -> test validate with message
     *
     * @author: thanhnb6719
     * @param int $projectId
     * @param string $sprintName
     * @param string $description
     */
    protected function testValidateCreate($projectId)
    {
        return $this->visit('projects/'. $projectId .'/kpt/new_kpt')
                    ->press('save_and_continue')
                    ->see('The description field is required.')
                    ->see('The action field is required.')
                    ->press('save')
                    ->see('The description field is required.')
                    ->see('The action field is required.');
    }
}
