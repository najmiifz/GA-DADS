<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    fwrite(STDERR, "Database file not found: $dbPath\n");
    exit(1);
}

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Find assets where tanggal_beli is NULL but tahun_beli is set
$rows = $pdo->query("SELECT id, tahun_beli FROM assets WHERE (tanggal_beli IS NULL OR tanggal_beli = '') AND tahun_beli IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);

$updated = 0;
foreach ($rows as $r) {
    $id = $r['id'];
    $year = (int)$r['tahun_beli'];
    if ($year > 0 && $year <= 9999) {
        $date = sprintf('%04d-01-01', $year);
        $up = $pdo->prepare('UPDATE assets SET tanggal_beli = :d WHERE id = :id');
        $up->execute([':d' => $date, ':id' => $id]);
        $updated++;
    }
}

echo "Backfill tanggal_beli complete. Rows found: " . count($rows) . ", updated: $updated\n";
if ($updated > 0) {
    $sample = $pdo->query('SELECT id, tahun_beli, tanggal_beli FROM assets WHERE tanggal_beli IS NOT NULL ORDER BY id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
    echo "Sample updates:\n";
    foreach ($sample as $s) {
        echo "- id={$s['id']} tahun_beli={$s['tahun_beli']} tanggal_beli={$s['tanggal_beli']}\n";
    }
}

exit(0);
