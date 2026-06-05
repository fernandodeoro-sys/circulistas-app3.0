<?php

try {
    $pdo = new PDO(
        "pgsql:host=192.168.30.254;port=5432;dbname=Padron_MCJ_BD",
        "postgres",
        "otIlE4NK93e6Mm2UzL20GX5V6cp6lvCSROYoNYa0LJYnmwkKmpMQ1ORd72xwnv0x"
    );

    echo "Conexión OK";
} catch (Exception $e) {
    echo $e->getMessage();
}