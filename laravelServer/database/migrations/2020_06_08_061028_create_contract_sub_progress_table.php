<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractSubProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_sub_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('contract_id');
            $table->integer('sub_progress_id');
            $table->enum('status', ['complete', 'running', 'hold', 'cancel'])->default('hold');
            $table->string('estimated_time')->nullable();
            $table->string('refer_to')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::dropIfExists('contract_sub_progress');
    }
}
