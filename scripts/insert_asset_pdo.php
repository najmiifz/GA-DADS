<?php
$db='database/database.sqlite';
$pdo=new PDO('sqlite:'.$db);
$now=(new DateTime())->format('Y-m-d H:i:s');

// Safe default: derive tanggal_beli from tahun_beli as YYYY-01-01
$tahun = 2025;
$tanggal_beli = $tahun . '-01-01';

$sql="INSERT INTO assets (tipe, jenis_aset, pic, merk, serial_number, project, lokasi, tanggal_beli, tahun_beli, harga_beli, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
$stmt=$pdo->prepare($sql);
$ok=$stmt->execute([
    'Elektronik',
    'Core Alignment',
    'John Doe',
    '2313',
    'SN12345',
    'Head Office',
    'Jakarta',
    $tanggal_beli,
    $tahun,
    2312312,
    $now,
    $now
]);
if ($ok) echo "INSERT OK, id=".$pdo->lastInsertId()."\n";
else {
    $err = $pdo->errorInfo();
    echo "INSERT FAILED: ".json_encode($err)."\n";
}
