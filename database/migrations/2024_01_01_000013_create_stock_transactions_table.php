<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('chemical_id')->constrained('chemicals')->onDelete('restrict');
            $table->enum('transaction_type', ['STOCK_IN', 'STOCK_OUT', 'ADJUSTMENT', 'TRANSFER'])->default('STOCK_IN');
            $table->decimal('quantity', 14, 4);
            $table->string('unit');
            $table->decimal('stock_before', 14, 4);
            $table->decimal('stock_after', 14, 4);
            $table->string('reference_number')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('chemical_locations')->onDelete('set null');
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('transaction_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['completed', 'pending', 'rejected'])->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
