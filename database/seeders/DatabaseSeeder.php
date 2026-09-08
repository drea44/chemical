<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SystemSettingSeeder::class,
            ChemicalCategorySeeder::class,
            ChemicalLocationSeeder::class,
            SupplierSeeder::class,
            UserSeeder::class,
            ChemicalSeeder::class,
            StockTransactionSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
