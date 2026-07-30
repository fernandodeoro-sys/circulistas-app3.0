<?php

try {
    $pdo = new PDO(
        "pgsql:host=localhost;port=5432;dbname=Padron_MCJ_BD",
        "postgres",
        "pelado86"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = ['circulistas', 'eventos', 'participaciones', 'roles', 'tipos_evento', 'users'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SELECT setval(pg_get_serial_sequence(:table, 'id'), COALESCE(MAX(id), 1)) FROM " . $table);
        $stmt->execute(['table' => $table]);
        echo "Reset sequence for $table successfully.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}