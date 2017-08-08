<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Repositories\Entry\EntryRepositoryInterface;
use App\Repositories\Import\ImportRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\Ticket\TicketRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class ImportController extends Controller
{
    public function __construct(
                                UserRepositoryInterface $user,
                                EntryRepositoryInterface $entry,
                                TicketRepositoryInterface $ticket,
                                ImportRepositoryInterface $import,
                                ProjectRepositoryInterface $project )
    {
        $this->user           = $user;
        $this->entry          = $entry;
        $this->ticket         = $ticket;
        $this->import         = $import;
        $this->project        = $project;
    }

    /**
     * Check file which uploaded before or not
     *
     * @author thanhnb6719
     * @param Request $request
     * @return string[]
     */
    public function checkFileUploadedOrNot(Request $request){
        $file = $request->get('_name');
        if ($file == "") {
            return ['data' => 'x'];
        } else {
            $explodeName = explode('fakepath', $file);

            $fileName = substr($explodeName[1], 1);
            $checkFileName = $this->import->findByAttribute('name', $fileName);
            if($checkFileName == null){
                $result = (['data' => '0']);
            } else {
                $result = (['data' => '1', 'fileName' => $fileName]);
            }
            return $result;
        }
    }

    /**
     * Import file excel if file still not imported before
     *
     * @author thanhnb6719
     * @param Request $request
     * @return
     */
    public function import(Request $request){

        $file      = $request->file('xlsfile');
        $excelType = $request->get('fileType');
        $checkBox  = $request->get('checkImport');
        $team  = $request->get('team');
        $project = $request->get('project');

        $confirmUpdatedBefore = false;
        $importFile = $this->import->import($file, $excelType, $confirmUpdatedBefore, $checkBox, $team, $project);
        return $importFile;
    }

    /**
     * Import file excel if file imported
     *
     * @author thanhnb6719
     * @param Request $request
     * @return
     */
    public function importAfterConfirm(Request $request){

        $file      = $request->file('xlsfile');

        $excelType = $request->get('fileType');
        $checkBox  = $request->get('checkImport');
        $team  = $request->get('team');
        $confirmUpdatedBefore = true;
        $importFile = $this->import->import($file, $excelType, $confirmUpdatedBefore, $checkBox, $team);
        return $importFile;
    }

    /**
     * Fill Ticket ID to file excel
     *
     * @author thanhnb6719
     * @param Request $request
     * @return file
     */
    public function fillTicketIdToExport(Request $request){
        try {
            $file           = $request->get('ticketID');
            $explodeFile    = explode('public', $file);
            $excelTemplate  = 'public'.$explodeFile[1];
            $exportFile = Excel::load($excelTemplate, function($reader) {

            })->export('xlsx');
        } catch (\PHPExcel_Exception $e) {
            return 'Error: '.$e->getMessage();
        }
    }
}