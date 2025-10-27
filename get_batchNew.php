<?php
include '../testmysql.php';
include "../Invoice_generator/dbfunctions.php";
require('libs/fpdf.php');

if (isset($_GET['ids'])) {
    // 1. Collect IDs from GET
    $ids = explode(",", $_GET['ids']);
    $ids = array_map('intval', $ids); // sanitize
    $idList = implode(",", $ids);

    if (empty($idList)) {
        die("No IDs provided!");
    }

    // 2. Get the last invoice number
    $result = $mysqli->query("SELECT invoice_number FROM invoices_data ORDER BY id DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_invoice = $row['invoice_number'];
        $parts = explode('-', $last_invoice);
        $number = (int)substr($parts[2], -4);
        $number++;
        $new_invoice_number = $parts[0] . '-' . $parts[1] . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    } else {
        $new_invoice_number = "INVOICE #2025-AMB-0001";
    }

    // 3. Fetch only selected rows
    $query = "SELECT * FROM make_generator WHERE id IN ($idList)";
    $query_inv_res = getData($mysqli, $query, "array");
    $query_inv_row = count($query_inv_res);

    if ($query_inv_row === 0) {
        die("No records found for selected IDs!");
    }

    // 4. Calculate totals
    $total = 0;
    $total_users = 0;
    foreach ($query_inv_res as $row) {
        $userAttempts = $row['User Attempts'];
        $amount = $userAttempts * 13;
        $total += $amount;
        $total_users += $userAttempts;
    }

    $tax = $total * 0.18;
    $totalDue = $total + $tax;

    // 5. Insert invoice record into DB
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    $assements_count = $query_inv_row; // number of selected exams
    $state = $query_inv_res[0]['State']; // pick first row's state

    $stmt = $mysqli->prepare("INSERT INTO invoices_data (invoice_number, sedi, state, assements_count, user_attempts, total_amount, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiidss", $new_invoice_number, $query_inv_res[0]['SEDI'], $state, $assements_count, $total_users, $totalDue, $created_at, $updated_at);
    $stmt->execute();

    // 6. Start generating PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 10);

    // Company Info
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 6, "AADDOO Softtech Pvt. Ltd.", 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, 'Plot No. C-5,', 0, 1);
    $pdf->Cell(0, 6, 'Institutional Area, Sector-6, 134109', 0, 1);
    $pdf->Cell(0, 6, 'Panchkula - 134109', 0, 1);

    // Invoice header
    $pdf->SetXY(140, 10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(169, 169, 169);
    $pdf->Cell(0, 6, 'INVOICE', 0, 1, 'R');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(140, 20);
    $pdf->Cell(0, 6, $new_invoice_number, 0, 1, 'R');
    $pdf->Cell(0, 6, 'DATE: ' . date('F d, Y'), 0, 1, 'R');
    $pdf->Ln(5);

    // Client Info
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, 'TO:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, "Ambuja Foundation", 0, 1);
    $pdf->Cell(0, 6, 'SEDI ' . $query_inv_res[0]['SEDI'] . ' - ' . $query_inv_res[0]['PIN'], 0, 1);
    $pdf->Cell(0, 6, $query_inv_res[0]['State']);
    $pdf->Ln(10);

    // Table Header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(30, 10, 'QUANTITY', 1, 0, 'C', true);
    $pdf->Cell(130, 10, 'DESCRIPTION', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'TOTAL', 1, 1, 'C', true);

    // Table Rows
    $pdf->SetFont('Arial', '', 10);
    foreach ($query_inv_res as $row) {
        $batch = $row['Batch'];
        $exam = $row['Exam'];
        $userAttempts = $row['User Attempts'];
        $examDate = $row['Exam Date'];
        $date = new DateTime($examDate);
        $formattedDate = $date->format('d-m-Y');
        $amount = $userAttempts * 13;

        $pdf->Cell(30, 10, $userAttempts . ' Users', 1);
        $pdf->Cell(130, 10, "Exam for $batch on $formattedDate [Rs.13 per user] ($userAttempts*13)", 1);
        $pdf->Cell(30, 10, "Rs. " . number_format($amount, 2), 1, 1, 'R');
    }

    // Totals
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 10, 'Total Users: ' . $total_users, 1);
    $pdf->Cell(130, 10, 'SUBTOTAL', 1);
    $pdf->Cell(30, 10, "Rs. " . number_format($total, 2), 1, 1, 'R');
    $pdf->Cell(160, 10, 'GST (18%)', 1);
    $pdf->Cell(30, 10, "Rs. " . number_format($tax, 2), 1, 1, 'R');
    $pdf->Cell(160, 10, 'TOTAL DUE', 1);
    $pdf->Cell(30, 10, "Rs. " . number_format($totalDue, 2), 1, 1, 'R');

    // Footer
    $pdf->Ln(10);
    $startX = 10;
    $startY = $pdf->GetY();
    $boxWidth = $pdf->GetPageWidth() - 20;

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Make all checks payable to AADDOO Softtech Private Limited', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Account Details:', 0, 1);
    $imageX = $startX + $boxWidth - 40;
    $imageY = $pdf->GetY() - 5;
    $pdf->Cell(100, 5, 'Bank: PNB Solan', 0, 0);
    $pdf->Image('aaddoo_stamp.jpg', $imageX, $imageY, 30, 30);
    $pdf->Ln(10);
    $pdf->Cell(0, 5, 'Account Number: 0433002100076170', 0, 1);
    $pdf->Cell(0, 5, 'IFSC: PUNB0043300', 0, 1);
    $pdf->Cell(0, 5, 'GSTIN: 02AAQCA0032M2ZW', 0, 1);
    $pdf->Cell(0, 5, 'PAN: AAQCA0032M', 0, 1);

    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Payment is due immediately.', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 6, 'Thank you for your business!', 0, 1);

    $endY = $pdf->GetY();
    $pdf->Rect($startX - 2, $startY - 2, $boxWidth + 4, $endY - $startY + 4);

    // Logo at bottom
    $pdf->Ln(10);
    $pageWidth = $pdf->GetPageWidth();
    $logoWidth = 40;
    $logoX = ($pageWidth - $logoWidth) / 2;
    $pdf->Image('aaddoo.ai.jpg', $logoX, null, $logoWidth);

    // 7. Show PDF in browser (preview, not download)
    $pdf->Output('I', 'invoice_preview.pdf'); // 'I' means inline preview
}
else {
    echo "No IDs selected!";
}
?>
