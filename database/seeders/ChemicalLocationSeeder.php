<?php

namespace Database\Seeders;

use App\Models\ChemicalLocation;
use Illuminate\Database\Seeder;

class ChemicalLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Laboratory A',  'building' => 'Science Block',   'room' => 'Lab-101', 'shelf' => 'A1', 'description' => 'Primary analytical laboratory', 'status' => 'active'],
            ['name' => 'Laboratory B',  'building' => 'Science Block',   'room' => 'Lab-102', 'shelf' => 'B2', 'description' => 'Secondary synthesis laboratory', 'status' => 'active'],
            ['name' => 'Storage Room 1','building' => 'Warehouse Block',  'room' => 'STR-001', 'shelf' => 'C3', 'description' => 'General chemical storage, ambient temperature', 'status' => 'active'],
            ['name' => 'Storage Room 2','building' => 'Warehouse Block',  'room' => 'STR-002', 'shelf' => 'D4', 'description' => 'Flammable and hazardous chemical storage', 'status' => 'active'],
            ['name' => 'Cold Room',     'building' => 'Science Block',   'room' => 'CLD-001', 'shelf' => 'E1', 'description' => 'Temperature-controlled storage (2-8°C)', 'status' => 'active'],
        ];

        foreach ($locations as $location) {
            ChemicalLocation::updateOrCreate(['name' => $location['name']], $location);
        }
    }
}
