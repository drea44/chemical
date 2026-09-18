<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('chemical_monthly_balances', function (Blueprint $table) {
            $table->index('period_month', 'idx_monthly_balances_period_month');
        });
    }

    public function down(): void
    {
        Schema::table('chemical_monthly_balances', function (Blueprint $table) {
            $table->dropIndex('idx_monthly_balances_period_month');
        });
    }
};

