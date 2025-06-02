<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 10);
            $table->string('name', 100)->default('');
            $table->string('csn', 10)->default('5107161813')->nullable();
            $table->string('autodesk_sold_to_csn', 10)->default('5500838202')->nullable();
            $table->string('npwp', 20)->nullable();
            $table->string('attn', 191)->nullable();
            $table->integer('sales_office_id')->nullable();
            $table->string('sales_office_code', 60)->nullable();
            $table->integer('manager_user_id')->nullable();
            $table->string('manager_name', 60)->nullable();
            $table->integer('division_manager_user_id')->nullable();
            $table->string('division_manager_name', 60)->nullable();
            $table->string('address', 300)->nullable();
            $table->string('sales_group', 10)->nullable();
            $table->string('dv', 10)->nullable();
            $table->string('dhcl', 10)->nullable();
            $table->string('phone_number', 60)->nullable();
            $table->string('wa_number', 60)->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('title', 10)->nullable();
            $table->string('group', 10)->nullable();
            $table->string('title1', 191)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('pic_name', 191)->nullable();
            $table->string('pic_phone_number', 191)->nullable();
            $table->date('pic_birth_date')->nullable();
            $table->integer('industry_id')->nullable();
            $table->string('website', 191)->nullable();
            $table->string('social_media_instagram', 191)->nullable();
            $table->string('social_media_facebook', 191)->nullable();
            $table->string('social_media_twitter', 191)->nullable();
            $table->string('phone_number1', 50)->nullable();
            $table->string('email', 191)->nullable();
            $table->integer('partner_type_id')->nullable();
            $table->string('company_email', 191)->nullable();
            $table->string('owner_name', 191)->nullable();
            $table->string('owner_email', 191)->nullable();
            $table->string('owner_phone', 191)->nullable();
            $table->date('owner_birth_date')->nullable();
            $table->string('pic_email', 191)->nullable();
            $table->longText('industries')->nullable();
            $table->longText('partner_types')->nullable();
            $table->longText('online_stores')->nullable();
            $table->integer('team_user_id')->nullable();
            $table->string('partner_alias', 191)->nullable();
            $table->boolean('is_pricelist')->nullable();
            $table->boolean('is_marketing')->nullable();
            $table->string('mobile_number', 191)->nullable();
            $table->string('postal_code', 191)->nullable();
            $table->char('is_subscribed_email', 1)->nullable();
            $table->timestamp('unsubscribe_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('code', 'm_db_partner_code_IDX');
            $table->index('is_subscribed_email', 'm_db_partner_is_subscribed_email_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partners');
    }
}
