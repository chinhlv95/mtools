<?php

use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Maatwebsite\Excel\Readers\Html;

class Helpers
{
    /**
     * @todo Convert format date:dd-mm-yyyy to yyyy-mm-dd
     * @param string $date
     */
    public static function formatDateYmd($date) {
        if(DateTime::createFromFormat('d/m/Y', $date)) {
            $formatDate = DateTime::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            return $formatDate;
        } else {
            return $date;
        }
    }

    /**
     *
     * @author tampt6722
     *
     * @param number $tu
     * @param number $mau
     * @param number $dex
     * @return number
     */
    public static function writeNumber($tu, $mau, $dex = 2){
        if ($mau == 0){
            return 'NA';
        }
        return round($tu/$mau, $dex);
    }
    
    public static function writeNum($tu, $mau, $dex = 2){
        if ($mau == 0){
            return 0;
        }
        return round($tu/$mau, $dex);
    }

    /**
     *
     * @author tampt6722
     *
     * @param number $tu
     * @param number $mau
     * @param number $dex
     * @return number
     */
    public static function writeNumberInPercent($tu, $mau, $dex = 3){
        if ($mau == 0){
            return 'NA';
        }
        return round(($tu/$mau)*100, $dex);
    }
    public static function writeNumberInPer($tu, $mau, $dex = 3){
        if ($mau == 0){
            return 0;
        }
        return round(($tu/$mau)*100, $dex);
    }
    /**
     * @todo Get all entry of each project
     *
     * @author thanhnb6719
     * @param collection $entries
     * @param int $projectId
     * @param int $total
     * @return int $total
     */
    public static function entryOfEachProject($entries, $projectId){
        $total = 0;
        foreach($entries as $entryTime){
            if($entryTime->all_project_id == $projectId){
                $total += $entryTime->actual_hour;
            }
        }
        return $total;
    }

    /**
     * @todo Match project with project member
     *
     * @author thanhnb6719
     * @param collection $projectMembers
     * @param int $eachProjectId
     * @return array[]
     */
    public static function matchProjectWithMember($projectMembers, $eachProjectId){
        $numberMemberInProject = array_count_values(array_map(function($member){return $member->project_id;}, $projectMembers));
        if(isset($numberMemberInProject["$eachProjectId"])){
            $countMember = $numberMemberInProject["$eachProjectId"];
            $flagCount = 1;
        }else{
            $countMember = 1;
            $flagCount = 2;
        }
        return ['count' => $countMember, 'flagCount' => $flagCount];
    }

    /**
     * @todo Get actual time of each position
     *
     * @author thanhnb6719
     * @param collection $entries
     * @param int $projectId
     * @return array[]
     */
    public static function getActualTimeOfEachPosition($entries, $projectId){
        $entryOther = $entryBse = $entryBsejp = $entryDevl = $entryDev = $entryQal = $entryQa = $entryComtor = $entryJpsupport = 0;
        foreach ($entries as $entry){
            if($entry->all_project_id == $projectId){
                switch (strtoupper($entry->user_position)){
                    case "BSE/VN":
                        $entryBse = $entry->actual_hour;
                        break;
                    case "BSE/JP":
                        $entryBsejp = $entry->actual_hour;
                        break;
                    case "DEVL":
                        $entryDevl = $entry->actual_hour;
                        break;
                    case "DEV":
                        $entryDev = $entry->actual_hour;
                        break;
                    case "QAL":
                        $entryQal = $entry->actual_hour;
                        break;
                    case "QA":
                        $entryQa = $entry->actual_hour;
                        break;
                    case "COMTOR":
                        $entryComtor = $entry->actual_hour;
                        break;
                    case "JP SUPPORTER":
                        $entryJpsupport = $entry->actual_hour;
                        break;
                    default:
                        $entryOther = $entry->actual_hour;
                        break;
                }
            }
        }
        return ['bse'       => $entryBse,
                'bsejp'     => $entryBsejp,
                'devl'      => $entryDevl,
                'dev'       => $entryDev,
                'qal'       => $entryQal,
                'qa'        => $entryQa,
                'comtor'    => $entryComtor,
                'jpsupport' => $entryJpsupport,
                'other'     => $entryOther
        ];
    }


    /**
     * @todo Get month in period
     *
     * @author thanhnb6719
     * @param date $start_date
     * @param date $end_date
     * @return DatePeriod
     */
    public static function findMonthInPeriodOfTime($start_date, $end_date){
        $start    = new DateTime($start_date);
        $end      = new DateTime($end_date);
        $start->modify('first day of this month');
        $end->modify('last day of this month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);
        return $period;
    }

    /**
     * @todo Get week in period
     *
     * @author thanhnb6719
     * @param date $start_date
     * @param date $end_date
     * @return DatePeriod
     */
    public static function findWeekInPeriodOfTime($start_date, $end_date){
        $start    = new DateTime($start_date);
        $end      = new DateTime($end_date);
        $start->modify('monday this week');
        $end->modify('sunday this week');
        $interval = DateInterval::createFromDateString('1 week');
        $period   = new DatePeriod($start, $interval, $end);
        return $period;
    }

    /**
     * @todo remove begin space and end space in string
     *
     * @author SonNA
     * @param string $str
     * @return $str
     */
    public static function mst_trim($str){
        return  preg_replace("/(^\s+)|(\s+$)/us", '', urldecode($str));
    }

    public static function getDepartmentManager($code){
        $checkMember = User::where('member_code', $code)->first();
        if ($checkMember == null) {
            return null;
        } else {
            return $checkMember->last_name." ".$checkMember->first_name;
        }
    }

    /**
     * @todo Get all admin or director
     *
     * @author thanhnb6719
     * @return array $ids
     */
    public static function getAdminOrDirectorId()
    {
        $ids          = [];
        $roleAdmin    = Sentinel::findRoleById(1);
        $admins       = $roleAdmin->users()->with('roles')->get();
        if (count($admins) > 0) {
            foreach ($admins as $admin) {
                $ids [] = $admin->id;
            }
        }
        $roleDirector = Sentinel::findRoleById(13);
        $directors    = $roleDirector->users()->with('roles')->get();
        if (count($directors) > 0) {
            foreach ($directors as $director) {
                $ids [] = $director->id;
            }
        }
        return $ids;
    }

    /**
     * @todo Get user login
     *
     * @author thanhnb6719
     * @return int $userId
     */
    public static function getIdOfUserLogin() {
        $userId = Sentinel::getUser()->id;
        return $userId;
    }

    /**
     *
     * @author tampt6722
     *
     * @param integer $id
     * @return string
     */
    public static function getMainEmail($id)
    {
        $email = '';
        if ($id > 0) {
            $user = User::select('email')
                ->where('id', $id)->first();
            if (count($user) > 0) {
               $email = $user->email;
            }
        }
        return $email;
    }

    public static function getListYear() {
        $listYear = [];
        $thisYear = date("Y");
        $firstYear = 2010;
        for ($x = $firstYear; $x <= $thisYear; $x++) {
            $listYear[$x] = $x;
        }
        return $listYear;
    }

    public static function getListMonth() {
        $listMonth = [ 1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                       5 => 'May', 6 => 'June', 7 => 'July', 8 => 'Aug',
                       9 => 'Sept', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
        return $listMonth;
    }
}
