<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReimburseRequest;
use App\Models\User;
use App\Models\Asset;

class ReimburseRequestSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $assets = Asset::where('jenis_aset', 'motor')->get();

        if ($users->isEmpty() || $assets->isEmpty()) {
            $this->command->warn('No users or motor assets found. Please create them first.');
            return;
        }

        $statuses = ['pending', 'approved', 'rejected'];

        for ($i = 1; $i <= 15; $i++) {
            $tanggalService = now()->subDays(rand(1, 90));

            $r = ReimburseRequest::create([
                // nomor pengajuan will be normalized after create to keep uniqueness and ordering
                'user_id' => $users->random()->id,
                'asset_id' => $assets->random()->id,
                'biaya' => rand(50000, 500000),
                'keterangan' => 'Service ' . ['rutin', 'perbaikan mesin', 'ganti oli', 'tune up', 'perbaikan rem'][array_rand(['rutin', 'perbaikan mesin', 'ganti oli', 'tune up', 'perbaikan rem'])],
                'tanggal_service' => $tanggalService,
                'status' => $statuses[array_rand($statuses)],
                'approved_at' => rand(0, 1) ? $tanggalService->addDays(rand(1, 7)) : null,
                'created_at' => $tanggalService->addDays(rand(1, 3)),
                'updated_at' => $tanggalService->addDays(rand(1, 10)),
            ]);

            // assign structured nomor_pengajuan based on created id
            $r->nomor_pengajuan = 'RBM-' . $tanggalService->format('Ym') . '-' . str_pad($r->id, 4, '0', STR_PAD_LEFT);
            $r->save();
        }

        $this->command->info('Reimburse requests seeded successfully.');
    }
}
