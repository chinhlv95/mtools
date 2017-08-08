<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 *
 * Mar 12, 2017 3:25:29 PM
 * @author Tampt6722
 *
 */
class QualityAndProductivityReport extends Command {

    protected function getDatetime($n, $str) {
        $startDate = '';
        $endDate = '';
        switch ($n)
        {
            case '1':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' January'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' January'));
                break;
            case '2':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' February'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' February'));
                break;
            case '3':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' March'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' March'));
                break;
            case '4':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' April'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' April'));
                break;
            case '5':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' May'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' May'));
                break;
            case '6':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' June'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' June'));
                break;
            case '7':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' July'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' July'));
                break;
            case '8':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' August'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' August'));
                break;
            case '9':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' September'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' September'));
                break;
            case '10':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' October'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' October'));
                break;
            case '11':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' November'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' November'));
                break;
            case '12':
                $startDate = date('Y-m-d 00:00:00', strtotime('first day of '. $str. ' December'));
                $endDate   = date('Y-m-d 23:59:59', strtotime('last day of '. $str. ' December'));
                break;
        }

        return $time = ['start_date' => $startDate, 'end_date' => $endDate];
}


}