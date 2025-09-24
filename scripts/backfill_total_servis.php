<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    fwrite(STDERR, "Database file not found: $dbPath\n");
    exit(1);
}

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch assets with their current total_servis
$stmt = $pdo->query('SELECT id, serial_number, total_servis FROM assets');
$assets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updated = 0;
$errors = 0;
$changes = [];

foreach ($assets as $a) {
    try {
        $countStmt = $pdo->prepare('SELECT COUNT(*) as cnt FROM service_histories WHERE asset_id = :id');
        $countStmt->execute([':id' => $a['id']]);
        $cnt = (int) $countStmt->fetchColumn();

        $current = is_null($a['total_servis']) ? 0 : (int) $a['total_servis'];
        if ($current !== $cnt) {
            $up = $pdo->prepare('UPDATE assets SET total_servis = :cnt WHERE id = :id');
            $up->execute([':cnt' => $cnt, ':id' => $a['id']]);
            $updated++;
            $changes[] = [
                'id' => $a['id'],
                'serial_number' => $a['serial_number'],
                'before' => $current,
                'after' => $cnt,
            ];
        }
    } catch (Exception $ex) {
        $errors++;
        fwrite(STDERR, "Error processing asset id={$a['id']}: " . $ex->getMessage() . "\n");
    }
}

echo "Backfill complete. Updated rows: $updated. Errors: $errors\n";
if (count($changes) > 0) {
    echo "Sample changes (up to 10):\n";
    $sample = array_slice($changes, 0, 10);
    foreach ($sample as $c) {
        echo "- id={$c['id']} serial={$c['serial_number']} before={$c['before']} after={$c['after']}\n";
    }
}

// Print top 5 assets by total_servis for verification
$top = $pdo->query('SELECT id, serial_number, total_servis FROM assets ORDER BY total_servis DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
echo "Top 5 assets by total_servis:\n";
foreach ($top as $t) {
    echo "- id={$t['id']} serial={$t['serial_number']} total_servis={$t['total_servis']}\n";
}

exit(0);
