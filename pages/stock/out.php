<?php
require_once __DIR__ . '/../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dates        = trim($_POST['dates'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $note        = trim($_POST['note'] ?? '');
    $productRaw  = $_POST['product_id'] ?? [];
    $qtys        = $_POST['qty'] ?? [];

    if ($dates === '' || $destination === '') {
        die('Dates and Supplier field must be filled');
    }

    if (!is_array($productRaw) || count($productRaw) === 0) {
        die('Product field must be filled');
    }

    try {

        $conn->beginTransaction();

        $refNumber = 'OUT-' . date('YmdHis');

        $stmt = $conn->prepare("
            INSERT INTO stock_out (ref_number, dates, destination, note, created_at)
            VALUES (:ref_number, :dates, :destination, :note, NOW())
            RETURNING id
        ");

        $stmt->execute([
            ':ref_number'  => $refNumber,
            ':dates'        => $dates,
            ':destination' => $destination,
            ':note'        => $note
        ]);

        $stockOutId = $stmt->fetchColumn();

        if (!$stockOutId) {
            throw new Exception('Gagal membuat transaksi stock out');
        }

        foreach ($productRaw as $index => $value) {

            if (!isset($qtys[$index])) continue;

            $parts = explode(' - ', $value);
            $productId = (int) ($parts[0] ?? 0);
            $qty = (int) $qtys[$index];

            if ($productId <= 0 || $qty <= 0) continue;

            $stmtCheck = $conn->prepare("
                SELECT stock FROM products WHERE id = :id FOR UPDATE
            ");
            $stmtCheck->execute([':id' => $productId]);
            $currentStock = $stmtCheck->fetchColumn();

            if ($currentStock === false) {
                throw new Exception("Produk ID $productId not found");
            }

            if ($currentStock < $qty) {
                throw new Exception("Insufficient stock for product ID $productId");
            }

            $stmtDetail = $conn->prepare("
                INSERT INTO stock_out_detail (stock_out_id, product_id, qty)
                VALUES (:stock_out_id, :product_id, :qty)
            ");

            $stmtDetail->execute([
                ':stock_out_id' => $stockOutId,
                ':product_id'   => $productId,
                ':qty'          => $qty
            ]);

            $stmtUpdate = $conn->prepare("
                UPDATE products
                SET stock = stock - :qty
                WHERE id = :id
            ");

            $stmtUpdate->execute([
                ':qty' => $qty,
                ':id'  => $productId
            ]);
        }

        $conn->commit();

        header("Location: ?page=stock-out");
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        die("Error: " . $e->getMessage());
    }
}

$stmt = $conn->prepare("
    SELECT id, name
    FROM products
    WHERE is_active = true
    ORDER BY name ASC
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$today = date('Y-m-d');
?>

<main class="ml-64 p-6 pt-24">

    <h1 class="text-2xl font-bold mb-6">Stock Out</h1>

    <form method="POST" class="space-y-6">

        <div class="bg-white p-6 rounded shadow space-y-4">

            <div class="grid grid-cols-3 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Date</label>
                    <input type="date" name="dates"
                        value="<?= $today ?>"
                        class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Destination</label>
                    <input type="text" name="destination"
                        class="w-full border rounded px-3 py-2"
                         required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Notes</label>
                    <input type="text" name="note" value="Sales"
                        class="w-full border rounded px-3 py-2">
                        
                </div>

            </div>

        </div>

        <div id="itemsContainer" class="space-y-4">

            <div class="bg-white p-6 rounded shadow relative itemRow">

                <button type="button"
                    class="removeBtn hidden absolute top-2 right-3 text-red-600 text-xl font-bold hover:text-red-800">
                    ×
                </button>

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium mb-1">Product</label>
                        <input list="productList"
                            name="product_id[]"
                            class="w-full border rounded px-3 py-2"
                            placeholder="..."
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Qty</label>
                        <input type="number"
                            name="qty[]"
                            min="1"
                            class="w-full border rounded px-3 py-2"
                            required>
                    </div>

                </div>

            </div>

        </div>

        <datalist id="productList">
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?> - <?= htmlspecialchars($p['name']) ?>"></option>
            <?php endforeach; ?>
        </datalist>

        <div class="flex gap-4">

            <button type="button"
                onclick="addItem()"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Add New Form
            </button>

            <button type="submit"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Save Stock Out
           </button>
        </div>
    </form>
</main>

<script>
function addItem() {
    const container = document.getElementById('itemsContainer');
    const firstItem = document.querySelector('.itemRow');
    const clone = firstItem.cloneNode(true);

    clone.querySelectorAll('input').forEach(input => {
        input.value = '';
    });

    const removeBtn = clone.querySelector('.removeBtn');
    removeBtn.classList.remove('hidden');

    container.appendChild(clone);
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeBtn')) {
        const allItems = document.querySelectorAll('.itemRow');
        if (allItems.length > 1) {
            e.target.closest('.itemRow').remove();
        }
    }
});
</script>