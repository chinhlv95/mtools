<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class ListDepartmentTest extends TestCase
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

    public function testUrl() {
        $response = $this->call('GET', '/structure');
        $this->assertEquals(200, $response->status());
    }

    public function testListDepartment() {
        $department     = DB::table('departments')->get();
        $random_keys    = array_rand($department,1);
        $departmentName = $department[$random_keys]->name;
        $this->visit('/structure')
             ->see("Department management")
             ->see("Department list")
             ->see("Parent group")
             ->see($departmentName);
    }
}
