<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

/**
 * Delay execution
 * Feb 20, 2017 11:49:45 AM
 * @author tampt6722
 *
 */
class IntervalAdjuster extends Command
{
   public $lapTimes = null;
   public function __construct()
   {
       $this->lapTimes = [];
   }

   public function lap() {
       array_push($this->lapTimes, Carbon::now());
   }

   /**
    * Delay execution
    * @author tampt6722
    *
    * @param number $second
    */
   public function adjust($second) {
      $now = Carbon::now();
      $lastTime = array_pop($this->lapTimes);
       if ($lastTime) {
           $diff = $now->diffInSeconds($lastTime);
           if ($diff < $second) {
               sleep($second - $diff);
           }
       }
   }
}
