<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('section_id');
            $table->enum('status', ['complete', 'running', 'hold', 'cancel'])->default('hold');
            $table->enum('user_role', ['admin', 'office', 'support', 'accountant','programmer','graphic'])->default('admin');
            $table->text('description');
            $table->text('private_description');
			$table->integer('percentage');
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
        Schema::dropIfExists('base_progress');
    }
}
