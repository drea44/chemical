<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique();
            $table->string('report_type'); // inventory_summary, stock_movement, expiry_report, usage_report, adjustment_report
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('file_path')->nullable();
            $table->string('format')->default('pdf'); // pdf, excel, csv
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
