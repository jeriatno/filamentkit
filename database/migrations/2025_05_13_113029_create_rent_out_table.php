<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_out', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('doc_no', 12);
            $table->unsignedBigInteger('rent_in_det_id')->nullable()->index();
            $table->dateTime('date_out')->nullable();
            $table->char('out_type', 1)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rent_in_det_id')->references('id')->on('rent_in_det');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_ouy');
    }
}
