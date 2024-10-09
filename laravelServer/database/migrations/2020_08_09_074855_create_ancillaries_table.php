<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAncillariesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'ancillaries', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'contract_code' )->nullable();
            $table->string( 'contract_id' )->nullable();
            $table->string( 'title' );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'ancillaries' );
    }
}
