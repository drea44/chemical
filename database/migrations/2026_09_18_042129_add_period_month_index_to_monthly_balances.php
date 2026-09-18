<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add standalone index on chemical_monthly_balances.period_month.
     *
     * The existing unique(chemical_id, period_month) covers queries that include
     * chemical_id in the WHERE clause. However, queries filtering only by period_month
     * (e.g. monitoring reports) benefit from a standalone index to avoid full table scans.
     */
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
