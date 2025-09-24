<?php
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    fwrite(STDERR, "Database file not found: $dbPath\n");
    exit(1);
}

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = 15;
$stmt = $pdo->prepare('SELECT * FROM assets WHERE id = :id');
$stmt->execute([':id' => $id]);
$asset = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$asset) {
    echo "Asset id=$id not found\n";
    exit(0);
}

echo "Asset row:\n";
foreach ($asset as $k => $v) {
    echo "- $k: ".($v === null ? 'NULL' : $v)."\n";
}

$svc = $pdo->prepare('SELECT id, description, vendor, service_date, cost, file_path FROM service_histories WHERE asset_id = :id ORDER BY service_date ASC');
$svc->execute([':id' => $id]);
$rows = $svc->fetchAll(PDO::FETCH_ASSOC);
echo "\nService histories (count=".count($rows)."):\n";
foreach ($rows as $r) {
    echo "- id={$r['id']} date={$r['service_date']} cost={$r['cost']} vendor={$r['vendor']} file=".($r['file_path'] ?? '') ." desc=".($r['description'] ?? '')."\n";
}

exit(0);
