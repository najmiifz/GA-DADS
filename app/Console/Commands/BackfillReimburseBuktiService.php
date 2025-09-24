<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReimburseRequest;
use Illuminate\Support\Facades\Storage;

class BackfillReimburseBuktiService extends Command
{
    protected $signature = 'reimburse:backfill-bukti-service {--dry-run}';

    protected $description = 'Backfill reimburse records: move image files from bukti_struk into foto_bukti_service and keep PDFs in bukti_struk.';

    public function handle()
    {
        $dry = $this->option('dry-run');
        $this->info('Starting backfill of reimburse bukti...');

        $query = ReimburseRequest::query();
        $total = $query->count();
        $this->info("Found {$total} reimburse records to inspect.");

        $processed = 0;
        foreach ($query->cursor() as $req) {
            $bukti = $req->bukti_struk;
            if (!$bukti) continue;

            $decoded = json_decode($bukti, true);
            $files = null;
            if (is_array($decoded)) {
                $files = $decoded;
            } else {
                $files = [$bukti];
            }

            // Separate images and non-images
            $images = [];
            $others = [];
            foreach ($files as $f) {
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png'])) {
                    $images[] = $f;
                } else {
                    $others[] = $f;
                }
            }

            // If there are images, move them to foto_bukti_service
            if (!empty($images)) {
                $this->line("Record {$req->id}: images found (" . count($images) . ")");

                $newBukti = null;
                if (!empty($others)) {
                    // if other files remain (pdfs), keep them in bukti_struk
                    if (count($others) === 1) {
                        $newBukti = $others[0];
                    } else {
                        $newBukti = json_encode(array_values($others));
                    }
                }

                $evidenceExisting = [];
                if ($req->foto_bukti_service) {
                    $exist = json_decode($req->foto_bukti_service, true);
                    if (is_array($exist)) $evidenceExisting = $exist;
                }

                $mergedEvidence = array_values(array_unique(array_merge($evidenceExisting, $images)));

                $this->line(" -> will set foto_bukti_service to " . count($mergedEvidence) . " images");
                if ($newBukti) {
                    $this->line(" -> will set bukti_struk to remaining files: " . (is_array(json_decode($newBukti, true)) ? 'array' : 'single') );
                } else {
                    $this->line(" -> will clear bukti_struk");
                }

                if (!$dry) {
                    $req->bukti_struk = $newBukti;
                    $req->foto_bukti_service = json_encode($mergedEvidence);
                    $req->save();
                }

                $processed++;
            }
        }

        $this->info("Backfill complete. Processed: {$processed} records.");
        if ($dry) {
            $this->info('Dry-run mode, no database changes were made.');
        }

        return 0;
    }
}
