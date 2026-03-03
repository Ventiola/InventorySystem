<?php
require_once __DIR__ . '/../../config/connection.php';

$stmt = $conn->prepare("
    SELECT id, name     
    FROM products 
    WHERE is_active
    ORDER BY name ASC
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


$today = date('Y-m-d');
?>

<main class="ml-64 p-6 pt-24">

    <h1 class="text-2xl font-bold mb-6">Stock In</h1>

    <form method="POST" action="?page=stock-in-save" class="space-y-6">

        <div class="bg-white p-6 rounded shadow space-y-4">

            <div class="grid grid-cols-3 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal</label>
                    <input type="date" name="tanggal"
                        value="<?= $today ?>"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Supplier</label>
                    <input type="text" name="supplier"
                        id="supplierInput"
                        class="w-full border rounded px-3 py-2"
                        placeholder="Nama Supplier">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Keterangan</label>
                    <input type="text" name="keterangan"
                        id="keteranganInput"
                        value="Pembelian"
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
                        <label class="block text-sm font-medium mb-1">Produk</label>
                        <input list="productList"
                            name="product_id[]"
                            class="w-full border rounded px-3 py-2 productInput"
                            placeholder="Ketik minimal 3 huruf..."
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
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                + Tambah Form
            </button>

            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Simpan Stock In
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