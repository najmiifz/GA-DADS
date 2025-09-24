<?php
$db='database/database.sqlite';
$pdo=new PDO('sqlite:'.$db);
$stmt=$pdo->query('SELECT id, merk, serial_number, created_at FROM assets ORDER BY id DESC LIMIT 20');
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r){ echo implode(' | ', [$r['id'],$r['merk'],$r['serial_number'],$r['created_at']])."\n"; }
