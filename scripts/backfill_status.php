<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
$updated = $db->exec("UPDATE assets SET status='Available' WHERE status IS NULL OR status='' ");
echo "Updated rows: " . ($updated === false ? 0 : $updated) . "\n";
