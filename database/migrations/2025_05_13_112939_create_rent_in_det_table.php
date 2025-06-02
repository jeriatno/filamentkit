<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentInDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_in_det', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rent_in_id')->nullable()->index();
            $table->string('pn', 70)->nullable();
            $table->string('sn', 70)->nullable();
            $table->double('vol_std', 16,2)->nullable();
            $table->integer('rent_qty')->nullable();
            $table->integer('avail_qty')->nullable();
            $table->double('vol_ccm', 16,2)->nullable();
            $table->double('ccm_per_qty', 16,2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rent_in_id')->references('id')->on('rent_in');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_in__det');
    }
}
