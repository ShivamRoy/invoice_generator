<?php
include "../testmysql.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Invoice_Report_FY_25_26.xls");
header("Pragma: no-cache");
header("Expires: 0");

$fyStart = '2025-04-01';
$fyEnd   = '2026-03-31';

$query = "
SELECT 
    invoice_number,
    sedi_name,
    state_name,
    invoice_date,
    invoice_amount,
    payment_received,
    (invoice_amount - payment_received) AS pending_amount
FROM invoices
WHERE invoice_date BETWEEN '$fyStart' AND '$fyEnd'
ORDER BY invoice_date";

$result = $mysqli->query($query);

echo "<table border='1'>";
echo "<tr>
<th>Invoice Number</th>
<th>SEDI Name</th>
<th>State</th>
<th>Date</th>
<th>Invoice Amount (₹)</th>
<th>Payment Received (₹)</th>
<th>Pending (₹)</th>
</tr>";

while($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['invoice_number']}</td>
        <td>{$row['sedi_name']}</td>
        <td>{$row['state_name']}</td>
        <td>{$row['invoice_date']}</td>
        <td>{$row['invoice_amount']}</td>
        <td>{$row['payment_received']}</td>
        <td>{$row['pending_amount']}</td>
    </tr>";
}
echo "</table>";
?>
