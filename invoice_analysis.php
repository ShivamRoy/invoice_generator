<?php
include "../testmysql.php";
?>

<?php


// --- Financial Year filter (Apr 2025 - Mar 2026) ---
$fyStart = '2025-04-01';
$fyEnd   = '2026-03-31';

// --- Fetch overall totals for FY 25-26 ---
$summaryQuery = "
SELECT 
    SUM(invoice_amount) AS total_invoiced,
    SUM(payment_received) AS total_received
FROM invoices
WHERE invoice_date BETWEEN '$fyStart' AND '$fyEnd'";
$summaryResult = $mysqli->query($summaryQuery);
$summary = $summaryResult->fetch_assoc();
$totalInvoiced = $summary['total_invoiced'] ?? 0;
$totalReceived = $summary['total_received'] ?? 0;

// --- SEDI-wise data ---
$sediQuery = "
SELECT 
    sedi_name,
    COUNT(invoice_id) AS total_invoices,
    GROUP_CONCAT(invoice_number SEPARATOR ', ') AS invoice_numbers,
    SUM(invoice_amount) AS total_invoiced,
    SUM(payment_received) AS total_received,
    (SUM(invoice_amount) - SUM(payment_received)) AS total_pending
FROM invoices
WHERE invoice_date BETWEEN '$fyStart' AND '$fyEnd'
GROUP BY sedi_name
ORDER BY total_invoiced DESC";
$sediResult = $mysqli->query($sediQuery);

// --- Month-wise data ---
$monthQuery = "
SELECT 
    DATE_FORMAT(invoice_date, '%Y-%m') AS month,
    COUNT(invoice_id) AS total_invoices,
    SUM(invoice_amount) AS total_invoiced,
    SUM(payment_received) AS total_received,
    (SUM(invoice_amount) - SUM(payment_received)) AS total_pending
FROM invoices
WHERE invoice_date BETWEEN '$fyStart' AND '$fyEnd'
GROUP BY month
ORDER BY month";
$monthResult = $mysqli->query($monthQuery);
?>


<?php
include "html/invoice_analysis.php";

?> 