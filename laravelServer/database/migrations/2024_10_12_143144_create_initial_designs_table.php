<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInitialDesignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('initial_designs', function (Blueprint $table) {
            $table->id();
            $table->string('main_color');
            $table->string('second_color');
            $table->string('logo')->nullable();
            $table->json('file');
            $table->text('description');
            $table->foreignId('checklist_contract_id');
            $table->foreign('checklist_contract_id')->references('id')->on('checklist_contract')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('initial_designs');
    }
}
