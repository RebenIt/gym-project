<?php
require_once 'includes/config.php';
$tables = db()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
foreach($tables as $table) {
    echo $table . "\n";
}
?>
