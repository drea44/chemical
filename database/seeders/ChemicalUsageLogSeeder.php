<?php

namespace Database\Seeders;

use App\Models\Chemical;
use App\Models\ChemicalCategory;
use App\Models\ChemicalDailyUsage;
use App\Models\ChemicalLocation;
use App\Models\ChemicalLogDate;
use App\Models\ChemicalMonthlyBalance;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChemicalUsageLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'ADMIN')->first() ?? User::first();
        $category = ChemicalCategory::first();
        $location = ChemicalLocation::first();

        // 30 chemicals exactly as depicted in Gambar 2
        $referenceChemicals = [
            ['name' => '1,10 - phenanthroline chloride monohydrate', 'saldo_awal' => 20, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => '1,10 - phenanthroline monohydrate', 'saldo_awal' => 1.1, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => '1,8-Dihydroxy-2-(4-Sulfophenylazo)-naphthalene-3,6-disulfonic acid trisodium salt', 'saldo_awal' => 5, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => '1,5-Diphenylcarbazide', 'saldo_awal' => 40, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => '1-Amino-2-hydroxide-4-naphtalene sulfonic acid', 'saldo_awal' => 20, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => '1-Butanol', 'saldo_awal' => 2500, 'unit' => 'ml', 'penerimaan' => 0],
            ['name' => '1-Naphtholbenzine', 'saldo_awal' => 2, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => '4-amino-2,3-dimethyl-1phenyl-3-pyrazolin-5-one', 'saldo_awal' => 3, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Acetic acid (glacial) 100%', 'saldo_awal' => 13500, 'unit' => 'ml', 'penerimaan' => 0],
            ['name' => 'Acetone', 'saldo_awal' => 3500, 'unit' => 'ml', 'penerimaan' => 10000],
            ['name' => 'Alizarin -3- methylamine-N,N diacetic acid dihydrate', 'saldo_awal' => 1, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Alizarin Red indicator', 'saldo_awal' => 24, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Alkylbenzyl dimethylammonium chloride', 'saldo_awal' => 200, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Aluminium hydroxide', 'saldo_awal' => 1950, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Aluminium kalium sulfat dodecahydrat', 'saldo_awal' => 500, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Amidosulfuris acid', 'saldo_awal' => 160, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Ammonia solution 25 %', 'saldo_awal' => 2000, 'unit' => 'ml', 'penerimaan' => 0],
            ['name' => 'Ammonium acetate', 'saldo_awal' => 1750, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Ammonium fluoride', 'saldo_awal' => 240, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Ammonium heptamolybdate tetrahydrate', 'saldo_awal' => 500, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Ammonium iron (III) sulfate dodecahydrate', 'saldo_awal' => 12080, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Amonium chloride', 'saldo_awal' => 1000, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Amonium monovanadate', 'saldo_awal' => 197, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Asam salisilat teknis', 'saldo_awal' => 302, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Asam sulfamat', 'saldo_awal' => 3, 'unit' => 'g', 'penerimaan' => 15000],
            ['name' => 'Barbituric acid', 'saldo_awal' => 3, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Barium acetat', 'saldo_awal' => 200, 'unit' => 'g', 'penerimaan' => 0],
            ['name' => 'Barium chloride dihydrate', 'saldo_awal' => null, 'unit' => null, 'penerimaan' => 0],
            ['name' => 'Biuret wasserfrei', 'saldo_awal' => null, 'unit' => null, 'penerimaan' => 0],
            ['name' => 'Boric acid', 'saldo_awal' => 500, 'unit' => 'g', 'penerimaan' => 0],
        ];

        // Create initial log dates for April 2026
        $date1 = ChemicalLogDate::where('period_month', '2026-04')->where('analyst_name', 'Fitria')->first();
        if (!$date1) {
            $date1 = ChemicalLogDate::create([
                'log_date'       => '2026-04-01',
                'analyst_name'   => 'Fitria',
                'analyst_take_1' => 'Fitria',
                'analyst_take_2' => null,
                'analyst_take_3' => null,
                'period_month'   => '2026-04',
                'created_by'     => $admin?->id,
            ]);
        } else {
            $date1->analyst_take_1 = 'Fitria';
            $date1->save();
        }

        $date2 = ChemicalLogDate::where('period_month', '2026-04')->where('analyst_name', 'Rizky')->first();
        if (!$date2) {
            $date2 = ChemicalLogDate::create([
                'log_date'       => '2026-04-02',
                'analyst_name'   => 'Rizky',
                'analyst_take_1' => 'Rizky',
                'analyst_take_2' => null,
                'analyst_take_3' => null,
                'period_month'   => '2026-04',
                'created_by'     => $admin?->id,
            ]);
        } else {
            $date2->analyst_take_1 = 'Rizky';
            $date2->save();
        }

        $date3 = ChemicalLogDate::where('period_month', '2026-04')->where('analyst_name', 'Analyst')->first();
        if (!$date3) {
            $date3 = ChemicalLogDate::create([
                'log_date'       => '2026-04-03',
                'analyst_name'   => 'Analyst',
                'analyst_take_1' => null,
                'analyst_take_2' => null,
                'analyst_take_3' => null,
                'period_month'   => '2026-04',
                'created_by'     => $admin?->id,
            ]);
        }

        foreach ($referenceChemicals as $index => $item) {
            $chemical = Chemical::where('chemical_name', $item['name'])
                ->orWhere('chemical_name', str_replace(' - ', '-', $item['name']))
                ->orWhere('chemical_name', str_replace('-', ' - ', $item['name']))
                ->first();

            if (!$chemical) {
                $code = 'CHM-IMG-' . str_pad((string)($index + 1), 3, '0', STR_PAD_LEFT);
                $chemical = Chemical::create([
                    'chemical_code'  => $code,
                    'chemical_name'  => $item['name'],
                    'cas_number'     => '100-' . ($index + 1) . '-0',
                    'category_id'    => $category?->id ?? 1,
                    'location_id'    => $location?->id ?? 1,
                    'unit'           => $item['unit'] ?? 'g',
                    'current_stock'  => (float)($item['saldo_awal'] ?? 0),
                    'minimum_stock'  => 10,
                    'status'         => 'SAFE',
                    'physical_state' => in_array($item['unit'] ?? '', ['ml', 'L']) ? 'liquid' : 'solid',
                ]);
            } else {
                $chemical->chemical_name = $item['name'];
                if ($item['unit']) {
                    $chemical->unit = $item['unit'];
                }
                $chemical->save();
            }

            // Monthly balance for April 2026
            ChemicalMonthlyBalance::updateOrCreate(
                [
                    'chemical_id'  => $chemical->id,
                    'period_month' => '2026-04',
                ],
                [
                    'saldo_awal' => $item['saldo_awal'] !== null ? (float)$item['saldo_awal'] : 0,
                    'penerimaan' => (float)$item['penerimaan'],
                ]
            );

            // Give sample usage for row 1 & row 6
            if ($index === 0) {
                ChemicalDailyUsage::updateOrCreate(
                    ['chemical_id' => $chemical->id, 'log_date_id' => $date1->id],
                    ['take_1' => 5, 'updated_by' => $admin?->id]
                );
                ChemicalDailyUsage::updateOrCreate(
                    ['chemical_id' => $chemical->id, 'log_date_id' => $date2->id],
                    ['take_1' => 20, 'updated_by' => $admin?->id]
                );
            } elseif ($index === 5) {
                ChemicalDailyUsage::updateOrCreate(
                    ['chemical_id' => $chemical->id, 'log_date_id' => $date1->id],
                    ['take_1' => 20, 'take_2' => 15, 'updated_by' => $admin?->id]
                );
                ChemicalDailyUsage::updateOrCreate(
                    ['chemical_id' => $chemical->id, 'log_date_id' => $date2->id],
                    ['take_1' => 25, 'updated_by' => $admin?->id]
                );
            }
        }
    }
}
