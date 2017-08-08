<?php

namespace App\Console\Commands;

use DB;
use Exception;
use Illuminate\Console\Command;
use App\Repositories\MemberReport\MemberReportRepositoryInterface;

/**
 *
 * Mar 12, 2017 3:30:04 PM
 * @author TamPT6722
 *
 */
class GetDataMemberForQP extends QualityAndProductivityReport
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member_report_data:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save member data for Q&P report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MemberReportRepositoryInterface $report)
    {
        $this->report = $report;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $this->report->deleteAllData();
            for ($flag = 1; $flag <= 2; $flag++) {
                // When $flag = 1, get data last year
                if ($flag == 1) {
                    $year = date('Y', strtotime('last year'));
                    for ($i = 1; $i <= 12; $i++) {
                        $time = $this->getDatetime($i, 'last year');
                        $this->report->saveDataMember($time['start_date'], $time['end_date'], 'last_year', $i);
                    }

                } elseif ($flag == 2) {
                    // flag = 2 get data this year
                    $year = date('Y', strtotime('this year'));
                    for ($i = 1; $i <= 12; $i++) {
                        $time = $this->getDatetime($i, 'this year');
                        $this->report->saveDataMember($time['start_date'], $time['end_date'], 'this_year', $i);
                    }
                }
            }

        } catch(Exception $e) {
            DB::rollBack();
            $this->line('Error!');
            print_r($e->getMessage());
        }
        DB::commit();
    }
}