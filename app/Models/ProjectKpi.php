<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectKpi extends Model
{
    protected $table = 'project_kpi';
    protected $fillable = [
                    'id',
                    'name',
                    'project_id',
                    'baseline_flag',
                    'start_date',
                    'end_date',
                    'actual_cost_efficiency',
                    'plan_cost_efficiency',
                    'actual_fix_code',
                    'plan_fix_code',
                    'actual_leakage',
                    'plan_leakage',
                    'actual_customer_survey',
                    'plan_customer_survey',
                    'actual_bug_after_release_number',
                    'plan_bug_after_release_number',
                    'actual_bug_after_release_weight',
                    'plan_bug_after_release_weight',
                    'actual_defect_remove_efficiency',
                    'plan_defect_remove_efficiency',
                    'actual_defect_rate',
                    'plan_defect_rate',
                    'actual_code_productivity',
                    'plan_code_productivity',
                    'actual_test_case_productivity',
                    'plan_test_case_productivity',
                    'actual_tested_productivity',
                    'plan_tested_productivity',
                    'description',
                    'created_at',
                    'updated_at'
    ];
}
