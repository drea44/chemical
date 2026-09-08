<?php

namespace Database\Seeders;

use App\Models\ChemicalCategory;
use Illuminate\Database\Seeder;

class ChemicalCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Solvents',    'description' => 'Organic and inorganic solvents used in laboratory processes.', 'status' => 'active'],
            ['name' => 'Acids',       'description' => 'Strong and weak acids for titration and chemical synthesis.', 'status' => 'active'],
            ['name' => 'Bases',       'description' => 'Alkaline compounds including hydroxides and carbonates.', 'status' => 'active'],
            ['name' => 'Oxidizers',   'description' => 'Oxidizing agents used in reactions and analytical procedures.', 'status' => 'active'],
            ['name' => 'Reagents',    'description' => 'General analytical and synthesis reagents.', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            ChemicalCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
