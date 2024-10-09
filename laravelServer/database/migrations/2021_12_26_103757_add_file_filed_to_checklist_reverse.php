<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileFiledToChecklistReverse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checklist_reverse', function (Blueprint $table) {
            $table->json('file_list')->after('status')->nullable();
            $table->dropColumn('status_code');
            $table->unsignedBigInteger('parent_id')->nullable()->after('file_list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checklist_reverse', function (Blueprint $table) {
            $table->json('file_list');
            $table->string('status_code');
        });
    }
}
