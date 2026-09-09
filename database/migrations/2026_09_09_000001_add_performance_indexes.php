<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * BUG-13: Add database indexes on frequently queried/filtered/sorted columns.
     */
    public function up(): void
    {
        Schema::table('chemicals', function (Blueprint $table) {
            $table->index('status', 'idx_chemicals_status');
            $table->index('expiry_date', 'idx_chemicals_expiry_date');
            $table->index('chemical_name', 'idx_chemicals_name');
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->index('transaction_date', 'idx_transactions_date');
            $table->index('transaction_type', 'idx_transactions_type');
            $table->index(['chemical_id', 'transaction_date'], 'idx_transactions_chem_date');
            $table->index(['transaction_date', 'transaction_type'], 'idx_transactions_date_type');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('created_at', 'idx_audit_logs_created_at');
            $table->index('action', 'idx_audit_logs_action');
            $table->index('module', 'idx_audit_logs_module');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->index('created_at', 'idx_adjustments_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chemicals', function (Blueprint $table) {
            $table->dropIndex('idx_chemicals_status');
            $table->dropIndex('idx_chemicals_expiry_date');
            $table->dropIndex('idx_chemicals_name');
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_date');
            $table->dropIndex('idx_transactions_type');
            $table->dropIndex('idx_transactions_chem_date');
            $table->dropIndex('idx_transactions_date_type');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_audit_logs_created_at');
            $table->dropIndex('idx_audit_logs_action');
            $table->dropIndex('idx_audit_logs_module');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropIndex('idx_adjustments_created_at');
        });
    }
};
