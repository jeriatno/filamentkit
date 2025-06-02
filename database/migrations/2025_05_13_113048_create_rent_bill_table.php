<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_bill', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('partner_id')->nullable()->index();
            $table->unsignedBigInteger('rent_in_id')->nullable()->index();
            $table->string('pn', 70)->nullable();
            $table->integer('qty')->nullable();
            $table->date('bill_period')->nullable();
            $table->date('acc_check_date')->nullable();
            $table->unsignedBigInteger('acc_check_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('acc_check_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_bill');
    }
}
