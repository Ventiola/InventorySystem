<?php
$host = "localhost";
$port = "5432";
$dbname = "db_inventory";
$user = "postgres"; 
$password = "Postgre123"; 

try {
    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Connection Fail: " . $e->getMessage());
}
