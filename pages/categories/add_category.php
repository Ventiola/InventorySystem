<?php
require_once __DIR__ . '/../../config/connection.php';

$errors = [];
$name = '';
$code = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    
    if ($code === ''){
        $errors[] = 'Category codes is required.';
    }
    if ($name === '') {
        $errors[] = 'Category name is required.';
    }

    
    if (!$errors) {
        $check = $conn->prepare("
            SELECT COUNT(*) FROM categories WHERE name = :name
        ");
        $check->execute(['name' => $name]);

        if ($check->fetchColumn() > 0) {
            $errors[] = 'Category already exists.';
        }
    }

    if (!$errors) {
        $stmt = $conn->prepare("
            INSERT INTO categories (code, name)
            VALUES (:code, :name)
        ");

        $stmt->execute([
            'code' => $code,
            'name' => $name,
        ]);

        header('Location: ?page=categories-create&success=1');
        exit;
    }
}
?>

<main class="ml-64 p-6 max-w-xl pt-24">
    <h1 class="text-2xl font-bold mb-4 ">Add Category</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            Category successfully added.
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
                    Category Code
                </label>
                <input type="text"
                       name="code"
                       value="<?= htmlspecialchars($code) ?>"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Category Name
                </label>
                <input type="text"
                       name="name"
                       value="<?= htmlspecialchars($name) ?>"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                       required>
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
