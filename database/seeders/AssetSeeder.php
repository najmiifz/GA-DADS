<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            // Kendaraan
            [
                'tipe' => 'Kendaraan',
                'jenis_aset' => 'Mobil Operasional',
                'status' => 'Available',
                'pic' => 'Ahmad Rizki',
                'merk' => 'Toyota',
                'serial_number' => 'B1234ABC',
                'project' => 'Project Alpha',
                'lokasi' => 'Jakarta Pusat',
                'tahun_beli' => 2023,
                'tanggal_beli' => '2023-01-01',
                'harga_beli' => 250000000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => 'Lunas',
                'total_servis' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipe' => 'Kendaraan',
                'jenis_aset' => 'Motor Operasional',
                'status' => 'Available',
                'pic' => 'Siti Nurhaliza',
                'merk' => 'Honda',
                'serial_number' => 'B5678DEF',
                'project' => 'Project Beta',
                'lokasi' => 'Jakarta Selatan',
                'tahun_beli' => 2022,
                'tanggal_beli' => '2022-01-01',
                'harga_beli' => 25000000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => 'Lunas',
                'total_servis' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipe' => 'Kendaraan',
                'jenis_aset' => 'Truk Angkut',
                'status' => 'Available',
                'pic' => 'Budi Santoso',
                'merk' => 'Mitsubishi',
                'serial_number' => 'B9012GHI',
                'project' => 'Project Gamma',
                'lokasi' => 'Jakarta Timur',
                'tahun_beli' => 2021,
                'tanggal_beli' => '2021-01-01',
                'harga_beli' => 450000000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => 'Belum Lunas',
                'total_servis' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Splicer
            [
                'tipe' => 'Splicer',
                'jenis_aset' => 'Splicer',
                'status' => 'Available',
                'pic' => 'Indra Permana',
                'merk' => 'Fujikura',
                'serial_number' => 'FJK-001',
                'project' => 'Project Alpha',
                'lokasi' => 'Jakarta Pusat',
                'tahun_beli' => 2023,
                'tanggal_beli' => '2023-01-01',
                'harga_beli' => 75000000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => null,
                'total_servis' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipe' => 'Splicer',
                'jenis_aset' => 'Splicer',
                'status' => 'Available',
                'pic' => 'Maya Sari',
                'merk' => 'EXFO',
                'serial_number' => 'EXFO-002',
                'project' => 'Project Beta',
                'lokasi' => 'Jakarta Selatan',
                'tahun_beli' => 2022,
                'tanggal_beli' => '2022-01-01',
                'harga_beli' => 85000000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => null,
                'total_servis' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Elektronik/IT
            [
                'tipe' => 'Elektronik',
                'jenis_aset' => 'Laptop',
                'status' => 'Available',
                'pic' => 'Andi Wijaya',
                'merk' => 'Dell',
                'serial_number' => 'DELL-LT-001',
                'project' => 'Project Alpha',
                'lokasi' => 'Jakarta Pusat',
                'tahun_beli' => 2023,
                'tanggal_beli' => '2023-01-01',
                'harga_beli' => 15000000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => null,
                'total_servis' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipe' => 'Elektronik',
                'jenis_aset' => 'Printer',
                'status' => 'Available',
                'pic' => 'Lisa Permata',
                'merk' => 'Canon',
                'serial_number' => 'CANON-PRN-001',
                'project' => 'Project Beta',
                'lokasi' => 'Jakarta Selatan',
                'tahun_beli' => 2022,
                'tanggal_beli' => '2022-01-01',
                'harga_beli' => 3500000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => null,
                'total_servis' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipe' => 'Elektronik',
                'jenis_aset' => 'Router',
                'status' => 'Available',
                'pic' => 'Dedi Kurniawan',
                'merk' => 'Cisco',
                'serial_number' => 'CISCO-RTR-001',
                'project' => 'Project Gamma',
                'lokasi' => 'Jakarta Timur',
                'tahun_beli' => 2021,
                'tanggal_beli' => '2021-01-01',
                'harga_beli' => 12000000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => null,
                'total_servis' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipe' => 'Elektronik',
                'jenis_aset' => 'Switch',
                'status' => 'Available',
                'pic' => 'Rini Susanti',
                'merk' => 'D-Link',
                'serial_number' => 'DLINK-SW-001',
                'project' => 'Project Alpha',
                'lokasi' => 'Jakarta Utara',
                'tahun_beli' => 2023,
                'tanggal_beli' => '2023-01-01',
                'harga_beli' => 8500000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => null,
                'total_servis' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipe' => 'Elektronik',
                'jenis_aset' => 'Server',
                'status' => 'Tersedia',
                'pic' => 'Agus Firmansyah',
                'merk' => 'HP',
                'serial_number' => 'HP-SRV-001',
                'project' => 'Project Delta',
                'lokasi' => 'Jakarta Pusat',
                'tahun_beli' => 2024,
                'tanggal_beli' => '2024-01-01',
                'harga_beli' => 450000000.00,
                'harga_sewa' => 0.00,
                'status_pajak' => null,
                'total_servis' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('assets')->insert($assets);
    }
}
