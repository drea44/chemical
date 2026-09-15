<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chemical_log_dates', function (Blueprint $table) {
            $table->string('analyst_take_1')->nullable()->after('analyst_name');
            $table->string('analyst_take_2')->nullable()->after('analyst_take_1');
            $table->string('analyst_take_3')->nullable()->after('analyst_take_2');
        });
    }

    public function down(): void
    {
        Schema::table('chemical_log_dates', function (Blueprint $table) {
            $table->dropColumn(['analyst_take_1', 'analyst_take_2', 'analyst_take_3']);
        });
    }
};
