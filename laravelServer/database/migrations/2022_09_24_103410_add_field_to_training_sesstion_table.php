<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToTrainingSesstionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->string('cancel_reason')->nullable();
            $table->json('contributors')->nullable();
            DB::statement("ALTER TABLE training_sessions MODIFY status ENUM('set_time' , 'done' , 'cancel' , 'new_session')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropColumn('cancel_reason');
            $table->dropColumn('contributors');
            DB::statement("ALTER TABLE training_sessions MODIFY status ENUM('set_time' , 'done' , 'cancel')");
        });
    }
}
