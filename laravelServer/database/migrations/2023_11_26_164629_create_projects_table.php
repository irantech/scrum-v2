<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('checklist_contract_id')->nullable();
            $table->foreign('checklist_contract_id')->references('id')->on('checklist_contract')->onDelete('cascade');

            $table->enum('status', ['complete', 'running', 'hold', 'cancel'])->default('hold');

            $table->text('description')->nullable();
            $table->string('site-link')->nullable() ;
            $table->string('theme-link')->nullable() ;

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
        Schema::dropIfExists('projects');
    }
}
