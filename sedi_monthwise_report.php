<?php
include "../testmysql.php";


// --- Financial Year Range ---
$fyStart = '2025-04-01';
$fyEnd   = '2026-03-31';

// --- Fetch Filter Options ---
$sediList = $mysqli->query("SELECT DISTINCT sedi_name FROM invoices ORDER BY sedi_name");
$monthList = $mysqli->query("
    SELECT DISTINCT DATE_FORMAT(invoice_date, '%b %Y') AS month
    FROM invoices 
    WHERE invoice_date BETWEEN '$fyStart' AND '$fyEnd'
    ORDER BY invoice_date
");

// --- Fetch Main Data ---
$query = "
SELECT 
    sedi_name,
    DATE_FORMAT(invoice_date, '%b %Y') AS month,
    SUM(invoice_amount) AS total_invoiced,
    SUM(payment_received) AS total_received
FROM invoices
WHERE invoice_date BETWEEN '$fyStart' AND '$fyEnd'
GROUP BY sedi_name, MONTH(invoice_date)
ORDER BY sedi_name, MONTH(invoice_date)";
$result = $mysqli->query($query);
?>

<?php
include "html/sedi_monthwise_report.php";

?> 