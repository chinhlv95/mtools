<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class CostPermissionTest extends TestCase
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
    public function __construct() {
        $this->emailLogin = 'admin@example.com';
    }

    public function setUp() {
        parent::setUp();
        $this->login([
                'email'    => $this->emailLogin,
                'password' => 'admin@example.com'
            ]);
    }

    /**
     * Test link Url
     *
     * @author thanhnb6719
     * @return void
     */
    public function testUrl() {
        $response = $this->call('GET', '/cost/project_cost/list');
        $this->assertEquals(200, $response->status());
    }

    private function getProjectJoin() {
        return DB::select( DB::raw('SELECT projects.id AS projects_id, projects.name as projects_name, roles.name, roles.permissions, users.email
                                    FROM projects
                                    JOIN project_member ON projects.id = project_member.project_id
                                    JOIN users ON users.id = project_member.user_id
                                    JOIN roles ON project_member.role_id = roles.id
                                    WHERE users.email = ?'), array($this->emailLogin));
    }

    public function testIndex() {
        $projectPermission = $this->getProjectJoin();
        foreach($projectPermission as $pp){
            if(array_key_exists("user.view_project_cost",json_decode($pp->permissions))){
                $projectTest = $pp->projects_name;
            }
        }
        return $this->visit('cost/project_cost/list')
                    ->see($projectTest)
                    ->see("Total number of records: ".count($projectPermission));
    }

    public function testImport() {
        $projectPermission = $this->getProjectJoin();
        foreach($projectPermission as $pp){
            if(array_key_exists("user.import_cost", json_decode($pp->permissions))){
                $projectTest = $pp->projects_name;
            }
        }
        return $this->visit('cost/project_cost/list#tableListProject')
                    ->press('buttonShowModal')
                    ->seeElement('#listImport')
                    ->see('Import project')
                    ->see($projectTest);
    }

    public function testExport() {
        $projectPermission = $this->getProjectJoin();
        foreach($projectPermission as $pp){
            if(array_key_exists("user.export_cost", json_decode($pp->permissions))){
                $projectTest = $pp->projects_name;
            }
        }
        return $this->visit('cost/project_cost/list')
                    ->press('buttonShowModal')
                    ->seeElement('#listImport')
                    ->see('Export cost')
                    ->see($projectTest);
    }
}
