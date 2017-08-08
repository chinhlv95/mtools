<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProcedureGetDataEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS total_entries');
        DB::unprepared('CREATE PROCEDURE total_entries(IN start_date DATETIME, IN end_date DATETIME)
            BEGIN
                SELECT projects.id AS all_project_id,
                       projects.name AS project_name,
                       SUM(entries.actual_hour) AS actual_hour
                FROM projects
                LEFT JOIN tickets ON projects.id = tickets.project_id
                LEFT JOIN entries ON tickets.id = entries.ticket_id
                AND entries.spent_at BETWEEN start_date AND end_date
                GROUP BY all_project_id;
            END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS total_entries');
    }
}
