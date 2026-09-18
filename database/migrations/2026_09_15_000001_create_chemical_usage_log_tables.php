<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('chemical_log_dates', function (Blueprint $table) {
            $table->id();
            $table->date('log_date');
            $table->string('analyst_name')->default('Analyst');
            $table->string('period_month', 7);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['log_date', 'analyst_name']);
            $table->index(['period_month', 'log_date']);
        });

        Schema::create('chemical_daily_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chemical_id')->constrained('chemicals')->onDelete('cascade');
            $table->foreignId('log_date_id')->constrained('chemical_log_dates')->onDelete('cascade');
            $table->decimal('take_1', 14, 4)->nullable();
            $table->decimal('take_2', 14, 4)->nullable();
            $table->decimal('take_3', 14, 4)->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['chemical_id', 'log_date_id']);
        });

        Schema::create('chemical_monthly_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chemical_id')->constrained('chemicals')->onDelete('cascade');
            $table->string('period_month', 7);
            $table->decimal('saldo_awal', 14, 4)->default(0);
            $table->decimal('penerimaan', 14, 4)->default(0);
            $table->timestamps();

            $table->unique(['chemical_id', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemical_daily_usages');
        Schema::dropIfExists('chemical_monthly_balances');
        Schema::dropIfExists('chemical_log_dates');
    }
};

