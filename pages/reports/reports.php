<?php
require_once __DIR__ . '/../../config/connection.php';

$start = $_GET['start'] ?? date('Y-m-01');
$end   = $_GET['end'] ?? date('Y-m-d');

$stmtIn = $conn->prepare("
SELECT
    si.id,
    si.invoice_number,
    si.dates,
    si.supplier,
    p.name AS product_name,
    sid.qty
FROM stock_in si
JOIN stock_in_detail sid ON sid.stock_in_id = si.id
JOIN products p ON p.id = sid.product_id
WHERE si.dates BETWEEN :start AND :end
ORDER BY si.dates DESC
");

$stmtIn->execute([
    ':start' => $start,
    ':end'   => $end
]);

$stockIn = $stmtIn->fetchAll(PDO::FETCH_ASSOC);


$stmtOut = $conn->prepare("
SELECT
    so.id,
    so.ref_number,
    so.dates,
    so.destination,
    p.name AS product_name,
    sod.qty
FROM stock_out so
JOIN stock_out_detail sod ON sod.stock_out_id = so.id
JOIN products p ON p.id = sod.product_id
WHERE so.dates BETWEEN :start AND :end
ORDER BY so.dates DESC
");

$stmtOut->execute([
    ':start' => $start,
    ':end'   => $end
]);

$stockOut = $stmtOut->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="ml-64 p-6 pt-24">

    <h1 class="text-2xl font-bold mb-6">Reports</h1>

    <!-- FILTER -->
    <form method="GET" class="mb-6 flex gap-4 items-end">

        <input type="hidden" name="page" value="reports">

        <div>
            <label class="block text-sm font-medium">Start Date</label>
            <input type="date" name="start" value="<?= $start ?>" class="border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium">End Date</label>
            <input type="date" name="end" value="<?= $end ?>" class="border rounded px-3 py-2">
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Filter
        </button>

        <a href="?page=print_report&start=<?= $start ?>&end=<?= $end ?>"
            class="bg-red-600 text-white px-4 py-2 rounded">
            Print PDF
        </a>

    </form>


    <div class="bg-white p-6 rounded shadow mb-10">

        <h2 class="text-lg font-bold mb-4">Stock In</h2>

        <table class="w-full border">

            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">Date</th>
                    <th class="border px-3 py-2">Invoice</th>
                    <th class="border px-3 py-2">Supplier</th>
                    <th class="border px-3 py-2">Product</th>
                    <th class="border px-3 py-2">Qty</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($stockIn as $row): ?>

                    <tr>
                        <td class="border px-3 py-2"><?= $row['dates'] ?></td>
                        <td class="border px-3 py-2"><?= $row['invoice_number'] ?></td>
                        <td class="border px-3 py-2"><?= htmlspecialchars($row['supplier']) ?></td>
                        <td class="border px-3 py-2"><?= htmlspecialchars($row['product_name']) ?></td>
                        <td class="border px-3 py-2"><?= $row['qty'] ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>

    </div>


    <div class="bg-white p-6 rounded shadow">

        <h2 class="text-lg font-bold mb-4">Stock Out</h2>

        <table class="w-full border">

            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">Date</th>
                    <th class="border px-3 py-2">Reference</th>
                    <th class="border px-3 py-2">Destination</th>
                    <th class="border px-3 py-2">Product</th>
                    <th class="border px-3 py-2">Qty</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($stockOut as $row): ?>

                    <tr>
                        <td class="border px-3 py-2"><?= $row['dates'] ?></td>
                        <td class="border px-3 py-2"><?= $row['ref_number'] ?></td>
                        <td class="border px-3 py-2"><?= htmlspecialchars($row['destination']) ?></td>
                        <td class="border px-3 py-2"><?= htmlspecialchars($row['product_name']) ?></td>
                        <td class="border px-3 py-2"><?= $row['qty'] ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>

    </div>

</main>