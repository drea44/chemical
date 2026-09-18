<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chemical_monthly_balances', function (Blueprint $table) {
            // Unit per bulan — bisa berbeda tiap bulan, nullable berarti tampil kosong
            $table->string('unit', 50)->nullable()->after('period_month');
        });
    }

    public function down(): void
    {
        Schema::table('chemical_monthly_balances', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};
