<?php

namespace Database\Seeders;

use App\Models\Chemical;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StockTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('email', 'admin@example.com')->first() ?? User::first();
        $fitria   = User::where('email', 'fitria@example.com')->first() ?? $admin;
        $tyas     = User::where('email', 'tyas@example.com')->first() ?? $admin;
        $nurJanah = User::where('email', 'nurjanah@example.com')->first() ?? $admin;
        $alya     = User::where('email', 'alya@example.com')->first() ?? $admin;
        $gebrina  = User::where('email', 'gebrina@example.com')->first() ?? $admin;
        $jihan    = User::where('email', 'jihan@example.com')->first() ?? $admin;
        $fahmi    = User::where('email', 'fahmi@example.com')->first() ?? $admin;
        $iseh     = User::where('email', 'iseh@example.com')->first() ?? $admin;
        $bayu     = User::where('email', 'bayu@example.com')->first() ?? $admin;
        $prapto   = User::where('email', 'prapto@example.com')->first() ?? $admin;

        $userMap = [
            'fitria' => $fitria,
            'tyas' => $tyas,
            'nur janah' => $nurJanah,
            'nurjanah' => $nurJanah,
            'alya' => $alya,
            'gebrina' => $gebrina,
            'jihan' => $jihan,
            'fahmi' => $fahmi,
            'iseh' => $iseh,
            'bayu' => $bayu,
            'prapto' => $prapto,
            'pri' => $prapto,
        ];

        $transCounter = 1;

        // ══════════════════════════════════════════════════════════════════════════
        // 1. SALDO AWAL MONITIRING MARET 2026 (INITIAL BALANCE TRANSACTIONS)
        // ══════════════════════════════════════════════════════════════════════════
        $marchStartingByName = [
            'Aluminium kalium sulfat dodecahydrat' => 2000.0,
            'Ammonia solution 25 %' => 2000.0,
            'Ammonium heptamolybdate tetrahydrate' => 1000.0,
            'Amonium chloride' => 1000.0,
            'Chromatropic Acid disodium salt' => 1700.0,
            'Copper (II) sulfate pentahydrate' => 1700.0,
            'Curcumine' => 11200.0,
            'Dimedone' => 18500.0,
            'Ethanol' => 11200.0,
            'Hydrochloric acid 37%' => 18500.0,
            'L (+) Asorbic Acid' => 8500.0,
            'Methyl red (CI 13020)' => 700.0,
            'Methyl red sodium salt (CI 13020)' => 50.0,
            'MUG EC Broth' => 1600.0,
            'n-Amylalkohol' => 4000.0,
            'Natrium molibdat.dihidrat' => 1500.0,
            'Nitric acid 65%' => 8500.0,
            'Perochloric acid 70-72%' => 1450.0,
            'Selenium reagent mixture' => 700.0,
            'Silver nitrate' => 50.0,
            'Sodium chloride' => 1600.0,
            'Sodium hydroxide' => 4000.0,
            'Sodium sulfate' => 1500.0,
            'Sulphuric acid' => 1450.0,
        ];

        $chemicals = Chemical::all();
        foreach ($chemicals as $chem) {
            $starting = $marchStartingByName[$chem->chemical_name] ?? (float) $chem->current_stock;
            if ($starting > 0) {
                StockTransaction::create([
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $chem->id,
                    'transaction_type' => 'STOCK_IN',
                    'quantity'         => $starting,
                    'unit'             => $chem->unit,
                    'stock_before'     => 0,
                    'stock_after'      => $starting,
                    'reference_number' => 'INIT-2026-03',
                    'reason'           => 'Saldo awal sementara monitoring Maret 2026',
                    'location_id'      => $chem->location_id,
                    'performed_by'     => $admin->id,
                    'transaction_date' => '2026-03-01 08:00:00',
                    'status'           => 'completed',
                ]);
            }
        }

        // ══════════════════════════════════════════════════════════════════════════
        // 2. TRANSAKSI PENGELUARAN MARET 2026
        // ══════════════════════════════════════════════════════════════════════════
        $marchTakes = [
            ['Ammonia solution 25 %', '2026-03-13 09:00:00', 'Tyas', 142.5, 'Take 1'],
            ['Amonium chloride', '2026-03-13 09:00:00', 'Tyas', 16.125, 'Take 1'],
            ['Amonium chloride', '2026-03-13 09:00:00', 'Tyas', 16.125, 'Take 2'],
            ['Amonium chloride', '2026-03-13 09:00:00', 'Tyas', 16.125, 'Take 3'],
            ['Amonium chloride', '2026-03-13 09:00:00', 'Tyas', 16.125, 'Take 4'],
            ['Copper (II) sulfate pentahydrate', '2026-03-01 09:00:00', 'Fitria', 2.0612, 'Take 1'],
            ['Ethanol', '2026-03-12 09:00:00', 'Tyas', 1000, 'Take 1'],
            ['Hydrochloric acid 37%', '2026-03-13 09:00:00', 'Tyas', 10, 'Take 1'],
            ['Hydrochloric acid 37%', '2026-03-16 09:00:00', 'Tyas', 10, 'Take 1'],
            ['Hydrochloric acid 37%', '2026-03-16 09:00:00', 'Tyas', 5, 'Take 2'],
            ['Nitric acid 65%', '2026-03-11 09:00:00', 'Tyas', 25, 'Take 1'],
            ['Nitric acid 65%', '2026-03-11 09:00:00', 'Tyas', 21, 'Take 2'],
            ['Nitric acid 65%', '2026-03-11 09:00:00', 'Tyas', 4, 'Take 3'],
            ['Nitric acid 65%', '2026-03-11 09:00:00', 'Tyas', 25, 'Take 4'],
            ['Nitric acid 65%', '2026-03-13 09:00:00', 'Tyas', 50, 'Take 1'],
            ['Nitric acid 65%', '2026-03-16 09:00:00', 'Tyas', 50, 'Take 1'],
            ['Selenium reagent mixture', '2026-03-04 09:00:00', 'Tyas', 0.2194, 'Take 1'],
            ['Selenium reagent mixture', '2026-03-04 09:00:00', 'Tyas', 0.2541, 'Take 2'],
            ['Silver nitrate', '2026-03-06 09:00:00', 'Nur Janah', 4.259, 'Take 1'],
            ['Sodium chloride', '2026-03-04 09:00:00', 'Tyas', 0.59, 'Take 1'],
            ['Sodium hydroxide', '2026-03-03 09:00:00', 'Fitria', 16.25, 'Take 1'],
            ['Sodium sulfate', '2026-03-01 09:00:00', 'Fitria', 2.0828, 'Take 1'],
            ['Sulphuric acid', '2026-03-01 09:00:00', 'Fitria', 25, 'Take 1'],
        ];

        foreach ($marchTakes as $item) {
            $c = Chemical::where('chemical_name', $item[0])->first();
            if ($c) {
                $user = $userMap[strtolower($item[2])] ?? $admin;
                StockTransaction::create([
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $c->id,
                    'transaction_type' => 'STOCK_OUT',
                    'quantity'         => $item[3],
                    'unit'             => $c->unit,
                    'stock_before'     => 0,
                    'stock_after'      => 0,
                    'reference_number' => 'USAGE-2026-03',
                    'reason'           => "Analyst usage ({$item[4]}) - {$item[2]}",
                    'location_id'      => $c->location_id,
                    'performed_by'     => $user->id,
                    'transaction_date' => $item[1],
                    'status'           => 'completed',
                ]);
            }
        }

        // ══════════════════════════════════════════════════════════════════════════
        // 3. TRANSAKSI PENERIMAAN & PENGELUARAN APRIL 2026
        // ══════════════════════════════════════════════════════════════════════════
        $aprilRestocks = [
            ['Acetone p.a', 10000],
            ['Aquabides', 15000],
            ['Methylenblue', 5],
            ['Micropipette 1 ml', 5],
            ['Nitric acid 65%', 10000],
            ['Perochloric acid 70-72%', 2500],
            ['Sarung tangan ukuran S', 8],
            ['Sarung tangan ukuran M', 8],
            ['Sodium tetraphenyl borate', 25],
            ['Sodium thiosulfate', 1000],
            ['Sulphuric acid', 10000],
        ];

        foreach ($aprilRestocks as $r) {
            $c = Chemical::where('chemical_name', $r[0])->first();
            if ($c) {
                StockTransaction::create([
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $c->id,
                    'transaction_type' => 'STOCK_IN',
                    'quantity'         => $r[1],
                    'unit'             => $c->unit,
                    'stock_before'     => 0,
                    'stock_after'      => $r[1],
                    'reference_number' => 'RESTOCK-2026-04',
                    'reason'           => 'Penerimaan restock April 2026',
                    'location_id'      => $c->location_id,
                    'performed_by'     => $admin->id,
                    'transaction_date' => '2026-04-01 08:00:00',
                    'status'           => 'completed',
                ]);
            }
        }

        $aprilTakes = [
            ['Barium chloride dihydrate', '2026-04-14 09:00:00', 'Jihan', 15, 'Take 1'],
            ['Copper (II) sulfate pentahydrate', '2026-04-08 09:00:00', 'Tyas', 2.0016, 'Take 1'],
            ['Glass fiber filter 1 mikron 47 mm 10 pk PALL', '2026-04-05 09:00:00', 'Alya', 1, 'Take 1'],
            ['Glass fiber filter 1 mikron 47 mm 10 pk PALL', '2026-04-15 09:00:00', 'Alya', 1, 'Take 1'],
            ['Hydrochloric acid 37%', '2026-04-09 09:00:00', 'Fitria', 10, 'Take 1'],
            ['Hydrochloric acid 37%', '2026-04-09 09:00:00', 'Fitria', 10, 'Take 2'],
            ['Magnesium oxide', '2026-04-15 09:00:00', 'Nur Janah', 4, 'Take 2'],
            ['Nitric acid 65%', '2026-04-06 09:00:00', 'Gebrina', 250, 'Take 1'],
            ['Nitric acid 65%', '2026-04-08 09:00:00', 'Gebrina', 120, 'Take 2'],
            ['Nitric acid 65%', '2026-04-09 09:00:00', 'Fitria', 25, 'Take 1'],
            ['Nitric acid 65%', '2026-04-09 09:00:00', 'Fitria', 25, 'Take 2'],
            ['Nitric acid 65%', '2026-04-09 09:00:00', 'Fitria', 25, 'Take 3'],
            ['Nitric acid 65%', '2026-04-09 09:00:00', 'Fitria', 25, 'Take 4'],
            ['Potassium hydroxide', '2026-04-15 09:00:00', 'Nur Janah', 60, 'Take 2'],
            ['Potassium peroxodisulfate', '2026-04-14 09:00:00', 'Alya', 10, 'Take 2'],
            ['Selenium reagent mixture', '2026-04-09 09:00:00', 'Fitria', 0.5, 'Take 1'],
            ['Selenium reagent mixture', '2026-04-09 09:00:00', 'Fitria', 0.5, 'Take 2'],
            ['Silver nitrate', '2026-04-14 09:00:00', 'Jihan', 2.6, 'Take 1'],
            ['Sodium hydroxide', '2026-04-02 09:00:00', 'Fitria', 400, 'Take 1'],
            ['Sodium sulfate', '2026-04-08 09:00:00', 'Tyas', 2.05, 'Take 1'],
            ['Sulphuric acid', '2026-04-06 09:00:00', 'Tyas', 500, 'Take 2'],
            ['Sulphuric acid', '2026-04-08 09:00:00', 'Tyas', 25, 'Take 1'],
            ['Sulphuric acid', '2026-04-09 09:00:00', 'Fitria', 16, 'Take 1'],
            ['Sulphuric acid', '2026-04-09 09:00:00', 'Fitria', 37.5, 'Take 2'],
            ['Sulphuric acid', '2026-04-14 09:00:00', 'Jihan', 261, 'Take 1'],
        ];

        foreach ($aprilTakes as $item) {
            $c = Chemical::where('chemical_name', $item[0])->first();
            if ($c) {
                $user = $userMap[strtolower($item[2])] ?? $admin;
                StockTransaction::create([
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $c->id,
                    'transaction_type' => 'STOCK_OUT',
                    'quantity'         => $item[3],
                    'unit'             => $c->unit,
                    'stock_before'     => 0,
                    'stock_after'      => 0,
                    'reference_number' => 'USAGE-2026-04',
                    'reason'           => "Analyst usage ({$item[4]}) - {$item[2]}",
                    'location_id'      => $c->location_id,
                    'performed_by'     => $user->id,
                    'transaction_date' => $item[1],
                    'status'           => 'completed',
                ]);
            }
        }

        // ══════════════════════════════════════════════════════════════════════════
        // 4. TRANSAKSI PENERIMAAN & PENGELUARAN MEI 2026
        // ══════════════════════════════════════════════════════════════════════════
        $mayRestocks = [
            ['Sarung tangan ukuran L', 7],
        ];

        foreach ($mayRestocks as $r) {
            $c = Chemical::where('chemical_name', $r[0])->first();
            if ($c) {
                StockTransaction::create([
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $c->id,
                    'transaction_type' => 'STOCK_IN',
                    'quantity'         => $r[1],
                    'unit'             => $c->unit,
                    'stock_before'     => 0,
                    'stock_after'      => $r[1],
                    'reference_number' => 'RESTOCK-2026-05',
                    'reason'           => 'Penerimaan restock Mei 2026',
                    'location_id'      => $c->location_id,
                    'performed_by'     => $admin->id,
                    'transaction_date' => '2026-05-01 08:00:00',
                    'status'           => 'completed',
                ]);
            }
        }

        $mayTakes = [
            ['Barium chloride dihydrate', '2026-05-14 09:00:00', 'Gebrina', 15, 'Take 1'],
            ['Devarda alloy', '2026-05-21 09:00:00', 'Nur Janah', 3, 'Take 1'],
            ['Hydrochloric acid 37%', '2026-05-04 09:00:00', 'Fitria', 10, 'Take 1'],
            ['Hydrochloric acid 37%', '2026-05-20 09:00:00', 'Nur Janah', 40, 'Take 3'],
            ['Hydrochloric acid 37%', '2026-05-21 09:00:00', 'Nur Janah', 10, 'Take 1'],
            ['Kertas saring 41 Diameter 125 mm Whatman', '2026-05-12 09:00:00', 'Alya', 1, 'Take 1'],
            ['Kertas saring 41 Diameter 125 mm Whatman', '2026-05-20 09:00:00', 'Gebrina', 1, 'Take 2'],
            ['Kertas saring 41 Diameter 125 mm Whatman', '2026-05-22 09:00:00', 'Alya', 1, 'Take 2'],
            ['Kertas saring 0,45 mikron Diameter 47 mm PALL', '2026-05-22 09:00:00', 'Gebrina', 1, 'Take 1'],
            ['L (+) Asorbic Acid', '2026-05-25 09:00:00', 'Nur Janah', 0.1, 'Take 1'],
            ['Nitric acid 65%', '2026-05-04 09:00:00', 'Fitria', 50, 'Take 1'],
            ['Nitric acid 65%', '2026-05-12 09:00:00', 'Gebrina', 200, 'Take 2'],
            ['Nitric acid 65%', '2026-05-19 09:00:00', 'Fitria', 50, 'Take 1'],
            ['Nitric acid 65%', '2026-05-20 09:00:00', 'Fitria', 200, 'Take 1'],
            ['Nitric acid 65%', '2026-05-20 09:00:00', 'Gebrina', 300, 'Take 2'],
            ['Nitric acid 65%', '2026-05-20 09:00:00', 'Nur Janah', 100, 'Take 3'],
            ['Nitric acid 65%', '2026-05-21 09:00:00', 'Nur Janah', 50, 'Take 1'],
            ['Oxalic acid dihydrate', '2026-05-21 09:00:00', 'Nur Janah', 0.5, 'Take 1'],
            ['Potassium chloride', '2026-05-19 09:00:00', 'Fitria', 10, 'Take 1'],
            ['Potassium chloride', '2026-05-20 09:00:00', 'Fitria', 40, 'Take 1'],
            ['Potassium hydroxide', '2026-05-15 09:00:00', 'Gebrina', 60, 'Take 2'],
            ['Potassium hydroxide', '2026-05-18 09:00:00', 'Gebrina', 8, 'Take 1'],
            ['Potassium hydroxide', '2026-05-21 09:00:00', 'Nur Janah', 10, 'Take 1'],
            ['Potassium peroxodisulfate', '2026-05-12 09:00:00', 'Alya', 10.0559, 'Take 1'],
            ['Selenium reagent mixture', '2026-05-09 09:00:00', 'Fitria', 0.5, 'Take 1'],
            ['Selenium reagent mixture', '2026-05-09 09:00:00', 'Fitria', 0.5, 'Take 2'],
            ['Sodium hydroxide', '2026-05-20 09:00:00', 'Fitria', 2.5581, 'Take 1'],
            ['Sodium thiosulfate', '2026-05-06 09:00:00', 'Fitria', 12, 'Take 1'],
        ];

        foreach ($mayTakes as $item) {
            $c = Chemical::where('chemical_name', $item[0])->first();
            if ($c) {
                $user = $userMap[strtolower($item[2])] ?? $admin;
                StockTransaction::create([
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $c->id,
                    'transaction_type' => 'STOCK_OUT',
                    'quantity'         => $item[3],
                    'unit'             => $c->unit,
                    'stock_before'     => 0,
                    'stock_after'      => 0,
                    'reference_number' => 'USAGE-2026-05',
                    'reason'           => "Analyst usage ({$item[4]}) - {$item[2]}",
                    'location_id'      => $c->location_id,
                    'performed_by'     => $user->id,
                    'transaction_date' => $item[1],
                    'status'           => 'completed',
                ]);
            }
        }

        // ══════════════════════════════════════════════════════════════════════════
        // 5. TRANSAKSI PENERIMAAN & PENGELUARAN JUNI 2026
        // ══════════════════════════════════════════════════════════════════════════
        $juneRestocks = [
            ['Barium chloride dihydrate', 500],
            ['Kertas saring 41 Diameter 125 mm Whatman', 26],
            ['Kertas saring 42 Diameter 125 mm Whatman', 16],
            ['Kertas saring 43 Diameter 125 mm Whatman', 23],
            ['Kertas saring 0,45 mikron Diameter 47 mm Whatman', 5],
            ['Kertas saring 0,45 mikron Diameter 47 mm PALL', 10],
            ['Magnesium (II) sulfate monohydrate', 500],
            ['Pipet pasteur plastik 3 ml', 100],
            ['Pipet pasteur plastik 5 ml', 107],
            ['Sarung tangan safety', 24],
            ['Sarung tangan ukuran M', 10],
            ['Titriplex III', 250],
        ];

        foreach ($juneRestocks as $r) {
            $c = Chemical::where('chemical_name', $r[0])->first();
            if ($c) {
                StockTransaction::create([
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $c->id,
                    'transaction_type' => 'STOCK_IN',
                    'quantity'         => $r[1],
                    'unit'             => $c->unit,
                    'stock_before'     => 0,
                    'stock_after'      => $r[1],
                    'reference_number' => 'RESTOCK-2026-06',
                    'reason'           => 'Penerimaan restock Juni 2026',
                    'location_id'      => $c->location_id,
                    'performed_by'     => $admin->id,
                    'transaction_date' => '2026-06-01 08:00:00',
                    'status'           => 'completed',
                ]);
            }
        }

        $juneTakes = [
            ['Acetic acid (glacial) 100%', '2026-06-08 09:00:00', 'Jihan', 20, 'Take 1'],
            ['Acetic acid (glacial) 100%', '2026-06-08 09:00:00', 'Jihan', 20, 'Take 2'],
            ['Acetic acid (glacial) 100%', '2026-06-21 09:00:00', 'Jihan', 20, 'Take 1'],
            ['Aluminium hydroxide', '2026-06-01 09:00:00', 'Tyas', 22.5, 'Take 3'],
            ['Citric acid monohydrate', '2026-06-25 09:00:00', 'Fitria', 12.5, 'Take 1'],
            ['Citric acid monohydrate', '2026-06-25 09:00:00', 'Fitria', 12.5, 'Take 2'],
            ['Copper (II) sulfate pentahydrate', '2026-06-23 09:00:00', 'Fitria', 6.25, 'Take 2'],
            ['Copper (II) sulfate pentahydrate', '2026-06-23 09:00:00', 'Fitria', 6.25, 'Take 3'],
            ['Devarda alloy', '2026-06-30 09:00:00', 'Nur Janah', 3, 'Take 1'],
            ['Di-ammonium oxalate monohydrate', '2026-06-29 09:00:00', 'Nur Janah', 40, 'Take 1'],
            ['Disodium hydrogen phosphate heptahydrate', '2026-06-26 09:00:00', 'Fitria', 25, 'Take 3'],
            ['Glass fiber filter 1 mikron 47 mm 10 pk PALL', '2026-06-18 09:00:00', 'Alya', 4, 'Take 1'],
            ['Hydrochloric acid 37%', '2026-06-03 09:00:00', 'Nur Janah', 35, 'Take 1'],
            ['Hydrochloric acid 37%', '2026-06-03 09:00:00', 'Tyas', 5, 'Take 2'],
            ['Hydrochloric acid 37%', '2026-06-03 09:00:00', 'Tyas', 2, 'Take 3'],
            ['Hydrochloric acid 37%', '2026-06-04 09:00:00', 'Tyas', 2, 'Take 2'],
            ['Hydrochloric acid 37%', '2026-06-04 09:00:00', 'Tyas', 10, 'Take 3'],
            ['Kertas saring 41 Diameter 125 mm Whatman', '2026-06-24 09:00:00', 'Tyas', 1, 'Take 1'],
            ['Kertas saring 42 Diameter 125 mm Whatman', '2026-06-09 09:00:00', 'Alya', 1, 'Take 1'],
            ['Kertas saring 42 Diameter 125 mm Whatman', '2026-06-24 09:00:00', 'Fitria', 1, 'Take 3'],
            ['Kertas saring 42 Diameter 125 mm Whatman', '2026-06-30 09:00:00', 'Nur Janah', 1, 'Take 1'],
            ['Lead (II) oxide', '2026-06-26 09:00:00', 'Fitria', 10, 'Take 1'],
            ['Lead (II) oxide', '2026-06-26 09:00:00', 'Fitria', 10, 'Take 2'],
            ['Magnesium chloride hexahydrate', '2026-06-04 09:00:00', 'Jihan', 30, 'Take 1'],
            ['Magnesium chloride hexahydrate', '2026-06-11 09:00:00', 'Jihan', 30, 'Take 1'],
            ['Nitric acid 65%', '2026-06-05 09:00:00', 'Tyas', 25, 'Take 1'],
            ['Nitric acid 65%', '2026-06-05 09:00:00', 'Tyas', 10, 'Take 2'],
            ['Nitric acid 65%', '2026-06-05 09:00:00', 'Tyas', 10, 'Take 3'],
            ['Nitric acid 65%', '2026-06-06 09:00:00', 'Tyas', 50, 'Take 1'],
            ['Nitric acid 65%', '2026-06-29 09:00:00', 'Gebrina', 150, 'Take 2'],
            ['Potassium hydroxide', '2026-06-01 09:00:00', 'Tyas', 200, 'Take 3'],
            ['Potassium hydroxide', '2026-06-03 09:00:00', 'Nur Janah', 88, 'Take 1'],
            ['Potassium hydroxide', '2026-06-15 09:00:00', 'Prapto', 60, 'Take 2'],
            ['Potassium hydroxide', '2026-06-18 09:00:00', 'Alya', 8, 'Take 1'],
            ['Potassium hydroxide', '2026-06-21 09:00:00', 'Jihan', 10, 'Take 1'],
            ['Potassium Iodide', '2026-06-29 09:00:00', 'Fitria', 25, 'Take 3'],
            ['Potassium nitrate', '2026-06-08 09:00:00', 'Jihan', 1, 'Take 1'],
            ['Potassium nitrate', '2026-06-17 09:00:00', 'Jihan', 1, 'Take 1'],
            ['Potassium Sulfate', '2026-06-02 09:00:00', 'Fitria', 16, 'Take 1'],
            ['Potassium Sulfate', '2026-06-17 09:00:00', 'Jihan', 0.111, 'Take 2'],
            ['Sarung tangan safety', '2026-06-09 09:00:00', 'Pri', 1, 'Take 2'],
            ['Sarung tangan safety', '2026-06-15 09:00:00', 'Prapto', 1, 'Take 1'],
            ['Sarung tangan safety', '2026-06-22 09:00:00', 'fahmi', 1, 'Take 1'],
            ['Sarung tangan safety', '2026-06-22 09:00:00', 'iseh', 1, 'Take 2'],
            ['Sarung tangan safety', '2026-06-23 09:00:00', 'Bayu', 2, 'Take 1'],
            ['Sarung tangan ukuran M', '2026-06-24 09:00:00', 'Gebrina', 1, 'Take 2'],
            ['Sarung tangan ukuran L', '2026-06-15 09:00:00', 'Prapto', 1, 'Take 1'],
            ['Silver nitrate', '2026-06-02 09:00:00', 'Jihan', 2.395, 'Take 2'],
            ['Silver nitrate', '2026-06-02 09:00:00', 'Jihan', 2.395, 'Take 3'],
            ['Silver sulfate', '2026-06-01 09:00:00', 'Jihan', 5.06, 'Take 1'],
            ['Sodium acetat trihydrate', '2026-06-07 09:00:00', 'Jihan', 5, 'Take 1'],
            ['Sodium acetat trihydrate', '2026-06-14 09:00:00', 'Jihan', 5, 'Take 1'],
            ['Sodium Carbonate', '2026-06-21 09:00:00', 'Fitria', 35.95, 'Take 2'],
            ['Sodium Carbonate', '2026-06-21 09:00:00', 'Fitria', 35.95, 'Take 3'],
            ['Sodium hydroxide', '2026-06-30 09:00:00', 'Nur Janah', 200, 'Take 1'],
            ['Sodium tetraphenyl borate', '2026-06-01 09:00:00', 'Tyas', 10, 'Take 2'],
            ['Sodium tetraphenyl borate', '2026-06-01 09:00:00', 'Tyas', 2, 'Take 3'],
            ['Sodium thiosulfate', '2026-06-27 09:00:00', 'Fitria', 7.91, 'Take 2'],
            ['Sodium thiosulfate', '2026-06-29 09:00:00', 'Nur Janah', 8, 'Take 1'],
            ['Sulphuric acid', '2026-06-01 09:00:00', 'Jihan', 500, 'Take 1'],
            ['Sulphuric acid', '2026-06-03 09:00:00', 'Nur Janah', 175, 'Take 1'],
            ['Sulphuric acid', '2026-06-03 09:00:00', 'Tyas', 7, 'Take 2'],
            ['Sulphuric acid', '2026-06-03 09:00:00', 'Tyas', 7, 'Take 3'],
            ['Sulphuric acid', '2026-06-04 09:00:00', 'Tyas', 7, 'Take 2'],
            ['Sulphuric acid', '2026-06-27 09:00:00', 'Fitria', 70, 'Take 1'],
        ];

        foreach ($juneTakes as $item) {
            $c = Chemical::where('chemical_name', $item[0])->first();
            if ($c) {
                $user = $userMap[strtolower($item[2])] ?? $admin;
                StockTransaction::create([
                    'transaction_code' => 'TXN-' . str_pad($transCounter++, 5, '0', STR_PAD_LEFT),
                    'chemical_id'      => $c->id,
                    'transaction_type' => 'STOCK_OUT',
                    'quantity'         => $item[3],
                    'unit'             => $c->unit,
                    'stock_before'     => 0,
                    'stock_after'      => 0,
                    'reference_number' => 'USAGE-2026-06',
                    'reason'           => "Analyst usage ({$item[4]}) - {$item[2]}",
                    'location_id'      => $c->location_id,
                    'performed_by'     => $user->id,
                    'transaction_date' => $item[1],
                    'status'           => 'completed',
                ]);
            }
        }
    }
}