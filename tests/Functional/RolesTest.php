<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class RolesTest extends TestCase
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

    /**
     * Test access to page index
     *
     * @return void
     */
    public function testAccess()
    {
        $response = $this->call('GET', '/setting/roles');
        $this->assertEquals(200, $response->status());
    }

    /**
     * Test create to page index
     *
     * @return void
     */
    public function testCreate(){
        return $this->visit('/setting/roles/create')
                    ->type('QAL','roleName')
                    ->press('Create')
                    ->seePageIs('/setting/roles');
    }

    /**
     * Test delete from page index
     *
     * @return void
     */
    public function testDelete(){

        $roleId = DB::table('roles')->first()->id;
        $this->withoutMiddleware();
        $this->visit('/setting/roles')
        ->click($roleId)
        ->see('Delete')
        ->press('delete');
    }

    /**
     * Test update from page index
     *
     * @return void
     */
    public function testUpdate(){
        $roleId = DB::table('roles')->first()->id;
        $this->visit('/setting/roles/edit/'.$roleId)
        ->see('Update role and permission')
        ->type('TEST44','roleName')
        ->press('btnUpdate')
        ->seePageIs('/setting/roles');
    }
}
