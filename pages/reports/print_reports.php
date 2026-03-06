<?php
ob_start();

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;

$start = $_GET['start'] ?? date('Y-m-01');
$end   = $_GET['end'] ?? date('Y-m-d');
$type  = $_GET['type'] ?? 'stock_in'; // 👈 read the type

if ($type === 'stock_out') {

    $stmt = $conn->prepare("
        SELECT so.ref_number, so.dates, so.destination, p.name, sod.qty
        FROM stock_out so
        JOIN stock_out_detail sod ON sod.stock_out_id = so.id
        JOIN products p ON p.id = sod.product_id
        WHERE so.dates BETWEEN :start AND :end
        ORDER BY so.dates DESC
    ");
    $stmt->execute([':start' => $start, ':end' => $end]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '
    <h2 style="text-align:center;">Stock Out Report</h2>
    <p style="text-align:center;">Period: '.$start.' - '.$end.'</p>
    <table border="1" width="100%" cellpadding="5" cellspacing="0">
    <tr><th>Date</th><th>Reference</th><th>Destination</th><th>Product</th><th>Qty</th></tr>
    ';
    foreach ($data as $row) {
        $html .= "
        <tr>
            <td>" . htmlspecialchars($row['dates']) . "</td>
            <td>" . htmlspecialchars($row['ref_number']) . "</td>
            <td>" . htmlspecialchars($row['destination']) . "</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['qty']) . "</td>
        </tr>";
    }
    $html .= '</table>';
    $filename = 'report_stock_out.pdf';

} else {

    $stmt = $conn->prepare("
        SELECT si.invoice_number, si.dates, si.supplier, p.name, sid.qty
        FROM stock_in si
        JOIN stock_in_detail sid ON sid.stock_in_id = si.id
        JOIN products p ON p.id = sid.product_id
        WHERE si.dates BETWEEN :start AND :end
        ORDER BY si.dates DESC
    ");
    $stmt->execute([':start' => $start, ':end' => $end]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '
    <h2 style="text-align:center;">Stock In Report</h2>
    <p style="text-align:center;">Period: '.$start.' - '.$end.'</p>
    <table border="1" width="100%" cellpadding="5" cellspacing="0">
    <tr><th>Date</th><th>Invoice</th><th>Supplier</th><th>Product</th><th>Qty</th></tr>
    ';
    foreach ($data as $row) {
        $html .= "
        <tr>
            <td>" . htmlspecialchars($row['dates']) . "</td>
            <td>" . htmlspecialchars($row['invoice_number']) . "</td>
            <td>" . htmlspecialchars($row['supplier']) . "</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['qty']) . "</td>
        </tr>";
    }
    $html .= '</table>';
    $filename = 'report_stock_in.pdf';
}

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

ob_end_clean();
$dompdf->stream($filename, ["Attachment" => true]);
exit;