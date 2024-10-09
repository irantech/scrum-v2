<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->char('email',191)->nullable()->unique();
            $table->string('name');
            $table->string('old_user')->nullable();
            $table->string('old_pass')->nullable();
            $table->integer('old_id_customer')->nullable()->unique()->index();
            $table->integer('old_number')->nullable()->unique()->index();
            $table->integer('old_whmcs_id')->nullable();
            $table->string('old_whmcs_hash')->nullable();

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
        Schema::dropIfExists('customers');
    }
}
