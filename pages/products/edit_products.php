<?php
require_once __DIR__ . '/../../config/connection.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: ?page=products');
    exit;
}

$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE id = :id
");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: ?page=products');
    exit;
}

$categories = $conn->query("
    SELECT id, name FROM categories ORDER BY name
")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code        = trim($_POST['code'] ?? '');
    $name        = trim($_POST['name'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $stock       = trim($_POST['stock'] ?? '');
    $price       = trim($_POST['price'] ?? '');
    $is_active   = $_POST['is_active'] ?? '1';

    if ($code === '') $errors[] = 'Product code is required.';
    if ($name === '') $errors[] = 'Product name is required.';
    if ($category_id === '') $errors[] = 'Category is required.';
    if (!is_numeric($stock)) $errors[] = 'Stock must be numeric.';
    if (!is_numeric($price)) $errors[] = 'Price must be numeric.';

    if (!$errors) {
        $update = $conn->prepare("
            UPDATE products SET
                code = :code,
                name = :name,
                category_id = :category_id,
                stock = :stock,
                price = :price,
                is_active = :is_active
            WHERE id = :id
        ");

        $update->execute([
            'code'        => $code,
            'name'        => $name,
            'category_id' => $category_id,
            'stock'       => $stock,
            'price'       => $price,
            'is_active'   => (bool)$is_active,
            'id'          => $id
        ]);

        header('Location: ?page=products&updated=1');
        exit;
    }
}
?>

<main class="ml-64 p-6 pt-24 max-w-xl">
    <h1 class="text-2xl font-bold mb-4">Edit Product</h1>

    <?php if ($errors): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white p-4 rounded shadow">
        <form method="post" class="space-y-4">

            <div>
                <label class="block text-sm font-medium mb-1">Product Code</label>
                <input type="text" name="code"
                    value="<?= htmlspecialchars($product['code']) ?>"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Product Name</label>
                <input type="text" name="name"
                    value="<?= htmlspecialchars($product['name']) ?>"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Category</label>
                <select name="category_id"
                    class="w-full border rounded px-3 py-2" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Stock</label>
                <input type="number" name="stock"
                    value="<?= (int)$product['stock'] ?>"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input type="number" name="price"
                    value="<?= (int)$product['price'] ?>"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <select name="is_active"
                    class="w-full border rounded px-3 py-2">
                    <option value="1" <?= $product['is_active'] ? 'selected' : '' ?>>
                        Active
                    </option>
                    <option value="0" <?= !$product['is_active'] ? 'selected' : '' ?>>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Update
                </button>
                <a href="?page=products"
                   class="px-4 py-2 border rounded hover:bg-gray-100">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</main>
