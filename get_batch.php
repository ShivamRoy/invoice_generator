<?php
include '../testmysql.php';
include "../Invoice_generator/dbfunctions.php";
require('libs/fpdf.php');

if (isset($_GET['sedi'])) {
    $sedi = $_GET['sedi'];

    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // Get the last invoice number
    $result = $mysqli->query("SELECT invoice_number FROM invoices_data ORDER BY id DESC LIMIT 1");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_invoice = $row['invoice_number'];

        // Extract the numeric part and increment it
        $parts = explode('-', $last_invoice);
        $number = (int)substr($parts[2], -4);
        $number++;
        $new_invoice_number = $parts[0] . '-' . $parts[1] . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    } else {
        $new_invoice_number = "INVOICE #2024-AMB-0001";
    }

    // Fetch data related to the selected SEDI
    $query = "SELECT * FROM make_generator WHERE `SEDI` = '$sedi'";
    $query_inv_res = getData($mysqli, $query, "array");
    $query_inv_row = count($query_inv_res);

    // Calculate totals
    $total = 0;
    $total_users = 0;

    for ($i = 0; $i < $query_inv_row; $i++) {
        $userAttempts = $query_inv_res[$i]['User Attempts'];
        $amount = $userAttempts * 13;
        $total += $amount;
        $total_users += $userAttempts;
    }

    // Subtotal, Tax, Total
    $tax = $total * 0.18;
    $totalDue = $total + $tax;

    // Insert data into database
    $assements_count = $query_inv_row; // Number of exams
    $state = $query_inv_res[0]['State']; // Assuming 'State' is available in the fetched data
    $stmt = $mysqli->prepare("INSERT INTO invoices_data (invoice_number, sedi, state, assements_count, user_attempts, total_amount, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiidss", $new_invoice_number, $sedi, $state, $assements_count, $total_users, $totalDue, $created_at, $updated_at);
    $stmt->execute();

    // Start generating PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 10);
    // Colors and font setup
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 14);
    // Company information on the left
    $pdf->Cell(0, 6, "AADDOO Softtech Pvt. Ltd.", 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, 'Plot No. C-5,', 0, 1);
    $pdf->Cell(0, 6, 'Institutional Area, Sector-6, 134109', 0, 1);
    $pdf->Cell(0, 6, 'Panchkula - 134109', 0, 1);
    // Move cursor up to the top right of the page for "INVOICE" information
    $pdf->SetXY(140, 10);
    $pdf->SetTextColor(169, 169, 169);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, 'INVOICE', 0, 1, 'R');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(140, 20);
    $pdf->Cell(0, 6, $new_invoice_number, 0, 1, 'R');
    $pdf->Cell(0, 6, 'DATE: ' . date('F d, Y'), 0, 1, 'R');
    $pdf->Ln(5);
    // Client Information
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

    // Table Content
    $pdf->SetFont('Arial', '', 10);
    for ($i = 0; $i < $query_inv_row; $i++) {
        $batch = $query_inv_res[$i]['Batch'];
        $exam = $query_inv_res[$i]['Exam'];
        $userAttempts = $query_inv_res[$i]['User Attempts'];
        $examDate = $query_inv_res[$i]['Exam Date'];
        $date = new DateTime($examDate);
        $formattedDate = $date->format('d-m-Y');
        $amount = $userAttempts * 13;
        $pdf->Cell(30, 10, $userAttempts . ' Users', 1);
        $pdf->Cell(130, 10, "Exam for $batch on $formattedDate [Rs.13 per user] ($userAttempts*13)", 1);
        $pdf->Cell(30, 10, "Rs. " . number_format($amount, 2), 1, 1, 'R');
    }

    // Subtotal, Tax, Total
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 10, 'Total Users: ' . $total_users, 1);
    $pdf->SetX($pdf->GetX() + 0);
    $pdf->Cell(130, 10, 'SUBTOTAL', 1);
    $pdf->Cell(30, 10, "Rs. " . number_format($total, 2), 1, 1, 'R');
    $pdf->Cell(160, 10, 'SALES TAX (18%)', 1);
    $pdf->Cell(30, 10, "Rs. " . number_format($tax, 2), 1, 1, 'R');
    $pdf->Cell(160, 10, 'TOTAL DUE', 1);
    $pdf->Cell(30, 10, "Rs. " . number_format($totalDue, 2), 1, 1, 'R');

    // --------- Footer box start ---------
    $pdf->Ln(10);
    $startX = 10;
    $startY = $pdf->GetY();
    $boxWidth = $pdf->GetPageWidth() - 20;

    // Footer content inside the box
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Make all checks payable to AADDOO Softtech Private Limited', 0, 1);

    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Account Details:', 0, 1);

    $imageX = $startX + $boxWidth - 40; // position image near right inside box
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

    // Draw rectangle around footer box content with some padding
    $pdf->Rect($startX - 2, $startY - 2, $boxWidth + 4, $endY - $startY + 4);

    // --------- Footer box end ---------

    // Add the logo centered below the footer box
    $pdf->Ln(10);
    $pageWidth = $pdf->GetPageWidth();
    $logoWidth = 40;
    $logoX = ($pageWidth - $logoWidth) / 2;
    $pdf->Image('aaddoo.ai.jpg', $logoX, null, $logoWidth);

    // Output PDF file and open in new tab
    $pdfFile = 'invoice1.pdf';
    $pdf->Output('F', $pdfFile);
    echo "<script>window.open('$pdfFile', '_blank');</script>";
}

elseif (isset($_GET['sedi']) && isset($_GET['fund'])) {
    // Logic for fund parameter can be handled similarly if needed
}
?>
