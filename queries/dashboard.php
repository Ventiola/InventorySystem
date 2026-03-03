<?php

require_once __DIR__ . '/../config/connection.php';

$totalProduct = $conn->query("
    SELECT COUNT(*) FROM products
")->fetchColumn();

$totalCategory = $conn->query("
    SELECT COUNT(*) FROM categories
")->fetchColumn();

$totalStock = $conn->query("
    SELECT COALESCE(SUM(stock), 0) FROM products
")->fetchColumn();

$recentLog = $conn->query("
    SELECT 
        l.type,
        p.name,
        l.quantity,
        l.created_at
    FROM stock_log l
    JOIN products p ON p.id = l.product_id
    ORDER BY l.created_at DESC
    LIMIT 5
")->fetchAll();
