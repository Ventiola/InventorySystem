<?php
require_once __DIR__ . '/../../config/connection.php';


$sql = "
    SELECT
        c.id,
        c.code,
        c.name,
        c.created_at
        
    FROM categories c
        ORDER BY c.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<main class="ml-64 p-6 pt-24">
    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Categories</h1>



        <a href="?page=category-create"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Add Category
        </a>


    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-center">#</th>
                    <th class="px-3 py-2 text-center">Code</th>
                    <th class="px-3 py-2 text-center">Name</th>
                    <th class="px-3 py-2 text-center">Created At</th>
                    <th class="px-3 py-2 text-center">Delete</th>

                </tr>
            </thead>
            <tbody>
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                        Category deleted successfully.
                    </div>
                <?php endif; ?>
                <?php if ($products): ?>
                    <?php $no = 1;
                    foreach ($products as $row): ?>

                        <tr class="border-b hover:bg-gray-50">

                            <td class="px-3 py-2 text-center"><?= $no++ ?></td>
                            <td class="px-3 py-2 text-center"><?= htmlspecialchars($row['code']) ?></td>
                            <td class="px-3 py-2 text-center"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="px-3 py-2 text-center text-gray-500">
                                <?= date('Y-m-d H:i', strtotime($row['created_at'])) ?>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <form method="post"
                                    action="?page=category-delete"
                                    onsubmit="return confirm('Delete this category?');">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-800 font-semibold">
                                        Delete
                                    </button>
                                </form>
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