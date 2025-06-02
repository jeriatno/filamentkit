<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCityIdInRentInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rent_in', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->after('warehouse_id')->nullable()->index();
            $table->string('city_name', 100)->after('city_id')->nullable();
            $table->date('est_return_date_in')->after('est_date_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rent_in', function (Blueprint $table) {
            $table->removeColumn('city_id');
            $table->removeColumn('city_name');
            $table->removeColumn('est_return_date_in');
        });
    }
}
