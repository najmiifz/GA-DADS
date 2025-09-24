<?php
$db='database/database.sqlite';
$pdo=new PDO('sqlite:'.$db);
$stmt=$pdo->query("PRAGMA table_info('assets')");
$cols=$stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c){
    echo $c['cid'].' | '.$c['name'].' | '.$c['type']."\n";
}
