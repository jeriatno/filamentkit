<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentOutDetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_out_det', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rent_out_id')->nullable()->index();
            $table->unsignedBigInteger('rent_out_det_id')->nullable()->index();
            $table->integer('qty')->nullable();
            $table->integer('qty_before')->nullable();
            $table->integer('qty_after')->nullable();
            $table->string('pn', 70)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rent_out_id')->references('id')->on('rent_out');
            $table->foreign('rent_out_det_id')->references('id')->on('rent_out_det');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_out_det');
    }
}
