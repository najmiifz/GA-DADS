<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
$stmt = $db->query("SELECT COALESCE(status, '') as status, COUNT(*) as c FROM assets GROUP BY status ORDER BY c DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) {
    echo "No rows\n";
    exit(0);
}
foreach ($rows as $r) {
    $s = $r['status'] === '' ? '<empty>' : $r['status'];
    echo $s . " | " . $r['c'] . "\n";
}
