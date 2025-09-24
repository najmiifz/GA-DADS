<?php

namespace App\Observers;

use App\Models\Asset;

class AssetObserver
{
    /**
     * Handle the Asset "created" event.
     */
    public function created(Asset $asset): void
    {
        // If serial_number already provided, keep it
        if (!empty($asset->serial_number)) return;

        $codes = [
            'Laptop'    => 'LAP',
            'Handphone' => 'HP',
            'Splicer'   => 'SPLCR',
            'Otdr'      => 'OTDR',
            'Ols'       => 'OLS',
            'Opm'       => 'OPM',
            'Motor'     => 'MTR',
            'Mobil'     => 'MBL',
            'Furniture' => 'FRNTR',
        ];

        $jenis = $asset->jenis_aset ?? null;
        $code = $codes[$jenis] ?? strtoupper(substr(preg_replace('/\s+/', '', (string)$jenis), 0, 3));
        $prefix = str_pad($asset->id, 3, '0', STR_PAD_LEFT);
        $asset->serial_number = "{$prefix}/{$code}";

        // Use saveQuietly to avoid firing observers again
        $asset->saveQuietly();
    }
}
