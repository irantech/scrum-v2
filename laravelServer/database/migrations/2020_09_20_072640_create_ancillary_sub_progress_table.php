<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAncillarySubProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ancillary_sub_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('ancillary_id');
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
        Schema::dropIfExists('ancillary_sub_progress');
    }
}
