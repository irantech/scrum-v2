<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionToTitleChecklistUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('title_checklist_user', function (Blueprint $table) {
            $table->unsignedBigInteger('section_id')->after('checklist_id')->nullable();
            $table->foreign('section_id')->references('id')->on('sections');
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
            $table->dropForeign('section_id');
        });
    }
}
