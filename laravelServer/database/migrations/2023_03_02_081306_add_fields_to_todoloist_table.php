<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTodoloistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('todo_lists', function (Blueprint $table) {
            $table->renameColumn('offering_time', 'difference_time');
            $table->enum('todo_status' , ['best' , 'on-time' , 'bad' , 'worst'])->after('offering_time')->nullable() ;

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('todo_lists', function (Blueprint $table) {
            $table->renameColumn('difference_time', 'offering_time');
            $table->dropColumn('todo_status');
        });
    }
}
