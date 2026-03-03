<?php
require_once __DIR__ . '/../../config/connection.php';

$sql = "
    SELECT
        p.id,
        p.code,
        p.name,
        p.stock,
        p.price,
        p.is_active,
        p.created_at,
        c.name AS category_name
    FROM products p
    JOIN categories c ON c.id = p.category_id
    ORDER BY p.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<main class="ml-64 p-6 pt-24">
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Products</h1>

        <div class="flex gap-4">

            <a href="?page=add_products"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Add Product
            </a>

        </div>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">#</th>
                    <th class="px-3 py-2 text-left">Code</th>
                    <th class="px-3 py-2 text-left">Name</th>
                    <th class="px-3 py-2 text-left">Category</th>
                    <th class="px-3 py-2 text-right">Stock</th>
                    <th class="px-3 py-2 text-right">Price</th>
                    <th class="px-3 py-2 text-right">Status</th>
                    <th class="px-3 py-2 text-right">Created At</th>
                    <th class="px-3 py-2 text-lrft">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products): ?>
                    <?php $no = 1;
                    foreach ($products as $row): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2"><?= $no++ ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($row['code']) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($row['category_name']) ?></td>
                            <td class="px-3 py-2 text-right"><?= (int)$row['stock'] ?></td>
                            <td class="px-3 py-2 text-right">
                                <?= number_format($row['price'], 0, ',', '.') ?>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="<?= $row['is_active'] ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right text-gray-500">
                                <?= date('Y-m-d H:i', strtotime($row['created_at'])) ?>
                            </td>
                            <td class=" px-3 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="?page=edit_products&id=<?= $row['id'] ?>"
                                        class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-xs">
                                        Edit
                                    </a>
                                    <a href="?page=delete_products&id=<?= $row['id'] ?>"
                                        onclick="return confirm('Yakin mau hapus produk ini?')"
                                        class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 transition">
                                        Delete
                                    </a>

                                </div>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-gray-500">
                            No data found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>