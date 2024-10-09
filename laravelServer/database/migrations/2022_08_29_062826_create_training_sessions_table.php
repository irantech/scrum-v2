<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('checklist_contract_id')->nullable();
            $table->foreign('checklist_contract_id')->references('id')->on('checklist_contract')->onDelete('cascade');

            $table->date('session_date');

            $table->time('session_time');

            $table->tinyInteger('location_status');

            $table->tinyInteger('location_place')->nullable();

            $table->string('address')->nullable();

            $table->enum('status' , ['set_time' , 'done' , 'cancel']);

            $table->time('duration' , 0)->nullable();

            $table->softDeletes();

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
        Schema::dropIfExists('training_sessions');
    }
}
