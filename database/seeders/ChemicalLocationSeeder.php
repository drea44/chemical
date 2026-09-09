<?php

namespace Database\Seeders;

use App\Models\ChemicalLocation;
use Illuminate\Database\Seeder;

class ChemicalLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Lemari A',       'building' => 'Main Lab', 'room' => 'Chemical Storage', 'shelf' => 'Lemari A', 'description' => 'Lemari Penyimpanan Kimia A', 'status' => 'active'],
            ['name' => 'Lemari B',       'building' => 'Main Lab', 'room' => 'Chemical Storage', 'shelf' => 'Lemari B', 'description' => 'Lemari Penyimpanan Kimia B', 'status' => 'active'],
            ['name' => 'Lemari D',       'building' => 'Main Lab', 'room' => 'Chemical Storage', 'shelf' => 'Lemari D', 'description' => 'Lemari Penyimpanan Kimia D', 'status' => 'active'],
            ['name' => 'Lemari E',       'building' => 'Main Lab', 'room' => 'Chemical Storage', 'shelf' => 'Lemari E', 'description' => 'Lemari Penyimpanan Kimia E', 'status' => 'active'],
            ['name' => 'Ruang Asam',     'building' => 'Main Lab', 'room' => 'Fume Hood Area',   'shelf' => 'Ruang Asam','description' => 'Penyimpanan Bahan Asam & Korosif', 'status' => 'active'],
            ['name' => 'Mikrobiologi',   'building' => 'Main Lab', 'room' => 'Microbiology Lab', 'shelf' => 'Mikro',      'description' => 'Penyimpanan Media & Mikrobiologi', 'status' => 'active'],
            ['name' => 'Baru',           'building' => 'Warehouse', 'room' => 'Stock Room',      'shelf' => 'Baru',       'description' => 'Stok Baru Belum Terbuka', 'status' => 'active'],
            ['name' => 'AKU',            'building' => 'Main Lab', 'room' => 'Instrument Room',  'shelf' => 'AKU',        'description' => 'Penyimpanan Khusus Analitis', 'status' => 'active'],
            ['name' => 'General Storage','building' => 'Main Lab', 'room' => 'General Area',     'shelf' => 'Gen',        'description' => 'Penyimpanan Umum', 'status' => 'active'],
            ['name' => 'Laboratory A',   'building' => 'Science Block', 'room' => 'Lab-101',     'shelf' => 'A1',         'description' => 'Primary analytical laboratory', 'status' => 'active'],
            ['name' => 'Laboratory B',   'building' => 'Science Block', 'room' => 'Lab-102',     'shelf' => 'B2',         'description' => 'Secondary synthesis laboratory', 'status' => 'active'],
            ['name' => 'Storage Room 1', 'building' => 'Warehouse Block', 'room' => 'STR-001',   'shelf' => 'C3',         'description' => 'General chemical storage', 'status' => 'active'],
            ['name' => 'Storage Room 2', 'building' => 'Warehouse Block', 'room' => 'STR-002',   'shelf' => 'D4',         'description' => 'Flammable and hazardous chemical storage', 'status' => 'active'],
            ['name' => 'Cold Room',      'building' => 'Science Block', 'room' => 'CLD-001',     'shelf' => 'E1',         'description' => 'Temperature-controlled storage (2-8°C)', 'status' => 'active'],
        ];

        foreach ($locations as $location) {
            ChemicalLocation::updateOrCreate(['name' => $location['name']], $location);
        }
    }
}
