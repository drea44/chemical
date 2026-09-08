<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chemicals', function (Blueprint $table) {
            $table->id();
            $table->string('chemical_code')->unique();
            $table->string('chemical_name');
            $table->string('cas_number')->nullable();
            $table->foreignId('category_id')->constrained('chemical_categories')->onDelete('restrict');
            $table->string('supplier')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('catalog_number')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('lot_number')->nullable();
            $table->string('concentration')->nullable();
            $table->enum('physical_state', ['solid', 'liquid', 'gas', 'powder', 'solution'])->nullable();
            $table->string('unit')->default('L');
            $table->decimal('current_stock', 12, 3)->default(0);
            $table->decimal('minimum_stock', 12, 3)->default(0);
            $table->decimal('maximum_stock', 12, 3)->nullable();
            $table->string('storage_condition')->nullable();
            $table->string('hazard_class')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('chemical_locations')->onDelete('set null');
            $table->string('qr_code')->nullable();
            $table->date('received_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['SAFE', 'LOW', 'CRITICAL', 'EXPIRED', 'EXPIRING_SOON'])->default('SAFE');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemicals');
    }
};
