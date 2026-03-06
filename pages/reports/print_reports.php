<?php
ob_start();

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;

$start = $_GET['start'] ?? date('Y-m-01');
$end   = $_GET['end'] ?? date('Y-m-d');

$stmt = $conn->prepare("
SELECT p.name, sid.qty, si.dates, si.invoice_number
FROM stock_in_detail sid
JOIN stock_in si ON sid.stock_in_id = si.id
JOIN products p ON sid.product_id = p.id
WHERE si.dates BETWEEN :start AND :end
ORDER BY si.dates DESC
");
$stmt->execute([':start' => $start, ':end' => $end]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<h2 style="text-align:center;">Stock In Report</h2>
<p>Period: '.$start.' - '.$end.'</p>
<table border="1" width="100%" cellpadding="5" cellspacing="0">
<tr><th>Date</th><th>Invoice</th><th>Product</th><th>Qty</th></tr>
';

foreach ($data as $row) {
    $html .= "
    <tr>
        <td>" . htmlspecialchars($row['dates']) . "</td>
        <td>" . htmlspecialchars($row['invoice_number']) . "</td>
        <td>" . htmlspecialchars($row['name']) . "</td>
        <td>" . htmlspecialchars($row['qty']) . "</td>
    </tr>";
}

$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

ob_end_clean();
$dompdf->stream("report_stock_in.pdf", ["Attachment" => true]);
exit;