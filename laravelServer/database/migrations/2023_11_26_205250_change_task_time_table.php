<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTaskTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_times', function (Blueprint $table) {

            $table->string('tasktimeable_type')->after('section_id');

            $table->integer('tasktimeable_id')->after('tasktimeable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_times', function (Blueprint $table) {

            $table->dropIfExists('tasktimeable_type');

            $table->dropIfExists('tasktimeable_id');
        });
    }
}
