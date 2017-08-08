<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Helpers\LoginTrait;
use Tests\Helpers\PageTrait;
use App\Http\Controllers\ImportController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportBugTest extends TestCase
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
        $response = $this->call('GET', '/defect-report');
        $this->assertEquals(200, $response->status());
    }

    public function testImportController(){
        $path = __DIR__.'/MockDatas/FileTestBug.xlsx';
        $pathCopy =  __DIR__.'/MockDatas/FileTestBug1.xlsx';
        $fileName = 'FileTestBug1.xlsx';
        $size = filesize($path);
        copy($path, $pathCopy);
        $file = new UploadedFile($pathCopy, $fileName, $size, 'xlsx', null, true);

        $this->testCheckFileUploadOrNot($file, $fileName)
             ->testCheckFileRequest($file, $fileName);
    }

    protected function testCheckFileUploadOrNot($file, $fileName){
        return $this->visit('/defect-report')
                    ->see('View Project - Defect Report')
                    ->press('Import')
                    ->attach($file, 'xlsfile');
    }

    protected function testCheckFileRequest($file, $fileName){
        $stub = $this->createMock(ImportController::class);
        $stub->method('checkFileRequest')
             ->with([$file, $fileName]);
        $uploadFile = $file->move(base_path() . '/public/uploads/uploadExcel', $fileName);
        $realPath = $uploadFile->getRealPath();
        return $this->assertFileExists($realPath);
    }

    public function testCheckTemplateOfExcelFile(){
        $path = base_path() . '/public/uploads/uploadExcel/FileTestBug1.xlsx';
        $checkTemplate = array('no', 'source_id', 'parent_id', 'ticket_id',
                        'ticket_subject', 'versionrelease', 'pagefunction',
                        'description_vnjp','tracker', 'bug_weight', 'priority',
                        'bug_type', 'status', 'created_dateddmmyyyy','created_by',
                        'author', 'assign_to', 'test_case','closed_dateddmmyyyy',
                        'root_cause', 'impact_analysis','loc', 'progress');

        $repositoryMock = $this->getMockBuilder(ImportController::class)
                               ->setMethods(['checkTemplateOfExcelFile'])
                               ->disableOriginalConstructor()
                               ->getMock();

        $repositoryMock->expects($this->any())
                       ->method('checkTemplateOfExcelFile')
                       ->with(array($path, $checkTemplate, count($checkTemplate)))
                       ->willReturnCallback([$this, null]);

        return $repositoryMock;
    }

    public function testCheckValueOfBugExcelFile(){
        $path = base_path() . '/public/uploads/uploadExcel/FileTestBug1.xlsx';
        $repositoryMock = $this->getMockBuilder(ImportController::class)
                                ->setMethods(['checkValueOfBugExcelFile'])
                                ->disableOriginalConstructor()
                                ->getMock();

        $repositoryMock->expects($this->any())
                       ->method('checkValueOfBugExcelFile')
                       ->with($path)
                       ->willReturnCallback([$this, null]);

        return $repositoryMock;
    }

    public function testSaveDataFromBugExcelToDatabase(){
        $path = base_path() . '/public/uploads/uploadExcel/FileTestBug1.xlsx';
        $repositoryMock = $this->getMockBuilder(ImportController::class)
                               ->setMethods(['saveDataFromBugExcelToDatabase'])
                               ->setConstructorArgs(array($path))
                               ->disableOriginalConstructor()
                               ->getMock();

        $repositoryMock->expects($this->any())
                       ->method('saveDataFromBugExcelToDatabase')
                       ->with($path)
                       ->willReturnCallback([$this, null]);

        return $this->seeInDatabase('tickets',['title' => 'ImportFile_bug1']);
    }
}
