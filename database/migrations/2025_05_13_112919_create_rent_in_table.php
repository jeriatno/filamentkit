<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_in', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('partner_id')->nullable()->index();
            $table->string('doc_no', 12)->index();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->dateTime('est_date_in')->nullable();
            $table->string('dn_number', 30)->index();
            $table->double('handling_fee', 16,2)->nullable();
            $table->dateTime('doc_date')->nullable();
            $table->text('address')->nullable();
            $table->char('doc_type', 1)->nullable();
            $table->char('partner_type', 3)->nullable();
            $table->double('est_value', 16,2)->nullable();
            $table->unsignedBigInteger('rate_id')->nullable()->index();
            $table->dateTime('act_date_in')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('rate_id')->references('id')->on('rent_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_in');
    }
}
