<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServiceRequest;
use App\Models\ApdRequest;
use App\Models\ReimburseRequest;
use App\Models\SpjRequest;
use App\Services\PengajuanNumberService;
use Illuminate\Support\Facades\DB;

class BackfillPengajuanNumbers extends Command
{
    protected $signature = 'pengajuan:backfill {--apply} {--types=}';
    protected $description = 'Backfill nomor_pengajuan for historical records. Use --apply to persist. Default is preview only.';

    public function handle()
    {
        $apply = $this->option('apply');
        $typesOpt = $this->option('types');
        $types = $typesOpt ? array_map('trim', explode(',', $typesOpt)) : ['SR','APD','RBM','SPJ'];

        $this->info(($apply ? 'APPLYING' : 'PREVIEW') . ' backfill for types: ' . implode(',', $types));

        foreach ($types as $type) {
            switch (strtoupper($type)) {
                case 'SR':
                    $this->processModel(ServiceRequest::class, 'SR', $apply);
                    break;
                case 'APD':
                    $this->processModel(ApdRequest::class, 'APD', $apply);
                    break;
                case 'RBM':
                    $this->processModel(ReimburseRequest::class, 'RBM', $apply);
                    break;
                case 'SPJ':
                    $this->processModel(SpjRequest::class, 'SPJ', $apply);
                    break;
                default:
                    $this->warn('Unknown type: ' . $type);
            }
        }

        $this->info('Done.');
        return 0;
    }

    protected function processModel($modelClass, $type, $apply)
    {
        $this->line('Processing type ' . $type . ' -> ' . $modelClass);

        // Group records by year-month of created_at
        $rows = $modelClass::select('id','nomor_pengajuan','created_at')
            ->orderBy('created_at')
            ->get()
            ->groupBy(function($r){ return $r->created_at ? $r->created_at->format('Ym') : date('Ym'); });

        foreach ($rows as $yearMonth => $group) {
            $this->line("  YearMonth: $yearMonth -> records: " . $group->count());
            DB::beginTransaction();
            try {
                foreach ($group as $row) {
                    // Skip if already matches expected format and non-empty
                    $expectedPrefix = strtoupper($type) . '-' . $yearMonth . '-';
                    if ($row->nomor_pengajuan && strpos($row->nomor_pengajuan, $expectedPrefix) === 0) {
                        $this->line('    Skipping id=' . $row->id . ' already ' . $row->nomor_pengajuan);
                        continue;
                    }

                    if ($apply) {
                        // allocate a new nomor via service
                        $new = PengajuanNumberService::next($type, $yearMonth);
                        $modelClass::where('id', $row->id)->update(['nomor_pengajuan' => $new]);
                        $this->line('    Applied id=' . $row->id . ' -> ' . $new);
                    } else {
                        // preview what would be assigned (do not allocate sequence)
                        $this->line('    Preview id=' . $row->id . ' would receive prefix ' . $expectedPrefix);
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Error processing ' . $type . ' ' . $yearMonth . ': ' . $e->getMessage());
            }
        }
    }
}
