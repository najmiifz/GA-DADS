<?php

namespace App\Services;

use App\Models\PengajuanCounter;
use Illuminate\Support\Facades\DB;

class PengajuanNumberService
{
    /**
     * Allocate next sequence for given type and year_month in a transaction-safe way.
     * Returns formatted nomor: TYPE-YYYYMM-XXXX
     */
    public static function next(string $type, string $yearMonth): string
    {
        return DB::transaction(function () use ($type, $yearMonth) {
            // lock or create the counter row
            $counter = PengajuanCounter::where('type', $type)
                ->where('year_month', $yearMonth)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                $counter = PengajuanCounter::create([
                    'type' => $type,
                    'year_month' => $yearMonth,
                    'last_seq' => 0,
                ]);
            }

            $counter->last_seq = $counter->last_seq + 1;
            $seq = $counter->last_seq;
            $counter->save();

            return strtoupper($type) . '-' . $yearMonth . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
        });
    }
}
