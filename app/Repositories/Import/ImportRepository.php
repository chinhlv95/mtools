<?php

namespace App\Repositories\Import;

use App\Models\FileImport;
use App\Models\User;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\BugType\BugTypeRepositoryInterface;
use App\Repositories\BugWeight\BugWeightRepositoryInterface;
use App\Repositories\Entry\EntryRepositoryInterface;
use App\Repositories\Loc\LocRepositoryInterface;
use App\Repositories\Priority\PriorityRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface;
use App\Repositories\RootCause\RootCauseRepositoryInterface;
use App\Repositories\Status\StatusRepositoryInterface;
use App\Repositories\Ticket\TicketRepositoryInterface;
use App\Repositories\TicketType\TicketTypeRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Models\Project;

class ImportRepository implements ImportRepositoryInterface {

    public function __construct(PermissionRepositoryInterface $permission, LocRepositoryInterface $loc, UserRepositoryInterface $user, EntryRepositoryInterface $entry, StatusRepositoryInterface $status, TicketRepositoryInterface $ticket, ProjectRepositoryInterface $project, BugTypeRepositoryInterface $bugType, ActivityRepositoryInterface $activity, PriorityRepositoryInterface $priority, RootCauseRepositoryInterface $rootCause, BugWeightRepositoryInterface $bugWeight, TicketTypeRepositoryInterface $ticketType, ProjectMemberRepositoryInterface $projectMembers, ProjectVersionRepositoryInterface $projectVersion) {
        $this->loc = $loc;
        $this->user = $user;
        $this->entry = $entry;
        $this->status = $status;
        $this->ticket = $ticket;
        $this->bugType = $bugType;
        $this->project = $project;
        $this->priority = $priority;
        $this->activity = $activity;
        $this->rootCause = $rootCause;
        $this->bugWeight = $bugWeight;
        $this->ticketType = $ticketType;
        $this->projectMembers = $projectMembers;
        $this->projectVersion = $projectVersion;
        $this->permission = $permission;
    }

    //get data file management
    public function getFileManagement() {
        $query = FileImport::select(
                    'file_import.*',
                    'users.first_name',
                    'users.last_name',
                    'projects.name as project_name',
                    'departments.name as departments_name'
                )
                ->leftjoin('users', 'file_import.user_id', '=', 'users.id')
                ->leftjoin('projects', 'file_import.project_id', '=', 'projects.id')
                ->leftjoin('departments', 'file_import.team', '=', 'departments.id')
                ->orderBy('file_import.type', 'DESC')
                ->orderBy('file_import.created_at', 'DESC');

        $user = Sentinel::getUser();
        $role = $user->roles()->get();
        $roleUser = $role[0]['id'];
        $listManager = DB::table('departments')
                ->join('users', 'departments.manager_id', '=', 'users.member_code')
                ->groupBy('users.id')
                ->pluck('users.id');

        if ($roleUser == 1 || $roleUser == 13) {
            $data = $query;
        } else {
            if (in_array($user->id, $listManager)) {
                $listTeam = $this->project->getDepartmentWhichManagerManage($user->id);
            } else {
                $listTeam = Project::join('project_member', 'project_member.project_id', '=', 'projects.id')
                        ->where('project_member.user_id', $user->id)
                        ->pluck('projects.department_id');
            }
            $data = $query->whereIn('file_import.team', $listTeam);
        }
//         dd($data->get()->toArray());
        $data =  $data->get()->groupBy(function($item){
            return $item->created_at->format('Y-m-d');
        })->toArray();
        
        return $data;
    }

    public function all() {
        return FileImport::all();
    }

    public function paginate($quantity) {
        return FileImport::paginate($quantity);
    }

    public function find($id) {
        return FileImport::find($id);
    }

    // -------------------------------------------------------------------------- //
    // -------------------------------------------------------------------------- //
    // --------------------------- FUNCTION GET DATA ---------------------------- //
    // -------------------------------------------------------------------------- //
    // -------------------------------------------------------------------------- //

    /**
     * @todo Find data in table file_import
     *
     * @author thanhnb6719
     * @param string $att
     * @param string $name
     * @see \App\Repositories\Import\ImportRepositoryInterface::findByAttribute()
     */
    public function findByAttribute($att, $name) {
        return FileImport::where($att, $name)->first();
    }

    /**
     * @todo Get email in database
     *
     * @author thanhnb6719
     * @param string $userId
     * @see \App\Repositories\Import\ImportRepositoryInterface::getUserInDatabase()
     */
    public function getUserInDatabase($userId) {

        if ($userId != null) {
            return $checkUser = User::where('user_name', '=', $userId)
                    ->first();
        }
    }

    // -------------------------------------------------------------------------- //
    // -------------------------------------------------------------------------- //
    // ----------------------- FUNCTION SAVE FILE NAME -------------------------- //
    // -------------------------------------------------------------------------- //
    // -------------------------------------------------------------------------- //

    /**
     * @todo Save file excel name to database when import
     *
     * @author thanhnb6719
     * @param string $data
     * @see \App\Repositories\Import\ImportRepositoryInterface::saveFileName()
     */
    public function saveFileName($name, $userId, $projectId, $fileType, $parentId, $team, $excelType) {
        $fileName = new FileImport();
        $fileName->name = $name;
        $fileName->user_id = $userId;
        $fileName->project_id = $projectId;
        $fileName->type = $fileType;
        $fileName->parent_id = $parentId;
        $fileName->team = $team;
        $fileName->file_type = $excelType;
        $fileName->save();
        return $fileName->id;
    }

    // -------------------------------------------------------------------------- //
    // -------------------------------------------------------------------------- //
    // -------------------- FUNCTION CHECK DATA OF FILE EXCEL ------------------- //
    // -------------------------------------------------------------------------- //
    // -------------------------------------------------------------------------- //

    /**
     * @todo Check file required, file size and type of file
     *
     * @author thanhnb6719
     * @param file $file
     * @param string $fileName
     * @return string
     */
    private function checkFileRequest($file, $fileName) {
        $fileType = array("xlsx", "xls");
        $nameOfFile = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!in_array($nameOfFile, $fileType)) {
            return (['switch' => '1']);
        } elseif (filesize($file) > 2000000) {
            return (['switch' => '2']);
        }
    }

    /**
     * @todo Check row in Excel file null or not
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkNullInExcelFile($rowName, $messageError, $sheetName, $row) {
        if ($rowName == null) {
            return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - You must fill ' . $messageError . ' in excel!';
        }
    }

    /**
     * @todo Check row ticket type  in Excel file bug or bug after release
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkTicketTypeCostImport($rowName, $messageError, $sheetName, $row) {
        if ($rowName == 'Bug' || $rowName == 'Bug after release') {
            return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - You must check ' . $messageError . ' in excel!';
        }
    }

    /**
     * @todo Check permission import
     *
     * @author thanhnb6719
     * @param string $rowData
     * @param string $sheetName
     * @param int $row
     * @param array $checkBox
     * @return
     */
    private function checkPermissionImport($rowData, $sheetName, $row, $checkBox) {
        if ($rowData != null) {
            $checkProject = $this->project->findByAttribute('name', $rowData);
            if ($checkProject != null) {
                if (!in_array($checkProject->id, $checkBox)) {
                    return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - You dont have permission to import into project named: ' . $rowData;
                }
            }
        }
    }

    private function getPositionOfTicketId($allData) {
        $listPosition = [];
        foreach ($allData as $data) {
            $sheetName = $data->getTitle();
            $arrayData = $data->toArray();
            $position = array_search('ticket_id', array_keys($arrayData[0])) + 1;
            switch ($position) {
                case 1:
                    $abcPosition = "A";
                    break;
                case 2:
                    $abcPosition = "B";
                    break;
                case 3:
                    $abcPosition = "C";
                    break;
                case 4:
                    $abcPosition = "D";
                    break;
                case 5:
                    $abcPosition = "E";
                    break;
                case 6:
                    $abcPosition = "F";
                    break;
                case 7:
                    $abcPosition = "G";
                    break;
                case 8:
                    $abcPosition = "H";
                    break;
                case 9:
                    $abcPosition = "I";
                    break;
                case 10:
                    $abcPosition = "J";
                    break;
                case 11:
                    $abcPosition = "K";
                    break;
                case 12:
                    $abcPosition = "L";
                    break;
                case 13:
                    $abcPosition = "M";
                    break;
                case 14:
                    $abcPosition = "N";
                    break;
                case 15:
                    $abcPosition = "O";
                    break;
                default:
                    $abcPosition = "P";
                    break;
            }
            $listPosition[$sheetName] = $abcPosition;
        }
        return $listPosition;
    }

    /**
     * @todo Check row in Excel file is number or not
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkIsNumberInExcelFile($rowName, $messageError, $sheetName, $row) {
        if ($rowName != null) {
            if (!is_numeric($rowName)) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - ' . $messageError . ' must be number in excel!';
            }
        }
    }

    /**
     * @todo Check row in Excel file is int format or not
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkNumberIsIntInExcelFile($rowName, $messageError, $sheetName, $row) {
        if ($rowName != null) {
            if (!is_int($rowName)) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - ' . $messageError . ' must be int number in excel!';
            }
        }
    }

    /**
     * @todo Check number is negative or not
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkNumberIsNotNegativeNumber($rowName, $messageError, $sheetName, $row) {
        if ($rowName != null) {
            if ($rowName < 0) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - ' . $messageError . ' can not be negative number!';
            }
        }
    }

    /**
     * @todo Check Email in Excel file is correct format or not
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkEmailInExcelFile($email, $messageError, $sheetName, $row) {
        if ($email != null) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - ' . $messageError . ' is badly formatted!';
            }
        }
    }

    /**
     * @todo Check Email in Database or not
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkUserInDatabase($userId, $messageError, $sheetName, $row) {
        if ($userId != null) {

            $checkUser = User::where('user_name', '=', $userId)
                    ->get();
            if (count($checkUser) == 0) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - ' . $messageError . ' can not find in database!';
            }
        }
    }

    /**
     * @todo Check date in Excel file is correct or not. (Ex: 30/02/2016)
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkDateCorrectInExcelFile($date, $messageError, $sheetName, $row) {
        $checkError = 0;
        if ($date != null) {
            try {
                if (strpos($date, '/') !== false) {
                    $part = explode("/", $date);
                    if (count($part) == 3) {
                        $day = $part[1];
                        $month = $part[0];
                        $year = $part[2];
                        $check = checkdate($month, $day, $year);
                        if ($check == false) {
                            $checkError = 1;
                        }
                    } else {
                        $checkError = 1;
                    }
                } else {
                    $checkError = 1;
                }
            } catch (Exception $e) {
                $checkError = 1;
            }
        }
        if ($checkError == 1) {
            return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - ' . $messageError . ' is not correct!';
        }
    }

    /**
     * @todo Check time of close date must greater than start date
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkCloseDateAndStartDate($start, $close, $sheetName, $row) {
        if ($start != null && $close != null) {
            $startDate = strtotime($start);
            $closeDate = strtotime($close);
            if ($startDate > $closeDate) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Created date greater than closed date !';
            }
        }
    }

    /**
     * @todo Check length of value
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkLengthOfValue($rowName, $length, $messageError, $sheetName, $row) {
        if ($rowName != null) {
            if (strlen($rowName) > $length) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - ' . $messageError . ' larger than max number charaters!';
            }
        }
    }

    /**
     * @todo Check special character
     *
     * @author thanhnb6719
     * @param string $rowName
     * @param string $messageError
     * @param string $sheetName
     * @param int $row
     * @return
     */
    private function checkSpecialCharacter($rowName, $messageError, $sheetName, $row) {
        if ($rowName != null) {
            if (preg_match('/[\'^Â£$%&*()}{@#~?><>,|=_+Â¬-]/', $rowName)) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - ' . $messageError . ' is not allowed using special character !';
            }
        }
    }

    /**
     * @todo Check a value in database or not
     *
     * @author thanhnb6719
     * @param string $modelNeedCheck
     * @param int $rowCheckInDatabase
     * @param string $dataNeedCheck
     * @param string $sheetName
     * @param string $attribute
     * @param int $row
     * @return string
     */
    private function checkInDatabase($modelNeedCheck, $rowCheckInDatabase, $dataNeedCheck, $sheetName, $attribute, $row) {
        if ($dataNeedCheck != null) {
            $check = $this->$modelNeedCheck->findByAttribute($rowCheckInDatabase, $dataNeedCheck);
            if ($check == null) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Can not find ' . $attribute . ' named "' . $dataNeedCheck . '" in database!';
            }
        }
    }

    /**
     * @todo Check source in database or not
     *
     * @author thanhnb6719
     * @param string $dataNeedCheck
     * @param string $sheetName
     * @param int $row
     * @return string
     */
    private function checkSource($dataNeedCheck, $sheetName, $row) {
        if ($dataNeedCheck != null) {
            $source = Config::get('constant.stream_types');
            if (!in_array($dataNeedCheck, $source)) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Can not find source named "' . $dataNeedCheck . '" in system!';
            }
        }
    }

    /**
     * @todo Check source in database or not
     *
     * @author thanhnb6719
     * @param string $dataNeedCheck
     * @param string $sheetName
     * @param int $row
     * @return string
     */
    private function checkTicketWithSource($dataNeedCheck, $sourceId, $sheetName, $row) {
        if ($sourceId != 0 && $dataNeedCheck == null) {
            return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Ticket ID with source can not null!';
        }
    }

    /**
     * @todo Check source match with data
     *
     * @author thanhnb6719
     * @param string $tableName
     * @param string $sourceNeedCheck
     * @param string $dataNeedCheck
     * @param string $sheetName
     * @param int $row
     * @return string
     */
    private function checkRelateSource($tableName, $sourceNeedCheck, $dataNeedCheck, $nameOfData, $sheetName, $row) {
        if ($dataNeedCheck != null && $sourceNeedCheck != null) {
            $source = Config::get('constant.stream_types');
            if (in_array($sourceNeedCheck, $source)) {
                $id = array_search($sourceNeedCheck, $source);
                $check = DB::table($tableName)
                        ->where('source_id', $id)
                        ->where('name', $dataNeedCheck)
                        ->first();
                if ($check == null) {
                    return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Can not match ' . $nameOfData . ' "' . $dataNeedCheck . '" with source "' . $sourceNeedCheck . '"!';
                }
            }
        }
    }

    /**
     * @todo Check version of project
     *
     * @author thanhnb6719
     * @param string $project
     * @param string $sourceNeedCheck
     * @param string $dataNeedCheck
     * @param string $sheetName
     * @param int $row
     * @return string
     */
    private function checkVersionWithSource($project, $sourceNeedCheck, $dataNeedCheck, $sheetName, $row) {
        if ($dataNeedCheck != null && $sourceNeedCheck != null && $project != null) {
            $projectCheck = DB::table('projects')->where('name', $project)->first();
            if ($projectCheck != null) {
                $source = Config::get('constant.stream_types');
                if (in_array($sourceNeedCheck, $source)) {
                    $id = array_search($sourceNeedCheck, $source);
                    $check = DB::table('project_versions')
                            ->where('source_id', $id)
                            ->where('name', $dataNeedCheck)
                            ->where('project_id', $projectCheck->id)
                            ->first();
                    if ($check == null) {
                        return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Can not find version: "' . $dataNeedCheck . '" in project named: "' . $project . '" with source "' . $sourceNeedCheck . '"!';
                    }
                }
            }
        }
    }

    /**
     * @todo Check number must between
     *
     * @author thanhnb6719
     * @param string $dataNeedCheck
     * @param int $min
     * @param int $max
     * @param string $sheetName
     * @param int $row
     * @param string $nameOfAttribute
     * @return string
     */
    private function checkPeriodOfNumber($dataNeedCheck, $min, $max, $sheetName, $row, $nameOfAttribute) {
        if ($dataNeedCheck != null) {
            if ($dataNeedCheck < $min || $dataNeedCheck > $max) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - ' . $nameOfAttribute . ' must be between ' . $min . ' and ' . $max . '!';
            }
        }
    }

    /**
     * @todo Check ticket which no parent
     *
     * @author thanhnb6719
     * @param string $sourceNeedCheck
     * @param string $ticketNeedCheck
     * @param string $sheetName
     * @param int $row
     * @return string
     */
    private function checkTicketWithNoParent($sourceNeedCheck, $ticketNeedCheck, $sheetName, $row) {
        if ($sourceNeedCheck != null) {
            $sourceId = $this->getSourceId($sourceNeedCheck);
            if ($ticketNeedCheck != null) {
                if ($sourceId != 0) {
                    $checkTicket = $this->ticket->findByAttribute('integrated_ticket_id', $ticketNeedCheck);
                    if ($checkTicket == null) {
                        $checkTicket = $this->ticket->findByAttribute('id', $ticketNeedCheck);
                    }
                } else {
                    $checkTicket = $this->ticket->findByAttribute('id', $ticketNeedCheck);
                }
                if ($checkTicket == null) {
                    return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Cannot find ticket ID: ' . $ticketNeedCheck . ' which matched with source name!';
                }
            }
        }
    }

    /**
     * @todo Check ticket which having parent
     *
     * @author thanhnb6719
     * @param string $ticketNeedCheck
     * @param string $parentTicketNeedCheck
     * @param string $sourceNeedCheck
     * @param string $sheetName
     * @param int $row
     * @return string
     */
    private function checkTicketWithParentRelation($ticketNeedCheck, $parentTicketNeedCheck, $sourceNeedCheck, $sheetName, $row) {
        if ($ticketNeedCheck != null && $parentTicketNeedCheck != null && $sourceNeedCheck != null) {
            $sourceId = $this->getSourceId($sourceNeedCheck);
            if ($sourceId == 0) {
                $checkTicket = DB::table('tickets')
                        ->where('source_id', $sourceId)
                        ->where('id', $ticketNeedCheck)
                        ->where('integrated_parent_id', $parentTicketNeedCheck)
                        ->first();
            } else {
                $checkTicket = DB::table('tickets')
                        ->where('source_id', $sourceId)
                        ->where('integrated_ticket_id', $ticketNeedCheck)
                        ->where('integrated_parent_id', $parentTicketNeedCheck)
                        ->first();
            }
            if ($checkTicket == null) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Pair source ID, ticket ID and parent ID not matched!';
            }
        }
    }

    /**
     * @todo Check member joined project or not
     *
     * @author thanhnb6719
     * @param string $dataNeedCheck
     * @param int $projectId
     * @param string $sheetName
     * @param int $row
     * @return string
     */
    private function checkMemberInProjectOrNot($dataNeedCheck, $projectId, $sheetName, $row) {
        if ($dataNeedCheck != null) {
            $data = $this->getUserInDatabase($dataNeedCheck);
            if ($data == null) {
                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Cannot find member "' . $dataNeedCheck . '" in database!';
            } else {
                $checkMemAssignTo = $this->projectMembers->findByAttributes('user_id', $data->id, 'project_id', $projectId);
                if ($checkMemAssignTo == null) {
                    return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Cannot find member "' . $dataNeedCheck . '" in project named "' . $sheetName . '"!';
                }
            }
        }
    }

    /**
     * @todo Check relate with parent id, ticket id and project
     *
     * @author thanhnb6719
     * @param string $project
     * @param int $ticket
     * @param int $parent
     * @param string $sheetName
     * @param string $row
     * @return string
     */
    private function checkProjectWithTicket($project, $ticket, $parent, $sheetName, $row, $sourceData) {
        $checkProject = $this->project->findByAttribute('name', $project);
        if ($checkProject == null) {
            return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Can not find project named "' . $project . '" in database!';
        } else {

            $checkUser = $this->getUserInDatabase(trim($sheetName));

            if ($checkUser != null) {

                $checkProjectMember = $this->projectMembers->findByAttributes('user_id', $checkUser->id, 'project_id', $checkProject->id);
                if ($checkProjectMember == null) {
                    return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Employee has member ID "' . trim($sheetName) . '" does not assign in project named: ' . $project . '';
                } elseif ($ticket != null) {
                    if ($sourceData != "Mtool") {
                        $checkTicket = $this->ticket->findByAttribute('integrated_ticket_id', $ticket);
                        if ($checkTicket == null) {
                            $checkTicket = $this->ticket->findByAttribute('id', $ticket);
                        }
                    } else {
                        $checkTicket = $this->ticket->findByAttribute('id', $ticket);
                    }
                    if ($checkTicket != null) {
                        if ($parent != null) {
                            $checkParentTicket = $this->ticket->findByAttribute('integrated_parent_id', $parent);
                            if ($checkParentTicket == null) {
                                return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Ticket ID:' . $ticket . ' and parent ID:' . $parent . ' not match in database!';
                            }
                        }
                    } else {
                        return '[ Sheet: ' . $sheetName . ' ] - [ Row: ' . $row . '] - Ticket ID: ' . $ticket . ' not found in database!';
                    }
                }
            }
        }
    }

    /**
     * @todo Get source id when import
     *
     * @author thanhnb6719
     * @param string $dataNeedCheck
     * @return int $int
     */
    private function getSourceId($dataNeedCheck) {
        if ($dataNeedCheck != null) {
            $source = Config::get('constant.stream_types');
            $id = array_search($dataNeedCheck, $source);
            return $id;
        }
    }

    /**
     * @todo Get id of data to save
     *
     * @author thanhnb6719
     * @param string $tableName
     * @param string $dataNeedCheck
     * @param int $sourceId
     * @return int $id
     */
    private function getDataId($tableName, $dataNeedCheck, $sourceId) {
        if ($dataNeedCheck != null) {
            if ($tableName == 'projects') {
                $data = DB::table('projects')
                        ->where('name', $dataNeedCheck)
                        ->first();
            } else {
                $data = DB::table($tableName)
                        ->where('name', $dataNeedCheck)
                        ->where('source_id', $sourceId)
                        ->first();
            }
            $id = $data->id;
            return $id;
        }
    }

    // -------------------------------------------------------------------------- //
    // -------------------- FUNCTION APPLY FOR IMPORT FILE ---------------------- //
    // -------------------------------------------------------------------------- //

    /**
     * @todo Upload file before import to database
     *
     * @author thanhnb6719
     * @param file $file
     * @param string $fileName
     */
    private function uploadFile($file, $fileName) {
        $uploadFile = $file->move(base_path() . '/public/uploads/uploadExcel', $fileName);
        $realPath = $uploadFile->getRealPath();
        return $realPath;
    }

    /**
     * @todo Delete file after import excel to database
     *
     * @author thanhnb6719
     * @param string $realPath
     */
    private function deleteFile($realPath) {
        if (is_file($realPath) && isset($realPath)) {
            unlink($realPath);
        }
    }

    /**
     * @todo Read time of excel file by Laravel excel
     *
     * @author thanhnb6719
     * @param file $file
     */
    private function loadTimeEntryFile($file) {
        if (!empty($file)) {
            config(['excel.import.startRow' => 1]);
            $excel = Excel::load($file, function($reader) {
                        $reader->formatDates(false);
                    });
            $allDatax = $excel->get();
            if ($excel->getSheetCount() == 1) {
                $allData[] = $allDatax;
            } else {
                $allData = $allDatax;
            }
            config(['excel.import.startRow' => 7]);
            return $allData;
        }
    }

    /**
     * @todo Read content of excel file by Laravel excel
     *
     * @author thanhnb6719
     * @param file $file
     */
    private function loadFile($file) {
        if (!empty($file)) {
            config(['excel.import.startRow' => 7]);
            $excel = Excel::load($file, function($reader) {
                        $reader->formatDates(false);
                    });
            return $excel;
        }
    }

    /**
     * @todo Get data in sheet (1 or many)
     *
     * @author thanhnb6719
     * @param file $excel
     * @return string
     */
    private function getDataInSheet($excel) {
        if ($excel->getSheetCount() > 10) {
            return $messageError = 'File excel limited to 10 sheets!';
        } else {
            $allDatax = $excel->get();
            if ($excel->getSheetCount() == 1) {
                $allData[] = $allDatax;
            } else {
                $allData = $allDatax;
            }
            $countError = 0;
            foreach ($allData as $dataEachSheet) {
                if ($dataEachSheet->count() < 0) {
                    $countError++;
                    return $messageError = 'No any record!';
                } elseif ($dataEachSheet->count() > 300) {
                    $countError++;
                    return $messageError = 'Import max 300 records!';
                }
            }
            if ($countError == 0) {
                return $allData;
            }
        }
    }

    /**
     * @todo Get number day of month
     *
     * @author thanhnb6719
     * @param string $excelName
     * @return int $numberDaysOfMonth
     */
    private function getNumberDaysOfMonth($day, $month, $year) {
        $numberDaysOfMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        return $numberDaysOfMonth;
    }

    /**
     * @todo Save or Update loc in locs table
     *
     * @author thanhnb6719
     * @param int $projectId
     * @param int $ticketId
     * @param int $userId
     * @param int $dataloc
     * @param datetime $interCreatedAt
     * @param datetime $interUpdatedAt
     * @return true
     */
    private function saveOrUpdateLoc($projectId, $ticketId, $userId, $dataloc, $interCreatedAt, $interUpdatedAt) {
        if ($ticketId != null) {
            $checkLoc = $this->loc->findByTriAttributes('project_id', $projectId, 'ticket_id', $ticketId, 'user_id', $userId);
            $loc['project_id'] = $projectId;
            $loc['ticket_id'] = $ticketId;
            $loc['user_id'] = $userId;
            $loc['loc'] = $dataloc;
            if ($checkLoc == null) {
                $loc['integrated_created_at'] = $interCreatedAt;
                $loc['integrated_updated_at'] = $interUpdatedAt;
                $locId = $this->loc->save($loc);
            } else {
                $loc['integrated_created_at'] = $checkLoc->integrated_created_at;
                $loc['integrated_updated_at'] = $checkLoc->integrated_updated_at;
                $locId = $this->loc->update($loc, $checkLoc->id);
            }
        }
        return true;
    }

    /**
     * @todo Check template of file is correct or not
     *
     * @author thanhnb6719
     * @param file $file
     * @param array $checkTemplate
     * @param string $columnNeedCheck
     * @return true if template correct
     */
    private function checkTemplateOfExcelFile($excelType, $file, $checkTemplate, $columnNeedCheck) {
        try {
            $excel = $this->loadFile($file);
            $checklLoadFile = 0;
        } catch (\Exception $e) {
            $checklLoadFile = 1;
            $errors = 'Error: ' . $e->getMessage();
        }
//         $allData = $excel->get();
//         var_dump(print_r($allData->getTitle()));
        if ($checklLoadFile == 0) {
            try {
                // Check name of file - if change format, can delete it
                $allData = $excel->get();
                if ($excel->getSheetCount() == 1) {
                    // allData = data of 1 sheet
                    $sheetName = $allData->getTitle();
                    if ($allData->first() == null) {
                        $errors = '[' . $sheetName . '] - ' . 'Wrong template format. List row of file excel is not correct !';
                    } else {
                        $rowData = $allData->first()->toArray();
                        $headerSheet = array_keys($rowData);
                        $arrayNeedCheckTemplate = array_slice($headerSheet, 0);
                        $checkNewTemplate = array_intersect($checkTemplate, $arrayNeedCheckTemplate);
                        if ($checkTemplate != $checkNewTemplate) {
                            $errors = '[' . $sheetName . '] - ' . 'Wrong template format. List row of file excel is not correct !';
                        }
                    }
                } else {
                    // allData = data of all sheets
                    foreach ($allData as $sheet) {
                        $sheetName = $sheet->getTitle();
                        if ($sheet->first() == null) {
                            $errors = '[' . $sheetName . '] - ' . 'Wrong template format. List row of file excel is not correct !';
                        } else {
                            if (count($sheet->first()) == 1) {
                                $rowData = $sheet->all();
                            } else {
                                $rowData = $sheet->first()->toArray();
                            }
                            $headerSheet = array_keys($rowData);
                            $arrayNeedCheckTemplate = array_slice($headerSheet, 0);
                            $checkNewTemplate = array_intersect($checkTemplate, $arrayNeedCheckTemplate);
                            if ($checkTemplate != $checkNewTemplate) {
                                $errors = '[' . $sheetName . '] - ' . 'Wrong template format. List row of file excel is not correct !';
                            }
                        }
                    }
                }
            } catch (\PHPExcel_Exception $e) {
                if ($e->getMessage() == "Row 7 is out of range (7 - 1)") {
                    $errors = "Wrong template format. Having sheet do not begin at rows 7!";
                } else {
                    $errors = 'Error: ' . $e->getMessage();
                }
            }
        }
        if (isset($errors)) {
            return (['switch' => '3', 'content' => $errors]);
        }
    }

    /**
     * @todo Validate data of excel file
     *
     * @author thanhnb6719
     * @param file $file
     * @return
     */
    private function checkValueOfCostExcelFile($allData, $checkBox, $day, $month, $year) {
        $numberDayOfMonth = $this->getNumberDaysOfMonth($day, $month, $year);
        foreach ($allData as $dataEachSheet) {
            $sheetName = trim($dataEachSheet->getTitle());
            if ($this->getUserInDatabase($sheetName) == null) {
                $errorMessages[] = '[ Sheet: ' . $sheetName . ' ] - Name of sheet can not match with member ID in database!';
            }
            $row = 7;
            foreach ($dataEachSheet as $data) {
                $row++;
                $checkData[] = $this->checkNullInExcelFile(trim($data['project']), 'project id', $sheetName, $row);
                $checkData[] = $this->checkNullInExcelFile(trim($data['ticket_subject']), 'ticket title', $sheetName, $row);
                $checkData[] = $this->checkNullInExcelFile(trim($data['ticket_type']), 'ticket type', $sheetName, $row);
                $checkData[] = $this->checkNullInExcelFile(trim($data['source_id']), 'source_id', $sheetName, $row);
                $checkData[] = $this->checkNullInExcelFile(trim($data['activity']), 'activity', $sheetName, $row);
                $checkData[] = $this->checkNullInExcelFile(trim($data['status']), 'status', $sheetName, $row);
                $checkData[] = $this->checkNullInExcelFile(trim($data['start_datemmddyyyy']), 'start date', $sheetName, $row);
                $checkData[] = $this->checkTicketWithSource(trim($data['ticket_id']), trim($data['source_id']), $sheetName, $row);
                $checkData[] = $this->checkDateCorrectInExcelFile(trim($data['start_datemmddyyyy']), 'Created date: ' . $data['start_datemmddyyyy'], $sheetName, $row);
                $checkData[] = $this->checkDateCorrectInExcelFile(trim($data['end_datemmddyyyy']), 'End date: ' . $data['end_datemmddyyyy'], $sheetName, $row);
                $checkData[] = $this->checkCloseDateAndStartDate(trim($data['start_datemmddyyyy']), trim($data['end_datemmddyyyy']), $sheetName, $row);
                $checkData[] = $this->checkPermissionImport(trim($data['project']), $sheetName, $row, $checkBox);
                $checkData[] = $this->checkIsNumberInExcelFile(trim($data['loc']), 'Line of code', $sheetName, $row);
                $checkData[] = $this->checkIsNumberInExcelFile(trim($data['progress']), 'Progress', $sheetName, $row);
                $checkData[] = $this->checkIsNumberInExcelFile(trim($data['estimateh']), 'Estimate hour', $sheetName, $row);
                $checkData[] = $this->checkIsNumberInExcelFile(trim($data['actualh']), 'Actual hour', $sheetName, $row);
                $checkData[] = $this->checkPeriodOfNumber(trim($data['progress']), 0, 100, $sheetName, $row, 'Progress');
                $checkData[] = $this->checkNumberIsNotNegativeNumber(trim($data['versionrelease']), 'Version', $sheetName, $row);
                $checkData[] = $this->checkNumberIsNotNegativeNumber(trim($data['loc']), 'Line of code', $sheetName, $row);
                $checkData[] = $this->checkNumberIsNotNegativeNumber(trim($data['estimateh']), 'Estimate hour', $sheetName, $row);
                $checkData[] = $this->checkNumberIsNotNegativeNumber(trim($data['actualh']), 'Actual hour', $sheetName, $row);
                $checkData[] = $this->checkNumberIsNotNegativeNumber(trim($data['ticket_id']), 'Ticket ID', $sheetName, $row);
                $checkData[] = $this->checkNumberIsNotNegativeNumber(trim($data['parent_id']), 'Parent ID of ticket', $sheetName, $row);
                $checkData[] = $this->checkLengthOfValue(trim($data['ticket_subject']), 200, 'Ticket title', $sheetName, $row);
                $checkData[] = $this->checkLengthOfValue(trim($data['description_vnjp']), 300, 'Ticket detail', $sheetName, $row);
                $checkData[] = $this->checkLengthOfValue(trim($data['versionrelease']), 128, 'Version', $sheetName, $row);
                $checkData[] = $this->checkInDatabase('activity', 'name', trim($data['activity']), $sheetName, 'activity', $row);
                $checkData[] = $this->checkInDatabase('ticketType', 'name', trim($data['ticket_type']), $sheetName, 'ticket type', $row);
                $checkData[] = $this->checkInDatabase('status', 'name', trim($data['status']), $sheetName, 'status', $row);
                $checkData[] = $this->checkSource(trim($data['source_id']), $sheetName, $row);
                $checkData[] = $this->checkVersionWithSource(trim($data['project']), trim($data['source_id']), trim($data['versionrelease']), $sheetName, $row);
                $checkData[] = $this->checkRelateSource('status', trim($data['source_id']), trim($data['status']), 'status', $sheetName, $row);
                $checkData[] = $this->checkRelateSource('ticket_type', trim($data['source_id']), trim($data['ticket_type']), 'ticket type', $sheetName, $row);
                $checkData[] = $this->checkRelateSource('activities', trim($data['source_id']), trim($data['activity']), 'activity', $sheetName, $row);
                $checkData[] = $this->checkTicketWithNoParent(trim($data['source_id']), (int) $data['ticket_id'], $sheetName, $row);
                $checkData[] = $this->checkTicketWithParentRelation((int) $data['ticket_id'], (int) $data['parent_id'], trim($data['source_id']), $sheetName, $row);
                $checkData[] = $this->checkProjectWithTicket(trim($data['project']), (int) $data['ticket_id'], (int) $data['parent_id'], $sheetName, $row, trim($data['source_id']));
                foreach ($checkData as $checkRowData) {
                    if ($checkRowData != null) {
                        $errorMessages[] = $checkRowData;
                    }
                }
                if (isset($data["$numberDayOfMonth"]) == false) {
                    $errorMessages[] = '[ Sheet: ' . $sheetName . ' ] - The number of days incorrect. This month have ' . $numberDayOfMonth . ' days!';
                }
                unset($checkData);
            } // end foreach $dataEachSheet
        }
        if (isset($errorMessages)) {
            return (['switch' => '5', 'content' => $errorMessages]);
        }
    }

    /**
     * @todo Save data from excel file to database
     *
     * @author thanhnb6719
     * @param array $allData
     * @param string $excelName
     * @return $sheetArray
     */
    private function saveDataFromCostExcelToDatabase($allData, $day, $month, $year) {
        DB::beginTransaction();
        try {
            $numberDayOfMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            foreach ($allData as $dataEachSheet) {
                $sheetName = trim($dataEachSheet->getTitle());
                foreach ($dataEachSheet as $data) {
                    $sourceId = $this->getSourceId(trim($data['source_id']));
                    $statusId = $this->getDataId('status', trim($data['status']), $sourceId);
                    $ticketTypeId = $this->getDataId('ticket_type', trim($data['ticket_type']), $sourceId);
                    $activityId = $this->getDataId('activities', trim($data['activity']), $sourceId);
                    $projectId = $this->getDataId('projects', trim($data['project']), $sourceId);
                    $versionId = $this->getDataId('project_versions', trim($data['versionrelease']), $sourceId);
                    $userId = $this->getUserInDatabase($sheetName);
                    if ($projectId != null) {
                        $projectMemberId = $this->projectMembers->findByAttributes('user_id', $userId->id, 'project_id', $projectId);
                        if ($projectMemberId != null) {
                            $ticket = [];
                            // Step 1: Save ticket to get ticket Id
                            $ticket['status_id'] = $statusId;
                            $ticket['title'] = trim($data['ticket_subject']);
                            $ticket['description'] = trim($data['description_vnjp']);
                            $ticket['category'] = '';
                            $ticket['version_id'] = $versionId;
                            $ticket['estimate_time'] = trim($data['estimateh']);
                            $ticket['start_date'] = date('Y-m-d 00:00:00', strtotime($data['start_datemmddyyyy']));
                            if ($data['end_datemmddyyyy'] == null) {
                                $ticket['due_date'] = '00-00-00 00:00:00';
                            } else {
                                $ticket['due_date'] = date('Y-m-d 00:00:00', strtotime($data['end_datemmddyyyy']));
                            }
                            $ticket['progress'] = $data['progress'] * 100;
                            $ticket['created_by_user'] = '';
                            $ticket['assign_to_user'] = $sheetName . '@co-well.com.vn';
                            $ticket['made_by_user'] = '';
                            $ticket['integrated_bug_type_id'] = '';
                            $ticket['test_case'] = trim($data['test_case']);
                            $ticket['project_id'] = $projectId;
                            $ticket['ticket_type_id'] = $ticketTypeId;
                            $ticket['source_id'] = $sourceId;
                            $ticket['integrated_created_at'] = date('Y-m-d 00:00:00', strtotime($data['start_datemmddyyyy']));
                            // Check ticket isset in database or not.
                            if ($sourceId != 0) {
                                $checkTicket = $this->ticket->findByAttribute('integrated_ticket_id', (int) $data['ticket_id']);
                                if ($checkTicket == null) {
                                    $checkTicket = $this->ticket->findByAttribute('id', (int) $data['ticket_id']);
                                }
                            } else {
                                $checkTicket = $this->ticket->findByAttribute('id', (int) $data['ticket_id']);
                            }
                            // If isset - update. If not - create new.
                            if ($data['ticket_id'] == null) {
                                $ticket['integrated_ticket_id'] = '0';
                                $ticket['integrated_parent_id'] = '0';
                                $ticketId = $this->ticket->save($ticket);
                            } else {
                                $ticket['integrated_ticket_id'] = (int) $data['ticket_id'];
                                if ($data['parent_id'] == null) {
                                    $ticket['integrated_parent_id'] = '0';
                                } else {
                                    $checkParentTicket = $this->ticket->findByAttribute('integrated_parent_id', (int) $data['parent_id']);
                                    if ($checkParentTicket != null) {
                                        $ticket['integrated_parent_id'] = (int) $data['parent_id'];
                                    }
                                }
                                $updateTicket = $this->ticket->update($ticket, $checkTicket->id);
                                if ($sourceId == 0) {
                                    $ticketId = $checkTicket->id;
                                } else {
                                    $ticketId = $checkTicket->integrated_ticket_id;
                                }
                            }
                            // Step 2: Save/update loc in locs table.
                            $saveLoc = $this->saveOrUpdateLoc($projectId, $ticketId, $userId->id, trim($data['loc']), '', '');

                            // Step 3: Save ticket Id temporarily (prepare for export file with id after import)
                            $arrayTicketId[] = $ticketId;
                            // Step 4: Save entry
                            for ($x = 1; $x <= $numberDayOfMonth; $x++) {
                                $timeDate = $year . '-' . $month . '-' . $x;
                                if ($data["$x"] != null) {
                                    $spentAt = date('Y-m-d 00:00:00', strtotime($timeDate));
                                    $checkEntry = $this->entry->getEntryBeforeSaveImport($spentAt, $ticketId, $projectId, $userId->id)->get();
                                    if ($checkEntry == null) {
                                        $entry['ticket_id'] = $ticketId;
                                        $entry['project_id'] = $projectId;
                                        $entry['activity_id'] = $activityId;
                                        $entry['actual_hour'] = $data["$x"];
                                        $entry['spent_at'] = $spentAt;
                                        $entry['user_id'] = $userId->id;
                                        $insertEntry = $this->entry->save($entry);
                                    } else {
                                        foreach ($checkEntry as $cEntry) {
                                            if ($cEntry->spent_at == $spentAt) {
                                                $entry['actual_hour'] = $data["$x"];
                                                $insertEntry = $this->entry->updateEntryWhenImportFile($entry, $cEntry->id);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } // end foreach $dataEachSheet
                // Get data to fill to file export
                $sheetName = array('sheetName' => $dataEachSheet->getTitle());
                $sheetTicketId = array('ticketId' => $arrayTicketId);
                $sheetArray[] = array_merge($sheetName, $sheetTicketId);
                unset($arrayTicketId);
                $arrayTicketId = array();
            }// end foreach $allData
            DB::commit();
            return serialize($sheetArray);
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    /**
     * @todo Check data in import bug file
     *
     * @author thanhnb6719
     * @param array $allData
     */
    private function checkValueOfBugExcelFile($allData, $checkBox) {
        $row = 7;
        foreach ($allData as $dataEachSheet) {
            $sheetName = trim($dataEachSheet->getTitle());
            $checkProject = $this->project->findByAttribute('name', $sheetName);
            if ($checkProject == null) {
                $errorMessages[] = '[ ' . $sheetName . ' ] - ' . 'Project name is not correct!';
            } else {

                foreach ($dataEachSheet as $dataEachRow) {
                    $row++;
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['source_id']), 'Source ID', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['pagefunction']), 'Page/ Function', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['tracker']), 'Tracker', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['bug_weight']), 'Bug weight', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['priority']), 'Priority', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['bug_type']), 'Bug type', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['status']), 'Status', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['created_datemmddyyyy']), 'Created date', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['created_by_account_id']), 'Created by user', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['author_account_id']), 'Author', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['assign_to_account_id']), 'Assign to', $sheetName, $row);
                    $checkData[] = $this->checkNullInExcelFile(trim($dataEachRow['root_cause']), 'Root cause', $sheetName, $row);
                    $checkData[] = $this->checkTicketWithSource(trim($dataEachRow['ticket_id']), trim($dataEachRow['source_id']), $sheetName, $row);
                    $checkData[] = $this->checkPermissionImport($sheetName, $sheetName, $row, $checkBox);
                    $checkData[] = $this->checkDateCorrectInExcelFile(trim($dataEachRow['created_datemmddyyyy']), 'Created date: ' . $dataEachRow['created_datemmddyyyy'], $sheetName, $row);
                    $checkData[] = $this->checkDateCorrectInExcelFile(trim($dataEachRow['closed_datemmddyyyy']), 'Closed date: ' . $dataEachRow['closed_datemmddyyyy'], $sheetName, $row);
                    $checkData[] = $this->checkIsNumberInExcelFile((int) $dataEachRow['parent_id'], 'Parent ID', $sheetName, $row);
                    $checkData[] = $this->checkIsNumberInExcelFile((int) $dataEachRow['ticket_id'], 'Ticket ID', $sheetName, $row);
                    $checkData[] = $this->checkIsNumberInExcelFile((int) $dataEachRow['loc'], 'Line of code (LOC)', $sheetName, $row);
                    $checkData[] = $this->checkIsNumberInExcelFile(trim($dataEachRow['progress']), 'Progress', $sheetName, $row);
                    $checkData[] = $this->checkPeriodOfNumber(trim($dataEachRow['progress']), 0, 100, $sheetName, $row, 'Progress');
                    $checkData[] = $this->checkNumberIsNotNegativeNumber((int) $dataEachRow['parent_id'], 'Parent ID', $sheetName, $row);
                    $checkData[] = $this->checkNumberIsNotNegativeNumber((int) $dataEachRow['ticket_id'], 'Ticket ID', $sheetName, $row);
                    $checkData[] = $this->checkNumberIsNotNegativeNumber((int) $dataEachRow['loc'], 'Line of code (LOC)', $sheetName, $row);
                    $checkData[] = $this->checkNumberIsNotNegativeNumber(trim($dataEachRow['progress']), 'Progress', $sheetName, $row);
                    $checkData[] = $this->checkLengthOfValue(trim($dataEachRow['pagefunction']), 200, 'Page/ Function', $sheetName, $row);
                    $checkData[] = $this->checkLengthOfValue(trim($dataEachRow['ticket_subject']), 200, 'Ticket subject', $sheetName, $row);
                    $checkData[] = $this->checkLengthOfValue(trim($dataEachRow['description_vnjp']), 999, 'Description', $sheetName, $row);
                    $checkData[] = $this->checkLengthOfValue(trim($dataEachRow['impact_analysis']), 128, 'Impact analysis', $sheetName, $row);
                    $checkData[] = $this->checkCloseDateAndStartDate(trim($dataEachRow['created_datemmddyyyy']), trim($dataEachRow['closed_datemmddyyyy']), $sheetName, $row);
                    $checkData[] = $this->checkSource(trim($dataEachRow['source_id']), $sheetName, $row);
                    $checkData[] = $this->checkVersionWithSource($sheetName, trim($dataEachRow['source_id']), trim($dataEachRow['versionrelease']), $sheetName, $row);
                    $checkData[] = $this->checkRelateSource('status', trim($dataEachRow['source_id']), trim($dataEachRow['status']), 'status', $sheetName, $row);
                    $checkData[] = $this->checkRelateSource('ticket_type', trim($dataEachRow['source_id']), trim($dataEachRow['tracker']), 'ticket type', $sheetName, $row);
                    $checkData[] = $this->checkRelateSource('bugs_type', trim($dataEachRow['source_id']), trim($dataEachRow['bug_type']), 'bug type', $sheetName, $row);
                    $checkData[] = $this->checkRelateSource('bugs_weight', trim($dataEachRow['source_id']), trim($dataEachRow['bug_weight']), 'bug weight', $sheetName, $row);
                    $checkData[] = $this->checkRelateSource('priority', trim($dataEachRow['source_id']), trim($dataEachRow['priority']), 'priority', $sheetName, $row);
                    $checkData[] = $this->checkRelateSource('root_cause', trim($dataEachRow['source_id']), trim($dataEachRow['root_cause']), 'root cause', $sheetName, $row);
                    $checkData[] = $this->checkInDatabase('ticketType', 'name', trim($dataEachRow['tracker']), $sheetName, 'ticket type', $row);
                    $checkData[] = $this->checkInDatabase('ticket', 'id', trim($dataEachRow['parent_id']), $sheetName, 'parent id', $row);
                    $checkData[] = $this->checkInDatabase('projectVersion', 'name', trim($dataEachRow['versionrelease']), $sheetName, 'project version', $row);
                    $checkData[] = $this->checkInDatabase('bugWeight', 'name', trim($dataEachRow['bug_weight']), $sheetName, 'bug weight', $row);
                    $checkData[] = $this->checkInDatabase('priority', 'name', trim($dataEachRow['priority']), $sheetName, 'priority', $row);
                    $checkData[] = $this->checkInDatabase('bugType', 'name', trim($dataEachRow['bug_type']), $sheetName, 'bug type', $row);
                    $checkData[] = $this->checkInDatabase('rootCause', 'name', trim($dataEachRow['root_cause']), $sheetName, 'root cause', $row);
                    $checkData[] = $this->checkInDatabase('status', 'name', trim($dataEachRow['status']), $sheetName, 'status', $row);
                    $checkData[] = $this->checkUserInDatabase(trim($dataEachRow['assign_to_account_id']), 'User assign to', $sheetName, $row);
                    $checkData[] = $this->checkUserInDatabase(trim($dataEachRow['author_account_id']), 'Author', $sheetName, $row);
                    $checkData[] = $this->checkUserInDatabase(trim($dataEachRow['created_by_account_id']), 'Created by', $sheetName, $row);
                    $checkData[] = $this->checkTicketWithNoParent(trim($dataEachRow['source_id']), (int) $dataEachRow['ticket_id'], $sheetName, $row);
                    $checkData[] = $this->checkTicketWithParentRelation((int) $dataEachRow['ticket_id'], (int) $dataEachRow['parent_id'], $dataEachRow['source_id'], $sheetName, $row);
                    $checkData[] = $this->checkMemberInProjectOrNot(trim($dataEachRow['author_account_id']), $checkProject->id, $sheetName, $row);
                    $checkData[] = $this->checkMemberInProjectOrNot(trim($dataEachRow['assign_to_account_id']), $checkProject->id, $sheetName, $row);
                    $checkData[] = $this->checkMemberInProjectOrNot(trim($dataEachRow['created_by_account_id']), $checkProject->id, $sheetName, $row);

                    foreach ($checkData as $checkRowData) {
                        if ($checkRowData != null) {

                            $errorMessages[] = $checkRowData;
                        }
                    }

                    unset($checkData);
                }
            }
        }

        if (isset($errorMessages)) {

            return (['switch' => '5', 'content' => $errorMessages]);
        }
    }

    /**
     * @todo Save data of bug file to database
     *
     * @author thanhnb6719
     * @param array $allData
     */
    private function saveDataFromBugExcelToDatabase($allData) {
        DB::beginTransaction();
        try {
            foreach ($allData as $dataEachSheet) {
                $sheetName = trim($dataEachSheet->getTitle());
                $projectId = $this->project->findByAttribute('name', $sheetName)->id;
                foreach ($dataEachSheet as $dataEachRow) {
                    // Step 1: Save ticket
                    if ($dataEachRow['ticket_subject'] == null) {
                        $ticket['title'] = trim($dataEachRow['pagefunction']);
                    } else {
                        $ticket['title'] = trim($dataEachRow['ticket_subject']);
                    }

                    if ($dataEachRow['versionrelease'] == null) {
                        $ticket['version_id'] = 0;
                    } else {
                        $ticket['version_id'] = $this->projectVersion->findByAttribute('name', trim($dataEachRow['versionrelease']))->id;
                    }

                    if ($dataEachRow['pagefunction'] == null) {
                        $ticket['category'] = '';
                    } else {
                        $ticket['category'] = trim($dataEachRow['pagefunction']);
                    }

                    if ($dataEachRow['description_vnjp'] == null) {
                        $ticket['description'] = 0;
                    } else {
                        $ticket['description'] = trim($dataEachRow['description_vnjp']);
                    }

                    if ($dataEachRow['test_case_id'] == null) {
                        $ticket['test_case'] = 0;
                    } else {
                        $ticket['test_case'] = trim($dataEachRow['test_case_id']);
                    }

                    if ($dataEachRow['closed_datemmddyyyy'] == null) {
                        $ticket['completed_date'] = null;
                    } else {
                        $ticket['completed_date'] = date('Y-m-d 00:00:00', strtotime($dataEachRow['closed_datemmddyyyy']));
                    }

                    if ($dataEachRow['impact_analysis'] == null) {
                        $ticket['impact_analysis'] = '';
                    } else {
                        $ticket['impact_analysis'] = trim($dataEachRow['impact_analysis']);
                    }

                    if ($dataEachRow['progress'] == null) {
                        $ticket['progress'] = 0;
                    } else {
                        $ticket['progress'] = $dataEachRow['progress'];
                    }

                    if ($dataEachRow['tracker'] == null) {
                        $ticket['ticket_type_id'] = $this->ticketType->getTicketTypeIdDefault();
                    } else {
                        $ticket['ticket_type_id'] = $this->ticketType->findByAttribute('name', trim($dataEachRow['tracker']))->id;
                    }

                    if ($dataEachRow['bug_weight'] == null) {
                        $ticket['bug_weight_id'] = $this->bugWeight->getBugWeightIdDefault();
                    } else {
                        $ticket['bug_weight_id'] = $this->bugWeight->findByAttribute('name', trim($dataEachRow['bug_weight']))->id;
                    }

                    if ($dataEachRow['root_cause'] == null) {
                        $ticket['root_cause_id'] = $this->rootCause->getRootCauseIdDefault();
                    } else {
                        $ticket['root_cause_id'] = $this->rootCause->findByAttribute('name', trim($dataEachRow['root_cause']))->id;
                    }

                    if ($dataEachRow['priority'] == null) {
                        $ticket['priority_id'] = $this->priority->getPriorityIdDefault();
                    } else {
                        $ticket['priority_id'] = $this->priority->findByAttribute('name', trim($dataEachRow['priority']))->id;
                    }

                    if ($dataEachRow['bug_type'] == null) {
                        $ticket['bug_type_id'] = $this->bugType->getBugTypeIdDefault();
                    } else {
                        $ticket['bug_type_id'] = $this->bugType->findByAttribute('name', trim($dataEachRow['bug_type']))->id;
                    }

                    if ($dataEachRow['status'] == null) {
                        $ticket['status_id'] = $this->status->getStatusIdDefault();
                    } else {
                        $ticket['status_id'] = $this->status->findByAttribute('name', trim($dataEachRow['status']))->id;
                    }
                    $ticket['project_id'] = $projectId;
                    $ticket['start_date'] = date('Y-m-d 00:00:00', strtotime($dataEachRow['created_datemmddyyyy']));
                    $ticket['integrated_created_at'] = date('Y-m-d 00:00:00', strtotime($dataEachRow['created_datemmddyyyy']));
                    $ticket['assign_to_user'] = $this->getUserInDatabase(trim($dataEachRow['assign_to_account_id']))->user_name;
                    $ticket['made_by_user'] = $this->getUserInDatabase(trim($dataEachRow['author_account_id']))->user_name;
                    $ticket['created_by_user'] = $this->getUserInDatabase(trim($dataEachRow['created_by_account_id']))->user_name;

                    $sourceId = $this->getSourceId(trim($dataEachRow['source_id']));
                    if ($sourceId != 0) {
                        $ticket['source_id'] = $sourceId;
                        $checkTicket = $this->ticket->findByAttribute('integrated_ticket_id', (int) $dataEachRow['ticket_id']);
                        if ($checkTicket == null) {
                            $checkTicket = $this->ticket->findByAttribute('id', (int) $dataEachRow['ticket_id']);
                        }
                    } else {
                        $ticket['source_id'] = 0;
                        $checkTicket = $this->ticket->findByAttribute('id', (int) $dataEachRow['ticket_id']);
                    }

                    if ($dataEachRow['ticket_id'] == null) {
                        $ticket['integrated_ticket_id'] = '0';
                        $ticket['integrated_parent_id'] = '0';
                        $ticketId = $this->ticket->save($ticket);
                    } else {
                        $ticket['integrated_ticket_id'] = (int) $dataEachRow['ticket_id'];
                        if ($dataEachRow['parent_id'] == null) {
                            $ticket['integrated_parent_id'] = '0';
                        } else {
                            $checkParentTicket = $this->ticket->findByAttribute('integrated_parent_id', (int) $dataEachRow['parent_id']);
                            if ($checkParentTicket == null) {
                                $ticket['integrated_parent_id'] = (int) $dataEachRow['parent_id'];
                            }
                        }
                        $updateTicket = $this->ticket->update($ticket, $checkTicket->id);
                        if ($sourceId == 0) {
                            $ticketId = $checkTicket->id;
                        } else {
                            $ticketId = $checkTicket->integrated_ticket_id;
                        }
                    }
                    // Step 2: Save/update loc in locs table.
                    $userId = $this->getUserInDatabase(trim($dataEachRow['assign_to_account_id']))->id;
                    if ($dataEachRow['loc'] != null) {
                        $loc = trim($dataEachRow['loc']);
                    } else {
                        $loc = 0;
                    }
                    $saveLoc = $this->saveOrUpdateLoc($projectId, $ticketId, $userId, $loc, '', '');

                    // Step 3: Get ticket Id to fill after import
                    $arrayTicketId[] = $ticketId;
                }
                $sheetName = array('sheetName' => $dataEachSheet->getTitle());
                $sheetTicketId = array('ticketId' => $arrayTicketId);
                $sheetArray[] = array_merge($sheetName, $sheetTicketId);
                unset($arrayTicketId);
                $arrayTicketId = array();
            }// end foreach $allData\
            DB::commit();
            return serialize($sheetArray);
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    /**
     * Fill Ticket ID to file excel
     *
     * @author thanhnb6719
     * @param Request $request
     * @return file
     */
    public function fillTicketIdToExport($file, $importExcel, $columnNeedFill, $saveFileName, $checkBox, $team, $excelType) {
        try {
            if (Sentinel::check()) {
                $userId = Sentinel::getUser()->id;
            }

            $fileType = Config::get('constant.file_type');

            if (count($checkBox) == 1) {
                $projectId = $checkBox[0];
            } else {
                $projectId = null;
            }
            $sheets = unserialize($importExcel);
            $explodeFile = explode('public', $file);
            $excelTemplate = 'public' . $explodeFile[1];
            $exportFile = Excel::load($excelTemplate, function($reader) use ($sheets, $columnNeedFill) {
                        foreach ($sheets as $eachSheet) {
                            foreach ($columnNeedFill as $key => $value) {
                                if ($key == $eachSheet['sheetName']) {
                                    $reader->sheet($eachSheet['sheetName'], function ($sheet) use ($eachSheet, $value) {
                                        $num = count($eachSheet['ticketId']) + 8;
                                        for ($i = 8; $i < $num; $i++) {
                                            $sheet->setCellValue($value . $i, $eachSheet['ticketId'][$i - 8]);
                                        }
                                    });
                                }
                            }
                        }
                    })->store('xlsx', public_path('/uploads/exportFile'));
            $now = Carbon::now();
            $now = str_replace(' ', '_', $now);
            $now = str_replace(':', '_', $now);
            $path_parts = pathinfo($file);
            $fileName = $path_parts['filename'] . "_" . $now . ".xlsx";
            $oldName = public_path('uploads/exportFile/') . $path_parts['filename'] . ".xlsx";
            $newName = public_path('uploads/exportFile/') . $fileName;
            rename($oldName, $newName);

            $this->saveFileName($fileName, $userId, $projectId, $fileType[0], $saveFileName, $team, $excelType);
            return $newName;
        } catch (\PHPExcel_Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * @todo Import file to database(Include validate file + save data)
     *
     * @author thanhnb6719
     * @param file $file
     * @param string $excelType
     * @param boolean $confirmUpdatedBefore
     * @see \App\Repositories\Import\ImportRepositoryInterface::import()
     */
    public function import($file, $excelType, $confirmUpdatedBefore, $checkBox, $team = null, $project = null) {
        try {
            if (Sentinel::check()) {
                $userId = Sentinel::getUser()->id;
            }
            $fileType = Config::get('constant.file_type');
            if (count($checkBox) == 1) {
                $projectId = $checkBox[0];
            } else {
                $projectId = null;
            }
            if ($excelType == "bug") {
                $checkTemplate = array('no', 'source_id', 'parent_id', 'ticket_id',
                    'ticket_subject', 'versionrelease', 'pagefunction',
                    'description_vnjp', 'tracker', 'bug_weight', 'priority',
                    'bug_type', 'status', 'created_datemmddyyyy', 'created_by_account_id',
                    'author_account_id', 'assign_to_account_id', 'progress', 'test_case_id', 'closed_datemmddyyyy',
                    'root_cause', 'impact_analysis', 'loc');
            } elseif ($excelType == "cost") {
                $checkTemplate = array('no', 'project', 'source_id', 'parent_id', 'ticket_id',
                    'ticket_subject', 'description_vnjp', 'ticket_type',
                    'versionrelease', 'activity', 'progress',
                    'status', 'loc', 'test_case', 'start_datemmddyyyy',
                    'end_datemmddyyyy', 'estimateh', 'actualh');
            }

            $fileName = $file->getClientOriginalName();
            $checkFileRequest = $this->checkFileRequest($file, $fileName);
            if ($checkFileRequest == null) {
                $realPath = $this->uploadFile($file, $fileName);
                $checkTemplate = $this->checkTemplateOfExcelFile($excelType, $realPath, $checkTemplate, count($checkTemplate));
                if ($checkTemplate == null) {
                    $excel = $this->loadFile($realPath);
                    $allData = $this->getDataInSheet($excel);
                    if (is_string($allData)) {
                        return (['switch' => '4', 'content' => $allData]);
                    } else {
                        $errorTimes = [];
                        if ($excelType == "cost") {
                            $errorTimes = [];
                            $checkTimeFile = $this->loadTimeEntryFile($realPath);
                            $dateRaw = $checkTimeFile[0][0]->date_time;
                            if (strlen($dateRaw) == 9 || strlen($dateRaw) == 10) {
                                $date = explode('/', $dateRaw);
                                $month = $date[0];
                                if (is_numeric($month)) {
                                    $day = $date[1];
                                    $year = $date[2];
                                    if (!is_numeric($month) || !is_numeric($day) || !is_numeric($year)) {
                                        $errorTimes[] = 'Time import is not correct! Time must be mm/dd/yyyy (Example: 3/22/2017)';
                                    } else {
                                        if ($day <= 0 || $day > 31) {
                                            $errorTimes[] = 'Day of excel file not correct!';
                                        } elseif ($month <= 0 || $month > 12) {
                                            $errorTimes[] = 'Month of excel file not correct!';
                                        }
                                    }
                                } else {
                                    $errorTimes[] = 'Time import is not correct! Time must be mm/dd/yyyy (Example: 3/22/2017)';
                                }
                            } else {
                                $errorTimes[] = 'Time import is not correct! Time must be mm/dd/yyyy (Example: 3/22/2017)';
                            }
                            if (count($errorTimes) > 0) {
                                return (['switch' => '5', 'content' => $errorTimes]);
                            }
                        }
                        if ($excelType == "bug") {
                            if ($project == null || $project == -1) {
                                return (['switch' => '8']);
                            }

                            $checkExcel = $this->checkValueOfBugExcelFile($allData, $checkBox);
                        } elseif ($excelType == "cost") {
                            if ($team == null || $team == -1) {
                                return (['switch' => '8']);
                            }
                            $checkExcel = $this->checkValueOfCostExcelFile($allData, $checkBox, $day, $month, $year);
                        }
                        if ($checkExcel == null) {
                            if ($excelType == "bug") {
                                $importExcel = $this->saveDataFromBugExcelToDatabase($allData);
                            } elseif ($excelType == "cost") {
                                $importExcel = $this->saveDataFromCostExcelToDatabase($allData, $day, $month, $year);
                            }
                            $column = $this->getPositionOfTicketId($allData);

                            if (!empty($importExcel)) {
                                // Sau nÃ y, má»—i khi save data tá»« quÃ¡ trÃ¬nh import pháº£i lÆ°u láº¡i history cá»§a nÃ³ báº±ng cÃ¡ch lÆ°u file name + project ID + user import vÃ o trong báº£ng file_import.
                                // Hiá»‡n táº¡i, lÃ  chá»‰ lÆ°u tÃªn file náº¿u import thÃ nh cÃ´ng.
                                $name = substr($fileName, 0, -5);
                                $now = Carbon::now();
                                $now = str_replace(' ', '_', $now);
                                $now = str_replace(':', '_', $now);
                                $fileNameImport = $name . "_" . $now . ".xlsx";
                                $saveFileName = $this->saveFileName($fileNameImport, $userId, $projectId, $fileType[1], $parentId = null, $team, $excelType);
                                $copyFile = copy($realPath, base_path() . "/public/uploads/importFile/$fileNameImport");
                                if ($copyFile == true) {
                                    $newFile = base_path() . "/public/uploads/importFile/$fileNameImport";
                                } else {
                                    $errorCopy[] = "Error when create file export!";
                                    return (['switch' => '5', 'content' => $errorCopy]);
                                }
                                // Hiá»‡n táº¡i, return cÃ¡i nÃ y lÃ  cho export ra luÃ´n.
                                // Sau nÃ y lÃ m chá»©c nÄƒng file management thÃ¬ khÃ´ng cáº§n return cÃ¡c dá»¯ liá»‡u nÃ y ná»¯a, mÃ  Ä‘iá»�n ticket ID má»›i vÃ o vÃ  store() trong folder cá»§a dá»± Ã¡n luÃ´n
                                // Chá»‰ cáº§n return message thÃ´ng bÃ¡o thÃ nh cÃ´ng.
                                $fillTicket = $this->fillTicketIdToExport($realPath, $importExcel, $column, $saveFileName, $checkBox, $team, $excelType);
                                return (['switch' => '7', 'importExcel' => $fillTicket]);
                            } else {
                                return (['switch' => '6']);
                            }
                        } else {
                            return $checkExcel;
                        }
                    }
                } else {
                    return $messageError = $checkTemplate;
                }
                if (!isset($messageError)) {
                    return (['switch' => '7']);
                }
            } else {
                return $messageError = $checkFileRequest;
            }
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

}
