<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApdRequest;
use App\Models\User;

class ApdRequestSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please create users first.');
            return;
        }

        $clusters = ['Cluster A', 'Cluster B', 'Cluster C', 'Cluster D'];
        $statuses = ['pending', 'approved', 'rejected'];
        $mandors = ['Tim Mandor 1', 'Tim Mandor 2', 'Tim Mandor 3'];

        for ($i = 1; $i <= 20; $i++) {
            ApdRequest::create([
                'nomor_pengajuan' => 'APD-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => $users->random()->id,
                'team_mandor' => $mandors[array_rand($mandors)],
                'jumlah_apd' => rand(5, 50),
                'nama_cluster' => $clusters[array_rand($clusters)],
                'status' => $statuses[array_rand($statuses)],
                'approved_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('APD requests seeded successfully.');
    }
}
