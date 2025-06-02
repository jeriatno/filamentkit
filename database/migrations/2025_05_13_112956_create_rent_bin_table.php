<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentBinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_bin', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->unsignedBigInteger('warehouse_bin_id')->nullable()->index();
            $table->unsignedBigInteger('rent_in_det_id')->nullable()->index();
            $table->integer('qty')->nullable();
            $table->integer('move_seq')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('warehouse_bin_id')->references('id')->on('warehouse_bin');
            $table->foreign('rent_in_det_id')->references('id')->on('rent_in');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_bin');
    }
}
