<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
$stmt = $db->query("PRAGMA table_info('assets')");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['cid'] . ' | ' . $r['name'] . ' | ' . $r['type'] . "\n";
}
