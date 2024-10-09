<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameChecklistContractIdTitleChecklistUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('title_checklist_user', function (Blueprint $table) {
            $table->renameColumn('checklist_contarct_id' , 'checklist_contract_id');
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
            $table->renameColumn('checklist_contract_id' , 'checklist_contarct_id');
        });
    }
}
