<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistReverseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_reverse', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('status');
            $table->string('status_code');
            $table->unsignedBigInteger('checklist_process_id');
            $table->foreign('checklist_process_id')->references('id')->on('checklist_processes');
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
        Schema::dropIfExists('checklist_reverse');
    }
}
