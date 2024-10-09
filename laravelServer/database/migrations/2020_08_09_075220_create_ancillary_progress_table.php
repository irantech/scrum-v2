<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAncillaryProgressTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'ancillary_progress', function ( Blueprint $table ) {
            $table->id();
            $table->integer( 'ancillary_id' );
            $table->integer( 'base_progress_id' );
            $table->enum('status', ['complete', 'running', 'hold', 'cancel'])->default('hold');
            $table->softDeletes();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'ancillary_progress' );
    }
}
