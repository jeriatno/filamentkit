<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->tinyInteger('is_smi_partner')->default(0)->after('name');
            $table->foreignIdFor(\App\Models\Master\SMIPartner::class)->nullable()->after('is_smi_partner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->removeColumn('is_smi_partner');
            $table->removeColumn('smi_partner_id');
        });
    }
};
