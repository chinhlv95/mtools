<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameTypeFileUploadTableToField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_upload', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->integer('size')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_upload', function (Blueprint $table) {
            $table->text('name');
            $table->dropColumn('size');
        });
    }
}
