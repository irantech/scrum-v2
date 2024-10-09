<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToDosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo_lists', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('title') ;

            $table->text('description')->nullable();

            $table->enum('status' , ['started' , 'in_progress' , 'done' , 'delay']);

            $table->string('todoable_type');

            $table->integer('todoable_id');

            $table->timestamp('starting_time')->nullable();

            $table->timestamp('ending_time')->nullable();

            $table->timestamp('offering_time')->nullable();

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
        Schema::dropIfExists('to_dos');
    }
}
