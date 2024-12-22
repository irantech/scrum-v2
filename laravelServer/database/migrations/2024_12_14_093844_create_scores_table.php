<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_contract_id');
            $table->foreign('checklist_contract_id')->references('id')->on('checklist_contract')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('user_negative')->default(0);
            $table->integer('user_positive')->default(0);
            $table->integer('manager_negative')->default(0);
            $table->integer('manager_positive')->default(0);
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
        Schema::dropIfExists('scores');
    }
}
