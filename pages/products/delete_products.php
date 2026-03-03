<?php
require_once __DIR__ . '/../../config/connection.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: ?page=products");
    exit;
}

$check = $conn->prepare("SELECT id FROM products WHERE id = :id");
$check->execute(['id' => $id]);

if (!$check->fetch()) {
    header("Location: ?page=products");
    exit;
}

// Hapus
$stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);

header("Location: ?page=products");
exit;
