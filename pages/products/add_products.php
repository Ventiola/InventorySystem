<?php
require_once __DIR__ . '/../../config/connection.php';

$errors = [];
$name = '';
$code = '';
$category_id = '';
$stock = '';
$price = '';
$active = 'Active';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $active = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : false;


    if ($code === '') {
        $errors[] = 'Category codes is required.';
    }
    if ($name === '') {
        $errors[] = 'Category name is required.';
    }
    if ($category_id === '') {
        $errors[] = 'Category is required.';
    }
    if (!is_numeric($stock)) {
        $errors[] = 'Stock must be numeric.';
    }
    if (!is_numeric($price)) {
        $errors[] = 'Price must be numeric.';
    }




    if (!$errors) {
        $check = $conn->prepare("
            SELECT COUNT(*) FROM products WHERE name = :name
        ");
        $check->execute(['name' => $name]);

        if ($check->fetchColumn() > 0) {
            $errors[] = 'Products already exists.';
        }
    }

    if (!$errors) {
        $stmt = $conn->prepare("
            INSERT INTO products (code, name, category_id, stock, price, is_active)
            VALUES (:code, :name, :category_id, :stock, :price, :is_active)
        ");

        $stmt->execute([
            'code' => $code,
            'name' => $name,
            'category_id' => $category_id,
            'stock' => $stock,
            'price' => $price,
            'is_active' => $active
        ]);

        header('Location: ?page=add_products&success=1');
        exit;
    }
}

$categories = $conn->query("
    SELECT id, name FROM categories ORDER BY name
")->fetchAll();

?>

<main class="ml-64 p-6 max-w-xl pt-24">
    <h1 class="text-2xl font-bold mb-4 ">Add Product</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            Product successfully added.
        </div>
    <?php endif; ?>

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
                <label class="block text-sm font-medium mb-1">
                    Product Code
                </label>
                <input type="text"
                    name="code"
                    value="<?= htmlspecialchars($code) ?>"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Product Name
                </label>
                <input type="text"
                    name="name"
                    value="<?= htmlspecialchars($name) ?>"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Category
                </label>

                <select
                    name="category_id"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                    required>

                    <option value="">-- Select Category --</option>

                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= $category_id == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Stock
                </label>
                <input type="number"
                    name="stock"
                    value="<?= htmlspecialchars($stock) ?>"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Price
                </label>
                <input type="number"
                    name="price"
                    value="<?= htmlspecialchars($price) ?>"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Is Active
                </label>

                <select
                    name="is_active"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                    required>

                    <option value="1" <?= $active === 'Active' ? 'selected' : '' ?>>
                        Active
                    </option>
                    <option value="0" <?= $active === 'Inactive' ? 'selected' : '' ?>>
                        Inactive
                    </option>

                </select>
            </div>


            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Save
                </button>

                <a href="?page=dashboard"
                    class="px-4 py-2 border rounded hover:bg-gray-100">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>