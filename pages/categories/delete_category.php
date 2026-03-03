<?php
require_once __DIR__ . '/../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ?page=category');
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    header('Location: ?page=category');
    exit;
}

$stmt = $conn->prepare("
    DELETE FROM categories
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

header('Location: ?page=category&deleted=1');
exit;
