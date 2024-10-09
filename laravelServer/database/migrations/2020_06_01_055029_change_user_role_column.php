<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserRoleColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
//            $table->enum('user_role',['admin','customer','staff'])->default('customer')->change();
        $table->enum('role',['admin','programmer','graphic','accountant','support','office','customer'])->default('customer')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
//            $table->integer('user_role')->nullable()->change();
            $table->dropIfExists('role');
        });

    }
}
