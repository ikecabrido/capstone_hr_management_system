<?php
require_once __DIR__ . '/config/db.php';

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "=== $table ===\n";
    $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        $info = $col['Field'] . ' (' . $col['Type'] . ')';
        if ($col['Null'] == 'NO') $info .= ' NOT NULL';
        if ($col['Key'] == 'PRI') $info .= ' PRIMARY KEY';
        if ($col['Key'] == 'MUL') $info .= ' FOREIGN KEY';
        echo "  " . $info . "\n";
    }
    echo "\n";
}
?>
