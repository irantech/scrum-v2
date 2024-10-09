<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToContractProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_progress', function (Blueprint $table) {
            $table->enum('status', ['complete', 'running', 'hold', 'cancel'])->after('base_progress_id')->default('hold');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_progress', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
