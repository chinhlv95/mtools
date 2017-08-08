<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Nov 5, 20163:27:25 PM
 * @author tampt6722
 *
 */
class AddKeyColumnToBugsWeightTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bugs_weight', function (Blueprint $table) {
            $table->integer('key')->after('id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bugs_weight', function (Blueprint $table) {
            $table->dropColumn('key');
        });
    }
}
