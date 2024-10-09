<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStatusToTitleSubProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_progress', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('estimated_time');
            $table->dropColumn('refer_to');
            $table->dropColumn('start_date');
            $table->string('title')->after('section_id')->nullable(false)->default('');
//            $table->string('status')->after('id')->nullable()->change();
//            $table->renameColumn('status', 'title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_progress', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->string('estimated_time');
            $table->string('refer_to')->nullable();
            $table->date('start_date')->nullable();
            $table->enum('status', ['complete', 'running', 'hold', 'cancel'])->default('hold')->after('estimated_time');
        });
    }
}
