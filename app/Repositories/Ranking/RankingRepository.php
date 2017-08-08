<?php
namespace App\Repositories\Ranking;

use App\Repositories\Ranking\RankingRepositoryInterface;
use Helpers;
use Config;
use App\Models\ProjectMember;
use DB;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Models\Department;
use App\Models\ProjectReport;

 class RankingRepository implements RankingRepositoryInterface{
     public function __construct(DepartmentRepositoryInterface $department)
     {
         $this->department     = $department;
     }
     public function rankingDev($dataDevs){
         $resultDevs = [];
         $resultKloc = [];
         $resultAssign = [];
         $resultTaskMem = [];
         $resultBugKloc = [];
         $resultBugMem = [];
         $mm = Config::get('constant.men_month'); // Men month value
         foreach($dataDevs as $data)
         {
             $mem_name = $data['name'];
             $kloc_mem = Helpers::writeNumber(floatval($data['kloc']), $data['workload'] / $mm) ;
             if($kloc_mem == "NA")
                 $kloc_mem = 0;
                 $assign_mem = Helpers::writeNumber(floatval($data['bug_weighted']), $data['workload']/$mm);
             if($assign_mem == "NA")
                 $assign_mem = 0;
                 $task_mem = Helpers::writeNumber(floatval($data['task']), $data['workload']/$mm);
             if($task_mem == "NA")
                 $task_mem = 0;
                 $bug_klog = Helpers::writeNumber(floatval($data['madebug_weighted']), $data['kloc']) ;
             if($bug_klog == "NA")
                 $bug_klog = 0;
                 $bug_mem = Helpers::writeNumber(floatval($data['madebug_weighted']), $data['workload']/$mm) ;
             if($bug_mem == "NA")
                 $bug_mem = 0;
                 array_push($resultDevs,array(
                     'user_id' => $data['user_id'],
                     'name'=>$mem_name,
                 'department_name' => $data['department_name'],
                 'kloc_men' => [
                                 'result'=> $kloc_mem,
                                 'rank' => 0,
                 ],
                 'assign_mem'=>[
                                 'result'=> $assign_mem,
                                 'rank' => 0,
                 ],
                 'task_mem' =>[
                                 'result'=> $task_mem,
                                 'rank' => 0,
                 ],
                 'bug_klog'=>[
                                 'result'=> $bug_klog,
                                 'rank' => 0,
                 ],
                 'bug_mem' => [
                                 'result'=> $bug_mem,
                                 'rank' => 0,
                             ],
             ));
             if(!in_array($kloc_mem,$resultKloc))
                 array_push($resultKloc,$kloc_mem);

             if(!in_array($assign_mem,$resultAssign))
                 array_push($resultAssign,$assign_mem);

             if(!in_array($task_mem,$resultTaskMem))
                 array_push($resultTaskMem,$task_mem);

             if(!in_array($bug_klog,$resultBugKloc))
                 array_push($resultBugKloc,$bug_klog);

             if(!in_array($bug_mem,$resultBugMem))
                 array_push($resultBugMem,$bug_mem);
             }
             rsort($resultKloc);
             rsort($resultAssign);
             rsort($resultTaskMem);
             sort($resultBugKloc);
             sort($resultBugMem);

             $resultP = [];
             $resultQ = [];
             $resultR = [];
             foreach($resultDevs as $position => $item)
             {
                 foreach ($resultKloc as $key=>$value)
                 {
                     if($item['kloc_men']['result'] == $value)
                     {
                         $resultDevs[$position]['kloc_men']['rank'] = $key;
                         break;
                     }
                 }
                 foreach ($resultAssign as $key=>$value)
                 {
                     if($item['assign_mem']['result'] == $value)
                     {
                         $resultDevs[$position]['assign_mem']['rank'] = $key;
                         break;
                     }
                 }
                 foreach ($resultTaskMem as $key=>$value)
                 {
                     if($item['task_mem']['result'] == $value)
                     {
                         $resultDevs[$position]['task_mem']['rank'] = $key;
                         break;
                     }
                 }
                 $resultDevs[$position]['rankP'] = round (( $resultDevs[$position]['kloc_men']['rank'] + $resultDevs[$position]['assign_mem']['rank'] + $resultDevs[$position]['task_mem']['rank'] ) / 3 , 2);
                 foreach ($resultBugKloc as $key=>$value)
                 {
                     if($item['bug_klog']['result'] == $value)
                     {
                         $resultDevs[$position]['bug_klog']['rank'] = $key;
                         break;
                     }
                 }
                 foreach ($resultBugMem as $key=>$value)
                 {
                     if($item['bug_mem']['result'] == $value)
                     {
                         $resultDevs[$position]['bug_mem']['rank'] = $key;
                         break;
                     }
                 }
                 $resultDevs[$position]['rankQ'] = round (( $resultDevs[$position]['bug_klog']['rank'] + $resultDevs[$position]['bug_mem']['rank'] ) / 2,2);

                 if(!in_array($resultDevs[$position]['rankP'],$resultP))
                     array_push($resultP,$resultDevs[$position]['rankP']);
                     if(!in_array($resultDevs[$position]['rankQ'],$resultQ))
                         array_push($resultQ,$resultDevs[$position]['rankQ']);
             }
             sort($resultP);
             sort($resultQ);

             foreach($resultDevs as $position => $item)
             {
                 foreach ($resultP as $key=>$value)
                 {
                     if($item['rankP'] == $value)
                     {
                         $resultDevs[$position]['rankP'] = $key;
                         break;
                     }
                 }
                 foreach ($resultQ as $key=>$value)
                 {
                     if($item['rankQ'] == $value)
                     {
                         $resultDevs[$position]['rankQ'] = $key;
                         break;
                     }
                 }
                 $resultDevs[$position]['rank'] = round (( $resultDevs[$position]['rankP'] + $resultDevs[$position]['rankQ'] ) / 2,2);
                 if(!in_array($resultDevs[$position]['rank'],$resultR))
                     array_push($resultR,$resultDevs[$position]['rank']);
             }
             sort($resultR);

             foreach($resultDevs as $position => $item)
             {
                 foreach ($resultR as $key=>$value)
                 {
                     if($item['rank'] == $value)
                     {
                         $resultDevs[$position]['rank'] = $key;
                         break;
                     }
                 }
             }
             return $resultDevs;
     }
     public function rankingQA($dataQas){
         $resultDevs = [];
         $resultKloc = [];
         $resultAssign = [];
         $resultTaskMem = [];
         $resultBugKloc = [];
         $resultBugMem = [];
         $mm = Config::get('constant.men_month'); // Men month value
         foreach($dataQas as $data)
         {
             $mem_name = $data['name'];
             $kloc_mem = Helpers::writeNumber($data['testcase_create'], $data['createTc_workload']/$mm);
             if($kloc_mem == "NA")
                 $kloc_mem = 0;
                 $assign_mem = Helpers::writeNumber($data['testcase_test'], $data['test_workload']/$mm);
             if($assign_mem == "NA")
                 $assign_mem = 0;
                 $task_mem = Helpers::writeNumber($data['task'], $data['workload']/$mm);
             if($task_mem == "NA")
                 $task_mem = 0;
                 $bug_klog = Helpers::writeNumber($data['foundbug_weighted'], ($data['testcase_test']/1000)) ;
             if($bug_klog == "NA")
                 $bug_klog = 0;
                 $bug_mem = Helpers::writeNumber($data['foundbug_weighted'], $data['workload']/$mm) ;
             if($bug_mem == "NA")
                 $bug_mem = 0;
                 array_push($resultDevs,array(
                     'user_id' => $data['user_id'],
                     'name'=>$mem_name,
                     'department_name' => $data['department_name'],
                     'kloc_men' => [
                                     'result'=> $kloc_mem,
                                     'rank' => 0,
                     ],
                     'assign_mem'=>[
                                     'result'=> $assign_mem,
                                     'rank' => 0,
                     ],
                     'task_mem' =>[
                                     'result'=> $task_mem,
                                     'rank' => 0,
                     ],
                     'bug_klog'=>[
                                     'result'=> $bug_klog,
                                     'rank' => 0,
                     ],
                     'bug_mem' => [
                                     'result'=> $bug_mem,
                                     'rank' => 0,
                     ],
                 ));
         if(!in_array($kloc_mem,$resultKloc))
             array_push($resultKloc,$kloc_mem);

         if(!in_array($assign_mem,$resultAssign))
             array_push($resultAssign,$assign_mem);

         if(!in_array($task_mem,$resultTaskMem))
             array_push($resultTaskMem,$task_mem);

         if(!in_array($bug_klog,$resultBugKloc))
             array_push($resultBugKloc,$bug_klog);

         if(!in_array($bug_mem,$resultBugMem))
             array_push($resultBugMem,$bug_mem);
         }
         rsort($resultKloc);
         rsort($resultAssign);
         rsort($resultTaskMem);
         rsort($resultBugKloc);
         rsort($resultBugMem);

         $resultP = [];
         $resultQ = [];
         $resultR = [];
         foreach($resultDevs as $position => $item)
         {
             foreach ($resultKloc as $key=>$value)
             {
                 if($item['kloc_men']['result'] == $value)
                 {
                     $resultDevs[$position]['kloc_men']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultAssign as $key=>$value)
             {
                 if($item['assign_mem']['result'] == $value)
                 {
                     $resultDevs[$position]['assign_mem']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultTaskMem as $key=>$value)
             {
                 if($item['task_mem']['result'] == $value)
                 {
                     $resultDevs[$position]['task_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultDevs[$position]['rankP'] = round (( $resultDevs[$position]['kloc_men']['rank'] + $resultDevs[$position]['assign_mem']['rank'] + $resultDevs[$position]['task_mem']['rank'] ) / 3 , 2);
             foreach ($resultBugKloc as $key=>$value)
             {
                 if($item['bug_klog']['result'] == $value)
                 {
                     $resultDevs[$position]['bug_klog']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugMem as $key=>$value)
             {
                 if($item['bug_mem']['result'] == $value)
                 {
                     $resultDevs[$position]['bug_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultDevs[$position]['rankQ'] = round (( $resultDevs[$position]['bug_klog']['rank'] + $resultDevs[$position]['bug_mem']['rank'] ) / 2,2);

             if(!in_array($resultDevs[$position]['rankP'],$resultP))
                 array_push($resultP,$resultDevs[$position]['rankP']);
             if(!in_array($resultDevs[$position]['rankQ'],$resultQ))
                 array_push($resultQ,$resultDevs[$position]['rankQ']);
         }
         sort($resultP);
         sort($resultQ);

         foreach($resultDevs as $position => $item)
         {
             foreach ($resultP as $key=>$value)
             {
                 if($item['rankP'] == $value)
                 {
                     $resultDevs[$position]['rankP'] = $key;
                     break;
                 }
             }
             foreach ($resultQ as $key=>$value)
             {
                 if($item['rankQ'] == $value)
                 {
                     $resultDevs[$position]['rankQ'] = $key;
                     break;
                 }
             }
             $resultDevs[$position]['rank'] = round (( $resultDevs[$position]['rankP'] + $resultDevs[$position]['rankQ'] ) / 2,2);
             if(!in_array($resultDevs[$position]['rank'],$resultR))
                 array_push($resultR,$resultDevs[$position]['rank']);
         }
         sort($resultR);

         foreach($resultDevs as $position => $item)
         {
             foreach ($resultR as $key=>$value)
             {
                 if($item['rank'] == $value)
                 {
                     $resultDevs[$position]['rank'] = $key;
                     break;
                 }
             }
         }

         return $resultDevs;
     }
     public function rankingProject($dataProjects)
     {
         $resultProjects = [];
         $resultKloc = [];
         $resultTC = [];
         $resultTaskMem = [];
         $resultBugKloc = [];
         $resultBugUat = [];
         $resultBugTC = [];
         $resultBugRe = [];
         $resultBugMem = [];
         $mm = Config::get('constant.men_month'); // Men month value
         foreach($dataProjects as $data)
         {
             $kloc = Helpers::writeNumber($data['loc'], 1000);
             $workload = Helpers::writeNumber($data['actual_hour'], $mm);
             $kloc_mem = Helpers::writeNumber($kloc, $workload);
             $kTestcase = Helpers::writeNumber($data['tested_tc'], 1000);

             if($kloc_mem == "NA")
                 $kloc_mem = 0;
             $testCase_mem = Helpers::writeNumber($data['tested_tc'], $workload);
             if($testCase_mem == "NA")
                 $testCase_mem = 0;
             $task_mem = Helpers::writeNumber($data['task'], $workload);
             if($task_mem == "NA")
                 $task_mem = 0;
             $bug_lock = Helpers::writeNumber($data['weighted_bug'], $kloc);
             if($bug_lock == "NA")
                 $bug_lock = 0;
             $bugUAT = Helpers::writeNumber($data['weighted_uat_bug'], $kloc);
             if($bugUAT == "NA")
                 $bugUAT = 0;
             $bugTc = Helpers::writeNumber($data['weighted_bug'], $kTestcase);
             if($bugTc == "NA")
                 $bugTc = 0;
             $bugBeforeRelease = Helpers::writeNumberInPercent($data['weighted_bug'], ($data['weighted_bug']+$data['weighted_uat_bug']));
             if($bugBeforeRelease == "NA")
                 $bugBeforeRelease = 0;
             $bugMem = Helpers::writeNumber($data['weighted_bug'], $workload);
             if($bugMem == "NA")
                 $bugMem = 0;
             array_push($resultProjects,array(
                             'project_id' => $data['project_id'],
                             'language_id' => $data['language_id'],
                             'type_id' => $data['type_id'],
                             'name'=>$data['project_name'],
                             'department_name' => $data['department_name'],
                             'kloc_men' => [
                                     'result'=> $kloc_mem,
                                     'rank' => 0,
                             ],
                             'test_case_mem'=>[
                                     'result'=> $testCase_mem,
                                     'rank' => 0,
                             ],
                             'task_mem' =>[
                                             'result'=> $task_mem,
                                             'rank' => 0,
                             ],
                             'bug_klog'=>[
                                             'result'=> $bug_lock,
                                             'rank' => 0,
                             ],
                             'bug_uat' => [
                                             'result'=> $bugUAT,
                                             'rank' => 0,
                             ],
                             'bug_Tc' => [
                                             'result'=> $bugTc,
                                             'rank' => 0,
                             ],
                             'bug_bf_re' => [
                                             'result'=> $bugBeforeRelease,
                                             'rank' => 0,
                             ],
                             'bug_mem' => [
                                             'result'=> $bugMem,
                                             'rank' => 0,
                             ],
             ));
             if(!in_array($kloc_mem,$resultKloc))
                 array_push($resultKloc,$kloc_mem);

             if(!in_array($testCase_mem,$resultTC))
                 array_push($resultTC,$testCase_mem);

             if(!in_array($task_mem,$resultTaskMem))
                 array_push($resultTaskMem,$task_mem);

             if(!in_array($bug_lock,$resultBugKloc))
                 array_push($resultBugKloc,$bug_lock);

             if(!in_array($bugUAT,$resultBugUat))
                 array_push($resultBugUat,$bugUAT);

             if(!in_array($bugTc,$resultBugTC))
                 array_push($resultBugTC,$bugTc);

             if(!in_array($bugBeforeRelease,$resultBugRe))
                 array_push($resultBugRe,$bugBeforeRelease);

             if(!in_array($bugMem,$resultBugMem))
                 array_push($resultBugMem,$bugMem);
         }
         rsort($resultKloc);
         rsort($resultTC);
         rsort($resultTaskMem);
         sort($resultBugKloc);
         sort($resultBugUat);
         sort($resultBugTC);
         rsort($resultBugRe);
         rsort($resultBugMem);

         $resultP = [];
         $resultQ = [];
         $resultR = [];

         foreach($resultProjects as $position => $item)
         {
             foreach ($resultKloc as $key=>$value)
             {
                 if($item['kloc_men']['result'] == $value)
                 {
                     $resultProjects[$position]['kloc_men']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultTC as $key=>$value)
             {
                 if($item['test_case_mem']['result'] == $value)
                 {
                     $resultProjects[$position]['test_case_mem']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultTaskMem as $key=>$value)
             {
                 if($item['task_mem']['result'] == $value)
                 {
                     $resultProjects[$position]['task_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultProjects[$position]['rankP'] = round (( $resultProjects[$position]['kloc_men']['rank'] + $resultProjects[$position]['test_case_mem']['rank'] + $resultProjects[$position]['task_mem']['rank'] ) / 3 , 2);
             foreach ($resultBugKloc as $key=>$value)
             {
                 if($item['bug_klog']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_klog']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugUat as $key=>$value)
             {
                 if($item['bug_uat']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_uat']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugTC as $key=>$value)
             {
                 if($item['bug_Tc']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_Tc']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugRe as $key=>$value)
             {
                 if($item['bug_bf_re']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_bf_re']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugMem as $key=>$value)
             {
                 if($item['bug_mem']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultProjects[$position]['rankQ'] = round (( $resultProjects[$position]['bug_klog']['rank'] + $resultProjects[$position]['bug_uat']['rank']
                                                 + $resultProjects[$position]['bug_Tc']['rank'] + $resultProjects[$position]['bug_bf_re']['rank']
                                                 + $resultProjects[$position]['bug_mem']['rank']
                                                ) / 5,2);

             if(!in_array($resultProjects[$position]['rankP'],$resultP))
                 array_push($resultP,$resultProjects[$position]['rankP']);
             if(!in_array($resultProjects[$position]['rankQ'],$resultQ))
                 array_push($resultQ,$resultProjects[$position]['rankQ']);
         }
         sort($resultP);
         sort($resultQ);

         foreach($resultProjects as $position => $item)
         {
             foreach ($resultP as $key=>$value)
             {
                 if($item['rankP'] == $value)
                 {
                     $resultProjects[$position]['rankP'] = $key;
                     break;
                 }
             }
             foreach ($resultQ as $key=>$value)
             {
                 if($item['rankQ'] == $value)
                 {
                     $resultProjects[$position]['rankQ'] = $key;
                     break;
                 }
             }
             $resultProjects[$position]['rank'] = round (( $resultProjects[$position]['rankP'] + $resultProjects[$position]['rankQ'] ) / 2,2);
             if(!in_array($resultProjects[$position]['rank'],$resultR))
                 array_push($resultR,$resultProjects[$position]['rank']);
         }

         sort($resultR);

         foreach($resultProjects as $position => $item)
         {
             foreach ($resultR as $key=>$value)
             {
                 if($item['rank'] == $value)
                 {
                     $resultProjects[$position]['rank'] = $key;
                     break;
                 }
             }
         }

         return $resultProjects;
     }
     public function rankingBres($years,$months){
         $dataDevs = ProjectMember::select(
                 'project_member.user_id as user_id',
                 DB::raw('sum(members_report.workload) as workload'),
                 DB::raw('sum(members_report.task) as task'),
                 DB::raw('sum(members_report.kloc) as kloc'),
                 DB::raw('sum(members_report.bug_weighted)  as bug_weighted'),
                 DB::raw('sum(members_report.madebug_weighted) as madebug_weighted')
                 )->leftJoin('members_report','project_member.project_id','=','members_report.project_id')
                 ->whereIn('project_member.role_id',array(7,8))
                 ->where('members_report.position','=','Dev')
                 ->where('members_report.year', $years);
         $dataProjects = ProjectMember::select(
                 'project_member.user_id as user_id',
                 'users.last_name',
                 'users.first_name',
                 'departments.name as deparments_name',
                 DB::raw('sum(project_report.actual_hour) as actual_hour'),
                 DB::raw('sum(project_report.loc)  as loc'),
                 DB::raw('sum(project_report.task)  as task'),
                 DB::raw('sum(project_report.tested_tc)  as tested_tc'),
                 DB::raw('sum(project_report.weighted_bug)  as weighted_bug'),
                 DB::raw('sum(project_report.weighted_uat_bug)  as weighted_uat_bug')
                 )
                 ->leftJoin('project_report','project_member.project_id','=','project_report.project_id')
                 ->leftJoin('users','users.id','=','project_member.user_id')
                 ->leftJoin('departments','departments.id','=','users.department_id')
                 ->whereIn('project_member.role_id',array(7,8))
                 ->where('project_report.year', $years);

         if (!in_array(0, $months)) {
             $dataDevs = $dataDevs->whereIn('members_report.month', $months);
             $dataProjects = $dataProjects->whereIn('project_report.month', $months);
         }
         $dataDevs = $dataDevs->groupBy('project_member.user_id')
         ->get()->toArray();
         $dataProjects = $dataProjects->groupBy('project_member.user_id')
         ->get()->toArray();

         $resultDevs = $this->rankDevBrse($dataDevs);
         $resultProjects = $this->rankProjectBrse($dataProjects);
         $maxP = 0;
         $maxQ = 0;
         foreach($resultDevs as $keyD=>$itemD)
         {
            if($itemD['rankP'] > $maxP)
                $maxP = $itemD['rankP'];
            if($itemD['rankQ'] > $maxQ)
                $maxQ = $itemD['rankQ'];
         }
         $resultBres = [];
         $resultP = [];
         $resultQ = [];
         $resultR = [];
         foreach($resultProjects as $keyP=>$itemP)
         {
             $check = false;
            foreach($resultDevs as $keyD=>$itemD)
            {
                if($itemP['user_id'] == $itemD['user_id'])
                {
                    array_push($resultBres,array(
                        'user_id' => $itemD['user_id'],
                        'name' => $itemP['name'],
                        'department_name' => $itemP['deparments_name'],
                        'rankP' => round(($itemP['rankP'] + $itemD['rankP']) / 2 ,2),
                        'rankQ' => round(($itemP['rankQ'] + $itemD['rankQ']) / 2 ,2),
                    ));
                    $check = true;
                    if(!in_array(round(($itemP['rankP'] + $itemD['rankP']) / 2 ,2),$resultP))
                    array_push($resultP,round(($itemP['rankP'] + $itemD['rankP']) / 2 ,2));
                    if(!in_array(round(($itemP['rankQ'] + $itemD['rankQ']) / 2 ,2),$resultQ))
                        array_push($resultQ,round(($itemP['rankQ'] + $itemD['rankQ']) / 2 ,2));
                    break;
                }
            }
            if(!$check)
            {
                array_push($resultBres,array(
                        'user_id' => $itemP['user_id'],
                        'name' => $itemP['name'],
                        'department_name' => $itemP['deparments_name'],
                        'rankP' =>  round(($itemP['rankP'] + $maxP + 1)/2,2),
                        'rankQ' => round(($itemP['rankQ'] + $maxQ + 1)/2,2),
                ));

                if(!in_array(round(($itemP['rankP'] + $maxP + 1)/2,2),$resultP))
                    array_push($resultP,round(($itemP['rankP'] + $maxP + 1)/2,2));
                if(!in_array(round(($itemP['rankQ'] + $maxQ + 1)/2,2),$resultQ))
                    array_push($resultQ,round(($itemP['rankQ'] + $maxQ + 1)/2,2));
            }
         }
         sort($resultP);
         sort($resultQ);
         foreach($resultBres as $position=>$item)
         {
             foreach ($resultP as $key=>$value)
             {
                 if($item['rankP'] == $value)
                 {
                     $resultBres[$position]['rankP'] = $key;
                     break;
                 }
             }
             foreach ($resultQ as $key=>$value)
             {
                 if($item['rankQ'] == $value)
                 {
                     $resultBres[$position]['rankQ'] = $key;
                     break;
                 }
             }
             $resultBres[$position]['rank'] = round (( $resultBres[$position]['rankP'] + $resultBres[$position]['rankQ'] ) / 2,2);
             if(!in_array($resultBres[$position]['rank'],$resultR))
                 array_push($resultR,$resultBres[$position]['rank']);
         }
         sort($resultR);
         foreach($resultBres as $position => $item)
         {
             foreach ($resultR as $key=>$value)
             {
                 if($item['rank'] == $value)
                 {
                     $resultBres[$position]['rank'] = $key;
                     break;
                 }
             }
         }

         return $resultBres;
     }
     public function rankingQAL($years,$months){
         $dataQas = ProjectMember::select(
                 'project_member.user_id as user_id',
                 DB::raw('sum(members_report.testcase_create) as testcase_create'),
                 DB::raw('sum(members_report.createTc_workload) as createTc_workload'),
                 DB::raw('sum(members_report.test_workload) as test_workload'),
                 DB::raw('sum(members_report.task)  as task'),
                 DB::raw('sum(members_report.foundbug_weighted) as foundbug_weighted'),
                 DB::raw('sum(members_report.testcase_test) as testcase_test'),
                 DB::raw('sum(members_report.workload) as workload')
                 )->leftJoin('members_report','project_member.project_id','=','members_report.project_id')
                 ->whereIn('project_member.role_id',array(6))
                 ->where('members_report.position','=','QA')
                 ->where('members_report.year', $years);
         $dataProjects = ProjectMember::select(
                 'project_member.user_id as user_id',
                 'users.last_name',
                 'users.first_name',
                 'departments.name as deparments_name',
                 DB::raw('sum(project_report.actual_hour) as actual_hour'),
                 DB::raw('sum(project_report.loc)  as loc'),
                 DB::raw('sum(project_report.task)  as task'),
                 DB::raw('sum(project_report.tested_tc)  as tested_tc'),
                 DB::raw('sum(project_report.weighted_bug)  as weighted_bug'),
                 DB::raw('sum(project_report.weighted_uat_bug)  as weighted_uat_bug')
                 )
                 ->leftJoin('project_report','project_member.project_id','=','project_report.project_id')
                 ->leftJoin('users','users.id','=','project_member.user_id')
                 ->leftJoin('departments','departments.id','=','users.department_id')
                 ->whereIn('project_member.role_id',array(6))
                 ->where('project_report.year', $years);
         if (!in_array(0, $months)) {
             $dataQas= $dataQas->whereIn('members_report.month', $months);
             $dataProjects = $dataProjects->whereIn('project_report.month', $months);
         }
         $dataQas= $dataQas->groupBy('project_member.user_id')
         ->get()->toArray();
         $dataProjects = $dataProjects->groupBy('project_member.user_id')
         ->get()->toArray();

         $resultQas = $this->rankQaQal($dataQas);
         $resultProjects = $this->rankProjectBrse($dataProjects);
         $maxP = 0;
         $maxQ = 0;
         foreach($resultQas as $keyD=>$itemD)
         {
             if($itemD['rankP'] > $maxP)
                 $maxP = $itemD['rankP'];
             if($itemD['rankQ'] > $maxQ)
                 $maxQ = $itemD['rankQ'];
         }
         $resultQAL = [];
         $resultP = [];
         $resultQ = [];
         $resultR = [];
         foreach($resultProjects as $keyP=>$itemP)
         {
             $check = false;
             foreach($resultQas as $keyD=>$itemD)
             {
                 if($itemP['user_id'] == $itemD['user_id'])
                 {
                     array_push($resultQAL,array(
                                     'user_id' => $itemD['user_id'],
                                     'name' => $itemP['name'],
                                     'department_name' => $itemP['deparments_name'],
                                     'rankP' => round(($itemP['rankP'] + $itemD['rankP']) / 2 ,2),
                                     'rankQ' => round(($itemP['rankQ'] + $itemD['rankQ']) / 2 ,2),
                     ));
                     $check = true;
                     if(!in_array(round(($itemP['rankP'] + $itemD['rankP']) / 2 ,2),$resultP))
                         array_push($resultP,round(($itemP['rankP'] + $itemD['rankP']) / 2 ,2));
                     if(!in_array(round(($itemP['rankQ'] + $itemD['rankQ']) / 2 ,2),$resultQ))
                         array_push($resultQ,round(($itemP['rankQ'] + $itemD['rankQ']) / 2 ,2));
                         break;
                 }
             }
             if(!$check)
             {
                 array_push($resultQAL,array(
                                 'user_id' => $itemP['user_id'],
                                 'name' => $itemP['name'],
                                 'department_name' => $itemP['deparments_name'],
                                 'rankP' =>  round(($itemP['rankP'] + $maxP + 1)/2,2),
                                 'rankQ' => round(($itemP['rankQ'] + $maxQ + 1)/2,2),
                 ));

                 if(!in_array(round(($itemP['rankP'] + $maxP + 1)/2,2),$resultP))
                     array_push($resultP,round(($itemP['rankP'] + $maxP + 1)/2,2));
                 if(!in_array(round(($itemP['rankQ'] + $maxQ + 1)/2,2),$resultQ))
                     array_push($resultQ,round(($itemP['rankQ'] + $maxQ + 1)/2,2));
             }
         }
         sort($resultP);
         sort($resultQ);
         foreach($resultQAL as $position=>$item)
         {
             foreach ($resultP as $key=>$value)
             {
                 if($item['rankP'] == $value)
                 {
                     $resultQAL[$position]['rankP'] = $key;
                     break;
                 }
             }
             foreach ($resultQ as $key=>$value)
             {
                 if($item['rankQ'] == $value)
                 {
                     $resultQAL[$position]['rankQ'] = $key;
                     break;
                 }
             }
             $resultQAL[$position]['rank'] = round (( $resultQAL[$position]['rankP'] + $resultQAL[$position]['rankQ'] ) / 2,2);
             if(!in_array($resultQAL[$position]['rank'],$resultR))
                 array_push($resultR,$resultQAL[$position]['rank']);
         }
         sort($resultR);
         foreach($resultQAL as $position => $item)
         {
             foreach ($resultR as $key=>$value)
             {
                 if($item['rank'] == $value)
                 {
                     $resultQAL[$position]['rank'] = $key;
                     break;
                 }
             }
         }
         return $resultQAL;
     }
     function rankQaQal($dataQas)
     {
         $resultDevs = [];
         $resultKloc = [];
         $resultAssign = [];
         $resultTaskMem = [];
         $resultBugKloc = [];
         $resultBugMem = [];
         $mm = Config::get('constant.men_month'); // Men month value
         foreach($dataQas as $data)
         {
             $mem_name = $data['user_id'];
             $kloc_mem = Helpers::writeNumber($data['testcase_create'], $data['createTc_workload']/$mm);
             if($kloc_mem == "NA")
                 $kloc_mem = 0;
                 $assign_mem = Helpers::writeNumber($data['testcase_test'], $data['test_workload']/$mm);
             if($assign_mem == "NA")
                 $assign_mem = 0;
                 $task_mem = Helpers::writeNumber($data['task'], $data['workload']/$mm);
             if($task_mem == "NA")
                 $task_mem = 0;
                 $bug_klog = Helpers::writeNumber($data['foundbug_weighted'], ($data['testcase_test']/1000)) ;
             if($bug_klog == "NA")
                 $bug_klog = 0;
                 $bug_mem = Helpers::writeNumber($data['foundbug_weighted'], $data['workload']/$mm) ;
             if($bug_mem == "NA")
                 $bug_mem = 0;
                 array_push($resultDevs,array(
                                 'user_id'=>$mem_name,
                                 'kloc_men' => [
                                                 'result'=> $kloc_mem,
                                                 'rank' => 0,
                                 ],
                                 'assign_mem'=>[
                                                 'result'=> $assign_mem,
                                                 'rank' => 0,
                                 ],
                                 'task_mem' =>[
                                                 'result'=> $task_mem,
                                                 'rank' => 0,
                                 ],
                                 'bug_klog'=>[
                                                 'result'=> $bug_klog,
                                                 'rank' => 0,
                                 ],
                                 'bug_mem' => [
                                                 'result'=> $bug_mem,
                                                 'rank' => 0,
                                 ],
                 ));
             if(!in_array($kloc_mem,$resultKloc))
                 array_push($resultKloc,$kloc_mem);

             if(!in_array($assign_mem,$resultAssign))
                 array_push($resultAssign,$assign_mem);

             if(!in_array($task_mem,$resultTaskMem))
                 array_push($resultTaskMem,$task_mem);

             if(!in_array($bug_klog,$resultBugKloc))
                 array_push($resultBugKloc,$bug_klog);

             if(!in_array($bug_mem,$resultBugMem))
                 array_push($resultBugMem,$bug_mem);
         }
         rsort($resultKloc);
         rsort($resultAssign);
         rsort($resultTaskMem);
         rsort($resultBugKloc);
         rsort($resultBugMem);

         $resultP = [];
         $resultQ = [];
         $resultR = [];
         foreach($resultDevs as $position => $item)
         {
             foreach ($resultKloc as $key=>$value)
             {
                 if($item['kloc_men']['result'] == $value)
                 {
                     $resultDevs[$position]['kloc_men']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultAssign as $key=>$value)
             {
                 if($item['assign_mem']['result'] == $value)
                 {
                     $resultDevs[$position]['assign_mem']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultTaskMem as $key=>$value)
             {
                 if($item['task_mem']['result'] == $value)
                 {
                     $resultDevs[$position]['task_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultDevs[$position]['rankP'] = round (( $resultDevs[$position]['kloc_men']['rank'] + $resultDevs[$position]['assign_mem']['rank'] + $resultDevs[$position]['task_mem']['rank'] ) / 3 , 2);
             foreach ($resultBugKloc as $key=>$value)
             {
                 if($item['bug_klog']['result'] == $value)
                 {
                     $resultDevs[$position]['bug_klog']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugMem as $key=>$value)
             {
                 if($item['bug_mem']['result'] == $value)
                 {
                     $resultDevs[$position]['bug_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultDevs[$position]['rankQ'] = round (( $resultDevs[$position]['bug_klog']['rank'] + $resultDevs[$position]['bug_mem']['rank'] ) / 2,2);

             if(!in_array($resultDevs[$position]['rankP'],$resultP))
                 array_push($resultP,$resultDevs[$position]['rankP']);
                 if(!in_array($resultDevs[$position]['rankQ'],$resultQ))
                     array_push($resultQ,$resultDevs[$position]['rankQ']);
         }
         sort($resultP);
         sort($resultQ);

         foreach($resultDevs as $position => $item)
         {
             foreach ($resultP as $key=>$value)
             {
                 if($item['rankP'] == $value)
                 {
                     $resultDevs[$position]['rankP'] = $key;
                     break;
                 }
             }
             foreach ($resultQ as $key=>$value)
             {
                 if($item['rankQ'] == $value)
                 {
                     $resultDevs[$position]['rankQ'] = $key;
                     break;
                 }
             }
             $resultDevs[$position]['rank'] = round (( $resultDevs[$position]['rankP'] + $resultDevs[$position]['rankQ'] ) / 2,2);
             if(!in_array($resultDevs[$position]['rank'],$resultR))
                 array_push($resultR,$resultDevs[$position]['rank']);
         }
         sort($resultR);

         foreach($resultDevs as $position => $item)
         {
             foreach ($resultR as $key=>$value)
             {
                 if($item['rank'] == $value)
                 {
                     $resultDevs[$position]['rank'] = $key;
                     break;
                 }
             }
         }

         return $resultDevs;
     }
     function rankDm($years,$months)
     {
         $dataProjects = ProjectReport::select('project_id','project_name','department_id','department_name',
                'departments.parent_id as parent_id',
                DB::raw('sum(actual_hour) as actual_hour'),
                DB::raw('sum(loc)  as loc'),
                DB::raw('sum(task)  as task'),
                DB::raw('sum(tested_tc)  as tested_tc'),
                DB::raw('sum(weighted_bug)  as weighted_bug'),
                DB::raw('sum(weighted_uat_bug)  as weighted_uat_bug'))
                ->join('departments','departments.id','=','project_report.department_id')
                ->where('year', $years);
        if (!in_array(0, $months)) {
            $dataProjects= $dataProjects->whereIn('month', $months)->groupBy('project_id');
        } else {
            $dataProjects= $dataProjects->groupBy('project_id');
        }
        $mm = Config::get('constant.men_month'); // Men month value
        $resultProjects = [];
        foreach($dataProjects->get()->toArray() as $data)
        {
            $kloc = Helpers::writeNumber($data['loc'], 1000);
            $workload = Helpers::writeNumber($data['actual_hour'], $mm);
            $kloc_mem = Helpers::writeNumber($kloc, $workload);
            $kTestcase = Helpers::writeNumber($data['tested_tc'], 1000);

            if($kloc_mem == "NA")
                $kloc_mem = 0;
                $testCase_mem = Helpers::writeNumber($data['tested_tc'], $workload);
            if($testCase_mem == "NA")
                $testCase_mem = 0;
                $task_mem = Helpers::writeNumber($data['task'], $workload);
            if($task_mem == "NA")
                $task_mem = 0;
                $bug_lock = Helpers::writeNumber($data['weighted_bug'], $kloc);
            if($bug_lock == "NA")
                $bug_lock = 0;
                $bugUAT = Helpers::writeNumber($data['weighted_uat_bug'], $kloc);
            if($bugUAT == "NA")
                $bugUAT = 0;
                $bugTc = Helpers::writeNumber($data['weighted_bug'], $kTestcase);
            if($bugTc == "NA")
                $bugTc = 0;
                $bugBeforeRelease = Helpers::writeNumberInPercent($data['weighted_bug'], ($data['weighted_bug']+$data['weighted_uat_bug']));
            if($bugBeforeRelease == "NA")
                $bugBeforeRelease = 0;
                $bugMem = Helpers::writeNumber($data['weighted_bug'], $workload);
            if($bugMem == "NA")
                $bugMem = 0;
                array_push($resultProjects,array(
                                'name'=>$data['project_name'],
                                'department_name' => $data['department_name'],
                                'parent_id' => $data['parent_id'],
                                'kloc_men' => $kloc_mem,
                                'test_case_mem'=> $testCase_mem,
                                'task_mem' => $task_mem,
                                'bug_klog'=> $bug_lock,
                                'bug_uat' => $bugUAT,
                                'bug_Tc' => $bugTc,
                                'bug_bf_re' =>$bugBeforeRelease,
                                'bug_mem' =>  $bugMem,
            ));
        }

        $data           = Department::select(
                'departments.*',
                'users.first_name as first_name',
                'users.last_name as last_name',
                'users.id as user_id'
                )
                ->join('users','users.member_code','=','departments.manager_id')
                ->get();
        $department_all = $this->department->getDepDevTeam($data);


        $resultDm = [];
        $resultKloc = [];
        $resultTC = [];
        $resultTaskMem = [];
        $resultBugKloc = [];
        $resultBugUat = [];
        $resultBugTC = [];
        $resultBugRe = [];
        $resultBugMem = [];

        foreach($department_all['divisions'] as $department)
        {
            $kloc_mem = 0;
            $testCase_mem = 0;
            $task_mem = 0;
            $bug_lock = 0;
            $bugUAT= 0;
            $bugTc = 0;
            $bugBeforeRelease = 0;
            $bugMem = 0;
   
            foreach ($resultProjects as $result)
            {
                if($department['id'] == $result['parent_id'])
                {
                    $kloc_mem += $result['kloc_men'];
                    $testCase_mem += $result['test_case_mem'];
                    $task_mem += $result['task_mem'];
                    $bug_lock += $result['bug_klog'];
                    $bugUAT += $result['bug_uat'];
                    $bugTc += $result['bug_Tc'];
                    $bugBeforeRelease += $result['bug_bf_re'];
                    $bugMem += $result['bug_mem'];
                }
            }
            array_push($resultDm,array(
                    'name'=> $department['last_name'] . ' ' .$department['first_name'],
                    'department_name' => $department['name'],
                    'kloc_men' => [
                            'result' => $kloc_mem,
                            'rank' => 0,
                    ],
                    'test_case_mem'=> [
                                    'result' => $testCase_mem,
                                    'rank' => 0,
                    ],
                    'task_mem' =>[
                                    'result' => $task_mem,
                                    'rank' => 0,
                    ],
                    'bug_klog'=>[
                                    'result' => $bug_lock,
                                    'rank' => 0,
                    ],
                    'bug_uat' =>[
                                    'result' => $bugUAT,
                                    'rank' => 0,
                    ],
                    'bug_Tc' =>[
                                    'result' => $bugTc,
                                    'rank' => 0,
                    ],
                    'bug_bf_re' =>[
                                    'result' => $bugBeforeRelease,
                                    'rank' => 0,
                    ],
                    'bug_mem' =>[
                                    'result' => $bugMem,
                                    'rank' => 0,
                    ],
            ));
            if(!in_array($kloc_mem,$resultKloc))
                array_push($resultKloc,$kloc_mem);

            if(!in_array($testCase_mem,$resultTC))
                array_push($resultTC,$testCase_mem);

            if(!in_array($task_mem,$resultTaskMem))
                array_push($resultTaskMem,$task_mem);

            if(!in_array($bug_lock,$resultBugKloc))
                array_push($resultBugKloc,$bug_lock);

            if(!in_array($bugUAT,$resultBugUat))
                array_push($resultBugUat,$bugUAT);

            if(!in_array($bugTc,$resultBugTC))
                array_push($resultBugTC,$bugTc);

            if(!in_array($bugBeforeRelease,$resultBugRe))
                array_push($resultBugRe,$bugBeforeRelease);

            if(!in_array($bugMem,$resultBugMem))
                array_push($resultBugMem,$bugMem);
        }
        rsort($resultKloc);
        rsort($resultTC);
        rsort($resultTaskMem);
        sort($resultBugKloc);
        sort($resultBugUat);
        sort($resultBugTC);
        rsort($resultBugRe);
        rsort($resultBugMem);

        $resultP = [];
        $resultQ = [];
        $resultR = [];

        foreach($resultDm as $position => $item)
        {
            foreach ($resultKloc as $key=>$value)
            {
                if($item['kloc_men']['result'] == $value)
                {
                    $resultDm[$position]['kloc_men']['rank'] = $key;
                    break;
                }
            }
            foreach ($resultTC as $key=>$value)
            {
                if($item['test_case_mem']['result'] == $value)
                {
                    $resultDm[$position]['test_case_mem']['rank'] = $key;
                    break;
                }
            }
            foreach ($resultTaskMem as $key=>$value)
            {
                if($item['task_mem']['result'] == $value)
                {
                    $resultDm[$position]['task_mem']['rank'] = $key;
                    break;
                }
            }
            $resultDm[$position]['rankP'] = round (( $resultDm[$position]['kloc_men']['rank'] + $resultDm[$position]['test_case_mem']['rank'] + $resultDm[$position]['task_mem']['rank'] ) / 3 , 2);
            foreach ($resultBugKloc as $key=>$value)
            {
                if($item['bug_klog']['result'] == $value)
                {
                    $resultDm[$position]['bug_klog']['rank'] = $key;
                    break;
                }
            }
            foreach ($resultBugUat as $key=>$value)
            {
                if($item['bug_uat']['result'] == $value)
                {
                    $resultDm[$position]['bug_uat']['rank'] = $key;
                    break;
                }
            }
            foreach ($resultBugTC as $key=>$value)
            {
                if($item['bug_Tc']['result'] == $value)
                {
                    $resultDm[$position]['bug_Tc']['rank'] = $key;
                    break;
                }
            }
            foreach ($resultBugRe as $key=>$value)
            {
                if($item['bug_bf_re']['result'] == $value)
                {
                    $resultDm[$position]['bug_bf_re']['rank'] = $key;
                    break;
                }
            }
            foreach ($resultBugMem as $key=>$value)
            {
                if($item['bug_mem']['result'] == $value)
                {
                    $resultDm[$position]['bug_mem']['rank'] = $key;
                    break;
                }
            }
            $resultDm[$position]['rankQ'] = round (( $resultDm[$position]['bug_klog']['rank'] + $resultDm[$position]['bug_uat']['rank']
                    + $resultDm[$position]['bug_Tc']['rank'] + $resultDm[$position]['bug_bf_re']['rank']
                    + $resultDm[$position]['bug_mem']['rank']
                    ) / 5,2);

            if(!in_array($resultDm[$position]['rankP'],$resultP))
                array_push($resultP,$resultDm[$position]['rankP']);
                if(!in_array($resultDm[$position]['rankQ'],$resultQ))
                    array_push($resultQ,$resultDm[$position]['rankQ']);
        }
        sort($resultP);
        sort($resultQ);

        foreach($resultDm as $position => $item)
        {
            foreach ($resultP as $key=>$value)
            {
                if($item['rankP'] == $value)
                {
                    $resultDm[$position]['rankP'] = $key;
                    break;
                }
            }
            foreach ($resultQ as $key=>$value)
            {
                if($item['rankQ'] == $value)
                {
                    $resultDm[$position]['rankQ'] = $key;
                    break;
                }
            }
            $resultDm[$position]['rank'] = round (( $resultDm[$position]['rankP'] + $resultDm[$position]['rankQ'] ) / 2,2);
            if(!in_array($resultDm[$position]['rank'],$resultR))
                array_push($resultR,$resultDm[$position]['rank']);
        }

        sort($resultR);

        foreach($resultDm as $position => $item)
        {
            foreach ($resultR as $key=>$value)
            {
                if($item['rank'] == $value)
                {
                    $resultDm[$position]['rank'] = $key;
                    break;
                }
            }
        }

        return $resultDm;

     }
     function rankDevBrse($dataDevs)
     {
         $resultDevs = [];
         $resultKloc = [];
         $resultAssign = [];
         $resultTaskMem = [];
         $resultBugKloc = [];
         $resultBugMem = [];
         $mm = Config::get('constant.men_month'); // Men month value
         foreach($dataDevs as $data)
         {
             $kloc_mem = Helpers::writeNumber(floatval($data['kloc']), $data['workload'] / $mm) ;
             if($kloc_mem == "NA")
                 $kloc_mem = 0;
                 $assign_mem = Helpers::writeNumber(floatval($data['bug_weighted']), $data['workload']/$mm);
             if($assign_mem == "NA")
                 $assign_mem = 0;
                 $task_mem = Helpers::writeNumber(floatval($data['task']), $data['workload']/$mm);
             if($task_mem == "NA")
                 $task_mem = 0;
                 $bug_klog = Helpers::writeNumber(floatval($data['madebug_weighted']), $data['kloc']) ;
             if($bug_klog == "NA")
                 $bug_klog = 0;
                 $bug_mem = Helpers::writeNumber(floatval($data['madebug_weighted']), $data['workload']/$mm) ;
             if($bug_mem == "NA")
                 $bug_mem = 0;
                 array_push($resultDevs,array(
                                 'user_id' => $data['user_id'],
                                 'kloc_men' => [
                                                 'result'=> $kloc_mem,
                                                 'rank' => 0,
                                 ],
                                 'assign_mem'=>[
                                                 'result'=> $assign_mem,
                                                 'rank' => 0,
                                 ],
                                 'task_mem' =>[
                                                 'result'=> $task_mem,
                                                 'rank' => 0,
                                 ],
                                 'bug_klog'=>[
                                                 'result'=> $bug_klog,
                                                 'rank' => 0,
                                 ],
                                 'bug_mem' => [
                                                 'result'=> $bug_mem,
                                                 'rank' => 0,
                                 ],
                 ));
             if(!in_array($kloc_mem,$resultKloc))
                 array_push($resultKloc,$kloc_mem);

             if(!in_array($assign_mem,$resultAssign))
                 array_push($resultAssign,$assign_mem);

             if(!in_array($task_mem,$resultTaskMem))
                 array_push($resultTaskMem,$task_mem);

             if(!in_array($bug_klog,$resultBugKloc))
                 array_push($resultBugKloc,$bug_klog);

             if(!in_array($bug_mem,$resultBugMem))
                 array_push($resultBugMem,$bug_mem);
         }
         rsort($resultKloc);
         rsort($resultAssign);
         rsort($resultTaskMem);
         sort($resultBugKloc);
         sort($resultBugMem);

         $resultP = [];
         $resultQ = [];
         $resultR = [];
         foreach($resultDevs as $position => $item)
         {
             foreach ($resultKloc as $key=>$value)
             {
                 if($item['kloc_men']['result'] == $value)
                 {
                     $resultDevs[$position]['kloc_men']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultAssign as $key=>$value)
             {
                 if($item['assign_mem']['result'] == $value)
                 {
                     $resultDevs[$position]['assign_mem']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultTaskMem as $key=>$value)
             {
                 if($item['task_mem']['result'] == $value)
                 {
                     $resultDevs[$position]['task_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultDevs[$position]['rankP'] = round (( $resultDevs[$position]['kloc_men']['rank'] + $resultDevs[$position]['assign_mem']['rank'] + $resultDevs[$position]['task_mem']['rank'] ) / 3 , 2);
             foreach ($resultBugKloc as $key=>$value)
             {
                 if($item['bug_klog']['result'] == $value)
                 {
                     $resultDevs[$position]['bug_klog']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugMem as $key=>$value)
             {
                 if($item['bug_mem']['result'] == $value)
                 {
                     $resultDevs[$position]['bug_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultDevs[$position]['rankQ'] = round (( $resultDevs[$position]['bug_klog']['rank'] + $resultDevs[$position]['bug_mem']['rank'] ) / 2,2);

             if(!in_array($resultDevs[$position]['rankP'],$resultP))
                 array_push($resultP,$resultDevs[$position]['rankP']);
                 if(!in_array($resultDevs[$position]['rankQ'],$resultQ))
                     array_push($resultQ,$resultDevs[$position]['rankQ']);
         }
         sort($resultP);
         sort($resultQ);

         foreach($resultDevs as $position => $item)
         {
             foreach ($resultP as $key=>$value)
             {
                 if($item['rankP'] == $value)
                 {
                     $resultDevs[$position]['rankP'] = $key;
                     break;
                 }
             }
             foreach ($resultQ as $key=>$value)
             {
                 if($item['rankQ'] == $value)
                 {
                     $resultDevs[$position]['rankQ'] = $key;
                     break;
                 }
             }
             $resultDevs[$position]['rank'] = round (( $resultDevs[$position]['rankP'] + $resultDevs[$position]['rankQ'] ) / 2,2);
             if(!in_array($resultDevs[$position]['rank'],$resultR))
                 array_push($resultR,$resultDevs[$position]['rank']);
         }
         sort($resultR);

         foreach($resultDevs as $position => $item)
         {
             foreach ($resultR as $key=>$value)
             {
                 if($item['rank'] == $value)
                 {
                     $resultDevs[$position]['rank'] = $key;
                     break;
                 }
             }
         }
         return $resultDevs;
     }
     function rankProjectBrse($dataProjects)
     {
         $resultProjects = [];
         $resultKloc = [];
         $resultTC = [];
         $resultTaskMem = [];
         $resultBugKloc = [];
         $resultBugUat = [];
         $resultBugTC = [];
         $resultBugRe = [];
         $resultBugMem = [];
         $mm = Config::get('constant.men_month'); // Men month value
         foreach($dataProjects as $data)
         {
             $kloc = Helpers::writeNumber($data['loc'], 1000);
             $workload = Helpers::writeNumber($data['actual_hour'], $mm);
             $kloc_mem = Helpers::writeNumber($kloc, $workload);
             $kTestcase = Helpers::writeNumber($data['tested_tc'], 1000);

             if($kloc_mem == "NA")
                 $kloc_mem = 0;
                 $testCase_mem = Helpers::writeNumber($data['tested_tc'], $workload);
             if($testCase_mem == "NA")
                 $testCase_mem = 0;
                 $task_mem = Helpers::writeNumber($data['task'], $workload);
             if($task_mem == "NA")
                 $task_mem = 0;
                 $bug_lock = Helpers::writeNumber($data['weighted_bug'], $kloc);
             if($bug_lock == "NA")
                 $bug_lock = 0;
                 $bugUAT = Helpers::writeNumber($data['weighted_uat_bug'], $kloc);
             if($bugUAT == "NA")
                 $bugUAT = 0;
                 $bugTc = Helpers::writeNumber($data['weighted_bug'], $kTestcase);
             if($bugTc == "NA")
                 $bugTc = 0;
                 $bugBeforeRelease = Helpers::writeNumberInPercent($data['weighted_bug'], ($data['weighted_bug']+$data['weighted_uat_bug']));
             if($bugBeforeRelease == "NA")
                 $bugBeforeRelease = 0;
                 $bugMem = Helpers::writeNumber($data['weighted_bug'], $workload);
             if($bugMem == "NA")
                 $bugMem = 0;
                 array_push($resultProjects,array(
                         'user_id' => $data['user_id'],
                         'name' => $data['first_name'] .' ' .$data['last_name'],
                         'deparments_name' => $data['deparments_name'],
                         'kloc_men' => [
                                         'result'=> $kloc_mem,
                                         'rank' => 0,
                         ],
                         'test_case_mem'=>[
                                         'result'=> $testCase_mem,
                                         'rank' => 0,
                         ],
                         'task_mem' =>[
                                         'result'=> $task_mem,
                                         'rank' => 0,
                         ],
                         'bug_klog'=>[
                                         'result'=> $bug_lock,
                                         'rank' => 0,
                         ],
                         'bug_uat' => [
                                         'result'=> $bugUAT,
                                         'rank' => 0,
                         ],
                         'bug_Tc' => [
                                         'result'=> $bugTc,
                                         'rank' => 0,
                         ],
                         'bug_bf_re' => [
                                         'result'=> $bugBeforeRelease,
                                         'rank' => 0,
                         ],
                         'bug_mem' => [
                                         'result'=> $bugMem,
                                         'rank' => 0,
                         ],
                 ));
                 if(!in_array($kloc_mem,$resultKloc))
                     array_push($resultKloc,$kloc_mem);

                 if(!in_array($testCase_mem,$resultTC))
                     array_push($resultTC,$testCase_mem);

                 if(!in_array($task_mem,$resultTaskMem))
                     array_push($resultTaskMem,$task_mem);

                 if(!in_array($bug_lock,$resultBugKloc))
                     array_push($resultBugKloc,$bug_lock);

                 if(!in_array($bugUAT,$resultBugUat))
                     array_push($resultBugUat,$bugUAT);

                 if(!in_array($bugTc,$resultBugTC))
                     array_push($resultBugTC,$bugTc);

                 if(!in_array($bugBeforeRelease,$resultBugRe))
                     array_push($resultBugRe,$bugBeforeRelease);

                 if(!in_array($bugMem,$resultBugMem))
                     array_push($resultBugMem,$bugMem);
         }
         rsort($resultKloc);
         rsort($resultTC);
         rsort($resultTaskMem);
         sort($resultBugKloc);
         sort($resultBugUat);
         sort($resultBugTC);
         rsort($resultBugRe);
         rsort($resultBugMem);

         $resultP = [];
         $resultQ = [];
         $resultR = [];

         foreach($resultProjects as $position => $item)
         {
             foreach ($resultKloc as $key=>$value)
             {
                 if($item['kloc_men']['result'] == $value)
                 {
                     $resultProjects[$position]['kloc_men']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultTC as $key=>$value)
             {
                 if($item['test_case_mem']['result'] == $value)
                 {
                     $resultProjects[$position]['test_case_mem']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultTaskMem as $key=>$value)
             {
                 if($item['task_mem']['result'] == $value)
                 {
                     $resultProjects[$position]['task_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultProjects[$position]['rankP'] = round (( $resultProjects[$position]['kloc_men']['rank'] + $resultProjects[$position]['test_case_mem']['rank'] + $resultProjects[$position]['task_mem']['rank'] ) / 3 , 2);
             foreach ($resultBugKloc as $key=>$value)
             {
                 if($item['bug_klog']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_klog']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugUat as $key=>$value)
             {
                 if($item['bug_uat']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_uat']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugTC as $key=>$value)
             {
                 if($item['bug_Tc']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_Tc']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugRe as $key=>$value)
             {
                 if($item['bug_bf_re']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_bf_re']['rank'] = $key;
                     break;
                 }
             }
             foreach ($resultBugMem as $key=>$value)
             {
                 if($item['bug_mem']['result'] == $value)
                 {
                     $resultProjects[$position]['bug_mem']['rank'] = $key;
                     break;
                 }
             }
             $resultProjects[$position]['rankQ'] = round (( $resultProjects[$position]['bug_klog']['rank'] + $resultProjects[$position]['bug_uat']['rank']
                     + $resultProjects[$position]['bug_Tc']['rank'] + $resultProjects[$position]['bug_bf_re']['rank']
                     + $resultProjects[$position]['bug_mem']['rank']
                     ) / 5,2);

             if(!in_array($resultProjects[$position]['rankP'],$resultP))
                 array_push($resultP,$resultProjects[$position]['rankP']);
                 if(!in_array($resultProjects[$position]['rankQ'],$resultQ))
                     array_push($resultQ,$resultProjects[$position]['rankQ']);
         }
         sort($resultP);
         sort($resultQ);

         foreach($resultProjects as $position => $item)
         {
             foreach ($resultP as $key=>$value)
             {
                 if($item['rankP'] == $value)
                 {
                     $resultProjects[$position]['rankP'] = $key;
                     break;
                 }
             }
             foreach ($resultQ as $key=>$value)
             {
                 if($item['rankQ'] == $value)
                 {
                     $resultProjects[$position]['rankQ'] = $key;
                     break;
                 }
             }
             $resultProjects[$position]['rank'] = round (( $resultProjects[$position]['rankP'] + $resultProjects[$position]['rankQ'] ) / 2,2);
             if(!in_array($resultProjects[$position]['rank'],$resultR))
                 array_push($resultR,$resultProjects[$position]['rank']);
         }

         sort($resultR);

         foreach($resultProjects as $position => $item)
         {
             foreach ($resultR as $key=>$value)
             {
                 if($item['rank'] == $value)
                 {
                     $resultProjects[$position]['rank'] = $key;
                     break;
                 }
             }
         }

         return $resultProjects;
     }
 }
