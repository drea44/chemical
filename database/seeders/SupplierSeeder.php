<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name'           => 'Sigma-Aldrich',
                'contact_person' => 'Customer Care Indonesia',
                'email'          => 'order@sigmaaldrich.com',
                'phone'          => '+62 21 526 8888',
                'website'        => 'https://www.sigmaaldrich.com',
                'address'        => 'Menara Sentraya Lt. 18, Jl. Iskandarsyah Raya No. 1A, Jakarta Selatan',
                'status'         => 'active',
                'notes'          => 'Primary supplier for analytical grade reagents and solvents.',
            ],
            [
                'name'           => 'Merck KGaA',
                'contact_person' => 'PT Merck Tbk Industrial',
                'email'          => 'sales.id@merckgroup.com',
                'phone'          => '+62 21 840 0081',
                'website'        => 'https://www.merckgroup.com',
                'address'        => 'Jl. TB Simatupang No. 8, Pasar Rebo, Jakarta Timur',
                'status'         => 'active',
                'notes'          => 'High-purity acids, buffer solutions, and certified reference materials.',
            ],
            [
                'name'           => 'Thermo Fisher Scientific',
                'contact_person' => 'Scientific Sales Rep',
                'email'          => 'contact.id@thermofisher.com',
                'phone'          => '+62 21 2922 8888',
                'website'        => 'https://www.thermofisher.com',
                'address'        => 'AIA Central Lt. 25, Jl. Jend. Sudirman Kav. 48A, Jakarta Selatan',
                'status'         => 'active',
                'notes'          => 'Chromatography solvents and molecular biology grade chemicals.',
            ],
            [
                'name'           => 'PT Brataco Chemical',
                'contact_person' => 'Budi Hermanto (Branch Mgr)',
                'email'          => 'sales@brataco.com',
                'phone'          => '+62 21 629 9999',
                'website'        => 'https://www.brataco.com',
                'address'        => 'Jl. Mangga Besar IV No. 22, Jakarta Barat',
                'status'         => 'active',
                'notes'          => 'Bulk industrial grade chemicals and standard laboratory solutions.',
            ],
            [
                'name'           => 'PT Smart-Lab Indonesia',
                'contact_person' => 'Hendra Setiawan',
                'email'          => 'info@smartlab.co.id',
                'phone'          => '+62 21 7588 0205',
                'website'        => 'https://www.smartlab.co.id',
                'address'        => 'Taman Tekno BSD Sektor XI Blok M No. 36, Tangerang Selatan',
                'status'         => 'active',
                'notes'          => 'Domestic chemical manufacturer and analytical reagent distributor.',
            ],
        ];

        foreach ($suppliers as $s) {
            Supplier::updateOrCreate(['name' => $s['name']], $s);
        }
    }
}
