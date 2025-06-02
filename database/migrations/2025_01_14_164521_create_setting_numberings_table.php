<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingNumberingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_numberings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('module', 100)->nullable();
            $table->string('for', 100)->nullable();
            $table->string('format', 150)->nullable();
            $table->string('prefix', 10)->nullable();
            $table->string('clause', 100)->nullable();
            $table->integer('sequence')->default(1);
            $table->text('example')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_numberings');
    }
}
