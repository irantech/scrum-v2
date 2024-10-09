<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTitleChecklistUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('title_checklist_user', function (Blueprint $table) {
            $table->unsignedBigInteger('checklist_id')->nullable()->after('contract_id');
            $table->foreign('checklist_id')->references('id')->on('checklists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('title_checklist_user', function (Blueprint $table) {
            $table->dropForeign('checklist_id');
        });
    }
}
