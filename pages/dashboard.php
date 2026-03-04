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
        l.product_id,
        l.qty,
        l.type
    FROM stock_log l
    JOIN products p ON p.id = l.product_id
    ORDER BY l.created_at DESC
    LIMIT 5
")->fetchAll();
?>

<main class="ml-64 p-6 pt-24">

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Dashboard</h1>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded shadow p-4">
            <p class="text-sm text-gray-500">Total Products</p>
            <p class="text-2xl font-bold"><?= $totalProduct ?></p>
        </div>

        <div class="bg-white rounded shadow p-4">
            <p class="text-sm text-gray-500">Total Categories</p>
            <p class="text-2xl font-bold"><?= $totalCategory ?></p>
        </div>

        <div class="bg-white rounded shadow p-4">
            <p class="text-sm text-gray-500">Total Stock</p>
            <p class="text-2xl font-bold"><?= $totalStock ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <a href="?page=products-create"
            class="bg-blue-600 text-white rounded p-4 hover:bg-blue-700">
            List Product
        </a>

        <a href="?page=categories-create"
            class="bg-green-600 text-white rounded p-4 hover:bg-green-700">
            List Category
        </a>

        <a href="?page=stock-in"
            class="bg-indigo-600 text-white rounded p-4 hover:bg-indigo-700">
            Stock In
        </a>

        <a href="?page=stock-out"
            class="bg-rose-600 text-white rounded p-4 hover:bg-rose-700">
            Stock Out
        </a>
    </div>

    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-4">Recent Stock Activity</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Type</th>
                    <th class="text-left">Product</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recentLog) > 0): ?>
                    <?php foreach ($recentLog as $log): ?>
                        <tr class="border-b">
                            <td class="py-2">
                                <?= $log['type'] === 'IN' ? 'IN' : 'OUT' ?>
                            </td>
                            <td><?= htmlspecialchars($log['name']) ?></td>
                            <td class="text-right"><?= $log['quantity'] ?></td>
                            <td class="text-right text-gray-500">
                                <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">
                            ...
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>