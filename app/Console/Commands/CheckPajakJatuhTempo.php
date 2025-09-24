<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asset;
use App\Models\User;
use App\Notifications\PajakJatuhTempoNotification;
use Carbon\Carbon;

class CheckPajakJatuhTempo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pajak:check-jatuh-tempo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa aset kendaraan yang pajaknya akan jatuh tempo dalam 7 hari dan kirim notifikasi.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mengecek pajak kendaraan yang akan jatuh tempo...');

        $tanggalPeringatan = Carbon::now()->addDays(7)->toDateString();

        $assets = Asset::where('tipe', 'Kendaraan')
                       ->whereDate('tanggal_pajak', $tanggalPeringatan)
                       ->get();

        if ($assets->isEmpty()) {
            $this->info('Tidak ada pajak kendaraan yang jatuh tempo dalam 7 hari ke depan.');
            return;
        }

        $admins = User::where('role', 'admin')->get();

        foreach ($assets as $asset) {
            foreach ($admins as $admin) {
                $admin->notify(new PajakJatuhTempoNotification($asset));
            }
            $this->info("Notifikasi terkirim untuk aset: {$asset->merk} {$asset->model} ({$asset->serial_number})");
        }

        $this->info('Selesai.');
    }
}
