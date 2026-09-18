<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\ChemicalMonthlyBalance;
use App\Models\ChemicalLogDate;
use App\Models\ChemicalDailyUsage;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Jadikan kolom saldo_awal nullable
        Schema::table('chemical_monthly_balances', function (Blueprint $table) {
            $table->decimal('saldo_awal', 14, 4)->nullable()->default(null)->change();
        });

        // 2. Konversi data dummy bawaan seeder yang tidak pernah diinput user menjadi NULL
        // (yaitu record dengan saldo_awal = 0, unit kosong/null, penerimaan = 0, dan tidak ada pengeluaran harian)
        $candidates = ChemicalMonthlyBalance::where('saldo_awal', 0)
            ->where(function ($q) {
                $q->whereNull('unit')->orWhere('unit', '');
            })
            ->where('penerimaan', 0)
            ->get();

        foreach ($candidates as $mb) {
            $dateIds = ChemicalLogDate::where('period_month', $mb->period_month)->pluck('id');
            $hasUsage = ChemicalDailyUsage::where('chemical_id', $mb->chemical_id)
                ->whereIn('log_date_id', $dateIds)
                ->where(function ($q) {
                    $q->where('take_1', '>', 0)
                      ->orWhere('take_2', '>', 0)
                      ->orWhere('take_3', '>', 0);
                })
                ->exists();

            if (!$hasUsage) {
                $mb->saldo_awal = null;
                $mb->saveQuietly();
            }
        }
    }

    public function down(): void
    {
        ChemicalMonthlyBalance::whereNull('saldo_awal')->update(['saldo_awal' => 0]);

        Schema::table('chemical_monthly_balances', function (Blueprint $table) {
            $table->decimal('saldo_awal', 14, 4)->default(0)->change();
        });
    }
};
