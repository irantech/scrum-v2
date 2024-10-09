<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConractChecklistToTitleChecklistUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('title_checklist_user', function (Blueprint $table) {
            $table->unsignedBigInteger('checklist_contarct_id')->nullable();
            $table->foreign('checklist_contarct_id')->references('id')->on('checklist_contract')->onDelete('cascade');
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
            $table->dropForeign('checklist_contarct_id');
        });
    }
}
