<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTicketNumberToNullInChecklistProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checklist_processes', function (Blueprint $table) {
            $table->string('ticket_number')->after('checklist_contract_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('null_in_checklist_process', function (Blueprint $table) {
            $table->string('ticket_number')->after('checklist_contract_id');
        });
    }
}
