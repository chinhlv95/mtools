<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;

class DefectReportTest extends TestCase
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
    public function testIndex()
    {
        $response = $this->call('GET', '/defect-report');
        $this->assertEquals(200, $response->status());
    }
    public function testSearch()
    {
        return $this->visit('defect-report')
                    ->select('1', 'type_bug')
                    ->select('summary', 'report_type')
                    ->select('23', 'project')
                    ->press('Search')
                    ->see('By root cause');
    }
}
