<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('base_progress_id')->unsigned();
			$table->integer('section_id')->unsigned();
            $table->string('estimated_time');
            $table->enum('status', ['complete', 'running', 'hold', 'cancel'])->default('hold');
            $table->string('refer_to')->nullable();
            $table->text('description')->nullable();
			$table->date('start_date')->nullable();
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
        Schema::dropIfExists('sub_progress');
    }
}
